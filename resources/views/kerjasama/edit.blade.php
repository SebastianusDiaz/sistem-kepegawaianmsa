@extends('layouts.app')

@section('title', 'Edit Kerjasama')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="mb-6">
            <a href="{{ route('kerjasama.show', $kerjasama) }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Detail
            </a>
        </nav>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white">
                <h2 class="text-xl font-bold text-gray-900">Edit Kerjasama</h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi perjanjian kerjasama.</p>
            </div>

            <form action="{{ route('kerjasama.update', $kerjasama) }}" method="POST" enctype="multipart/form-data"
                class="p-6 md:p-8 space-y-8">
                @csrf
                @method('PUT')

                {{-- Section 1: Company Info --}}
                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Informasi Perusahaan</h3>
                    <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-6">
                            <label for="company_name" class="block text-sm font-medium text-gray-700">Nama Perusahaan <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="company_name" id="company_name" required
                                value="{{ old('company_name', $kerjasama->company_name) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-2.5">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="start_date" id="start_date" required
                                value="{{ old('start_date', $kerjasama->start_date->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-2.5">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Berakhir <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="end_date" id="end_date" required
                                value="{{ old('end_date', $kerjasama->end_date->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-2.5">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Section 2: Representative --}}
                <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                    <h3 class="text-base font-semibold text-blue-900 mb-4">Perwakilan Perusahaan</h3>
                    <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-6">
                            <label for="representative_name" class="block text-sm font-medium text-blue-800">Nama Perwakilan
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="representative_name" id="representative_name" required
                                value="{{ old('representative_name', $kerjasama->representative_name) }}"
                                class="mt-1 block w-full rounded-lg border-blue-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-2.5">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="representative_phone" class="block text-sm font-medium text-blue-800">Nomor
                                Telepon</label>
                            <input type="text" name="representative_phone" id="representative_phone"
                                value="{{ old('representative_phone', $kerjasama->representative_phone) }}"
                                class="mt-1 block w-full rounded-lg border-blue-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-2.5">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="representative_email" class="block text-sm font-medium text-blue-800">Email</label>
                            <input type="email" name="representative_email" id="representative_email"
                                value="{{ old('representative_email', $kerjasama->representative_email) }}"
                                class="mt-1 block w-full rounded-lg border-blue-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-2.5">
                        </div>
                    </div>
                </div>

                {{-- Section 3: Internal PIC & File --}}
                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Penanggung Jawab Internal</h3>
                    <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="pic_id" class="block text-sm font-medium text-gray-700">Pilih Karyawan (PIC) <span
                                    class="text-red-500">*</span></label>
                            <select name="pic_id" id="pic_id" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-2.5">
                                @foreach($employees as $e)
                                    <option value="{{ $e->id }}" {{ old('pic_id', $kerjasama->pic_id) == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <label for="file" class="block text-sm font-medium text-gray-700">Dokumen MoU (Opsional)</label>
                            <input type="file" name="file" id="file" accept=".pdf,.doc,.docx"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @if($kerjasama->file_path)
                                <p class="mt-1 text-xs text-green-600">Dokumen sudah ada. Upload baru untuk mengganti.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('kerjasama.show', $kerjasama) }}"
                        class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection