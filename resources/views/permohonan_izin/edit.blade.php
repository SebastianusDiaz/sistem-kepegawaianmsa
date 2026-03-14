@extends('layouts.app')

@section('title', 'Edit Permohonan')
@section('page_title', 'Edit Permohonan Cuti / Izin')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-lg rounded-xl p-6 max-w-2xl mx-auto">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Edit Permohonan #{{ $permohonanIzin->id }}
            </h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('permohonan-izin.update', $permohonanIzin) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="jenis_izin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Permohonan</label>
                    <select name="jenis_izin" id="jenis_izin" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                        <option value="cuti" {{ old('jenis_izin', $permohonanIzin->jenis_izin) == 'cuti' ? 'selected' : '' }}>
                            Cuti</option>
                        <option value="izin" {{ old('jenis_izin', $permohonanIzin->jenis_izin) == 'izin' ? 'selected' : '' }}>
                            Izin</option>
                        <option value="sakit" {{ old('jenis_izin', $permohonanIzin->jenis_izin) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                            Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" required
                            value="{{ old('tanggal_mulai', $permohonanIzin->tanggal_mulai->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                            Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" required
                            value="{{ old('tanggal_selesai', $permohonanIzin->tanggal_selesai->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="alasan" class="block text-sm font-medium text-gray-700 mb-1">Alasan / Keterangan</label>
                    <textarea name="alasan" id="alasan" rows="4" required minlength="10"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5"
                        placeholder="Jelaskan alasan pengajuan cuti atau izin Anda...">{{ old('alasan', $permohonanIzin->alasan) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter.</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('permohonan-izin.show', $permohonanIzin) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection