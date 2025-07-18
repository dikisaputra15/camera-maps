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
        overflow: hidden; /* Ensure content doesn't spill out */
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
        </div>
        <div class="section-body">

            {{-- CAMERA SECTION --}}
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
                    <img id="imageKWH" style="display: none;">
                </div>
            </div>

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
                    <img id="imageRumah" style="display: none;">
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // --- Configuration ---
    // Menggunakan Nominatim (OpenStreetMap's own geocoder) untuk reverse geocoding.
    // PENTING: Penggunaan instance publik Nominatim (https://nominatim.openstreetmap.org/)
    // memiliki kebijakan penggunaan yang ketat dan batasan frekuensi permintaan.
    // Untuk penggunaan komersial atau volume tinggi, disarankan untuk
    // menginstal instance Nominatim Anda sendiri.
    const NOMINATIM_REVERSE_GEOCODING_API_URL = 'https://nominatim.openstreetmap.org/reverse';

    // --- Helper Functions ---

    /**
     * Starts the camera feed.
     * @param {HTMLVideoElement} video - The video element to display the camera feed.
     * @param {HTMLCanvasElement} overlay - The canvas element for overlaying text.
     * @param {HTMLElement} msgEl - The element to display status messages.
     * @param {HTMLButtonElement} btnCapture - The capture button to enable/disable.
     * @returns {Promise<MediaStream|null>} - The media stream if successful, null otherwise.
     */
    async function startCamera(video, overlay, msgEl, btnCapture) {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } }); // Prefer rear camera
            video.srcObject = stream;
            btnCapture.disabled = false;
            msgEl.innerText = "Kamera aktif";
            video.onloadedmetadata = () => {
                // Set overlay dimensions to match video
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

    /**
     * Retrieves current geographical location (latitude and longitude).
     * @returns {Promise<{lat: string, lon: string}>} - A promise that resolves with latitude and longitude.
     */
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
                    resolve({ lat: 'N/A', lon: 'N/A' }); // Fallback for location failure
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }

    /**
     * Performs reverse geocoding to get a human-readable address from coordinates using Nominatim.
     * @param {string} lat - Latitude.
     * @param {string} lon - Longitude.
     * @returns {Promise<string>} - A promise that resolves with the formatted address or 'Lokasi tidak ditemukan'.
     */
    async function getAddressFromCoordinates(lat, lon) {
        if (lat === 'N/A' || lon === 'N/A') {
            return 'Lokasi tidak tersedia';
        }
        try {
            // Parameter Nominatim: format=json, lat=latitude, lon=longitude, zoom=18 (detail level), addressdetails=1 (untuk detail alamat)
            const response = await fetch(`${NOMINATIM_REVERSE_GEOCODING_API_URL}?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`);
            const data = await response.json();

            if (data && data.display_name) {
                // Nominatim memberikan `display_name` yang sudah diformat.
                // Anda juga bisa mencoba mengkonstruksi dari `address` object jika diperlukan.
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

    /**
     * Captures an image from the video stream, adds location and timestamp overlay, and displays it.
     * @param {HTMLVideoElement} video - The video element to capture from.
     * @param {HTMLCanvasElement} overlay - The canvas element for live overlay.
     * @param {HTMLImageElement} imgTarget - The image element to display the captured photo.
     * @param {HTMLElement} msgEl - The element to display status messages.
     * @param {HTMLElement} mapContainer - The div element for the map.
     */
    async function captureImage(video, overlay, imgTarget, msgEl, mapContainer) {
        msgEl.innerText = "Mengambil foto dan lokasi...";

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Clear live overlay
        const overlayCtx = overlay.getContext('2d');
        overlayCtx.clearRect(0, 0, overlay.width, overlay.height);

        // Get location information
        const { lat, lon } = await getLocationCoordinates();
        const fullAddress = await getAddressFromCoordinates(lat, lon);
        const timestamp = new Date().toLocaleString();

        const locationTextLatLon = `Lat: ${lat}, Lon: ${lon}`;
        const locationTextAddress = `${fullAddress}`;
        const dateTimeText = `${timestamp}`;

        // Prepare text properties for drawing on the captured image
        ctx.font = "24px Arial"; // Larger font for better readability
        ctx.fillStyle = "white";
        ctx.strokeStyle = "black";
        ctx.lineWidth = 2; // Outline for better contrast

        // Draw background rectangle for text
        const textPadding = 10;
        const lineHeight = 30; // Estimate line height
        const rectHeight = (3 * lineHeight) + (4 * textPadding); // For 3 lines of text
        const rectY = canvas.height - rectHeight - 10; // 10px from bottom

        ctx.fillStyle = "rgba(0,0,0,0.6)"; // Semi-transparent black background
        ctx.fillRect(10, rectY, canvas.width - 20, rectHeight); // Full width, 10px padding from sides

        // Draw text lines
        ctx.textBaseline = 'top';
        ctx.fillStyle = "white"; // Reset fill style for text

        let currentY = rectY + textPadding;
        ctx.strokeText(locationTextLatLon, 20, currentY);
        ctx.fillText(locationTextLatLon, 20, currentY);

        currentY += lineHeight + textPadding / 2; // Move to next line
        ctx.strokeText(locationTextAddress, 20, currentY);
        ctx.fillText(locationTextAddress, 20, currentY);

        currentY += lineHeight + textPadding / 2; // Move to next line
        ctx.strokeText(dateTimeText, 20, currentY);
        ctx.fillText(dateTimeText, 20, currentY);

        // Set the image source
        imgTarget.src = canvas.toDataURL('image/png');
        imgTarget.style.display = 'block';

        msgEl.innerText = "Foto berhasil diambil dengan informasi lokasi.";

        // --- Leaflet Map Integration ---
        if (lat !== 'N/A' && lon !== 'N/A') {
            mapContainer.style.display = 'block';
            initializeLeafletMap(mapContainer.id, parseFloat(lat), parseFloat(lon), fullAddress);
        } else {
            mapContainer.style.display = 'none'; // Hide map if no location
            console.warn("No valid coordinates to display map.");
        }
    }

    /**
     * Initializes a Leaflet map in the specified container.
     * @param {string} mapId - The ID of the div element to contain the map.
     * @param {number} lat - Latitude.
     * @param {number} lon - Longitude.
     * @param {string} popupText - Text to display in the marker popup.
     */
    let maps = {}; // Store map instances to avoid re-initializing

    function initializeLeafletMap(mapId, lat, lon, popupText) {
        // Destroy existing map instance if it exists
        if (maps[mapId]) {
            maps[mapId].remove();
            maps[mapId] = null; // Clear the reference
        }

        const map = L.map(mapId).setView([lat, lon], 17); // Set zoom level to 17 for close-up

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([lat, lon])
            .addTo(map)
            .bindPopup(`<b>Lokasi Foto:</b><br>${popupText || 'Koordinat: ' + lat + ', ' + lon}`).openPopup();

        maps[mapId] = map; // Store the new map instance
    }

    // --- Live Overlay Drawing (Optional but good for user feedback) ---
    function drawLiveOverlay(video, overlay) {
        const overlayCtx = overlay.getContext('2d');
        const draw = async () => {
            if (!video.srcObject) return; // Stop drawing if camera is off

            overlayCtx.clearRect(0, 0, overlay.width, overlay.height);

            const { lat, lon } = await getLocationCoordinates();
            const timestamp = new Date().toLocaleTimeString(); // Only time for live

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


    // --- KWH Camera Setup ---
    const videoKWH = document.getElementById('videoKWH');
    const overlayKWH = document.getElementById('overlayKWH');
    const msgKWH = document.getElementById('msgKWH');
    const btnStartKWH = document.getElementById('startKWH');
    const btnCaptureKWH = document.getElementById('captureKWH');
    const imgKWH = document.getElementById('imageKWH');
    const mapKWH = document.getElementById('mapKWH'); // Get map container

    let streamKWH = null;

    btnStartKWH.addEventListener('click', async () => {
        streamKWH = await startCamera(videoKWH, overlayKWH, msgKWH, btnCaptureKWH);
        if (streamKWH) {
            drawLiveOverlay(videoKWH, overlayKWH); // Start live overlay
        }
    });

    btnCaptureKWH.addEventListener('click', () => {
        captureImage(videoKWH, overlayKWH, imgKWH, msgKWH, mapKWH); // Pass map container
        if (streamKWH) {
            streamKWH.getTracks().forEach(track => track.stop());
            videoKWH.srcObject = null;
            btnCaptureKWH.disabled = true; // Disable capture after stopping
            msgKWH.innerText = "Status: Kamera dimatikan";
        }
    });

    // --- Rumah Camera Setup ---
    const videoRumah = document.getElementById('videoRumah');
    const overlayRumah = document.getElementById('overlayRumah');
    const msgRumah = document.getElementById('msgRumah');
    const btnStartRumah = document.getElementById('startRumah');
    const btnCaptureRumah = document.getElementById('captureRumah');
    const imgRumah = document.getElementById('imageRumah');
    const mapRumah = document.getElementById('mapRumah'); // Get map container

    let streamRumah = null;

    btnStartRumah.addEventListener('click', async () => {
        streamRumah = await startCamera(videoRumah, overlayRumah, msgRumah, btnCaptureRumah);
        if (streamRumah) {
            drawLiveOverlay(videoRumah, overlayRumah); // Start live overlay
        }
    });

    btnCaptureRumah.addEventListener('click', () => {
        captureImage(videoRumah, overlayRumah, imgRumah, msgRumah, mapRumah); // Pass map container
        if (streamRumah) {
            streamRumah.getTracks().forEach(track => track.stop());
            videoRumah.srcObject = null;
            btnCaptureRumah.disabled = true; // Disable capture after stopping
            msgRumah.innerText = "Status: Kamera dimatikan";
        }
    });
</script>
@endpush
