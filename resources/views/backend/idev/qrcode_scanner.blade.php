<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pindai QR Kehadiran</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Tabler Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f1f3f5;
            /* Latar belakang abu-abu */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #212529;
        }

        /* Card utama pemindai */
        .scanner-card {
            width: 100%;
            max-width: 500px;
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #dee2e6;
        }

        .scanner-header {
            text-align: center;
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .scanner-header h4 {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .scanner-body {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        /* Kontainer video/kamera */
        .video-container {
            width: 100%;
            max-width: 300px;
            /* Ukuran kotak pemindai */
            aspect-ratio: 1 / 1;
            background-color: #111;
            /* Latar belakang kamera gelap */
            border-radius: 0.75rem;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* == PERUBAHAN ==
           Target div untuk html5-qrcode, bukan <video>
        */
        #qr-reader {
            width: 100%;
            height: 100%;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        /* Video yang di-render oleh library */
        #qr-reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        /* Sembunyikan UI bawaan library agar desain kita bersih */
        #qr-reader__dashboard_section_csr,
        #qr-reader__dashboard_section_fsr,
        #qr-reader__dashboard_section>div:first-child {
            display: none !important;
        }

        /* Garis pemindai animasi */
        .scan-line {
            position: absolute;
            top: 0;
            left: 5%;
            right: 5%;
            height: 3px;
            background: linear-gradient(90deg, rgba(0, 26, 255, 0.1), rgba(0, 213, 255, 0.8), rgba(0, 128, 255, 0.1));
            box-shadow: 0 0 10px 2px rgba(0, 17, 255, 0.5);
            border-radius: 3px;
            animation: scan 3s linear infinite;
            z-index: 10;
            /* Pastikan di atas video */
        }

        @keyframes scan {
            0% {
                top: 0;
            }

            50% {
                top: calc(100% - 3px);
            }

            100% {
                top: 0;
            }
        }

        /* Bingkai sudut */
        .scan-corners {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            /* Pastikan di atas video */
        }

        .scan-corners::before,
        .scan-corners::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            border: 5px solid rgba(255, 255, 255, 0.9);
        }

        /* Pojok kiri atas */
        .scan-corners::before {
            top: 15px;
            left: 15px;
            border-right: 0;
            border-bottom: 0;
            border-top-left-radius: 0.5rem;
        }

        .scan-corners::after {
            top: 15px;
            right: 15px;
            border-left: 0;
            border-bottom: 0;
            border-top-right-radius: 0.5rem;
        }

        /* Pojok kiri bawah & kanan bawah (dibuat dengan :nth-child) */
        .scan-corners>div:nth-child(1) {
            position: absolute;
            bottom: 15px;
            left: 15px;
            width: 30px;
            height: 30px;
            border: 5px solid rgba(255, 255, 255, 0.9);
            border-right: 0;
            border-top: 0;
            border-bottom-left-radius: 0.5rem;
        }

        .scan-corners>div:nth-child(2) {
            position: absolute;
            bottom: 15px;
            right: 15px;
            width: 30px;
            height: 30px;
            border: 5px solid rgba(255, 255, 255, 0.9);
            border-left: 0;
            border-top: 0;
            border-bottom-right-radius: 0.5rem;
        }


        .status-text {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 1rem;
            color: #495057;
            text-align: center;
        }

        .result-area {
            width: 100%;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            border: 1px solid #e9ecef;
            text-align: center;
            display: none;
            /* Sembunyikan awalnya */
        }

        .result-area.success {
            background-color: #e6f9f0;
            border-color: #b6e9d1;
            color: #0a6840;
        }

        .result-area.error {
            background-color: #fdf0f1;
            border-color: #f7c6ca;
            color: #d93a4a;
        }

        .result-area .fw-bold {
            font-size: 1.1rem;
        }
    </style>
</head>

<body>

    <div class="scanner-card">
        <div class="scanner-header">
            <h4>QRCode Scanner</h4>
            <p class="text-muted mb-0"><i class="ti ti-scan"></i> arahkan kamera anda ke qrcode untuk memindai.</p>
        </div>

        <div class="scanner-body">

            <!-- Area Pemindai -->
            <div class="video-container" id="scanner-container">
                <!-- Video feed dari kamera akan muncul di sini -->
                <!-- == PERUBAHAN == Mengganti <video> dengan <div> target -->
                <div id="qr-reader"></div>
                <!-- Animasi Garis Pemindai -->
                <div class="scan-line"></div>
                <!-- Bingkai Sudut -->
                <!-- <div class="scan-corners">
                    <div></div>
                    <div></div>
                </div> -->
            </div>

            <!-- Teks Status -->
            <div id="status-text" class="status-text">
                <i class="ti ti-scan"></i>
                <span>Arahkan kamera ke QR Code...</span>
            </div>

            <!-- Tombol Aksi -->
            <button class="btn btn-danger w-100" id="stop-scan">
                <i class="ti ti-circle-stop me-1"></i>
                Batalkan
            </button>

            <button class="btn btn-primary w-100" id="scan-again" style="display: none;">
                <i class="ti ti-scan me-1"></i>
                Pindai Lagi
            </button>
            
            <a href="#" class="btn btn-success w-100" id="link" style="display: none;">
                <i class="ti ti-link me-1"></i>
                Go to Link
            </a>

            <!-- Area Hasil (disembunyikan) -->
            <div id="result-area" class="result-area success">
                <div class="fw-bold"><i class="ti ti-check me-1"></i> Data diterima:</div>
                <div id="result-text">
                    <p id="result-data" style="overflow:hidden;"></p>
                </div>
            </div>

            <div id="error-area" class="result-area error">
                <div class="fw-bold"><i class="ti ti-x me-1"></i> Gagal!</div>
                <div id="error-text">QR Code tidak valid atau kedaluwarsa.</div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS (Bundle) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- == PERUBAHAN == Menambahkan library html5-qrcode -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <!-- == PERUBAHAN == Mengganti skrip mockup dengan skrip fungsional -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const statusText = document.getElementById('status-text');
            const resultArea = document.getElementById('result-area');
            const resultText = document.getElementById('result-text');
            const resultData = document.getElementById('result-data');
            const errorArea = document.getElementById('error-area');
            const errorText = document.getElementById('error-text');
            const stopBtn = document.getElementById('stop-scan');
            const scanAgainBtn = document.getElementById('scan-again');
            const linkBtn = document.getElementById('link');
            const scannerContainer = document.getElementById('scanner-container');

            let html5QrCode;

            function startScanner() {
                // Tampilkan elemen pemindai
                scannerContainer.style.display = 'block';
                statusText.style.display = 'none';
                stopBtn.style.display = 'block';

                // Sembunyikan hasil
                resultArea.style.display = 'none';
                errorArea.style.display = 'none';
                scanAgainBtn.style.display = 'none';
                linkBtn.style.display = 'none';

                statusText.innerHTML = `<i class="ti ti-scan"></i> <span>Arahkan kamera ke QR Code...</span>`;

                // Inisialisasi library
                html5QrCode = new Html5Qrcode("qr-reader");
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    supportedScanTypes: [
                        Html5QrcodeScanType.SCAN_TYPE_CAMERA
                    ]
                };

                // Fungsi callback saat pindai BERHASIL
                const onScanSuccess = (decodedText, decodedResult) => {
                    // console.log(`Scan berhasil: ${decodedText}`, decodedResult);

                    // Hentikan pemindai
                    html5QrCode.stop().then(() => {
                        // Tampilkan UI sukses
                        statusText.style.display = 'none';
                        errorArea.style.display = 'none';
                        stopBtn.style.display = 'none';

                        resultArea.style.display = 'block';
                        scanAgainBtn.style.display = 'block';
                        linkBtn.href = `${decodedText}`;
                        linkBtn.style.display = 'block';
                        scannerContainer.style.display = 'none'; // Sembunyikan video

                        // Di sini Anda akan memproses `decodedText`
                        // Misalnya, validasi token dan tampilkan nama
                        // Untuk demo, kita tampilkan saja teks mentahnya
                        resultData.textContent = `${decodedText}`;

                    }).catch((err) => {
                        console.error("Gagal menghentikan pemindai.", err);
                    });
                };

                // Fungsi callback saat pindai GAGAL (dijalankan terus-menerus)
                const onScanFailure = (error) => {
                    // Abaikan error "QR code not found"
                    // Cukup tampilkan di konsol untuk debug
                    // console.warn(`Scan gagal: ${error}`);
                };

                // Mulai pemindai
                html5QrCode.start(
                    { facingMode: "environment" }, // Gunakan kamera belakang
                    config,
                    onScanSuccess,
                    onScanFailure
                ).catch((err) => {
                    // Tampilkan error jika kamera tidak bisa diakses
                    statusText.style.display = 'none';
                    errorArea.style.display = 'block';
                    errorText.textContent = 'Tidak dapat mengakses kamera. Pastikan izin telah diberikan.';
                    console.error('Gagal memulai pemindai', err);
                });
            }

            // Tombol "Batalkan"
            stopBtn.addEventListener('click', () => {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        console.log("Pemindai dihentikan oleh pengguna.");
                        // Anda bisa tambahkan logika lain, misal kembali ke halaman sebelumnya
                        window.history.back(); // Contoh: kembali
                    }).catch(err => console.error("Gagal menghentikan pemindai.", err));
                }
            });

            // Tombol "Pindai Lagi"
            scanAgainBtn.addEventListener('click', () => {
                startScanner();
            });

            // Mulai pemindai saat halaman dimuat
            startScanner();
        });
    </script>
</body>

</html>