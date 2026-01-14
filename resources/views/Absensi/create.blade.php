@extends('layouts.app')

@section('title', 'Tambah Absensi')
@section('page_title', 'Form Tambah Absensi Pegawai')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-lg rounded-xl p-6 max-w-2xl mx-auto">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Tambah Data Absensi Baru (Admin)</h2>

            <form method="POST" action="{{ route('absensi.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pegawai</label>
                        <select name="user_id" id="user_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="attendance_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe
                            Absensi</label>
                        <select name="attendance_type" id="attendance_type"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                            <option value="office">Office</option>
                            <option value="field">Field</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4 hidden" id="assignment_wrapper">
                    <label for="assignment_id" class="block text-sm font-medium text-gray-700 mb-1">Surat Tugas (Field
                        Only)</label>
                    <select name="assignment_id" id="assignment_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                        <option value="">-- Pilih Assignment --</option>
                        @foreach($assignments as $assignment)
                            <option value="{{ $assignment->id }}">{{ $assignment->title }} ({{ $assignment->letter_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="jam_masuk" class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="jam_masuk"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    </div>
                    <div>
                        <label for="jam_keluar" class="block text-sm font-medium text-gray-700 mb-1">Jam Keluar</label>
                        <input type="time" name="jam_keluar" id="jam_keluar"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Legacy Status (Untuk Laporan
                        Lama)</label>
                    <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpha">Alpha</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Admin Note / Keterangan</label>
                    <textarea name="note" id="note" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5"
                        placeholder="Catatan manual entry..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('absensi.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Simpan Manual
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const typeSelect = document.getElementById('attendance_type');
        const assignWrapper = document.getElementById('assignment_wrapper');

        typeSelect.addEventListener('change', function () {
            if (this.value === 'field') {
                assignWrapper.classList.remove('hidden');
            } else {
                assignWrapper.classList.add('hidden');
            }
        });
    </script>
@endsection