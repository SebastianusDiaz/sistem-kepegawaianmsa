@extends('layouts.app')

@section('title', 'Cuti & Izin')
@section('page_title', 'Permohonan Cuti & Izin')

@section('content')
    <div class="p-6 space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Daftar Permohonan</h3>
                <a href="{{ route('permohonan-izin.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Permohonan
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-orange-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">
                                Pemohon</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">Jenis
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">
                                Durasi</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-orange-800 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($permohonanIzins as $izin)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold text-sm">
                                            {{ substr($izin->user->name, 0, 2) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $izin->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $izin->created_at->format('d M Y, H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $jenis_colors = [
                                            'cuti' => 'bg-purple-100 text-purple-800',
                                            'izin' => 'bg-orange-100 text-orange-800',
                                            'sakit' => 'bg-pink-100 text-pink-800',
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $jenis_colors[$izin->jenis_izin] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($izin->jenis_izin) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $izin->tanggal_mulai->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">s/d {{ $izin->tanggal_selesai->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $izin->jumlah_hari }} Hari
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status_colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'approved' => 'bg-green-100 text-green-800 border-green-200',
                                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $status_colors[$izin->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ strtoupper($izin->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-medium">
                                    <a href="{{ route('permohonan-izin.show', $izin) }}"
                                        class="text-orange-600 hover:text-orange-900">Detail</a>
                                    @if($izin->status === 'pending' && ($izin->user_id === Auth::id() || Auth::user()->hasRole('admin')))
                                        <a href="{{ route('permohonan-izin.edit', $izin) }}"
                                            class="ml-3 text-yellow-600 hover:text-yellow-900">Edit</a>
                                        <form action="{{ route('permohonan-izin.destroy', $izin) }}" method="POST"
                                            class="inline ml-3">
                                            @csrf @method('DELETE')
                                            <button type="submit" data-confirm="Yakin ingin menghapus permohonan ini?"
                                                class="text-red-600 hover:text-red-900 bg-transparent border-0 cursor-pointer">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Belum ada permohonan cuti atau izin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection