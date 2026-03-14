@extends('layouts.app')

@section('title', 'Kerjasama')
@section('page_title', 'Manajemen Kerjasama')

@section('content')
    <div class="p-6 space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Daftar Kerjasama</h2>
                <p class="text-sm text-gray-500">Kelola perjanjian kerjasama (MoU) dengan perusahaan mitra.</p>
            </div>
            <a href="{{ route('kerjasama.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Kerjasama Baru
            </a>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('kerjasama.index') }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ !request('status') ? 'bg-orange-100 text-orange-700' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('kerjasama.index', ['status' => 'pending']) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request('status') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                Menunggu Persetujuan
            </a>
            <a href="{{ route('kerjasama.index', ['status' => 'active']) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request('status') === 'active' ? 'bg-green-100 text-green-700' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                Aktif
            </a>
            <a href="{{ route('kerjasama.index', ['status' => 'rejected']) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request('status') === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                Ditolak
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table Card --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                {{-- REMOVED @role('direktur') around columns to allow others to see and manage --}}
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Perusahaan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PIC
                                Internal</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kerjasamas as $k)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $k->company_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $k->representative_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $k->start_date->format('d M Y') }} - {{ $k->end_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $k->pic->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'active' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'expired' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusColors[$k->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($k->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <a href="{{ route('kerjasama.show', $k) }}"
                                        class="text-orange-600 hover:text-orange-900">Detail</a>
                                    @if(Auth::user()->hasRole('direktur') || Auth::id() == $k->created_by)
                                        <a href="{{ route('kerjasama.edit', $k) }}"
                                            class="text-gray-500 hover:text-gray-700">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada data kerjasama.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($kerjasamas->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $kerjasamas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection