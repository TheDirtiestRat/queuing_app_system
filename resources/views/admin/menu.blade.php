@extends('admin.layout')

@section('admin-content')
    <div class="flex flex-row gap-4 w-full h-auto">

        {{-- Section 1: Windows Management --}}
        <div class="basis-full bg-base-100 flex flex-col gap-4 rounded-xl p-4 shadow-sm border border-base-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold">Service Windows</h2>
                <div class="badge badge-outline">{{ count($windows) }} Active</div>
            </div>

            <div class="flex flex-row gap-2">
                <form action="{{ url('/admin/add_window/') }}" class="" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm flex-1">Add Window</button>
                </form>
                <form action="{{ url('/admin/reset_window/') }}" class="" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-error btn-sm">Reset All</button>
                </form>
            </div>

            <div class="overflow-y-auto grow">
                <table class="table table-sm w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- list of windows --}}
                        @foreach ($windows as $window)
                            <tr>
                                <td>{{ $window->window_name }}</td>
                                <td><span class="badge badge-success badge-xs">{{ $window->status }}</span></td>
                                <td class="flex flex-row gap-3">
                                    <form action="{{ url('/admin/remove_window/'.$window->id) }}" class="" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                    </form>
                                    <a href="{{ url('admin/window/' . $window->id) }}" target="_blank">
                                        <button class="btn btn-ghost btn-xs text-primary">Open</button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 2: Ticket Queue --}}
        <div class="basis-full bg-base-100 flex flex-col gap-4 rounded-xl p-4 shadow-sm border border-base-200">
            <div class="flex flex-row gap-2 justify-between items-center">
                <h2 class="text-lg font-bold">Waiting Tickets</h2>
                <div class="badge badge-secondary h-auto">{{ count($tickets) }} Pending</div>

                <form action="{{ url('/admin/reset_tickets/') }}" class="" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-error btn-sm">Reset Tickets</button>
                </form>
            </div>
            <form action="{{ url('/admin/genereate-tickets') }}" class="flex flex-row gap-3" method="POST">
                @csrf

                <input type="number" name="number_of_tickets" class="input input-sm">
                <button type="submit" class="btn btn-primary btn-sm flex-1">Generate tickets</button>
            </form>

            <div class="flex flex-col gap-2 overflow-y-auto">
                @foreach ($tickets as $ticket)
                    {{-- Repeatable Ticket Card --}}
                    <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                        <div>
                            <p class="font-mono font-bold text-lg">{{ $ticket->type }}-{{ $ticket->number }}</p>
                            <p class="text-xs opacity-60">{{ $ticket->status }}</p>
                        </div>
                        <button class="btn btn-circle btn-ghost btn-sm">-></button>
                    </div>
                @endforeach


            </div>
        </div>

    </div>
@endsection

{{-- @extends('admin.layout')

@section('admin-content')
    <div class="flex flex-row gap-3 w-full h-full">
        {{-- windows --}
        <div class="basis-full bg-base-100 flex flex-col gap-3 rounded-xl p-2 w-full">
            <p class="text-1xl">Windows</p>
            <div class="flex flex-row gap-3 w-full">
                <button class="btn btn-primary btn-sm basis-full">
                    Add
                </button>
                <button class="btn btn-primary btn-sm">
                    Sub
                </button>
            </div>
        </div>
        {{-- tickets --}
        <div class="basis-full bg-base-100 rounded-xl p-2">

        </div>
        {{-- current --}
        <div class="basis-full bg-base-100 rounded-xl p-2">

        </div>
    </div>
@endsection --}}
