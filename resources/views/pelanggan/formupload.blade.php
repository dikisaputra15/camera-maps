@extends('layouts.app')
@section('title', 'Upload Gambar')

@push('style')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    .camera-section {
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 1px dashed #eee;
    }

    .camera-feed-container {
        position: relative;
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
        border: 1px solid #ddd;
        background-color: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    video, canvas {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    canvas.overlay {
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
    }

    .camera-controls {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 15px 0;
    }

    .camera-controls button {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        border: none;
        color: white;
    }

    .btn-start { background: #28a745; }
    .btn-capture { background: #007bff; }
    .btn-submit { background: #6c757d; } /* New submit button style */


    .message {
        margin-top: 10px;
        text-align: center;
    }

    .image-results {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .image-results img {
        margin-top: 10px;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        max-width: 100%;
        height: auto;
    }

    .map-container {
        height: 300px; /* Fixed height for the map */
        width: 100%;
        max-width: 640px;
        margin-top: 20px;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Upload Gambar KWH dan Rumah</h1>
            {{-- Hidden input to store the customer ID passed from the route --}}
            {{-- Assumes route is something like /search-pelanggan/{id}/formupload --}}
            {{-- Pastikan objek $pelanggan dilewatkan dari controller --}}
            <input type="hidden" id="pelangganId" value="{{ $pelanggan->id }}">
        </div>
        <div class="section-body">

            {{-- CAMERA SECTION KWH --}}
            <div class="camera-section">
                <h3>Foto KWH</h3>
                <div class="camera-feed-container">
                    <video id="videoKWH" autoplay playsinline></video>
                    <canvas id="overlayKWH" class="overlay"></canvas>
                </div>
                <div class="camera-controls">
                    <button id="startKWH" class="btn-start">Mulai Kamera</button>
                    <button id="captureKWH" class="btn-capture" disabled>Ambil Foto</button>
                </div>
                <div class="message" id="msgKWH">Status: Standby</div>
                <div class="image-results">
                    {{-- Tampilkan gambar KWH yang sudah ada atau yang baru diambil di sini --}}
                    <img id="imageKWH"
                         @if(!empty($pelanggan->gambar_kwh))
                             src="{{ asset('storage/' . $pelanggan->gambar_kwh) }}"
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    >
                    <div id="mapKWH" class="map-container" style="display: none;"></div>
                </div>
            </div>

            {{-- CAMERA SECTION RUMAH --}}
            <div class="camera-section">
                <h3>Foto Rumah</h3>
                <div class="camera-feed-container">
                    <video id="videoRumah" autoplay playsinline></video>
                    <canvas id="overlayRumah" class="overlay"></canvas>
                </div>
                <div class="camera-controls">
                    <button id="startRumah" class="btn-start">Mulai Kamera</button>
                    <button id="captureRumah" class="btn-capture" disabled>Ambil Foto</button>
                </div>
                <div class="message" id="msgRumah">Status: Standby</div>
                <div class="image-results">
                    {{-- Tampilkan gambar Rumah yang sudah ada atau yang baru diambil di sini --}}
                    <img id="imageRumah"
                         @if(!empty($pelanggan->gambar_rumah))
                             src="{{ asset('storage/' . $pelanggan->gambar_rumah) }}"
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    >
                    <div id="mapRumah" class="map-container" style="display: none;"></div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button id="submitAllImages" class="btn btn-submit" disabled>Simpan Semua Gambar</button>
                <div id="submitMessage" class="message text-info"></div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // --- Configuration ---
    const NOMINATIM_REVERSE_GEOCODING_API_URL = 'https://nominatim.openstreetmap.org/reverse';
    const LARAVEL_WEB_UPLOAD_URL = '/pelanggans/';

    // Initialize capturedImage variables based on existing data
    // Use 'EXISTS_AND_UNCHANGED' as a flag for images already in the database
    // that haven't been re-captured in the current session.
    let capturedImageKWH = '{{ !empty($pelanggan->gambar_kwh) ? "EXISTS_AND_UNCHANGED" : "null" }}';
    let capturedImageRumah = '{{ !empty($pelanggan->gambar_rumah) ? "EXISTS_AND_UNCHANGED" : "null" }}';

    const pelangganId = document.getElementById('pelangganId').value;
    const submitAllImagesBtn = document.getElementById('submitAllImages');
    const submitMessageEl = document.getElementById('submitMessage');

    // --- Helper Functions (Tetap sama seperti kode Anda sebelumnya) ---

    async function startCamera(video, overlay, msgEl, btnCapture) {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            btnCapture.disabled = false;
            msgEl.innerText = "Kamera aktif";
            video.onloadedmetadata = () => {
                overlay.width = video.videoWidth;
                overlay.height = video.videoHeight;
            };
            return stream;
        } catch (err) {
            msgEl.innerText = "Gagal mengakses kamera. Pastikan izin diberikan.";
            console.error("Error accessing camera:", err);
            return null;
        }
    }

    function getLocationCoordinates() {
        return new Promise((resolve) => {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude.toFixed(6);
                    const lon = pos.coords.longitude.toFixed(6);
                    resolve({ lat, lon });
                },
                (err) => {
                    console.warn(`ERROR(${err.code}): ${err.message}`);
                    resolve({ lat: 'N/A', lon: 'N/A' });
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }

    async function getAddressFromCoordinates(lat, lon) {
        if (lat === 'N/A' || lon === 'N/A') {
            return 'Lokasi tidak tersedia';
        }
        try {
            const response = await fetch(`${NOMINATIM_REVERSE_GEOCODING_API_URL}?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`);
            const data = await response.json();

            if (data && data.display_name) {
                const address = data.address;
                const addressParts = [];

                if (address.road) addressParts.push(address.road);
                if (address.suburb) addressParts.push(address.suburb);
                if (address.village) addressParts.push(address.village);
                if (address.city) addressParts.push(address.city);
                if (address.county) addressParts.push(address.county);
                if (address.state) addressParts.push(address.state);
                if (address.postcode) addressParts.push(`(${address.postcode})`);
                if (address.country) addressParts.push(address.country);

                return addressParts.join(', ') || data.display_name;
            } else {
                return 'Lokasi tidak ditemukan (Nominatim)';
            }
        } catch (error) {
            console.error("Error during reverse geocoding with Nominatim:", error);
            return 'Gagal mendapatkan lokasi lengkap (Nominatim)';
        }
    }

    function wrapText(context, text, maxWidth) {
        const words = text.split(' ');
        let line = '';
        const lines = [];

        for (let n = 0; n < words.length; n++) {
            const testLine = line + words[n] + ' ';
            const metrics = context.measureText(testLine);
            const testWidth = metrics.width;

            if (testWidth > maxWidth && n > 0) {
                lines.push(line.trim());
                line = words[n] + ' ';
            } else {
                line = testLine;
            }
        }
        lines.push(line.trim());
        return lines;
    }

    async function captureImage(video, overlay, imgTarget, msgEl, mapContainer, imageType) {
        msgEl.innerText = "Mengambil foto dan lokasi...";

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const overlayCtx = overlay.getContext('2d');
        overlayCtx.clearRect(0, 0, overlay.width, overlay.height);

        const { lat, lon } = await getLocationCoordinates();
        const fullAddress = await getAddressFromCoordinates(lat, lon);
        const timestamp = new Date().toLocaleString();

        const locationTextLatLon = `Lat: ${lat}, Lon: ${lon}`;
        const dateTimeText = `${timestamp}`;

        const FONT_SIZE = 24;
        ctx.font = `${FONT_SIZE}px Arial`;
        ctx.fillStyle = "white";
        ctx.strokeStyle = "black";
        ctx.lineWidth = 2;

        const textPaddingX = 20;
        const textPaddingY = 10;
        const lineHeight = FONT_SIZE * 1.2;

        const availableWidthForText = canvas.width - (2 * textPaddingX);

        const wrappedAddressLines = wrapText(ctx, fullAddress, availableWidthForText);

        const totalTextLines = 1 + wrappedAddressLines.length + 1;
        const rectHeight = (totalTextLines * lineHeight) + (2 * textPaddingY);
        const rectY = canvas.height - rectHeight - 10;

        ctx.fillStyle = "rgba(0,0,0,0.6)";
        ctx.fillRect(10, rectY, canvas.width - 20, rectHeight);

        ctx.textBaseline = 'top';
        ctx.fillStyle = "white";

        let currentY = rectY + textPaddingY;

        ctx.strokeText(locationTextLatLon, textPaddingX, currentY);
        ctx.fillText(locationTextLatLon, textPaddingX, currentY);
        currentY += lineHeight;

        for (let i = 0; i < wrappedAddressLines.length; i++) {
            ctx.strokeText(wrappedAddressLines[i], textPaddingX, currentY);
            ctx.fillText(wrappedAddressLines[i], textPaddingX, currentY);
            currentY += lineHeight;
        }

        ctx.strokeText(dateTimeText, textPaddingX, currentY);
        ctx.fillText(dateTimeText, textPaddingX, currentY);

        const imageDataURL = canvas.toDataURL('image/png');
        imgTarget.src = imageDataURL;
        imgTarget.style.display = 'block';

        if (imageType === 'kwh') {
            capturedImageKWH = imageDataURL;
        } else if (imageType === 'rumah') {
            capturedImageRumah = imageDataURL;
        }

        msgEl.innerText = "Foto berhasil diambil dengan informasi lokasi.";

        if (lat !== 'N/A' && lon !== 'N/A' && mapContainer) {
            mapContainer.style.display = 'block';
            initializeLeafletMap(mapContainer.id, parseFloat(lat), parseFloat(lon), fullAddress);
        } else if (mapContainer) {
            mapContainer.style.display = 'none';
            console.warn("No valid coordinates to display map or map container not found.");
        }
        checkSubmitButtonStatus();
    }

    let maps = {};

    function initializeLeafletMap(mapId, lat, lon, popupText) {
        if (maps[mapId]) {
            maps[mapId].remove();
            maps[mapId] = null;
        }

        const map = L.map(mapId).setView([lat, lon], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([lat, lon])
            .addTo(map)
            .bindPopup(`<b>Lokasi Foto:</b><br>${popupText || 'Koordinat: ' + lat + ', ' + lon}`).openPopup();

        maps[mapId] = map;
    }

    function drawLiveOverlay(video, overlay) {
        const overlayCtx = overlay.getContext('2d');
        const draw = async () => {
            if (!video.srcObject) return;

            overlayCtx.clearRect(0, 0, overlay.width, overlay.height);

            const { lat, lon } = await getLocationCoordinates();
            const timestamp = new Date().toLocaleTimeString();

            const text1 = `Lat: ${lat}, Lon: ${lon}`;
            const text2 = `Time: ${timestamp}`;

            overlayCtx.font = "18px Arial";
            overlayCtx.fillStyle = "rgba(255, 255, 255, 0.9)";
            overlayCtx.strokeStyle = "rgba(0,0,0,0.7)";
            overlayCtx.lineWidth = 1;

            const textPadding = 5;
            const lineHeight = 22;
            const rectHeight = (2 * lineHeight) + (3 * textPadding);
            const rectY = overlay.height - rectHeight - 5;

            overlayCtx.fillStyle = "rgba(0,0,0,0.4)";
            overlayCtx.fillRect(5, rectY, overlay.width - 10, rectHeight);

            overlayCtx.fillStyle = "white";
            overlayCtx.textBaseline = 'top';
            overlayCtx.strokeText(text1, 10, rectY + textPadding);
            overlayCtx.fillText(text1, 10, rectY + textPadding);

            overlayCtx.strokeText(text2, 10, rectY + textPadding + lineHeight);
            overlayCtx.fillText(text2, 10, rectY + textPadding + lineHeight);

            requestAnimationFrame(draw);
        };
        requestAnimationFrame(draw);
    }

    function checkSubmitButtonStatus() {
        // Tombol submit aktif jika pelangganId ada DAN
        // (capturedImageKWH tidak null/EXISTS_AND_UNCHANGED ATAU gambar KWH sebelumnya ada) DAN
        // (capturedImageRumah tidak null/EXISTS_AND_UNCHANGED ATAU gambar Rumah sebelumnya ada)
        const kwhReady = (capturedImageKWH !== null && capturedImageKWH !== "null");
        const rumahReady = (capturedImageRumah !== null && capturedImageRumah !== "null");

        if (pelangganId && kwhReady && rumahReady) {
            submitAllImagesBtn.disabled = false;
        } else {
            submitAllImagesBtn.disabled = true;
        }
    }


    async function uploadImages() {
        if (!pelangganId) {
            submitMessageEl.innerText = "ID Pelanggan tidak ditemukan. Tidak dapat menyimpan.";
            submitMessageEl.style.color = 'red';
            return;
        }

        const data = {};
        let hasNewImage = false; // Flag untuk memeriksa apakah ada gambar baru yang diambil

        // Hanya kirim gambar jika itu adalah gambar baru yang diambil
        if (capturedImageKWH !== "null" && capturedImageKWH !== "EXISTS_AND_UNCHANGED") {
            data.gambar_kwh = capturedImageKWH;
            hasNewImage = true;
        }
        if (capturedImageRumah !== "null" && capturedImageRumah !== "EXISTS_AND_UNCHANGED") {
            data.gambar_rumah = capturedImageRumah;
            hasNewImage = true;
        }

        // Jika tidak ada gambar baru yang diambil dan tidak ada gambar sebelumnya, jangan submit
        if (!hasNewImage && capturedImageKWH === "EXISTS_AND_UNCHANGED" && capturedImageRumah === "EXISTS_AND_UNCHANGED") {
            submitMessageEl.innerText = "Tidak ada gambar baru untuk disimpan. Kedua gambar sudah ada.";
            submitMessageEl.style.color = 'orange';
            submitAllImagesBtn.disabled = false;
            return;
        } else if (!hasNewImage) {
             submitMessageEl.innerText = "Harap ambil setidaknya satu foto baru jika belum ada gambar sebelumnya.";
            submitMessageEl.style.color = 'orange';
            submitAllImagesBtn.disabled = false;
            return;
        }


        data._method = 'PUT';

        submitAllImagesBtn.disabled = true;
        submitMessageEl.innerText = "Menyimpan gambar, harap tunggu...";
        submitMessageEl.style.color = 'blue';

        try {
            const response = await fetch(`${LARAVEL_WEB_UPLOAD_URL}${pelangganId}/update-gambar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                submitMessageEl.innerText = `Gambar berhasil disimpan! ${result.message || ''}`;
                submitMessageEl.style.color = 'green';
                // REFRESH HALAMAN SETELAH SUKSES
                setTimeout(() => {
                    location.reload();
                }, 1500); // Beri sedikit waktu untuk pesan terlihat sebelum reload
            } else {
                submitMessageEl.innerText = `Gagal menyimpan gambar: ${result.message || 'Terjadi kesalahan'}`;
                submitMessageEl.style.color = 'red';
                console.error("Upload failed:", result);
            }
        } catch (error) {
            submitMessageEl.innerText = `Terjadi kesalahan jaringan: ${error.message}`;
            submitMessageEl.style.color = 'red';
            console.error("Network error during upload:", error);
        } finally {
            submitAllImagesBtn.disabled = false;
        }
    }


    // --- KWH Camera Setup ---
    const videoKWH = document.getElementById('videoKWH');
    const overlayKWH = document.getElementById('overlayKWH');
    const msgKWH = document.getElementById('msgKWH');
    const btnStartKWH = document.getElementById('startKWH');
    const btnCaptureKWH = document.getElementById('captureKWH');
    const imgKWH = document.getElementById('imageKWH');
    const mapKWH = document.getElementById('mapKWH');

    let streamKWH = null;

    btnStartKWH.addEventListener('click', async () => {
        streamKWH = await startCamera(videoKWH, overlayKWH, msgKWH, btnCaptureKWH);
        if (streamKWH) {
            drawLiveOverlay(videoKWH, overlayKWH);
        }
    });

    btnCaptureKWH.addEventListener('click', () => {
        captureImage(videoKWH, overlayKWH, imgKWH, msgKWH, mapKWH, 'kwh');
        if (streamKWH) {
            streamKWH.getTracks().forEach(track => track.stop());
            videoKWH.srcObject = null;
            btnCaptureKWH.disabled = true;
            msgKWH.innerText = "Status: Kamera KWH dimatikan";
        }
    });

    // --- Rumah Camera Setup ---
    const videoRumah = document.getElementById('videoRumah');
    const overlayRumah = document.getElementById('overlayRumah');
    const msgRumah = document.getElementById('msgRumah');
    const btnStartRumah = document.getElementById('startRumah');
    const btnCaptureRumah = document.getElementById('captureRumah');
    const imgRumah = document.getElementById('imageRumah');
    const mapRumah = document.getElementById('mapRumah');

    let streamRumah = null;

    btnStartRumah.addEventListener('click', async () => {
        streamRumah = await startCamera(videoRumah, overlayRumah, msgRumah, btnCaptureRumah);
        if (streamRumah) {
            drawLiveOverlay(videoRumah, overlayRumah);
        }
    });

    btnCaptureRumah.addEventListener('click', () => {
        captureImage(videoRumah, overlayRumah, imgRumah, msgRumah, mapRumah, 'rumah');
        if (streamRumah) {
            streamRumah.getTracks().forEach(track => track.stop());
            videoRumah.srcObject = null;
            btnCaptureRumah.disabled = true;
            msgRumah.innerText = "Status: Kamera Rumah dimatikan";
        }
    });

    submitAllImagesBtn.addEventListener('click', uploadImages);

    // Initial check when the page loads
    checkSubmitButtonStatus();

    // Opsional: Inisialisasi peta jika ada koordinat yang tersimpan di database
    @if(!empty($pelanggan->gambar_kwh) && !empty($pelanggan->kwh_latitude) && !empty($pelanggan->kwh_longitude))
    document.addEventListener('DOMContentLoaded', () => {
        initializeLeafletMap('mapKWH', parseFloat({{ $pelanggan->kwh_latitude }}), parseFloat({{ $pelanggan->kwh_longitude }}), 'Lokasi KWH Tersimpan');
        document.getElementById('mapKWH').style.display = 'block';
    });
    @endif
    @if(!empty($pelanggan->gambar_rumah) && !empty($pelanggan->rumah_latitude) && !empty($pelanggan->rumah_longitude))
    document.addEventListener('DOMContentLoaded', () => {
        initializeLeafletMap('mapRumah', parseFloat({{ $pelanggan->rumah_latitude }}), parseFloat({{ $pelanggan->rumah_longitude }}), 'Lokasi Rumah Tersimpan');
        document.getElementById('mapRumah').style.display = 'block';
    });
    @endif
</script>
@endpush
