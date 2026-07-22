<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Queue Ticket</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        body {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f1b2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, sans-serif;
        }
        .ticket-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 420px;
            width: 90%;
        }
        .ticket-number {
            font-size: 4rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            color: #1e3a5f;
            margin: 1rem 0;
        }
        .ticket-label {
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }
        .type-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .type-btn {
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            border-radius: 1rem;
            padding: 1.5rem 1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        .type-btn:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59,130,246,0.15);
        }
        .type-btn .code-badge {
            display: inline-block;
            background: #1e3a5f;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            width: 2.5rem;
            height: 2.5rem;
            line-height: 2.5rem;
            border-radius: 0.6rem;
            margin-bottom: 0.5rem;
        }
        .type-btn .name {
            font-weight: 600;
            color: #1f2937;
        }
        .selected-ticket {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 1rem;
            display: none;
        }
        .selected-ticket .label {
            font-size: 0.8rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .selected-ticket .value {
            font-size: 3rem;
            font-weight: 800;
            color: #16a34a;
            letter-spacing: 0.1em;
        }
        .btn-print {
            margin-top: 1rem;
            padding: 0.6rem 2rem;
            background: #1e3a5f;
            color: white;
            border: none;
            border-radius: 0.6rem;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-print:hover {
            background: #152d4a;
        }
        .hidden { display: none; }
        .loading { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>
    <div class="ticket-card" id="app">
        <div class="ticket-label">Select Queue Type</div>

        {{-- Type Selection --}}
        <div id="selectionArea">
            <div class="type-grid">
                @foreach ($types as $type)
                    <button class="type-btn" data-code="{{ $type->code }}" onclick="getTicket('{{ $type->code }}')">
                        <div class="code-badge">{{ $type->code }}</div>
                        <div class="name">{{ $type->name }}</div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Ticket Result --}}
        <div class="selected-ticket" id="ticketResult">
            <div class="label">Your Queue Number</div>
            <div class="value" id="ticketValue">A-001</div>
            <button class="btn-print" onclick="window.print()">Print</button>
            <button class="btn-print" style="background:#6b7280;margin-left:0.5rem;" onclick="resetPage()">Get Another</button>
        </div>

        <p id="errorMsg" class="hidden" style="color:#ef4444;margin-top:1rem;font-size:0.9rem;"></p>
    </div>

    <script>
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function getTicket(typeCode) {
            var selectionArea = document.getElementById('selectionArea');
            var ticketResult = document.getElementById('ticketResult');
            var ticketValue = document.getElementById('ticketValue');
            var errorMsg = document.getElementById('errorMsg');

            selectionArea.classList.add('loading');
            errorMsg.classList.add('hidden');

            var formData = new FormData();
            formData.append('type_code', typeCode);
            formData.append('_token', csrfToken);

            fetch('{{ url("/generate-ticket") }}', {
                method: 'POST',
                body: formData,
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.ticket) {
                    ticketValue.innerText = data.ticket;
                    selectionArea.style.display = 'none';
                    ticketResult.style.display = 'block';
                }
            })
            .catch(function() {
                errorMsg.innerText = 'Failed to generate ticket. Please try again.';
                errorMsg.classList.remove('hidden');
                selectionArea.classList.remove('loading');
            });
        }

        function resetPage() {
            document.getElementById('ticketResult').style.display = 'none';
            document.getElementById('selectionArea').style.display = 'block';
            document.getElementById('selectionArea').classList.remove('loading');
        }
    </script>
</body>
</html>
