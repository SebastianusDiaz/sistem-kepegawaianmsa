@extends('layouts.app')

@section('title', 'Buat Penugasan Baru')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Back Link --}}
        <nav class="mb-6">
            <a href="{{ route('assignments.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </nav>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
                <h2 class="text-xl font-bold">
                    @role('wartawan')
                        Buat Laporan Mandiri (Self-Assignment)
                    @else
                        Buat Penugasan Liputan
                    @endrole
                </h2>
                <p class="text-indigo-100 text-sm mt-1">
                    @role('wartawan')
                        Buat penugasan untuk kejadian yang Anda temui langsung di lapangan.
                    @else
                        Isi detail lengkap untuk menugaskan wartawan ke lapangan.
                    @endrole
                </p>
            </div>

            <form action="{{ route('assignments.store') }}" method="POST" class="p-6 md:p-8 space-y-8">
                @csrf

                {{-- SECTION 1: Asal Penugasan (Kerjasama) --}}
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-200">
                    <h3 class="text-base font-semibold text-amber-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Asal Penugasan (Opsional)
                    </h3>
                    <div>
                        <label for="kerjasama_id" class="block text-sm font-medium text-amber-800 mb-1">Pilih Kerjasama
                            (MoU)</label>
                        <select id="kerjasama_id" name="kerjasama_id"
                            class="shadow-sm focus:ring-amber-500 focus:border-amber-500 block w-full sm:text-sm border-amber-300 rounded-lg py-2.5 bg-white">
                            <option value="">-- Tanpa Kerjasama / Penugasan Internal --</option>
                            @foreach($activeKerjasamas as $k)
                                <option value="{{ $k->id }}">{{ $k->company_name }} (s/d {{ $k->end_date->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-amber-600">Pilih jika penugasan ini berasal dari perjanjian kerjasama
                            dengan mitra.</p>
                    </div>
                </div>

                {{-- SECTION 2: Informasi Dasar --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-6">
                        {{-- Judul --}}
                        <div class="sm:col-span-6">
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Liputan <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" required
                                placeholder="Contoh: Liputan Banjir Bandang di Area X"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5">
                        </div>

                        {{-- Reporter --}}
                        @unlessrole('wartawan')
                        <div class="sm:col-span-3">
                            <label for="reporter_id" class="block text-sm font-medium text-gray-700">Pilih Wartawan</label>
                            <select id="reporter_id" name="reporter_id"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5">
                                <option value="">-- Opsional / Open Bid --</option>
                                @foreach($reporters as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}
                                        ({{ $r->profile->position->name ?? 'Wartawan' }})</option>
                                @endforeach
                            </select>
                        </div>
                        @endunlessrole

                        {{-- Prioritas --}}
                        <div class="sm:col-span-3">
                            <label for="priority" class="block text-sm font-medium text-gray-700">Tingkat Prioritas</label>
                            <select id="priority" name="priority"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5">
                                <option value="normal">Normal (Standard)</option>
                                <option value="high">High (Penting)</option>
                                <option value="urgent">Urgent (Segera)</option>
                            </select>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Brief / Instruksi
                                Khusus</label>
                            <textarea id="description" name="description" rows="4"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                placeholder="Jelaskan angle berita yang diinginkan, narasumber yang perlu diwawancara, dll."></textarea>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- SECTION 3: Lokasi --}}
                <div class="bg-indigo-50 rounded-xl p-5 border border-indigo-100">
                    <h3 class="text-base font-semibold text-indigo-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lokasi & Geofence
                    </h3>
                    <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-indigo-800">Nama Lokasi / Venue <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="location_name" required placeholder="Gedung DPR, Senayan"
                                class="mt-1 block w-full border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-2.5 bg-white">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-medium text-indigo-600 uppercase tracking-wider">Latitude
                                (Opsional)</label>
                            <input type="text" name="latitude" placeholder="-6.200000"
                                class="mt-1 block w-full border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-medium text-indigo-600 uppercase tracking-wider">Longitude
                                (Opsional)</label>
                            <input type="text" name="longitude" placeholder="106.816666"
                                class="mt-1 block w-full border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                        </div>
                        <div class="sm:col-span-6">
                            <p class="text-xs text-indigo-500 flex items-start">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                Jika Lat/Long diisi, wartawan hanya bisa check-in dalam radius 500m dari lokasi.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: Waktu --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Jadwal & Deadline
                    </h3>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Waktu Mulai <span
                                    class="text-red-500">*</span></label>
                            <input type="datetime-local" name="start_time" id="start_time" required
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-2.5">
                        </div>
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700">Batas Akhir (Deadline)
                                <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="deadline" id="deadline" required
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-2.5">
                            <p class="mt-1 text-xs text-gray-500">Wartawan akan mendapat notifikasi mendekati waktu ini.</p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-8 mt-8 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('assignments.index') }}"
                        class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection