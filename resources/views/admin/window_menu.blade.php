@extends('admin.layout')

@section('admin-content')
    <div class="flex flex-row gap-3 w-full h-auto">
        {{-- current task --}}
        <div class="basis-full bg-base-100 rounded-xl p-4">
            <p class="text-2xl font-bold text-base-content mb-2 text-center">{{ $window->id }}-{{ $window->window_name }}
            </p>
            <div class="grow h-50 shadow flex items-center justify-center bg-base-200 rounded-2xl">
                <p class="text-6xl font-black text-base-500 drop-shadow-sm">
                    @if ($cur_ticket)
                        {{ $cur_ticket->type }}-{{ $cur_ticket->number }}
                    @else
                        ----
                    @endif
                </p>
            </div>
            <div class="flex flex-row gap-3 mt-3">
                <div class="basis-full">
                    <button type="submit" class="btn btn-secondary rounded-md  w-full"
                        onclick="window_calling('{{ $window->id }}')">Call back</button>
                </div>
                {{-- check if the que is empty --}}
                @php
                    $cur_win_que = 0;
                    if ($window->queue_ticket != 0) {
                        $cur_win_que = $window->queue_ticket;
                    }else {
                        $cur_win_que = -1;
                    }
                @endphp
                <form action="{{ url('/admin/reserved_queue/' . $window->id . '-' . $cur_win_que) }}"
                    class="basis-full" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary rounded-md  w-full">Reserved</button>
                </form>
                
                <form action="{{ url('/admin/done_queue/' . $window->id . '-' . $cur_win_que) }}"
                    class="basis-full" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary rounded-md  w-full">Done</button>
                </form>
                <form action="{{ url('/admin/next_queue/' . $window->id) }}" class="basis-full" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary rounded-md  w-full">Next</button>
                </form>
            </div>
            {{-- reserved taks --}}
            <div class="flex flex-col gap-3 pt-3">
                <p class="text-2xl font-bold text-base-content mb-2 text-center">
                    Reserved Tasked
                </p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach ($reserved_tickets as $ticket)
                        {{-- Repeatable Ticket Card --}}
                        <div class="flex basis-full shadow items-center justify-between p-3 bg-base-200 rounded-lg gap-3">
                            <div class="basis-full flex items-center flex-row gap-2 justify-between">
                                <p class="text-xs opacity-60">Ticket</p>
                                <p class="font-mono font-bold text-lg">{{ $ticket->type }}-{{ $ticket->number }}</p>
                            </div>
                            <form action="{{ url('/admin/select_queue/' . $window->id . '-' . $ticket->id) }}"
                                class="basis-auto" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary rounded-md text-nowrap">Do
                                    task</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{-- pending task --}}
        <div class="basis-auto bg-base-100 rounded-xl p-2">
            <p class="text-2xl font-bold text-base-content mb-2 text-center">Pending Task
            </p>
            <div class="flex flex-col gap-2">
                @foreach ($tickets as $ticket)
                    {{-- Repeatable Ticket Card --}}
                    <div class="flex w-60 shadow items-center justify-between p-3 bg-base-200 rounded-lg gap-3">
                        <div class="basis-full flex items-center flex-row gap-2 justify-between">
                            <p class="text-xs opacity-60">Ticket</p>
                            <p class="font-mono font-bold text-lg">{{ $ticket->type }}-{{ $ticket->number }}</p>
                        </div>
                        <form action="{{ url('/admin/select_queue/' . $window->id . '-' . $ticket->id) }}"
                            class="basis-auto" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary rounded-md text-nowrap">Do task</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function window_calling(win_id) {
            fetch('/api/window_call_send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        "window_id": win_id,
                        // Add other data as needed
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data['isCalling']);
                })
                .catch(error => console.error('Error fetching queue data:', error));
        }
    </script>
@endsection
