@extends('layouts.app')

@section('title', 'Ajukan Cuti / Izin')
@section('page_title', 'Form Pengajuan Cuti / Izin')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-lg rounded-xl p-6 max-w-2xl mx-auto">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Ajukan Permohonan Baru</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('permohonan-izin.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="jenis_izin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Permohonan</label>
                    <select name="jenis_izin" id="jenis_izin" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="cuti" {{ old('jenis_izin') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="izin" {{ old('jenis_izin') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ old('jenis_izin') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                            Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" required
                            value="{{ old('tanggal_mulai') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                            Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" required
                            value="{{ old('tanggal_selesai') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="alasan" class="block text-sm font-medium text-gray-700 mb-1">Alasan / Keterangan</label>
                    <textarea name="alasan" id="alasan" rows="4" required minlength="10"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5"
                        placeholder="Jelaskan alasan pengajuan cuti atau izin Anda...">{{ old('alasan') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter.</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('permohonan-izin.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ajukan Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection