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
                                Sedang Check-In
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 shadow-sm border border-gray-200">
                                <span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                                Belum Check-In
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
                                <textarea name="note" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Catatan aktivitas hari ini..."></textarea>
                            </div>
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all transform hover:scale-105">
                                Check Out Sekarang
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
                            <div class="flex rounded-lg bg-gray-200 p-1 mb-4">
                                <button type="button" class="flex-1 py-1.5 text-sm font-medium rounded-md shadow-sm bg-white text-gray-900 transition-colors" id="btn-office" onclick="setAttendanceType('office')">
                                    Kantor
                                </button>
                                <button type="button" class="flex-1 py-1.5 text-sm font-medium rounded-md text-gray-500 hover:text-gray-900 transition-colors" id="btn-field" onclick="setAttendanceType('field')">
                                    Lapangan
                                </button>
                            </div>
                            <input type="hidden" name="attendance_type" id="attendance_type" value="office">

                            <!-- Field Assignment Dropdown (Hidden by default) -->
                            <div id="field-options" class="hidden mb-4 space-y-3">
                                <div class="p-3 bg-blue-50 text-blue-800 rounded-lg text-xs leading-5">
                                    <span class="font-bold block mb-1">Mode Lapangan</span>
                                    Absensi lapangan tidak dibatasi geofence kantor.
                                </div>
                                
                                {{-- Sub-Mode Selector --}}
                                <div class="flex space-x-2 mb-2">
                                    <button type="button" id="field-mode-assignment" onclick="setFieldMode('selection')"
                                        class="flex-1 py-1 px-2 text-xs font-medium rounded border border-indigo-200 bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
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
                                    <select name="assignment_id" id="assignment_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
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
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                     <p class="mt-1 text-xs text-gray-500">Foto atau PDF. Maksimal 5MB.</p>
                                </div>
                            </div>

                            <!-- Office Info (Visible by default) -->
                            <div id="office-options" class="mb-4">
                                <div class="p-3 bg-indigo-50 text-indigo-800 rounded-lg text-xs leading-5 flex items-start">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <div>
                                        <span class="font-bold block mb-1">Mode Kantor</span>
                                        Anda wajib berada di area kantor (Radius 100m). Lokasi Anda akan dideteksi otomatis.
                                    </div>
                                </div>
                                <div id="location-status" class="mt-2 text-xs text-gray-500 italic flex items-center">
                                    <svg class="w-4 h-4 mr-1 animate-spin" id="loc-spinner" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Mendeteksi lokasi...
                                </div>
                            </div>

                            <button type="submit" id="btn-submit-checkin" disabled class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gray-400 cursor-not-allowed transition-all">
                                Check In Masuk
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
                       class="px-3 py-2 text-sm font-medium rounded-md {{ request('filter') === 'mine' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                       Absensi Saya
                    </a>
                    <a href="{{ route('absensi.index') }}" 
                       class="px-3 py-2 text-sm font-medium rounded-md {{ !request('filter') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                       Semua Data
                    </a>
                </div>
                
                <a href="{{ route('absensi.create') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Manual Entry
                </a>
                @endrole
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">Tanggal & Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-indigo-800 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($absensis as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">
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
                                    'izin' => 'bg-indigo-50 text-indigo-600',
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
                                <a href="{{ route('absensi.edit', $a->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs">Edit</a>
                                <form action="{{ route('absensi.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus absensi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs bg-transparent border-0 cursor-pointer">Hapus</button>
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
            fieldModeAssignment.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-200');
            fieldModeManual.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-200');
            
            inputAssignmentSelect.classList.remove('hidden');
            inputManualText.classList.add('hidden');

            assignmentSelect.setAttribute('required', 'required');
            assignmentSelect.value = ""; // Reset to force choice
            manualInput.removeAttribute('required');
            manualInput.value = "";

        } else {
            fieldModeManual.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-200');
            fieldModeAssignment.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-200');
            
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

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            });
        } else {
            locStatus.innerHTML = "<span class='text-red-600'>Geolocation not supported by this browser.</span>";
        }
    }

    function showPosition(position) {
        latInput.value = position.coords.latitude;
        lngInput.value = position.coords.longitude;
        accInput.value = position.coords.accuracy;

        locStatus.innerHTML = "<span class='text-green-600 flex items-center'><svg class='w-4 h-4 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'></path></svg> Lokasi ditemukan (Akurasi: " + Math.round(position.coords.accuracy) + "m)</span>";
        
        validateForm();
    }

    function showError(error) {
        let msg = "";
        switch(error.code) {
            case error.PERMISSION_DENIED:
                msg = "Anda menolak permintaan lokasi.";
                break;
            case error.POSITION_UNAVAILABLE:
                msg = "Informasi lokasi tidak tersedia.";
                break;
            case error.TIMEOUT:
                msg = "Waktu permintaan lokasi habis.";
                break;
            case error.UNKNOWN_ERROR:
                msg = "Terjadi kesalahan yang tidak diketahui.";
                break;
        }
        locStatus.innerHTML = "<span class='text-red-600'>" + msg + "</span>";
        // Retry logic or blocking could be here
        validateForm();
    }

    function validateForm() {
        let isValid = false;
        
        if (inputType.value === 'office') {
            if (latInput.value && lngInput.value) {
                isValid = true;
            }
        } else {
            // Field - HTML5 required attribute handles validation for inputs
            // We just ensure location is present for safer measure (though some field ops might not need it? controller enforces it for manual)
            if (latInput.value && lngInput.value) {
                isValid = true;
            }
        }

        if (isValid) {
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('bg-gray-400', 'cursor-not-allowed');
            btnSubmit.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
        } else {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('bg-gray-400', 'cursor-not-allowed');
            btnSubmit.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
        }
    }

    // Init
    getLocation();
</script>
@endpush