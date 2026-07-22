@extends('admin.layout')

@section('admin-content')
    <div class="bg-base-100 flex flex-col gap-4 rounded-xl p-4 shadow-sm border border-base-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-bold">Queue Types</h2>
            <div class="badge badge-outline">{{ count($types) }} type(s)</div>
        </div>

        <form action="{{ url('/admin/queue-types') }}" method="POST" class="flex flex-row gap-3 items-end">
            @csrf
            <div class="grow">
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" class="input input-bordered input-sm w-full rounded-xl" placeholder="e.g. Payments" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Code</label>
                <input type="text" name="code" class="input input-bordered input-sm w-20 rounded-xl uppercase" placeholder="e.g. P" maxlength="5" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Add</button>
        </form>

        <div class="overflow-y-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Added</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($types as $type)
                        <tr>
                            <td class="font-medium">{{ $type->name }}</td>
                            <td><span class="badge badge-primary badge-sm font-mono">{{ $type->code }}</span></td>
                            <td><span class="text-xs opacity-60">{{ $type->created_at }}</span></td>
                            <td class="flex flex-row gap-2">
                                <button class="btn btn-ghost btn-xs text-info edit-btn"
                                    data-id="{{ $type->id }}"
                                    data-name="{{ $type->name }}"
                                    data-code="{{ $type->code }}">Edit</button>
                                <form action="{{ url('/admin/queue-types/' . $type->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this queue type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center opacity-60 py-4">No queue types added yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Edit Modal --}}
    <dialog id="editModal" class="modal">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-lg font-bold">Edit Queue Type</h3>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost">X</button>
                </form>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" name="name" id="editName" class="input input-bordered w-full rounded-xl" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Code</label>
                        <input type="text" name="code" id="editCode" class="input input-bordered w-20 rounded-xl uppercase" maxlength="5" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
@endsection

@section('admin-scripts')
<script>
    document.querySelectorAll('.edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            var code = this.getAttribute('data-code');
            document.getElementById('editForm').action = '/admin/queue-types/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editCode').value = code;
            document.getElementById('editModal').showModal();
        });
    });
</script>
@endsection
