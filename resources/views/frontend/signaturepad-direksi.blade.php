<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tanda Tangan</title>
    <!-- Menerapkan font Inter dan Tailwind CSS untuk styling -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Warna latar belakang abu-abu terang */
            margin: 0;
            min-height: 100vh;
        }

        .page-shell {
            min-height: 100vh;
            display: flex;
            gap: 1rem;
            padding: 1rem;
            box-sizing: border-box;
            align-items: stretch;
        }

        .sidebar {
            width: 320px;
            flex: 0 0 320px;
            height: 100vh; /* setinggi layar */
            background-color: #111827;
            color: #f9fafb;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.10);
            display: flex;
            flex-direction: column;
            overflow-y: auto; /* scroll vertikal */
        }

        .sidebar h3 {
            font-size: 1rem;
            margin: 0 0 0.5rem 0;
            font-weight: 600;
        }

        .sidebar p {
            margin: 0 0 1rem 0;
            color: #d1d5db;
            font-size: 0.85rem;
        }

        .sidebar-list {
            overflow-y: auto;
            padding-right: 0.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .sidebar-item {
            display: block;
            border-radius: 0.85rem;
            padding: 0.85rem 0.9rem;
            background: rgba(255, 255, 255, 0.06);
            color: #f9fafb;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.10);
            transform: translateY(-1px);
        }

        .sidebar-item.active {
            background: #2563eb;
            border-color: #60a5fa;
        }

        .sidebar-item-title {
            font-size: 0.92rem;
            font-weight: 600;
            line-height: 1.35;
            margin-bottom: 0.35rem;
        }

        .sidebar-item-meta {
            font-size: 0.78rem;
            color: #cbd5e1;
            line-height: 1.4;
        }

        .sidebar-badge {
            display: inline-block;
            margin-top: 0.55rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            font-size: 0.72rem;
            font-weight: 600;
        }

        .content-shell {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-width: 0;
        }

        .signature-container {
            background-color: white;
            border-radius: 0.75rem; /* Sudut membulat */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Efek bayangan */
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            text-align: center;
            margin: 0;
        }

        h2 {
            color: #1f2937;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .canvas-wrapper {
            border: 2px dashed #d1d5db; /* Garis putus-putus untuk area tanda tangan */
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            overflow: hidden; /* Mencegah coretan keluar dari batas */
            position: relative;
        }

        canvas {
            display: block;
            width: 100%;
            /* Tinggi tetap agar area tanda tangan konsisten */
            height: 300px;
            cursor: crosshair;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            border: none;
        }

        .btn-clear {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .btn-clear:hover {
            background-color: #e5e7eb;
        }

        .btn-reset {
            background-color: rgba(11, 11, 170, 0.91);
            color: white;
        }

        .btn-reset:hover {
            background-color: rgba(11, 11, 170, 1);
        }

        .btn-save {
            background-color: #3ef63b;
            color: white;
        }

        .btn-save:hover {
            background-color: #3ef63b;
        }

        .saved-signature {
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: #f9fafb;
            text-align: left;
            overflow-x: auto;
        }

        .saved-signature svg {
            width: 100%;
            height: auto;
        }
        
        /* Area untuk menampilkan pesan sukses/gagal */
        #message-area {
            margin-top: 1rem;
            min-height: 1.5rem;
            font-size: 0.875rem;
        }
        .text-success { color: #10b981; }
        .text-error { color: #ef4444; }

        @media (max-width: 1024px) {
            .page-shell {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                flex: none;
            }

            .content-shell {
                width: 100%;
            }

            .signature-container {
                width: 100%;
                max-width: 100%;
            }
        }

    </style>
    <!-- Memuat library Signature Pad dari CDN -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
</head>
<body>
    <div class="page-shell">
        <aside class="sidebar">
            <h3>Pelatihan Menunggu Tanda Tangan</h3>
            <p>Daftar report training yang belum memiliki tanda tangan direktur.</p>

            <div class="sidebar-list">
                @forelse(($pendingReports ?? []) as $pendingReport)
                    <a href="{{ url('director-report-signature') }}?report_id={{ $pendingReport->id }}" class="sidebar-item {{ isset($report) && $report->id === $pendingReport->id ? 'active' : '' }}">
                        <div class="sidebar-item-title">
                            {{ $pendingReport->event->workshop->name ?? 'Tanpa judul pelatihan' }}
                        </div>
                        <div class="sidebar-item-meta">
                            <div>Report date: {{ $pendingReport->report_date ? \Carbon\Carbon::parse($pendingReport->report_date)->translatedFormat('d F Y') : '-' }}</div>
                            <div>Manager: {{ $pendingReport->managerUser->name ?? '-' }}</div>
                        </div>
                        <span class="sidebar-badge">Belum tanda tangan</span>
                    </a>
                @empty
                    <div class="sidebar-item">
                        <div class="sidebar-item-title">Semua report sudah ditandatangani</div>
                        <div class="sidebar-item-meta">Tidak ada report training yang masih menunggu tanda tangan director.</div>
                    </div>
                @endforelse
            </div>
        </aside>

        <div class="content-shell">
            <div class="signature-container">
        @php
            $isReportSignature = isset($report);
            $signatureRecord = $isReportSignature ? $report : ($trainer ?? null);
            $signatureAction = $isReportSignature ? route('director.signature.store') : route('trainer.signature.store');
            $signatureField = $isReportSignature ? 'report_id' : 'trainer_id';
            $signatureValue = $signatureRecord?->id ?? request($signatureField);
            $signatureFile = $isReportSignature ? ($report->director_signature ?? null) : ($trainer->signature ?? null);
            $signaturePath = $isReportSignature ? 'report_signature' : 'signature_external';
        @endphp

        <h2>Silakan Tanda Tangan di Bawah Ini</h2>

        @if(session('success'))
            <div id="flash-message" class="mb-3 text-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div id="flash-message" class="mb-3 text-error">{{ session('error') }}</div>
        @endif

        @if(!empty($signatureFile))
            <div class="saved-signature">
                <div class="mb-2 text-sm text-gray-500">Tanda tangan tersimpan</div>
                <img src="{{ asset($signaturePath . '/' . $signatureFile) }}" alt="Tanda Tangan Tersimpan" style="max-width: 100%; height: auto;">
            </div>
        @endif

        <form id="signature-form" action="{{ $signatureAction }}" method="POST">
            @csrf
            <input type="hidden" name="{{ $signatureField }}" value="{{ $signatureValue }}">
            <textarea name="signature_svg" id="signature-svg" class="hidden" aria-hidden="true"></textarea>

            <div class="canvas-wrapper">
                <canvas id="signature-pad"></canvas>
            </div>

            <div class="button-group">
                <button type="button" id="clear" class="btn btn-clear">Hapus</button>
                <button type="button" id="save" class="btn btn-save">Simpan Tanda Tangan</button>
            </div>
        </form>

        <div id="message-area"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var canvas = document.getElementById('signature-pad');
            var clearButton = document.getElementById('clear');
            var saveButton = document.getElementById('save');
            var signatureForm = document.getElementById('signature-form');
            var signatureSvgField = document.getElementById('signature-svg');
            var messageArea = document.getElementById('message-area');

            var signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgba(11, 11, 170, 0.91)'
            });

            function resizeCanvas() {
                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                var data = signaturePad.toData();

                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);

                signaturePad.clear();
                signaturePad.fromData(data);
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            clearButton.addEventListener('click', function () {
                signaturePad.clear();
                showMessage('', '');
            });

            saveButton.addEventListener('click', function () {
                if (signaturePad.isEmpty()) {
                    showMessage('Silakan berikan tanda tangan terlebih dahulu.', 'error');
                    return;
                }

                signatureSvgField.value = signaturePad.toSVG();
                signatureForm.submit();
            });

            function showMessage(text, type) {
                messageArea.textContent = text;
                messageArea.className = '';
                if (type === 'success') {
                    messageArea.classList.add('text-success');
                } else if (type === 'error') {
                    messageArea.classList.add('text-error');
                }
            }
        });
    </script>
</body>
</html>