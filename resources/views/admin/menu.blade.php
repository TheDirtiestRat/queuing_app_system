@extends('admin.layout')

@section('admin-content')
    <div class="flex flex-row gap-4 w-full h-auto">

        {{-- Section 1: Windows Management --}}
        <div class="basis-full bg-base-100 flex flex-col gap-4 rounded-xl p-4 shadow-sm border border-base-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold">Video Management</h2>
                <div class="badge badge-outline">{{ count($videos) }} video(s)</div>
            </div>

            {{-- upload video form --}}
            <div id="uploadSection" class="bg-base-200 rounded-xl p-4">
                <div class="flex flex-row gap-3 items-end">
                    <div class="grow">
                        <label class="block text-sm font-medium mb-1">Upload Video File (large files supported)</label>
                        <input type="file" id="fileInput" class="file-input file-input-bordered w-full rounded-xl" accept="video/*">
                    </div>
                    <button type="button" id="uploadBtn" class="btn btn-primary" onclick="startUpload()">Upload</button>
                </div>
                <div id="progressArea" class="hidden mt-3">
                    <progress id="progressBar" class="progress progress-primary w-full" value="0" max="100"></progress>
                    <p id="progressText" class="text-xs mt-1">0%</p>
                </div>
                <div class="divider text-xs opacity-50">OR</div>
                <div class="flex flex-row gap-3 items-end">
                    <div class="grow">
                        <label class="block text-sm font-medium mb-1">Link Remote URL</label>
                        <input type="text" id="urlInput" class="input input-bordered w-full rounded-xl"
                            placeholder="http://example.com/video.mp4">
                    </div>
                    <button type="button" class="btn btn-outline btn-primary" onclick="addUrl()">Add Link</button>
                </div>
            </div>

            {{-- list of videos --}}
            <div class="overflow-y-auto basis-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($videos as $video)
                            <tr>
                                <td><span class="badge badge-ghost badge-sm">{{ $video->display_name }}</span></td>
                                <td><span class="text-xs opacity-60">{{ $video->created_at }}</span></td>
                                <td class="flex flex-row gap-2">
                                    <button class="btn btn-ghost btn-xs text-primary preview-btn"
                                        data-url="{{ $video->video_url }}"
                                        data-name="{{ $video->display_name }}">Preview</button>
                                    <form action="{{ url('/delete-video/' . $video->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this video?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center opacity-60 py-4">No videos added yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
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
                                    <form action="{{ url('/admin/remove_window/' . $window->id) }}" class=""
                                        method="POST">
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
            <form action="{{ url('/admin/generate-tickets') }}" class="flex flex-row gap-3" method="POST">
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

@section('admin-scripts')
{{-- Video Preview Modal --}}
<dialog id="video_preview_modal" class="modal" onclick="if (event.target === this) this.close()">
    <div class="modal-box w-11/12 max-w-3xl p-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-bold" id="preview_modal_title">Video Preview</h3>
            <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('video_preview_modal').close()">X</button>
        </div>
        <video id="preview_video_player" class="w-full rounded-xl bg-black" controls playsinline preload="metadata">
            Your browser does not support the video tag.
        </video>
    </div>
</dialog>

<script>
    // ---- Video Preview ----
    var modal = document.getElementById('video_preview_modal');
    var player = document.getElementById('preview_video_player');
    var titleEl = document.getElementById('preview_modal_title');

    document.querySelectorAll('.preview-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-url');
            var name = this.getAttribute('data-name') || 'Video Preview';
            titleEl.innerText = name;
            player.src = url;
            modal.showModal();
            player.play().catch(function() {});
        });
    });

    player.addEventListener('error', function() {
        titleEl.innerText = 'Error: Could not load video. Check the URL or path.';
    });

    modal.addEventListener('close', function() {
        player.pause();
        player.removeAttribute('src');
        titleEl.innerText = 'Video Preview';
    });

    // ---- Chunked File Upload ----
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var chunkSize = 2 * 1024 * 1024; // 2MB per chunk

    function startUpload() {
        var fileInput = document.getElementById('fileInput');
        var file = fileInput.files[0];
        if (!file) return;

        var uploadBtn = document.getElementById('uploadBtn');
        var progressArea = document.getElementById('progressArea');
        var progressBar = document.getElementById('progressBar');
        var progressText = document.getElementById('progressText');

        uploadBtn.disabled = true;
        progressArea.classList.remove('hidden');

        var totalChunks = Math.ceil(file.size / chunkSize);
        var currentChunk = 0;

        function uploadNextChunk() {
            var chunk = file.slice(currentChunk * chunkSize, (currentChunk + 1) * chunkSize);

            var formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('file', chunk, file.name);
            formData.append('chunk', currentChunk);
            formData.append('chunks', totalChunks);

            console.log('Sending chunk', currentChunk + 1, 'of', totalChunks, 'bytes', chunk.size);

            fetch('{{ route("video.upload") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData,
            })
            .then(function(res) {
                if (!res.ok) {
                    return res.json().then(function(errData) {
                        throw new Error(JSON.stringify(errData));
                    }).catch(function() {
                        throw new Error('HTTP ' + res.status);
                    });
                }
                return res.json();
            })
            .then(function(data) {
                if (data.done) {
                    progressBar.value = 100;
                    progressText.innerText = 'Complete!';
                    setTimeout(function() { location.reload(); }, 500);
                } else {
                    currentChunk++;
                    var pct = Math.round((currentChunk / totalChunks) * 100);
                    progressBar.value = pct;
                    progressText.innerText = pct + '%';
                    uploadNextChunk();
                }
            })
            .catch(function(err) {
                progressText.innerText = 'Error: ' + err.message;
                uploadBtn.disabled = false;
                console.error(err);
            });
        }

        uploadNextChunk();
    }

    // ---- Add Remote URL ----
    function addUrl() {
        var urlInput = document.getElementById('urlInput');
        var url = urlInput.value.trim();
        if (!url) return;

        var formData = new FormData();
        formData.append('video_path', url);

        fetch('{{ route("video.store_url") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        })
        .then(function() { location.reload(); })
        .catch(function(err) { console.error(err); });
    }
</script>
@endsection
