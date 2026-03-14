@extends('layouts.app')

@section('title', 'Absensi')
@section('page_title', 'Absensi & Kehadiran')

@section('content')
<div class="p-6 space-y-8">
    
    <!-- Top Widget: Check-In/Out Area -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                
                <!-- Time & Status -->
                <div class="text-center md:text-left">
                    <h2 class="text-3xl font-bold text-gray-800" id="live-clock">--:--:--</h2>
                    <p class="text-gray-500 font-medium">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
                    <div class="mt-4">
                        @if($hasOpenAttendance)
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 shadow-sm border border-green-200">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Sedang Absen Masuk
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 shadow-sm border border-gray-200">
                                <span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                                Belum Absen Masuk
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Action Forms -->
                <div class="w-full md:w-auto flex-shrink-0" id="attendance-action-area">
                    
                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm max-w-lg mx-auto md:mx-0">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm text-sm max-w-lg mx-auto md:mx-0">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($hasOpenAttendance)
                        <!-- Check Out Form -->
                        <form action="{{ route('absensi.checkOut') }}" method="POST" class="w-full md:w-80">
                            @csrf
                            <div class="mb-4">
                                <label for="note" class="sr-only">Catatan</label>
                                <textarea name="note" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" placeholder="Catatan aktivitas hari ini..."></textarea>
                            </div>
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all transform hover:scale-105">
                                Absen Keluar Sekarang
                            </button>
                        </form>
                    @else
                        <!-- Check In Form -->
                        <form action="{{ route('absensi.checkIn') }}" method="POST" id="checkin-form" enctype="multipart/form-data" class="w-full md:w-96 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            @csrf
                            <input type="hidden" name="lat" id="lat">
                            <input type="hidden" name="lng" id="lng">
                            <input type="hidden" name="accuracy" id="accuracy">

                            <!-- Type Toggle -->
                            @php
                                $isPureWartawan = auth()->user()->hasRole('wartawan') && !auth()->user()->hasAnyRole(['admin', 'pegawai', 'direktur']);
                            @endphp
                            <div class="{{ $isPureWartawan ? 'hidden' : 'flex' }} rounded-lg bg-gray-200 p-1 mb-4">
                                <button type="button" class="flex-1 py-1.5 text-sm font-medium rounded-md shadow-sm bg-white text-gray-900 transition-colors" id="btn-office" onclick="setAttendanceType('office')">
                                    Kantor
                                </button>
                                <button type="button" class="flex-1 py-1.5 text-sm font-medium rounded-md text-gray-500 hover:text-gray-900 transition-colors" id="btn-field" onclick="setAttendanceType('field')">
                                    Lapangan
                                </button>
                            </div>
                            <input type="hidden" name="attendance_type" id="attendance_type" value="{{ $isPureWartawan ? 'field' : 'office' }}">

                            <!-- Field Assignment Dropdown (Hidden by default for non-wartawan) -->
                            <div id="field-options" class="{{ $isPureWartawan ? 'mb-4 space-y-3' : 'hidden mb-4 space-y-3' }}">
                                <div class="p-3 bg-blue-50 text-blue-800 rounded-lg text-xs leading-5">
                                    <span class="font-bold block mb-1">Mode Lapangan</span>
                                    Absensi lapangan tidak dibatasi geofence kantor.
                                </div>
                                
                                {{-- Sub-Mode Selector --}}
                                <div class="flex space-x-2 mb-2">
                                    <button type="button" id="field-mode-assignment" onclick="setFieldMode('selection')"
                                        class="flex-1 py-1 px-2 text-xs font-medium rounded border border-orange-200 bg-orange-100 text-orange-700 hover:bg-orange-200">
                                        Pilih Surat Tugas
                                    </button>
                                    <button type="button" id="field-mode-manual" onclick="setFieldMode('manual')"
                                        class="flex-1 py-1 px-2 text-xs font-medium rounded border border-gray-200 bg-white text-gray-500 hover:bg-gray-50">
                                        Upload Surat
                                    </button>
                                </div>

                                {{-- Input 1: Selection --}}
                                <div id="assignment_selector">
                                    <label for="assignment_id" class="block text-sm font-medium text-gray-700">Pilih Surat Tugas / Assignment</label>
                                    <select name="assignment_id" id="assignment_id" {{ $isPureWartawan ? 'required' : '' }} class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                        <option value="">-- Pilih Assignment --</option>
                                        @foreach($activeAssignments as $assignment)
                                            <option value="{{ $assignment->id }}">{{ $assignment->title }} ({{ $assignment->letter_number }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Input 2: Manual (Upload) --}}
                                <div id="manual_input_container" class="hidden">
                                     <label for="manual_assignment" class="block text-sm font-medium text-gray-700">Upload Bukti Surat Tugas</label>
                                     <input type="file" name="manual_assignment" id="manual_assignment" accept="image/*,.pdf"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                     <p class="mt-1 text-xs text-gray-500">Foto atau PDF. Maksimal 5MB.</p>
                                </div>
                            </div>

                            <!-- Office Info (Visible by default only for non-wartawan) -->
                            <div id="office-options" class="{{ $isPureWartawan ? 'hidden mb-4' : 'mb-4' }}">
                                <div class="p-3 bg-orange-50 text-orange-800 rounded-lg text-xs leading-5 flex items-start">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <div>
                                        <span class="font-bold block mb-1">Mode Kantor</span>
                                        Anda wajib berada di area kantor. Lokasi Anda akan dideteksi otomatis.
                                    </div>
                                </div>
                                <div id="location-status" class="mt-2 text-xs text-gray-500 italic flex items-center">
                                    <svg class="w-4 h-4 mr-1 animate-spin" id="loc-spinner" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Mendeteksi lokasi...
                                </div>
                            </div>

                            <button type="submit" id="btn-submit-checkin" disabled class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gray-400 cursor-not-allowed transition-all">
                                Absen Masuk
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- History Card -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">Riwayat Kehadiran</h3>
            
            <div class="flex space-x-2">
                @role('admin')
                <div class="flex items-center space-x-2 mr-2">
                    <a href="{{ route('absensi.index', ['filter' => 'mine']) }}" 
                       class="px-3 py-2 text-sm font-medium rounded-md {{ request('filter') === 'mine' ? 'bg-orange-100 text-orange-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                       Absensi Saya
                    </a>
                    <a href="{{ route('absensi.index') }}" 
                       class="px-3 py-2 text-sm font-medium rounded-md {{ !request('filter') ? 'bg-orange-100 text-orange-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                       Semua Data
                    </a>
                </div>
                
                @endrole
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">Tanggal & Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-orange-800 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($absensis as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold text-sm">
                                    {{ substr($a->user->name, 0, 2) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $a->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $a->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-md">IN: {{ $a->jam_masuk ?? '--:--' }}</span>
                                <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-md ml-1">OUT: {{ $a->jam_keluar ?? '--:--' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($a->attendance_type == 'office')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Kantor
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Lapangan
                                </span>
                                @if($a->assignment)
                                    <span class="block text-[10px] text-gray-500 mt-1 truncate max-w-[150px]">{{ $a->assignment->title }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($a->worked_minutes)
                                {{ floor($a->worked_minutes / 60) }}j {{ $a->worked_minutes % 60 }}m
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status_colors = [
                                    'open' => 'bg-green-100 text-green-800 border-green-200',
                                    'closed' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'auto_closed' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'manual_edit' => 'bg-orange-100 text-orange-800 border-orange-200',
                                ];
                                $legacy_colors = [
                                    'hadir' => 'bg-green-50 text-green-600',
                                    'izin' => 'bg-orange-50 text-orange-600',
                                    'sakit' => 'bg-pink-50 text-pink-600',
                                    'alpha' => 'bg-red-50 text-red-600',
                                ];
                            @endphp
                            
                            @if($a->status && isset($status_colors[$a->status]))
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $status_colors[$a->status] }}">
                                    {{ str_replace('_', ' ', strtoupper($a->status)) }}
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $legacy_colors[$a->legacy_status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ strtoupper($a->legacy_status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium">
                           @if($a->note)
                                <div class="text-xs text-left text-gray-500 italic max-w-xs whitespace-normal bg-gray-50 p-2 rounded">
                                    "{{ $a->note }}"
                                </div>
                           @elseif($a->keterangan)
                                <div class="text-xs text-left text-gray-500 italic max-w-xs whitespace-normal bg-gray-50 p-2 rounded">
                                    "{{ $a->keterangan }}"
                                </div>
                           @else
                                <span class="text-gray-300">-</span>
                           @endif

                           @role('admin')
                           <div class="mt-2 flex justify-center space-x-2">
                                <a href="{{ route('absensi.edit', $a->id) }}" class="text-orange-600 hover:text-orange-900 text-xs">Edit</a>
                                <form action="{{ route('absensi.destroy', $a->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-confirm="Yakin ingin menghapus absensi ini?" class="text-red-600 hover:text-red-900 text-xs bg-transparent border-0 cursor-pointer">Hapus</button>
                                </form>
                           </div>
                           @endrole
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Belum ada data absensi hari ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        document.getElementById('live-clock').innerText = now.toLocaleTimeString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Toggle Attendance Type
    const btnOffice = document.getElementById('btn-office');
    const btnField = document.getElementById('btn-field');
    const inputType = document.getElementById('attendance_type');
    const fieldOptions = document.getElementById('field-options');
    const officeOptions = document.getElementById('office-options');
    const btnSubmit = document.getElementById('btn-submit-checkin');

    // Field Mode Elements
    const fieldModeAssignment = document.getElementById('field-mode-assignment');
    const fieldModeManual = document.getElementById('field-mode-manual');
    const inputAssignmentSelect = document.getElementById('assignment_selector');
    const inputManualText = document.getElementById('manual_input_container');
    const assignmentSelect = document.getElementById('assignment_id');
    const manualInput = document.getElementById('manual_assignment');

    function setAttendanceType(type) {
        inputType.value = type;
        if (type === 'office') {
            btnOffice.classList.replace('bg-white', 'bg-white'); 
            btnOffice.classList.add('shadow-sm', 'text-gray-900');
            btnOffice.classList.remove('text-gray-500');
            
            btnField.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
            btnField.classList.add('text-gray-500');

            fieldOptions.classList.add('hidden');
            officeOptions.classList.remove('hidden');
            
            // Cleanup required
            assignmentSelect.removeAttribute('required');
            manualInput.removeAttribute('required');

        } else {
            btnField.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
            btnField.classList.remove('text-gray-500');
            
            btnOffice.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
            btnOffice.classList.add('text-gray-500');

            fieldOptions.classList.remove('hidden');
            officeOptions.classList.add('hidden');

            // Default to selection mode if not set
            setFieldMode('selection');
        }
        validateForm();
    }

    function setFieldMode(mode) {
        if (mode === 'selection') {
            fieldModeAssignment.classList.add('bg-orange-100', 'text-orange-700', 'border-orange-200');
            fieldModeManual.classList.remove('bg-orange-100', 'text-orange-700', 'border-orange-200');
            
            inputAssignmentSelect.classList.remove('hidden');
            inputManualText.classList.add('hidden');

            assignmentSelect.setAttribute('required', 'required');
            assignmentSelect.value = ""; // Reset to force choice
            manualInput.removeAttribute('required');
            manualInput.value = "";

        } else {
            fieldModeManual.classList.add('bg-orange-100', 'text-orange-700', 'border-orange-200');
            fieldModeAssignment.classList.remove('bg-orange-100', 'text-orange-700', 'border-orange-200');
            
            inputManualText.classList.remove('hidden');
            inputAssignmentSelect.classList.add('hidden');

            manualInput.setAttribute('required', 'required');
            assignmentSelect.removeAttribute('required');
            assignmentSelect.value = "";
        }
    }

    // Geolocation Logic
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const accInput = document.getElementById('accuracy');
    const locStatus = document.getElementById('location-status');
    const locSpinner = document.getElementById('loc-spinner');
    
    let watchId = null;
    let bestAccuracy = Infinity;
    let sampleCount = 0;
    let startTime = null;
    let autoRetryTimer = null;
    let lastAccuracyImprovement = null;

    const MAX_ACCURACY_OFFICE = 250; // Increased to 250m for Laptop/WiFi support
    const AUTO_RETRY_INTERVAL = 8000; // Retry every 8 seconds if no improvement
    let locationAccuracyOk = false;

    function getLocation() {
        if (navigator.geolocation) {
            // ... (rest of function same as before, no changes needed here) ...
            // Clear existing watch and timers
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
            }
            if (autoRetryTimer !== null) {
                clearInterval(autoRetryTimer);
            }
            
            // Reset state
            bestAccuracy = Infinity;
            sampleCount = 0;
            startTime = Date.now();
            lastAccuracyImprovement = Date.now();
            locationAccuracyOk = false;
            
            locStatus.innerHTML = "<div class='flex items-center'><svg class='w-4 h-4 mr-2 animate-spin' fill='none' viewBox='0 0 24 24'><circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle><path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path></svg><span>Mencari sinyal GPS...</span></div>";
            
            watchId = navigator.geolocation.watchPosition(showPosition, showError, {
                enableHighAccuracy: true,
                timeout: 30000, // Increased to 30 seconds
                maximumAge: 0
            });
            
            // Auto-retry mechanism
            autoRetryTimer = setInterval(function() {
                const timeSinceImprovement = Date.now() - lastAccuracyImprovement;
                if (timeSinceImprovement > AUTO_RETRY_INTERVAL && bestAccuracy > MAX_ACCURACY_OFFICE) {
                    console.log('GPS: No improvement for 8s, restarting watch...');
                    if (watchId !== null) {
                        navigator.geolocation.clearWatch(watchId);
                    }
                    watchId = navigator.geolocation.watchPosition(showPosition, showError, {
                        enableHighAccuracy: true,
                        timeout: 30000,
                        maximumAge: 0
                    });
                    lastAccuracyImprovement = Date.now();
                    sampleCount = 0;
                    locStatus.innerHTML = "<div class='flex items-center text-orange-600'><svg class='w-4 h-4 mr-2 animate-spin' fill='none' viewBox='0 0 24 24'><circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle><path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path></svg><span>Mencoba ulang... (Akurasi terakhir: " + bestAccuracy + "m)</span></div>";
                }
            }, AUTO_RETRY_INTERVAL);
        } else {
             locStatus.innerHTML = "<span class='text-red-600'>Browser tidak mendukung geolokasi.</span>";
        }
    }

    function showPosition(position) {
        const currentAccuracy = Math.round(position.coords.accuracy);
        sampleCount++;
        
        // Update input values
        latInput.value = position.coords.latitude;
        lngInput.value = position.coords.longitude;
        accInput.value = currentAccuracy;

        // Track best accuracy
        if (currentAccuracy < bestAccuracy) {
            bestAccuracy = currentAccuracy;
            lastAccuracyImprovement = Date.now();
        }
        
        const elapsedSeconds = Math.round((Date.now() - startTime) / 1000);

        // Always accept location regarding accuracy
        locationAccuracyOk = true;
        
        // Stop auto-retry since we accept any result
        if (autoRetryTimer !== null) {
            clearInterval(autoRetryTimer);
            autoRetryTimer = null;
        }

        locStatus.innerHTML = "<div class='flex items-center text-green-600'>" +
            "<svg class='w-5 h-5 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'></path></svg>" +
            "<span>Lokasi terkunci!</span>" +
        "</div>";
        
        validateForm();
    }

    function showError(error) {
        let msg = "";
        switch(error.code) {
            case error.PERMISSION_DENIED:
                msg = "Anda menolak permintaan lokasi. Silakan izinkan akses lokasi di pengaturan browser.";
                break;
            case error.POSITION_UNAVAILABLE:
                msg = "Lokasi tidak tersedia. Pastikan GPS aktif.";
                break;
            case error.TIMEOUT:
                // For watchPosition, just ignore timeout if we have any data
                if (bestAccuracy !== Infinity) return; 
                msg = "Timeout. Pastikan GPS aktif dan coba di area terbuka.";
                break;
            case error.UNKNOWN_ERROR:
                msg = "Terjadi kesalahan yang tidak diketahui.";
                break;
        }
        
        // Only show error if it's critical or we have no data at all
        if (error.code === error.PERMISSION_DENIED || bestAccuracy === Infinity) {
            locationAccuracyOk = false;
            locStatus.innerHTML = "<div class='text-red-600'>" + 
                "<div class='flex items-center'>" +
                    "<svg class='w-4 h-4 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'></path></svg>" +
                    "<span>" + msg + "</span>" +
                "</div>" +
                "<button type='button' onclick='retryLocation()' class='mt-2 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700'>Coba Lagi</button>" +
            "</div>";
            validateForm();
        }
    }

    function retryLocation() {
        getLocation();
    }

    function validateForm() {
        let isValid = false;
        
        if (inputType.value === 'office') {
            // Office mode requires location AND accuracy <= 50m
            if (latInput.value && lngInput.value && locationAccuracyOk) {
                isValid = true;
            }
        } else {
            // Field - HTML5 required attribute handles validation for inputs
            // We just ensure location is present for safer measure
            if (latInput.value && lngInput.value) {
                isValid = true;
            }
        }

        if (isValid) {
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('bg-gray-400', 'cursor-not-allowed');
            btnSubmit.classList.add('bg-orange-600', 'hover:bg-orange-700');
        } else {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('bg-gray-400', 'cursor-not-allowed');
            btnSubmit.classList.remove('bg-orange-600', 'hover:bg-orange-700');
        }
    }

    // Init
    getLocation();
</script>
@endpush