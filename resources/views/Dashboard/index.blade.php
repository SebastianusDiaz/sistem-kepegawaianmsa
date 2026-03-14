@extends('layouts.app')

@section('title', 'Dashboard Utama')
@section('page_title', 'Dashboard Overview')

@section('content')

    <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border-l-4 border-blue-600 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-gray-600 mt-1">Anda login sebagai <span class="font-bold uppercase text-blue-600">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>.</p>
        </div>
        <div class="hidden sm:block">
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">{{ now()->format('l, d F Y') }}</span>
        </div>
    </div>

    @if(Auth::user()->hasRole('admin'))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-5 rounded-xl shadow-sm flex items-center border border-gray-100">
            <div class="p-3 bg-indigo-100 text-indigo-600 rounded-lg">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total User</p>
                <p class="text-xl font-bold text-gray-800">124</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm flex items-center border border-gray-100">
            <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                <i class="fas fa-file-contract text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Laporan Masuk</p>
                <p class="text-xl font-bold text-gray-800">45</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm flex items-center border border-gray-100">
            <div class="p-3 bg-red-100 text-red-600 rounded-lg">
                <i class="fas fa-server text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Status Server</p>
                <p class="text-xl font-bold text-gray-800">Online</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-bold text-gray-700 mb-4">Aktivitas Sistem Terbaru</h3>
        <ul class="space-y-3">
            <li class="flex items-center text-sm text-gray-600">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span> User 'Budi' (Wartawan) mengupload berita baru.
            </li>
            <li class="flex items-center text-sm text-gray-600">
                <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span> User 'Siti' (Direktur) menyetujui kerjasama.
            </li>
        </ul>
    </div>

    @elseif(Auth::user()->hasRole('direktur'))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold opacity-90">Total Kerjasama</h3>
            <p class="text-3xl font-bold mt-2">Rp 1.2M</p>
            <p class="text-sm mt-1 opacity-75">+12% dari bulan lalu</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-gray-500 text-sm font-semibold">Kinerja Pegawai (Rata-rata)</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">88.5<span class="text-sm text-green-500 ml-2">▲ Baik</span></p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="font-bold text-gray-700 mb-4">Persetujuan Pending</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span>Proposal Kerjasama PT. ABC</span>
                    <button class="text-xs bg-blue-600 text-white px-3 py-1 rounded">Review</button>
                </div>
            </div>
        </div>
    </div>

    @elseif(Auth::user()->hasRole('wartawan'))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm">Deadline Hari Ini</p>
            <p class="text-2xl font-bold text-gray-800">3 Berita</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Berita Terbit</p>
            <p class="text-2xl font-bold text-gray-800">12</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500">
            <p class="text-gray-500 text-sm">Penugasan Baru</p>
            <p class="text-2xl font-bold text-gray-800">1</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-bold text-gray-700 mb-4">Penugasan Liputan Saya</h3>
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-800 font-semibold">
                <tr>
                    <th class="p-3">Topik</th>
                    <th class="p-3">Lokasi</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="p-3">Peliputan Banjir Kota</td>
                    <td class="p-3">Pusat Kota</td>
                    <td class="p-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Proses</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-gray-500">Absensi Hari Ini</p>
                <p class="text-xl font-bold text-green-600">Hadir (08:05)</p>
            </div>
            <i class="fas fa-fingerprint text-4xl text-gray-200"></i>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-gray-500">Sisa Cuti Tahunan</p>
                <p class="text-xl font-bold text-blue-600">8 Hari</p>
            </div>
            <i class="fas fa-calendar-check text-4xl text-gray-200"></i>
        </div>
    </div>
    @endif

@endsection