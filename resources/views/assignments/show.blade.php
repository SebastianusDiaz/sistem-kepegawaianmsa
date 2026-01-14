@extends('layouts.app')

@section('title', $assignment->title)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center justify-between">
            <a href="{{ route('assignments.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
            <span class="text-xs font-mono text-gray-400">ID: #{{ substr($assignment->id, 0, 8) }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- KOLOM KIRI: Informasi Utama --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Header --}}
                    <div
                        class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            {{-- Status Badge --}}
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'assigned' => 'bg-blue-100 text-blue-800',
                                    'accepted' => 'bg-indigo-100 text-indigo-800',
                                    'on_site' => 'bg-green-100 text-green-800',
                                    'submitted' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-gray-800 text-white',
                                ];
                                $color = $statusColors[$assignment->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $color }}">
                                {{ $assignment->status }}
                            </span>

                            {{-- Priority Badge --}}
                            @if($assignment->priority === 'urgent')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">
                                    Urgent
                                </span>
                            @elseif($assignment->priority === 'high')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20">
                                    High
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">
                            Dibuat: {{ $assignment->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    {{-- Body Content --}}
                    <div class="p-6 md:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 leading-tight">
                            {{ $assignment->title }}
                        </h1>

                        <div class="prose prose-indigo max-w-none text-gray-600">
                            <h3 class="text-gray-900 text-lg font-semibold">Brief & Deskripsi</h3>
                            <p class="whitespace-pre-line">{{ $assignment->description }}</p>
                        </div>
                    </div>
                </div>

                {{-- BUKTI LAPORAN (REPORT EVIDENCE) --}}
                @if($assignment->status === 'submitted' || $assignment->status === 'completed' || $assignment->evidence_photo || $assignment->evidence_document || $assignment->evidence_link)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900">Hasil Liputan</h3>

                            @if(Auth::id() === $assignment->reporter_id && $assignment->status === 'submitted')
                                <button type="button"
                                    onclick="document.getElementById('completion-modal').classList.remove('hidden')"
                                    class="text-xs text-indigo-600 hover:text-indigo-900 font-medium flex items-center bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Ubah Bukti
                                </button>
                            @endif
                        </div>
                        <div class="p-6 space-y-6">
                            {{-- Foto Bukti --}}
                            @if($assignment->evidence_photo)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">Foto Dokumentasi</h4>
                                    <div x-data="{ open: false }" class="relative group max-w-lg">
                                        <img src="{{ asset($assignment->evidence_photo) }}" alt="Bukti Foto"
                                            class="rounded-lg shadow-md w-40 h-40 object-cover cursor-zoom-in transition-transform hover:scale-[1.01]"
                                            @click="open = true">

                                        {{-- Lightbox Modal --}}
                                        <div x-show="open"
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                                            x-transition.opacity @click.self="open = false" style="display: none;">
                                            <div class="relative max-w-5xl w-full max-h-screen">
                                                <img src="{{ asset($assignment->evidence_photo) }}"
                                                    class="rounded shadow-2xl mx-auto max-h-[90vh]">
                                                <button @click="open = false"
                                                    class="absolute -top-10 right-0 text-white hover:text-gray-300">
                                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Dokumen --}}
                                @if($assignment->evidence_document)
                                    <div class="flex items-center p-4 bg-blue-50 border border-blue-100 rounded-xl">
                                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h5 class="text-sm font-bold text-gray-900">Dokumen Liputan</h5>
                                            <a href="{{ asset($assignment->evidence_document) }}" target="_blank"
                                                class="text-xs text-blue-600 hover:text-blue-800 font-medium mt-1 inline-flex items-center">
                                                Download File
                                                <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                {{-- Link --}}
                                @if($assignment->evidence_link)
                                    <div class="flex items-center p-4 bg-gray-50 border border-gray-100 rounded-xl">
                                        <div class="flex-shrink-0 bg-gray-200 p-3 rounded-lg text-gray-600">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                        </div>
                                        <div class="ml-4 flex-1 min-w-0">
                                            <h5 class="text-sm font-bold text-gray-900">Link Berita</h5>
                                            <a href="{{ $assignment->evidence_link }}" target="_blank"
                                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium mt-1 truncate block hover:underline">
                                                {{ $assignment->evidence_link }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- RESPON PEGAWAI (STAFF RESPONSE) --}}
                @if($assignment->staff_response_note || $assignment->staff_response_file)
                    <div class="bg-indigo-50 rounded-2xl shadow-sm border border-indigo-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-indigo-100 bg-indigo-100/50 flex items-center gap-2">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <h3 class="text-lg font-bold text-indigo-900">Respon Kantor</h3>
                        </div>
                        <div class="p-6">
                            <div
                                class="prose prose-sm max-w-none text-indigo-900 bg-white p-4 rounded-xl border border-indigo-100">
                                <p>{{ $assignment->staff_response_note }}</p>
                            </div>
                            @if($assignment->staff_response_file)
                                <div class="mt-4">
                                    <a href="{{ asset($assignment->staff_response_file) }}" target="_blank"
                                        class="inline-flex items-center px-4 py-2 border border-indigo-300 shadow-sm text-sm font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50 transition-colors">
                                        <svg class="mr-2 -ml-1 h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download Lampiran Respon
                                    </a>
                                </div>
                            @endif
                            <div class="mt-2 text-xs text-indigo-400 text-right">
                                Direspon pada:
                                {{ $assignment->staff_response_at ? $assignment->staff_response_at->format('d M Y, H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- KOLOM KANAN: Sidebar Aksi & Meta --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- CARD 1: Action Center (Hanya untuk Reporter yang bertugas) --}}
                @if(Auth::id() === $assignment->reporter_id)
                    <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 overflow-hidden relative">
                        <div class="bg-indigo-600 px-6 py-3">
                            <h3 class="text-white font-bold text-sm uppercase tracking-wider flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Aksi Reporter
                            </h3>
                        </div>

                        <div class="p-6">
                            {{-- Notifikasi Error/Success --}}
                            @if($errors->any())
                                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            @if(session('success'))
                                <div
                                    class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200 flex items-start">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            @if($assignment->status === 'assigned' || $assignment->status === 'draft')
                                <div class="text-center space-y-4">
                                    <p class="text-sm text-gray-600">Anda ditugaskan untuk liputan ini. Silakan konfirmasi.</p>
                                    <form action="{{ route('assignments.updateStatus', $assignment->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit"
                                            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow transition-all transform hover:-translate-y-0.5">
                                            Terima Penugasan
                                        </button>
                                    </form>
                                </div>

                            @elseif($assignment->status === 'accepted')
                                <div class="text-center space-y-4">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-800 text-left">
                                        <strong>Status:</strong> Anda telah menerima tugas ini. Klik tombol di bawah untuk mulai
                                        bekerja/check-in.
                                    </div>

                                    <form action="{{ route('assignments.updateStatus', $assignment->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="on_site">

                                        <button type="submit"
                                            class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold shadow-lg transition-all flex flex-col items-center justify-center">
                                            <span class="flex items-center text-lg">
                                                <svg class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                MULAI LIPUTAN (CHECK-IN)
                                            </span>
                                        </button>
                                    </form>
                                </div>

                            @elseif($assignment->status === 'on_site')
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200 text-center">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-3">
                                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-bold text-green-800">Sedang Liputan</h4>
                                    <p class="text-sm text-green-600 mt-1">Anda sudah check-in. Selamat bekerja!</p>
                                </div>

                                <button type="button"
                                    onclick="document.getElementById('completion-modal').classList.remove('hidden')"
                                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow transition-all transform hover:-translate-y-0.5 mt-4 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Selesai & Upload Bukti
                                </button>
                            @elseif($assignment->status === 'submitted' || $assignment->status === 'completed')
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 text-center space-y-3">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-blue-800">Liputan Selesai</h4>
                                        <p class="text-sm text-blue-600 mt-1">Tugas telah diselesaikan.</p>
                                    </div>

                                    @if($assignment->evidence_link)
                                        <div class="pt-3 border-t border-blue-200">
                                            <p class="text-xs text-blue-500 font-semibold uppercase tracking-wide mb-2">Bukti Tayang /
                                                Link Berita</p>
                                            <a href="{{ $assignment->evidence_link }}" target="_blank"
                                                class="inline-flex items-center text-sm font-medium text-blue-700 hover:text-blue-900 hover:underline break-all">
                                                <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 00-2 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                {{ $assignment->evidence_link }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- CARD 2: Lokasi & Deadline --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-5">

                    {{-- Lokasi --}}
                    <div>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Lokasi Liputan</h4>
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-gray-400 mt-0.5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $assignment->location_name }}</p>
                                @if($assignment->latitude && $assignment->longitude)
                                    <p class="text-xs text-gray-500 mt-1 font-mono">{{ $assignment->latitude }},
                                        {{ $assignment->longitude }}
                                    </p>
                                @endif

                                {{-- Smart Link ke Google Maps --}}
                                @php
                                    $mapsQuery = $assignment->latitude
                                        ? "{$assignment->latitude},{$assignment->longitude}"
                                        : urlencode($assignment->location_name);
                                @endphp
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $mapsQuery }}" target="_blank"
                                    class="inline-flex items-center mt-2 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                    Buka Google Maps &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Deadline --}}
                    <div>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deadline</h4>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p
                                    class="text-sm font-bold {{ $assignment->deadline->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $assignment->deadline->format('d M Y') }}
                                </p>
                                <p class="text-sm text-gray-600">Pukul {{ $assignment->deadline->format('H:i') }} WIB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 3: Reporter Info (Untuk Admin/Editor melihat siapa yang ditugaskan) --}}
                @if($assignment->reporter_id && Auth::id() !== $assignment->reporter_id)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center">
                        <div
                            class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                            {{ substr($assignment->reporter->name, 0, 2) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $assignment->reporter->name }}</p>
                            <p class="text-xs text-gray-500">Reporter Bertugas</p>
                        </div>
                    </div>
                @endif

                {{-- CARD 4: Dokumen & Export --}}
                @if(in_array($assignment->status, ['accepted', 'on_site', 'submitted', 'completed']) && ($assignment->reporter_id === Auth::id() || Auth::user()->hasRole(['admin', 'editor', 'direktur'])))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Dokumen</h4>
                        <a href="{{ route('assignments.export_pdf', $assignment->id) }}" target="_blank"
                            class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg class="mr-2 -ml-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Download Surat Tugas
                        </a>
                    </div>
                @endif

                {{-- FORM RESPON PEGAWAI (STAFF/ADMIN ONLY) --}}
                @if(Auth::user()->hasAnyRole(['admin', 'editor', 'pegawai']) && ($assignment->status === 'submitted' || $assignment->status == 'completed'))
                    <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 overflow-hidden mt-6">
                        <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-3">
                            <h3 class="text-white font-bold text-sm uppercase tracking-wider flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Aksi Pegawai / Editor
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('assignments.respond', $assignment->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Respon</label>
                                        <textarea name="staff_response_note" rows="3"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                            placeholder="Berikan catatan, revisi, atau feedback..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload File
                                            (Revisi/Dokumen)</label>
                                        <input type="file" name="staff_response_file"
                                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 hover:cursor-pointer">
                                        <p class="text-xs text-gray-500 mt-1">Opsional. PDF/Doc/Image</p>
                                    </div>
                                    <button type="submit"
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-800 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                                        Kirim Respon
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Completion Modal --}}
            <div id="completion-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                        onclick="document.getElementById('completion-modal').classList.add('hidden')"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('assignments.updateStatus', $assignment->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="submitted">

                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            {{ $assignment->status === 'submitted' ? 'Edit Bukti Liputan' : 'Pengumpulan Bukti Liputan' }}
                                        </h3>
                                        <div class="mt-2 text-sm text-gray-500">
                                            Silakan upload foto dokumentasi dan dokumen pendukung jika ada.
                                        </div>

                                        <div class="mt-6 space-y-5">
                                            {{-- Upload Foto --}}
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">1. Foto
                                                    Dokumentasi <span class="text-red-500">*</span></label>
                                                <div
                                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md bg-gray-50 hover:bg-white transition-colors">
                                                    <div class="space-y-1 text-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                            fill="none" viewBox="0 0 48 48">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600 justify-center">
                                                            <label for="evidence_photo"
                                                                class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                                <span>Upload Foto</span>
                                                                <input id="evidence_photo" name="evidence_photo" type="file"
                                                                    class="sr-only" accept="image/*">
                                                            </label>
                                                        </div>
                                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                                    </div>
                                                </div>
                                                @if($assignment->evidence_photo)
                                                    <p class="text-xs text-green-600 mt-2 flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Foto sudah ada. Upload baru untuk mengganti.
                                                    </p>
                                                @endif
                                            </div>

                                            {{-- Upload Document --}}
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">2. Dokumen
                                                    (Opsional)</label>
                                                <input type="file" name="evidence_document"
                                                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                                                <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX up to 10MB</p>
                                                @if($assignment->evidence_document)
                                                    <p class="text-xs text-green-600 mt-1">Dokumen sudah ada. Upload baru untuk
                                                        mengganti.</p>
                                                @endif
                                            </div>

                                            {{-- Link --}}
                                            <div>
                                                <label for="evidence_link"
                                                    class="block text-sm font-medium text-gray-700 mb-1">3. Link Berita
                                                    (Opsional)</label>
                                                <input type="text" name="evidence_link" id="evidence_link"
                                                    value="{{ old('evidence_link', $assignment->evidence_link) }}"
                                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                    placeholder="https://...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    {{ $assignment->status === 'submitted' ? 'Simpan Perubahan' : 'Kirim & Selesai' }}
                                </button>
                                <button type="button"
                                    onclick="document.getElementById('completion-modal').classList.add('hidden')"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection