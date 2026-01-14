@extends('layouts.app')

@section('title', 'Detail Kerjasama')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="mb-6">
            <a href="{{ route('kerjasama.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </nav>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Header Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div
                        class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold">{{ $kerjasama->company_name }}</h1>
                                <p class="text-indigo-100 text-sm mt-1">Kerjasama (MoU)</p>
                            </div>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-400 text-yellow-900',
                                    'active' => 'bg-green-400 text-green-900',
                                    'rejected' => 'bg-red-400 text-white',
                                    'expired' => 'bg-gray-400 text-gray-900',
                                ];
                            @endphp
                            <span
                                class="px-4 py-1.5 text-sm font-bold rounded-full {{ $statusColors[$kerjasama->status] ?? 'bg-gray-400' }}">
                                {{ ucfirst($kerjasama->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Periode --}}
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">{{ $kerjasama->start_date->format('d M Y') }} -
                                {{ $kerjasama->end_date->format('d M Y') }}</span>
                        </div>

                        {{-- Representative Box --}}
                        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                            <h4 class="text-sm font-semibold text-blue-900 mb-3">Perwakilan Perusahaan</h4>
                            <p class="text-sm text-blue-800 font-medium">{{ $kerjasama->representative_name }}</p>
                            @if($kerjasama->representative_phone)
                                <p class="text-xs text-blue-600 mt-1">📞 {{ $kerjasama->representative_phone }}</p>
                            @endif
                            @if($kerjasama->representative_email)
                                <p class="text-xs text-blue-600">✉️ {{ $kerjasama->representative_email }}</p>
                            @endif
                        </div>

                        {{-- PIC Internal --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Penanggung Jawab
                                Internal</h4>
                            <p class="text-gray-900 font-medium">{{ $kerjasama->pic->name ?? '-' }}</p>
                        </div>

                        {{-- File --}}
                        @if($kerjasama->file_path)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Dokumen MoU</h4>
                                <a href="{{ asset('storage/' . $kerjasama->file_path) }}" target="_blank"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Dokumen
                                </a>
                            </div>
                        @endif

                        {{-- Rejection Note --}}
                        @if($kerjasama->status === 'rejected' && $kerjasama->rejection_note)
                            <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                                <h4 class="text-sm font-semibold text-red-800 mb-2">Alasan Penolakan</h4>
                                <p class="text-sm text-red-700">{{ $kerjasama->rejection_note }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Assignments Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Daftar Penugasan Terkait</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reporter
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($kerjasama->assignments as $a)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $a->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $a->reporter->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-2 py-1 text-xs font-bold rounded-full bg-indigo-100 text-indigo-800">{{ ucfirst($a->status) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('assignments.show', $a) }}"
                                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Lihat</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada penugasan untuk
                                            kerjasama ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Actions Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Aksi</h3>

                    <a href="{{ route('kerjasama.edit', $kerjasama) }}"
                        class="w-full flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Kerjasama
                    </a>

                    @role('direktur')
                    @if($kerjasama->status === 'pending')
                        <form action="{{ route('kerjasama.approve', $kerjasama) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Setujui Kerjasama
                            </button>
                        </form>

                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true"
                                class="w-full flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tolak Kerjasama
                            </button>

                            {{-- Reject Modal --}}
                            <div x-show="open" x-cloak @click.away="open = false"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4" @click.stop>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Kerjasama</h3>
                                    <form action="{{ route('kerjasama.reject', $kerjasama) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="rejection_note"
                                                class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                                            <textarea name="rejection_note" id="rejection_note" required rows="3"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                                        </div>
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" @click="open = false"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                                            <button type="submit"
                                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                    @endrole
                </div>

                {{-- Approval Info --}}
                @if($kerjasama->approver)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Informasi Persetujuan</h4>
                        <p class="text-sm text-gray-700"><span class="font-medium">Oleh:</span> {{ $kerjasama->approver->name }}
                        </p>
                        <p class="text-sm text-gray-700"><span class="font-medium">Pada:</span>
                            {{ $kerjasama->approved_at->format('d M Y, H:i') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection