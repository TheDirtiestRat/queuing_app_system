<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QueuingController extends Controller
{
    // pages
    public function monitor_page()
    {
        $windows = DB::table('windows')->latest()->get();
        $tickets = DB::table('queue')->where("status", "!=", "done")->orderBy('number', 'asc')->limit(5)->get();
        $videos = DB::table('video_list')->get()->map(function ($video) {
            $video->video_url = self::resolveVideoUrl($video->video_path);
            return $video;
        });

        return view('pages.queue-monitor-display', compact('windows', 'tickets', 'videos'));
    }

    public function menu_page()
    {
        $windows = DB::table('windows')->latest()->get();
        $tickets = DB::table('queue')->orderBy('number', 'asc')->get();
        $videos = DB::table('video_list')->latest()->get()->map(function ($video) {
            $video->display_name = $video->original_name ?? basename($video->video_path);
            $video->video_url = self::resolveVideoUrl($video->video_path);
            return $video;
        });

        return view('admin.menu', compact('windows', 'tickets', 'videos'));
    }

    public function window_page(string $id)
    {
        $window = DB::table('windows')->where('id', $id)->first();

        if (!$window) {
            return back();
        }
        $cur_ticket = DB::table('queue')->where('id', $window->queue_ticket)->first();
        $tickets = DB::table('queue')
            ->whereNotIn('status', ['done', 'pending', 'reserved'])
            ->orderBy('number', 'asc')
            ->limit(10)
            ->get();

        $reserved_tickets = DB::table('queue')
            ->where('status', 'reserved')
            ->orderBy('number', 'asc')
            ->get();

        return view('admin.window_menu', compact('window', 'cur_ticket', 'tickets', 'reserved_tickets'));
    }

    // tickets function
    public function generate_tickets(Request $request)
    {
        $request->validate([
            'number_of_tickets' => 'required|integer|min:1',
        ]);

        $number_of_tickets = (int) $request->input("number_of_tickets");
        $lastTicket = DB::table('queue')->orderBy('id', 'desc')->value('number');
        $startFrom = $lastTicket ? (int) $lastTicket + 1 : 1;

        $tickets = [];
        for ($i = 0; $i < $number_of_tickets; $i++) {
            $num = $startFrom + $i;
            $tickets[] = [
                "number" => str_pad($num, 3, '0', STR_PAD_LEFT),
                "type" => "A",
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }

        DB::table('queue')->insert($tickets);

        return redirect('admin/menu');
    }

    public function next_queue(string $window_id)
    {
        $ticket = DB::table('queue')
            ->whereNotIn('status', ['done', 'pending', 'reserved'])
            ->orderBy('number', 'asc')
            ->first();

        if (!$ticket) {
            return back();
        }

        DB::transaction(function () use ($ticket, $window_id) {
            DB::table('queue')->where('id', $ticket->id)->update(['status' => 'done']);
            DB::table('windows')->where('id', $window_id)->update(['queue_ticket' => $ticket->id]);
        });

        return redirect('/admin/window/' . $window_id);
    }

    public function select_queue(string $window_id, $queue_id)
    {
        $ticket = DB::table('queue')
            ->whereNotIn('status', ['done', 'pending'])
            ->where('id', $queue_id)
            ->first();

        if (!$ticket) {
            return back();
        }

        DB::transaction(function () use ($ticket, $window_id) {
            DB::table('queue')->where('id', $ticket->id)->update(['status' => 'pending']);
            DB::table('windows')->where('id', $window_id)->update(['queue_ticket' => $ticket->id]);
        });

        return redirect('/admin/window/' . $window_id);
    }

    public function done_queue(string $window_id, $queue_id)
    {
        if (!$queue_id) {
            return redirect('/admin/window/' . $window_id);
        }

        DB::transaction(function () use ($queue_id, $window_id) {
            DB::table('queue')->where('id', $queue_id)->update(['status' => 'done']);
            DB::table('windows')->where('id', $window_id)->update(['queue_ticket' => null]);
        });

        return redirect('/admin/window/' . $window_id);
    }

    public function reserved_queue(string $window_id, $queue_id)
    {
        if (!$queue_id) {
            return redirect('/admin/window/' . $window_id);
        }

        DB::transaction(function () use ($queue_id, $window_id) {
            DB::table('queue')->where('id', $queue_id)->update(['status' => 'reserved']);
            DB::table('windows')->where('id', $window_id)->update(['queue_ticket' => null]);
        });

        return redirect('/admin/window/' . $window_id);
    }

    // windows function
    public function add_window(Request $request)
    {
        $count = DB::table('windows')->count();
        DB::table('windows')->insert([
            "window_name" => "Window " . ($count + 1),
            "status" => "online",
        ]);

        return redirect('admin/menu');
    }

    public function remove_window(string $id)
    {
        DB::table('windows')->where('id', $id)->delete();

        return redirect('admin/menu');
    }

    public function reset_window()
    {
        DB::table('windows')->delete();
        DB::table('queue')->delete();

        return redirect('admin/menu');
    }

    public function reset_tickets()
    {
        DB::table('windows')->update(['queue_ticket' => null]);
        DB::table('queue')->update(['status' => 'ready']);

        return redirect('admin/menu');
    }

    // apis
    public function monitor_update()
    {
        $windows = DB::table('windows')
            ->leftJoin('queue', 'windows.queue_ticket', '=', 'queue.id')
            ->select(
                'windows.id',
                'windows.window_name',
                'windows.status',
                'windows.queue_ticket',
                'windows.isCalling',
                'windows.created_at',
                'windows.updated_at',
                'queue.number',
                'queue.type',
                'queue.status as ticket_status'
            )
            ->latest('windows.created_at')
            ->get();
        $tickets = DB::table('queue')->where("status", "!=", "done")->where("status", "!=", "pending")->orderBy('number', 'asc')->limit(5)->get();
        $pending = DB::table('queue')->where("status", "!=", "done")->where("status", "!=", "pending")->count();

        return response()->json([
            'windows' => $windows,
            'tickets' => $tickets,
            'pending' => $pending,
        ]);
    }

    public function window_calling_sending(Request $request)
    {
        $request->validate([
            'window_id' => 'required',
        ]);

        DB::table('windows')->where("id", "=", $request->input('window_id'))->update([
            "isCalling" => 1
        ]);

        return response()->json([
            'isCalling' => true,
        ]);
    }

    public function window_calling_recieved(Request $request)
    {
        $request->validate([
            'window_id' => 'required',
        ]);

        DB::table('windows')->update([
            "isCalling" => 0,
        ]);

        $ticket = DB::table('windows')->where("id", $request->input('window_id'))->first();

        return response()->json([
            'isCalling' => false,
            'win_id' => $request->input('window_id'),
            // 'win_calling' => $ticket->isCalling,
        ]);
    }

    public function upload_chunk(Request $request)
    {
        try {
            $handlerClass = \Pion\Laravel\ChunkUpload\Handler\HandlerFactory::classFromRequest($request);

            $receiver = new \Pion\Laravel\ChunkUpload\Receiver\FileReceiver(
                'file', $request, $handlerClass
            );

            if (!$receiver->isUploaded()) {
                return response()->json([
                    'error' => 'No file uploaded',
                    'has_file' => $request->hasFile('file'),
                    'all_files' => array_keys($request->allFiles()),
                    'input' => $request->except('_token'),
                ], 400);
            }

            $save = $receiver->receive();

            if ($save->isFinished()) {
                $file = $save->getFile();
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '_' . str_replace(' ', '_', $originalName) . '.' . $ext;

                $file->move(storage_path('app/public/videos'), $filename);

                DB::table('video_list')->insert([
                    "video_path" => 'public/videos/' . $filename,
                    "original_name" => $originalName . '.' . $ext,
                ]);

                return response()->json(['done' => true, 'message' => 'Video uploaded successfully!']);
            }

            $handler = $save->handler();

            return response()->json([
                'done' => false,
                'percentage' => $handler->getPercentageDone(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function store_video_url(Request $request)
    {
        $request->validate([
            'video_path' => 'required|string|max:500',
        ]);

        DB::table('video_list')->insert([
            "video_path" => $request->input('video_path'),
            "original_name" => basename($request->input('video_path')),
        ]);

        return back()->with('success', 'Video URL added successfully!');
    }

    public function delete_video(string $id)
    {
        $video = DB::table('video_list')->where('id', $id)->first();

        if (!$video) {
            return back();
        }

        $path = storage_path('app/' . $video->video_path);
        if (file_exists($path)) {
            unlink($path);
        }

        DB::table('video_list')->where('id', $id)->delete();

        return back()->with('success', 'Video deleted successfully!');
    }

    // ---- Queue Types CRUD ----

    public function queue_types_index()
    {
        $types = DB::table('queue_types')->orderBy('name')->get();
        return view('admin.queue_types', compact('types'));
    }

    public function queue_types_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:5|unique:queue_types,code',
        ]);

        DB::table('queue_types')->insert([
            'name' => $request->input('name'),
            'code' => strtoupper($request->input('code')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/queue-types');
    }

    public function queue_types_update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:5|unique:queue_types,code,' . $id,
        ]);

        DB::table('queue_types')->where('id', $id)->update([
            'name' => $request->input('name'),
            'code' => strtoupper($request->input('code')),
            'updated_at' => now(),
        ]);

        return redirect('/admin/queue-types');
    }

    public function queue_types_destroy(string $id)
    {
        DB::table('queue_types')->where('id', $id)->delete();
        return redirect('/admin/queue-types');
    }

    // ---- Public Ticket Selection ----

    public function get_ticket_page()
    {
        $types = DB::table('queue_types')->orderBy('name')->get();
        return view('pages.get-ticket', compact('types'));
    }

    public function generate_ticket(Request $request)
    {
        $request->validate([
            'type_code' => 'required|string|exists:queue_types,code',
        ]);

        $typeCode = $request->input('type_code');

        $lastTicket = DB::table('queue')
            ->where('type', $typeCode)
            ->orderBy('id', 'desc')
            ->value('number');

        $nextNumber = $lastTicket ? (int) $lastTicket + 1 : 1;
        $number = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        DB::table('queue')->insert([
            'number' => $number,
            'type' => $typeCode,
            'status' => 'ready',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'ticket' => $typeCode . '-' . $number,
            'number' => $number,
            'type' => $typeCode,
        ]);
    }

    private static function resolveVideoUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        $path = str_replace('\\', '/', $path);
        $path = str_replace('public/', 'storage/', $path);

        if (str_starts_with($path, '/')) {
            return url($path);
        }

        return asset($path);
    }
}
