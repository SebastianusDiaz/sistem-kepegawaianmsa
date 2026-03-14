@extends('layouts.app')

@section('title', 'Detail Permohonan')
@section('page_title', 'Detail Permohonan Cuti / Izin')

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

        <div class="bg-white shadow-lg rounded-xl overflow-hidden max-w-3xl mx-auto">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Detail Permohonan #{{ $permohonanIzin->id }}</h3>
                @php
                    $status_colors = [
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                @endphp
                <span
                    class="px-4 py-1.5 text-sm font-bold rounded-full border {{ $status_colors[$permohonanIzin->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ strtoupper($permohonanIzin->status) }}
                </span>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Pemohon</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $permohonanIzin->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Jenis Permohonan</p>
                        @php
                            $jenis_colors = [
                                'cuti' => 'bg-purple-100 text-purple-800',
                                'izin' => 'bg-orange-100 text-orange-800',
                                'sakit' => 'bg-pink-100 text-pink-800',
                            ];
                        @endphp
                        <span
                            class="mt-1 px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $jenis_colors[$permohonanIzin->jenis_izin] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($permohonanIzin->jenis_izin) }}
                        </span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Tanggal Mulai</p>
                        <p class="text-base font-medium text-gray-800">{{ $permohonanIzin->tanggal_mulai->format('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Tanggal Selesai</p>
                        <p class="text-base font-medium text-gray-800">
                            {{ $permohonanIzin->tanggal_selesai->format('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Durasi</p>
                        <p class="text-base font-medium text-gray-800">{{ $permohonanIzin->jumlah_hari }} Hari</p>
                    </div>
                </div>

                <hr class="my-4">

                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Alasan / Keterangan</p>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 text-sm leading-relaxed">
                        {{ $permohonanIzin->alasan }}
                    </div>
                </div>

                @if($permohonanIzin->status === 'approved' && $permohonanIzin->approver)
                    <hr class="my-4">
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-xs text-green-600 uppercase tracking-wider mb-1 font-semibold">Disetujui Oleh</p>
                        <p class="text-base font-semibold text-green-800">{{ $permohonanIzin->approver->name }}</p>
                    </div>
                @endif

                @if($permohonanIzin->status === 'rejected')
                    <hr class="my-4">
                    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-xs text-red-600 uppercase tracking-wider mb-1 font-semibold">Ditolak Oleh</p>
                        <p class="text-base font-semibold text-red-800">{{ $permohonanIzin->approver->name ?? 'N/A' }}</p>
                        @if($permohonanIzin->alasan_penolakan)
                            <p class="mt-2 text-sm text-red-700">
                                <strong>Alasan Penolakan:</strong> {{ $permohonanIzin->alasan_penolakan }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Director Approval Section --}}
            @if($permohonanIzin->status === 'pending' && Auth::user()->hasRole('direktur'))
                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <h4 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4">Aksi Direktur</h4>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <form action="{{ route('permohonan-izin.approve', $permohonanIzin) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                Setujui Permohonan
                            </button>
                        </form>

                        <form action="{{ route('permohonan-izin.reject', $permohonanIzin) }}" method="POST" class="flex-1"
                            id="reject-form">
                            @csrf
                            <div class="mb-2">
                                <textarea name="alasan_penolakan" rows="2" required minlength="10"
                                    placeholder="Alasan penolakan (wajib diisi)..."
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2.5"></textarea>
                            </div>
                            <button type="submit"
                                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak Permohonan
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="px-6 py-4 bg-gray-100 border-t border-gray-200 flex justify-between items-center">
                <a href="{{ route('permohonan-izin.index') }}"
                    class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar
                </a>
                @if($permohonanIzin->status === 'pending' && ($permohonanIzin->user_id === Auth::id() || Auth::user()->hasRole('admin')))
                    <a href="{{ route('permohonan-izin.edit', $permohonanIzin) }}"
                        class="text-sm text-orange-600 hover:text-orange-900">Edit Permohonan</a>
                @endif
            </div>
        </div>
    </div>
@endsection