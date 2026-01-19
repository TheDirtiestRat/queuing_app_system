<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueuingController extends Controller
{
    // pages
    public function monitor_page()
    {
        $windows = DB::table('windows')->latest()->get();
        $tickets = DB::table('queue')->where("status", "!=", "done")->orderBy('number', 'asc')->limit(5)->get();

        return view('pages.queue-monitor-display', compact('windows', 'tickets'));
    }

    public function menu_page()
    {
        $windows = DB::table('windows')->latest()->get();
        $tickets = DB::table('queue')->orderBy('number', 'asc')->get();
        return view('admin.menu', compact('windows', 'tickets'));
    }

    public function window_page(string $id)
    {
        $window = DB::table('windows')->where('id', $id)->first();

        if (!$window) {
            return back();
        }
        $cur_ticket = DB::table('queue')->where('id', $window->queue_ticket)->first();
        $tickets = DB::table('queue')->where("status", "!=", "done")->where("status", "!=", "pending")->orderBy('number', 'asc')->limit(10)->get();

        return view('admin.window_menu', compact('window', 'cur_ticket','tickets'));
    }

    // tickets function
    public function generate_tickets(Request $request)
    {
        $request->validate([
            'number_of_tickets' => 'required',
        ]);

        $number_of_tickets = $request->input("number_of_tickets");

        if ($number_of_tickets <= 0) {
            return back();
        }

        for ($tickets = 1; $tickets <= $number_of_tickets; $tickets++) {
            $s_num = "";

            if ($tickets < 100 && $tickets >= 10) {
                $s_num = "0" . $tickets;
            } else if ($tickets < 10) {
                $s_num = "00" . $tickets;
            } else {
                $s_num = "" . $tickets;
            }

            DB::table('queue')->insert([
                "number" => $s_num,
                "type" => "A",
            ]);
        }

        return redirect('admin/menu');
    }

    public function next_queue(string $window_id)
    {
        $ticket = DB::table('queue')->where("status", "!=", "done")->where("status", "!=", "pending")->orderBy('number', 'asc')->first();

        if (!$ticket) {
            return back();
        }

        DB::table('queue')->where("id", $ticket->id)->update([
            "status" => "pending",
        ]);
        DB::table('windows')->where("id", $window_id)->update([
            "queue_ticket" => $ticket->id,
        ]);

        return redirect('/admin/window/' . $window_id);
    }

    public function done_queue(string $window_id, $queue_id)
    {
        DB::table('queue')->where("id", $queue_id)->update([
            "status" => "done",
        ]);
        DB::table('windows')->where("id", $window_id)->update([
            "queue_ticket" => null,
        ]);

        return redirect('/admin/window/' . $window_id);
    }

    // windows function
    public function add_window(Request $request)
    {
        $total_windows = DB::table('windows')->count("window_name");
        $window_name = "Window";

        if ($total_windows >= 0) {
            $cur_num = $total_windows + 1;
            $window_name = $window_name . " " . $cur_num;
        }

        DB::table('windows')->insert([
            "window_name" => $window_name,
            "status" => "online",
        ]);

        return redirect('admin/menu');
    }

    public function remove_window(string $id)
    {
        DB::table('windows')->delete($id);

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
        DB::table('windows')->update([
            "queue_ticket" => null
        ]);
        DB::table('queue')->update([
            "status" => 'ready'
        ]);

        return redirect('admin/menu');
    }

    // apis
    public function monitor_update()
    {
        // $windows = DB::table('windows')->latest()->get();
        $windows = DB::table('windows')
            ->leftJoin('queue', 'windows.queue_ticket', '=', 'queue.id')
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

        DB::table('windows')->where("id", "=", $request->input('window_id'))->update([
            "isCalling" => 0
        ]);

        return response()->json([
            'isCalling' => false,
        ]);
    }
}
