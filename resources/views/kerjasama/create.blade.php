@extends('layouts.app')

@section('title', 'Buat Kerjasama Baru')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Back Link --}}
        <nav class="mb-6">
            <a href="{{ route('kerjasama.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </nav>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white">
                <h2 class="text-xl font-bold text-gray-900">Formulir Kerjasama Baru</h2>
                <p class="text-sm text-gray-500 mt-1">Lengkapi informasi perjanjian kerjasama (MoU) dengan perusahaan mitra.
                </p>
            </div>

            <form action="{{ route('kerjasama.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6 md:p-8 space-y-8">
                @csrf

                {{-- Section 1: Company Info --}}
                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Informasi Perusahaan
                    </h3>
                    <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-6">
                            <label for="company_name" class="block text-sm font-medium text-gray-700">Nama Perusahaan <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="company_name" id="company_name" required
                                value="{{ old('company_name') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm py-2.5">
                            @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-3">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="start_date" id="start_date" required value="{{ old('start_date') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm py-2.5">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Berakhir <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="end_date" id="end_date" required value="{{ old('end_date') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm py-2.5">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Section 2: Representative --}}
                <div class="bg-orange-50 rounded-xl p-5 border border-orange-100">
                    <h3 class="text-base font-semibold text-orange-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Perwakilan Perusahaan (Contact Person)
                    </h3>
                    <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-6">
                            <label for="representative_name" class="block text-sm font-medium text-orange-800">Nama
                                Perwakilan
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="representative_name" id="representative_name" required
                                value="{{ old('representative_name') }}"
                                class="mt-1 block w-full rounded-lg border-orange-200 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm py-2.5">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="representative_phone" class="block text-sm font-medium text-blue-800">Nomor
                                Telepon</label>
                            <input type="text" name="representative_phone" id="representative_phone"
                                value="{{ old('representative_phone') }}"
                                class="mt-1 block w-full rounded-lg border-orange-200 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm py-2.5">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="representative_email" class="block text-sm font-medium text-blue-800">Email</label>
                            <input type="email" name="representative_email" id="representative_email"
                                value="{{ old('representative_email') }}"
                                class="mt-1 block w-full rounded-lg border-orange-200 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm py-2.5">
                        </div>
                    </div>
                </div>

                {{-- Section 3: Internal PIC & File --}}
                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Penanggung Jawab Internal
                    </h3>
                    <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="pic_id" class="block text-sm font-medium text-gray-700">Pilih Karyawan (PIC) <span
                                    class="text-red-500">*</span></label>
                            <select name="pic_id" id="pic_id" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm py-2.5">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $e)
                                    <option value="{{ $e->id }}" {{ old('pic_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <label for="file" class="block text-sm font-medium text-gray-700">Dokumen MoU (Opsional)</label>
                            <input type="file" name="file" id="file" accept=".pdf,.doc,.docx"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            <p class="mt-1 text-xs text-gray-500">PDF, DOC, DOCX. Maks 10MB.</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('kerjasama.index') }}"
                        class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-orange-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all">
                        Simpan Kerjasama
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection