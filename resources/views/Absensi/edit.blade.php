@extends('layouts.app')

@section('title', 'Edit Absensi')
@section('page_title', 'Form Edit Absensi Pegawai')

@section('content')
<div class="p-6">
    <div class="bg-white shadow-lg rounded-xl p-6 max-w-2xl mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Edit Data Absensi</h2>
        
        <form method="POST" action="{{ route('absensi.update', $absensi->id) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pegawai</label>
                <p class="w-full bg-gray-100 border border-gray-300 rounded-lg p-2.5 text-gray-600">
                    {{ $absensi->user->name }}
                </p>
            </div>
            
            <div class="mb-4">
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ $absensi->tanggal }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
            </div>
            
            <div class="mb-4">
                <label for="jam_masuk" class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                <input type="time" name="jam_masuk" id="jam_masuk" value="{{ $absensi->jam_masuk }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
            </div>

            <div class="mb-4">
                <label for="jam_keluar" class="block text-sm font-medium text-gray-700 mb-1">Jam Keluar</label>
                <input type="time" name="jam_keluar" id="jam_keluar" value="{{ $absensi->jam_keluar }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Kehadiran</label>
                <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                    @foreach(['hadir','izin','sakit','alpha'] as $s)
                        <option value="{{ $s }}"
                            @selected($absensi->status == $s)>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-6">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5">{{ $absensi->keterangan }}</textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11.166 6h-4.664m-4.664 0H5m4.664 0h4.664m4.664 0A8.001 8.001 0 004.834 15m0 0v-5"></path></svg>
                    Update Data Absensi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection