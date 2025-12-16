@extends('layouts.app')

@section('title', 'Tambah Absensi')
@section('page_title', 'Form Tambah Absensi Pegawai')

@section('content')
<div class="p-6">
    <div class="bg-white shadow-lg rounded-xl p-6 max-w-2xl mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Tambah Data Absensi Baru</h2>
        
        <form method="POST" action="{{ route('absensi.store') }}">
            @csrf
            
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pegawai</label>
                <select name="user_id" id="user_id" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                {{-- Tambahkan error handling jika diperlukan --}}
            </div>

            <div class="mb-4">
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
            </div>
            
            <div class="mb-4">
                <label for="jam_masuk" class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                <input type="time" name="jam_masuk" id="jam_masuk"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
            </div>

            <div class="mb-4">
                <label for="jam_keluar" class="block text-sm font-medium text-gray-700 mb-1">Jam Keluar</label>
                <input type="time" name="jam_keluar" id="jam_keluar"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Kehadiran</label>
                <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan (opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5"
                       placeholder="Masukkan keterangan (misalnya alasan izin/sakit)"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Simpan Data Absensi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection