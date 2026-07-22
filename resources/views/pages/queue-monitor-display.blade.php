<!DOCTYPE html>
<html lang="en" data-theme="light"> {{-- Added data-theme for DaisyUI consistency --}}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Queue Monitor</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="bg-base-200 overflow-hidden">
    <div class="w-screen h-screen p-4 flex flex-col gap-4">
        {{-- 2. Main Body --}}
        <div class="grow flex flex-row gap-4 overflow-hidden">

            {{-- Advertisement / Video --}}
            <div
                class="basis-full flex flex-col rounded-3xl overflow-hidden bg-linear-to-r from-blue-900 via-blue-800  to-black">
                <video class="basis-full bg-black" id="videoPlayer" autoplay muted playsinline>
                    Your browser does not support the video tag.
                </video>
                <div class="basis-auto flex flex-row gap-4 items-center p-3 px-4">
                    <h2 class="text-4xl font-bold text-white text-nowrap">ACLC ORMOC</h2>
                    <div class="text-white  ">Please have your tickets ready</div>
                </div>
            </div>


            {{-- Current Tickets (Now Serving) --}}
            <div class="basis-1/3 flex flex-col gap-4">
                <div
                    class="text-center py-2 bg-primary text-primary-content rounded-xl font-bold uppercase tracking-widest">
                    Now Serving
                </div>
                <div class="basis-full grid grid-cols-@if (count($windows) <= 3) 1 @else 2 @endif gap-3">
                    @foreach ($windows as $window)
                        <div
                            class="grow basis-full bg-black rounded-3xl relative overflow-hidden flex items-center justify-center">
                            <div class="bg-gray-300 border border-gray-400 absolute w-full h-full"
                                id="window_panel_{{ $window->window_name }}"></div>
                            <div class=" absolute rounded-xl p-4 flex flex-col shadow w-full h-full">
                                <p class="text-2xl font-bold text-base-content/70 mb-2 text-center">
                                    {{ $window->window_name }}</p>
                                <div class="grow flex items-center justify-center bg-base-200 rounded-2xl">
                                    {{-- Huge numbers for visibility --}}
                                    <p class="text-6xl font-black text-primary drop-shadow-sm"
                                        id="window_{{ $window->window_name }}">---</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Next Tickets (Queue) --}}
            <div class="basis-1/4 flex flex-col gap-4">
                <div
                    class="text-center py-2 bg-base-300 text-base-content rounded-xl font-bold uppercase tracking-widest">
                    Next in Line
                </div>
                @for ($i = 0; $i < 5; $i++)
                    <div
                        class="basis-full bg-white rounded-2xl p-4 flex flex-row items-center justify-between shadow-sm">
                        <div class="flex items-center gap-4">
                            <span
                                class="w-10 rounded-full bg-base-200 flex items-center justify-center font-bold">{{ $i + 1 }}</span>
                            <p class="text-4xl font-black font-mono text-nowrap" id="queue_{{ $i }}">---</p>
                            <div class="badge badge-ghost font-semibold">Ready</div>
                        </div>
                    </div>
                @endfor

                <div
                    class="basis-auto text-center bg-base-300 text-base-content rounded-xl font-bold uppercase tracking-widest flex justify-center items-center p-3">
                    <div>
                        <span id="pending_out">00</span> Pending
                    </div>
                </div>

                {{-- <button class="basis-auto btn" id="btnAPI" onclick='window_calling_recieved("0")'>disable isCalling</button> --}}
                <button class="basis-auto btn hidden" id="btnSound" onclick='playAudio()'>Flash with
                    sound</button>
            </div>
        </div>

        {{-- 3. Bottom Ticker --}}
        <div
            class="h-12 bg-linear-to-r from-blue-900 via-blue-800  to-black text-neutral-content rounded-xl flex flex-row items-center overflow-hidden relative">

            <div class="whitespace-nowrap overflow-hidden basis-full flex items-center gap-10">
                <p class="text-lg animate-marquee">
                    BY MR DIRTYRATS with the PROGRAMMERS GUILD
                    🕒 Our operating hours are 8:00 AM to 5:00 PM Monday-Saturday.
                </p>

            </div>

            <div class="p-3 basis-auto">
                <p id="current-time" class="text-3xl font-bold font-mono text-nowrap">14:36:12</p>
            </div>
        </div>
    </div>

    {{-- audios --}}
    <audio id="soundPlayer" src="{{ asset('/storage/sounds/notification_sound_1.mp3') }}" preload="auto"
        allow="autoplay"></audio>

    {{-- hidden file input for loading local videos --}}
    <input type="file" id="localVideoInput" accept="video/*" multiple
        style="position:fixed;opacity:0;pointer-events:none;width:0;height:0">
    <button id="loadLocalBtn"
        class="fixed bottom-20 right-4 btn btn-xs btn-ghost opacity-30 hover:opacity-100 z-50"
        title="Load local video files">Load Videos</button>
    <div id="localVideoCount"
        class="fixed bottom-14 right-4 text-xs opacity-0 transition-opacity z-50"></div>

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            animation: marquee 30s linear infinite;
        }
    </style>

    <script>
        // Simple clock script
        setInterval(() => {
            const now = new Date();
            document.getElementById('current-time').innerText = now.toLocaleTimeString('en-US', {
                hour12: true
            });
        }, 1000);

        // Update the monitor by interval
        function updateQueue(tickets, pendings) {
            const pendingElement = document.getElementById(`pending_out`);
            pendingElement.innerText = Math.max(0, pendings - 5)

            for (let i = 0; i <= tickets.length; i++) {
                if (tickets[i]) {
                    const ticketElement = document.getElementById(`queue_${i}`);

                    ticketElement.innerText = tickets[i].type + "-" + tickets[i].number;
                }
            }
        }

        function playAudio() {
            const theSound = document.getElementById(`soundPlayer`);
            theSound.play();
        }

        function speak(text_to_speak = "Calling A-002 on Window 1!") {
            if ('speechSynthesis' in window) {
                // Create a SpeechSynthesisUtterance
                const utterance = new SpeechSynthesisUtterance(text_to_speak);

                // 2. Customize properties (optional)
                utterance.volume = 1; // 0 to 1
                utterance.rate = 0.8; // 0.1 to 10
                utterance.pitch = 1.3; // 0 to 2
                utterance.lang = 'en-US'; // BCP 47 language tag

                // Select a voice
                const voices = speechSynthesis.getVoices();
                utterance.voice = voices[0]; // Choose a specific voice

                utterance.onend = function(event) {
                    console.log('Speech finished in ' + event.elapsedTime + ' seconds.');
                };

                // Speak the text
                speechSynthesis.speak(utterance);
            } else {
                console.log('Speech synthesis not supported in this browser.');
            }
        }

        function flashWindow(elemet_id) {
            const dot = document.getElementById(`window_panel_${elemet_id}`);
            // Using an arbitrary value "once" ping we discussed earlier
            const animationClass = 'animate-[pulse_1s_ease-in-out_1]';
            // play the sound
            const soundBtn = document.getElementById('btnSound');
            soundBtn.click();

            dot.classList.remove(animationClass);
            void dot.offsetWidth; // Force Reset
            dot.classList.add(animationClass);
        }



        function window_calling_recieved(win_id) {
            fetch('/api/window_call_recieved', {
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
                    console.log(data);
                })
                .catch(error => console.error('Error fetching queue data:', error));
        }

        function updateWindow(windows) {
            windows.forEach(window => {
                const windowElement = document.getElementById(`window_${ window.window_name }`);
                if (windowElement) {
                    let toChangeText = window.type + "-" + window.number;

                    if (window.type) {
                        if (windowElement.innerText != toChangeText || window.isCalling) {
                            flashWindow(window.window_name)

                            window_calling_recieved(window.id)

                            // play text to speech
                            speak("Calling the Number " + toChangeText + " on " + window.window_name + "!");
                            console.log(window.id)
                        }

                        windowElement.innerText = toChangeText // Update the ticket element text
                    } else {
                        windowElement.innerText = "---"
                    }
                }
            });
        }

        function updateMonitor() {
            fetch('/api/monitor-update')
                .then(response => response.json())
                .then(data => {
                    // Update windows
                    updateQueue(data['tickets'], data['pending'])
                    updateWindow(data['windows'])
                    // console.log(data['windows'])
                })
                .catch(error => console.error('Error fetching queue data:', error));
        }



        setInterval(updateMonitor, 1000);
        updateMonitor();
    </script>

    <script>
        var videoPlayer = document.getElementById('videoPlayer');
        var playlist = [
            @foreach ($videos as $video)
                "{{ $video->video_url }}",
            @endforeach
        ];
        var currentVideo = 0;

        function playVideo(index) {
            if (playlist.length === 0 || !playlist[index]) return;
            videoPlayer.src = playlist[index];
            videoPlayer.volume = 0.3;
            videoPlayer.load();
            var promise = videoPlayer.play();
            if (promise) {
                promise.catch(function(err) {
                    console.log('Could not play video, retrying in 2s...', err);
                    setTimeout(function() { playVideo(index); }, 2000);
                });
            }
        }

        function playNextVideo() {
            currentVideo = (currentVideo + 1) % playlist.length;
            playVideo(currentVideo);
        }

        function loadLocalVideos() {
            document.getElementById('localVideoInput').click();
        }

        document.getElementById('localVideoInput').addEventListener('change', function(e) {
            var files = e.target.files;
            if (!files.length) return;

            var wasEmpty = playlist.length === 0;

            for (var i = 0; i < files.length; i++) {
                if (files[i].type.startsWith('video/')) {
                    playlist.push(URL.createObjectURL(files[i]));
                }
            }

            var countEl = document.getElementById('localVideoCount');
            countEl.textContent = playlist.length + ' video(s)';
            countEl.classList.remove('opacity-0');
            countEl.classList.add('opacity-100');
            setTimeout(function() {
                countEl.classList.remove('opacity-100');
                countEl.classList.add('opacity-0');
            }, 4000);

            if (wasEmpty) {
                if (playlist.length > 0) {
                    videoPlayer.addEventListener('ended', playNextVideo, false);
                    videoPlayer.addEventListener('error', function() {
                        playNextVideo();
                    });
                    playVideo(0);
                }
            }

            this.value = '';
        });

        document.getElementById('loadLocalBtn').addEventListener('click', loadLocalVideos);

        if (playlist.length > 0) {
            videoPlayer.addEventListener('ended', playNextVideo, false);
            videoPlayer.addEventListener('error', function() {
                console.log('Video error, skipping to next');
                playNextVideo();
            });
            playVideo(0);
        }
    </script>
</body>

</html>
