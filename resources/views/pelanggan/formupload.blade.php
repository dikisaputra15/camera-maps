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
    .btn-submit { background: #6c757d; }

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
        height: 300px;
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
            <h1>Upload Gambar Pelanggan</h1>
            <input type="hidden" id="pelangganId" value="{{ $pelanggan->id }}">
        </div>
        <div class="section-body">

            <div class="col-md-12 mb-3">
                <label for="difoto_oleh" class="form-label">Difoto Oleh : {{ $pelanggan->difoto_oleh }}</label>
            </div>
            <div class="col-md-12 mb-3">
                <label for="tanggal_foto" class="form-label">Tanggal Foto : {{ $pelanggan->tanggal_foto }}</label>
            </div>
             <div class="col-md-12 mb-3">
                <label for="hasil_kunjungan" class="form-label">Hasil Kunjungan</label>
                <input type="text" class="form-control" id="hasil_kunjungan" name="hasil_kunjungan" value="{{ $pelanggan ? $pelanggan->hasil_kunjungan : '' }}">
            </div>
             <div class="col-md-12 mb-3">
                <label for="telp" class="form-label">Telephone</label>
                <input type="text" class="form-control" id="telp" name="telp" value="{{ $pelanggan ? $pelanggan->telp : '' }}">
            </div>
             <div class="col-md-12 mb-3">
                <label for="kabel_sl" class="form-label">Kabel SL</label>
               <select class="form-control" name="kabel_sl" id="kabel_sl" required>
                    @php
                        $kabelOptions = [
                            'TIC 2x10 mm2',
                            'TIC 2x16 mm2',
                            'TIC 4x16 mm2',
                            'TIC 4x25 mm2',
                            'TIC 4x75 mm2',
                            'SKSR',
                        ];
                        $selectedKabel = old('kabel_sl', $pelanggan->kabel_sl ?? '');
                    @endphp

                    @foreach ($kabelOptions as $option)
                        <option value="{{ $option }}" {{ $selectedKabel === $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>
             <div class="col-md-12 mb-3">
                <label for="jenis_sambungan" class="form-label">Jenis Sambungan</label>
               <select class="form-control" name="jenis_sambungan" id="jenis_sambungan" required>
                    @php
                        $sambungOptions = [
                            'Langsung',
                            'Seri',
                        ];
                        $selectedSambung = old('jenis_sambungan', $pelanggan->jenis_sambungan ?? '');
                    @endphp

                    @foreach ($sambungOptions as $sam)
                        <option value="{{ $sam }}" {{ $selectedSambung === $sam ? 'selected' : '' }}>
                            {{ $sam }}
                        </option>
                    @endforeach
                </select>
            </div>
             <div class="col-md-12 mb-3">
                <label for="merk_mcb" class="form-label">Merk MCB</label>
                <input type="text" class="form-control" id="merk_mcb" name="merk_mcb" value="{{ $pelanggan ? $pelanggan->merk_mcb : '' }}">
            </div>

             <div class="col-md-12 mb-3">
                <label for="ampere_mcb" class="form-label">Ampere MCB</label>
               <select class="form-control" name="ampere_mcb" id="ampere_mcb" required>
                    @php
                        $ampereOptions = [
                            '1ph 2A',
                            '1ph 4A',
                            '1ph 6A',
                            '1ph 10A',
                            '1ph 16A',
                            '1ph 20A',
                            '1ph 25A',
                            '1ph 35A',
                            '1ph 50A',
                            '3ph 10A',
                            '3ph 16A',
                            '3ph 20A',
                            '3ph 25A',
                            '3ph 35A',
                            '3ph 50A',
                        ];
                        $selectedAmpere = old('ampere_mcb', $pelanggan->ampere_mcb ?? '');
                    @endphp

                    @foreach ($ampereOptions as $amp)
                        <option value="{{ $amp }}" {{ $selectedAmpere === $amp ? 'selected' : '' }}>
                            {{ $amp }}
                        </option>
                    @endforeach
                </select>
            </div>

              <div class="col-md-12 mb-3">
                <label for="gardu" class="form-label">Gardu</label>
                <input type="text" class="form-control" id="gardu" name="gardu" value="{{ $pelanggan ? $pelanggan->gardu : '' }}">
            </div>

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
                    <img id="imageKWH"
                         @if(!empty($pelanggan->gambar_kwh))
                             src="{{ asset('storage/' . $pelanggan->gambar_kwh) }}"
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    >
                    <div id="mapKWH" class="map-container"
                         @if(!empty($pelanggan->kwh_latitude) && !empty($pelanggan->kwh_longitude))
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif >
                    </div>
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
                    <img id="imageRumah"
                         @if(!empty($pelanggan->gambar_rumah))
                             src="{{ asset('storage/' . $pelanggan->gambar_rumah) }}"
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    >
                    <div id="mapRumah" class="map-container"
                         @if(!empty($pelanggan->rumah_latitude) && !empty($pelanggan->rumah_longitude))
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    ></div>
                </div>
            </div>

            @role('admin')
            {{-- NEW CAMERA SECTION SR --}}
            <div class="camera-section">
                <h3>Foto SR</h3>
                <div class="camera-feed-container">
                    <video id="videoSR" autoplay playsinline></video>
                    <canvas id="overlaySR" class="overlay"></canvas>
                </div>
                <div class="camera-controls">
                    <button id="startSR" class="btn-start">Mulai Kamera</button>
                    <button id="captureSR" class="btn-capture" disabled>Ambil Foto</button>
                </div>
                <div class="message" id="msgSR">Status: Standby</div>
                <div class="image-results">
                    <img id="imageSR"
                         @if(!empty($pelanggan->gambar_sr))
                             src="{{ asset('storage/' . $pelanggan->gambar_sr) }}"
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    >
                    <div id="mapSR" class="map-container"
                         @if(!empty($pelanggan->sr_latitude) && !empty($pelanggan->sr_longitude))
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    ></div>
                </div>
            </div>

            {{-- NEW CAMERA SECTION TIANG --}}
            <div class="camera-section">
                <h3>Foto Tiang</h3>
                <div class="camera-feed-container">
                    <video id="videoTiang" autoplay playsinline></video>
                    <canvas id="overlayTiang" class="overlay"></canvas>
                </div>
                <div class="camera-controls">
                    <button id="startTiang" class="btn-start">Mulai Kamera</button>
                    <button id="captureTiang" class="btn-capture" disabled>Ambil Foto</button>
                </div>
                <div class="message" id="msgTiang">Status: Standby</div>
                <div class="image-results">
                    <img id="imageTiang"
                         @if(!empty($pelanggan->gambar_tiang))
                             src="{{ asset('storage/' . $pelanggan->gambar_tiang) }}"
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    >
                    <div id="mapTiang" class="map-container"
                         @if(!empty($pelanggan->tiang_latitude) && !empty($pelanggan->tiang_longitude))
                             style="display: block;"
                         @else
                             style="display: none;"
                         @endif
                    ></div>
                </div>
            </div>
            @endrole

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
    const LARAVEL_WEB_UPLOAD_URL = "/pelanggans/";

    // Initialize capturedImage variables based on existing data
    let capturedImageKWH = '{{ !empty($pelanggan->gambar_kwh) ? "EXISTS_AND_UNCHANGED" : "null" }}';
    let capturedImageRumah = '{{ !empty($pelanggan->gambar_rumah) ? "EXISTS_AND_UNCHANGED" : "null" }}';
    let capturedImageSR = '{{ !empty($pelanggan->gambar_sr) ? "EXISTS_AND_UNCHANGED" : "null" }}';
    let capturedImageTiang = '{{ !empty($pelanggan->gambar_tiang) ? "EXISTS_AND_UNCHANGED" : "null" }}';

     // Store Lat/Lon if they exist
    let kwhLat = @json($pelanggan->kwh_latitude);
    let kwhLon = @json($pelanggan->kwh_longitude);
    let rumahLat = @json($pelanggan->rumah_latitude);
    let rumahLon = @json($pelanggan->rumah_longitude);
    let srLat = @json($pelanggan->sr_latitude);
    let srLon = @json($pelanggan->sr_longitude);
    let tiangLat = @json($pelanggan->tiang_latitude);
    let tiangLon = @json($pelanggan->tiang_longitude);

    const pelangganId = document.getElementById('pelangganId').value;
    const submitAllImagesBtn = document.getElementById('submitAllImages');
    const submitMessageEl = document.getElementById('submitMessage');

    // --- Helper Functions ---

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

        const totalTextLines = 1 + wrappedAddressLines.length + 1; // Lat/Lon + Address Lines + Date/Time
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

        // Assign captured image data and coordinates to respective variables
        if (imageType === 'kwh') {
            capturedImageKWH = imageDataURL;
            kwhLat = lat;
            kwhLon = lon;
        } else if (imageType === 'rumah') {
            capturedImageRumah = imageDataURL;
            rumahLat = lat;
            rumahLon = lon;
        } else if (imageType === 'sr') {
            capturedImageSR = imageDataURL;
            srLat = lat;
            srLon = lon;
        } else if (imageType === 'tiang') {
            capturedImageTiang = imageDataURL;
            tiangLat = lat;
            tiangLon = lon;
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
        const kwhReady = (capturedImageKWH !== null && capturedImageKWH !== "null");
        const rumahReady = (capturedImageRumah !== null && capturedImageRumah !== "null");
        const srReady = (capturedImageSR !== null && capturedImageSR !== "null");
        const tiangReady = (capturedImageTiang !== null && capturedImageTiang !== "null");

        // Enable submit button only if all four images are either newly captured or already exist in DB
        if (pelangganId && kwhReady && rumahReady && srReady && tiangReady) {
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
        let hasNewImage = false;

        if (capturedImageKWH !== "null" && capturedImageKWH !== "EXISTS_AND_UNCHANGED") {
            data.gambar_kwh = capturedImageKWH;
            data.kwh_latitude = kwhLat;
            data.kwh_longitude = kwhLon;
            hasNewImage = true;
        }
        if (capturedImageRumah !== "null" && capturedImageRumah !== "EXISTS_AND_UNCHANGED") {
            data.gambar_rumah = capturedImageRumah;
            data.rumah_latitude = rumahLat;
            data.rumah_longitude = rumahLon;
            hasNewImage = true;
        }
        if (capturedImageSR !== "null" && capturedImageSR !== "EXISTS_AND_UNCHANGED") {
            data.gambar_sr = capturedImageSR;
            data.sr_latitude = srLat;
            data.sr_longitude = srLon;
            hasNewImage = true;
        }
        if (capturedImageTiang !== "null" && capturedImageTiang !== "EXISTS_AND_UNCHANGED") {
            data.gambar_tiang = capturedImageTiang;
            data.tiang_latitude = tiangLat;
            data.tiang_longitude = tiangLon;
            hasNewImage = true;
        }

        // Add other form fields to the data object
        data.hasil_kunjungan = document.getElementById('hasil_kunjungan').value;
        data.telp = document.getElementById('telp').value;
        data.kabel_sl = document.getElementById('kabel_sl').value;
        data.jenis_sambungan = document.getElementById('jenis_sambungan').value;
        data.merk_mcb = document.getElementById('merk_mcb').value;
        data.ampere_mcb = document.getElementById('ampere_mcb').value;
        data.gardu = document.getElementById('gardu').value;


        // If no new images were captured AND all images previously existed, no need to submit
        if (!hasNewImage &&
            capturedImageKWH === "EXISTS_AND_UNCHANGED" &&
            capturedImageRumah === "EXISTS_AND_UNCHANGED" &&
            capturedImageSR === "EXISTS_AND_UNCHANGED" &&
            capturedImageTiang === "EXISTS_AND_UNCHANGED") {
            submitMessageEl.innerText = "Tidak ada gambar baru untuk disimpan. Semua gambar sudah ada.";
            submitMessageEl.style.color = 'orange';
            submitAllImagesBtn.disabled = false;
            return;
        }

        data._method = 'PUT'; // Laravel will interpret this as a PUT request

        submitAllImagesBtn.disabled = true;
        submitMessageEl.innerText = "Menyimpan gambar dan data, harap tunggu...";
        submitMessageEl.style.color = 'blue';

        try {
            const response = await fetch(`${LARAVEL_WEB_UPLOAD_URL}${pelangganId}/update-gambar`, {
                method: 'POST', // Use POST for form submission, Laravel will handle _method=PUT
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                submitMessageEl.innerText = `Gambar dan data berhasil disimpan! ${result.message || ''}`;
                submitMessageEl.style.color = 'green';
                window.location.href = "{{ url('/searchdatapelanggan') }}"; // Redirect on success
            } else {
                submitMessageEl.innerText = `Gagal menyimpan gambar dan data: ${result.message || 'Terjadi kesalahan'}`;
                submitMessageEl.style.color = 'red';
                console.error("Upload failed:", result);
            }
        } catch (error) {
            submitMessageEl.innerText = `Terjadi kesalahan jaringan: ${error.message}`;
            submitMessageEl.style.color = 'red';
            console.error("Network error during upload:", error);
        } finally {
            checkSubmitButtonStatus(); // Re-check status, potentially re-enable if not all successful
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

    // --- SR Camera Setup ---
    const videoSR = document.getElementById('videoSR');
    const overlaySR = document.getElementById('overlaySR');
    const msgSR = document.getElementById('msgSR');
    const btnStartSR = document.getElementById('startSR');
    const btnCaptureSR = document.getElementById('captureSR');
    const imgSR = document.getElementById('imageSR');
    const mapSR = document.getElementById('mapSR');

    let streamSR = null;

    btnStartSR.addEventListener('click', async () => {
        streamSR = await startCamera(videoSR, overlaySR, msgSR, btnCaptureSR);
        if (streamSR) {
            drawLiveOverlay(videoSR, overlaySR);
        }
    });

    btnCaptureSR.addEventListener('click', () => {
        captureImage(videoSR, overlaySR, imgSR, msgSR, mapSR, 'sr');
        if (streamSR) {
            streamSR.getTracks().forEach(track => track.stop());
            videoSR.srcObject = null;
            btnCaptureSR.disabled = true;
            msgSR.innerText = "Status: Kamera SR dimatikan";
        }
    });

    // --- Tiang Camera Setup ---
    const videoTiang = document.getElementById('videoTiang');
    const overlayTiang = document.getElementById('overlayTiang');
    const msgTiang = document.getElementById('msgTiang');
    const btnStartTiang = document.getElementById('startTiang');
    const btnCaptureTiang = document.getElementById('captureTiang');
    const imgTiang = document.getElementById('imageTiang');
    const mapTiang = document.getElementById('mapTiang');

    let streamTiang = null;

    btnStartTiang.addEventListener('click', async () => {
        streamTiang = await startCamera(videoTiang, overlayTiang, msgTiang, btnCaptureTiang);
        if (streamTiang) {
            drawLiveOverlay(videoTiang, overlayTiang);
        }
    });

    btnCaptureTiang.addEventListener('click', () => {
        captureImage(videoTiang, overlayTiang, imgTiang, msgTiang, mapTiang, 'tiang');
        if (streamTiang) {
            streamTiang.getTracks().forEach(track => track.stop());
            videoTiang.srcObject = null;
            btnCaptureTiang.disabled = true;
            msgTiang.innerText = "Status: Kamera Tiang dimatikan";
        }
    });


    submitAllImagesBtn.addEventListener('click', uploadImages);

    // Initial check when the page loads
    checkSubmitButtonStatus();

    // Opsional: Inisialisasi peta jika ada koordinat yang tersimpan di database
    @if(!empty($pelanggan->gambar_kwh) && !empty($pelanggan->kwh_latitude) && !empty($pelanggan->kwh_longitude))
    document.addEventListener('DOMContentLoaded', () => {
        initializeLeafletMap('mapKWH', parseFloat({{ $pelanggan->kwh_latitude }}), parseFloat({{ $pelanggan->kwh_longitude }}), 'KWH Tersimpan');
        document.getElementById('mapKWH').style.display = 'block';
    });
    @endif

    @if(!empty($pelanggan->gambar_rumah) && !empty($pelanggan->rumah_latitude) && !empty($pelanggan->rumah_longitude))
    document.addEventListener('DOMContentLoaded', () => {
        initializeLeafletMap('mapRumah', parseFloat({{ $pelanggan->rumah_latitude }}), parseFloat({{ $pelanggan->rumah_longitude }}), 'Rumah Tersimpan');
        document.getElementById('mapRumah').style.display = 'block';
    });
    @endif

    @if(!empty($pelanggan->gambar_sr) && !empty($pelanggan->sr_latitude) && !empty($pelanggan->sr_longitude))
    document.addEventListener('DOMContentLoaded', () => {
        initializeLeafletMap('mapSR', parseFloat({{ $pelanggan->sr_latitude }}), parseFloat({{ $pelanggan->sr_longitude }}), 'SR Tersimpan');
        document.getElementById('mapSR').style.display = 'block';
    });
    @endif

    @if(!empty($pelanggan->gambar_tiang) && !empty($pelanggan->tiang_latitude) && !empty($pelanggan->tiang_longitude))
    document.addEventListener('DOMContentLoaded', () => {
        initializeLeafletMap('mapTiang', parseFloat({{ $pelanggan->tiang_latitude }}), parseFloat({{ $pelanggan->tiang_longitude }}), 'Tiang Tersimpan');
        document.getElementById('mapTiang').style.display = 'block';
    });
    @endif

</script>
@endpush
