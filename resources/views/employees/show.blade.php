@extends('layouts.app')

@section('title', $employee->name . ' - Profil Karyawan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center text-sm font-medium text-gray-500 mb-8">
            <a href="{{ route('employees.index') }}" class="hover:text-indigo-600 transition-colors flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Direktori
            </a>
            <span class="mx-3 text-gray-300">/</span>
            <span class="text-gray-900 font-semibold">{{ $employee->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Profile Card --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Main Identity Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                    {{-- Cover Background --}}
                    <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-700"></div>

                    <div class="px-6 pb-8 text-center relative">
                        {{-- Avatar --}}
                        <div class="-mt-16 mb-4 inline-block relative">
                            <div
                                class="h-32 w-32 rounded-full ring-4 ring-white bg-white shadow-lg flex items-center justify-center overflow-hidden">
                                <div
                                    class="h-full w-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-bold text-4xl select-none">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                            </div>
                            {{-- Status Indicator --}}
                            @if($employee->profile?->is_active ?? true)
                                <div class="absolute bottom-2 right-2 h-5 w-5 bg-green-500 border-4 border-white rounded-full"
                                    title="Active"></div>
                            @else
                                <div class="absolute bottom-2 right-2 h-5 w-5 bg-gray-400 border-4 border-white rounded-full"
                                    title="Inactive"></div>
                            @endif
                        </div>

                        <h1 class="text-xl font-bold text-gray-900">{{ $employee->name }}</h1>
                        <p class="text-indigo-600 font-medium mt-1">{{ $employee->profile?->department ?? 'General Staff' }}
                        </p>

                        <div class="mt-6 flex justify-center gap-2">
                            <a href="mailto:{{ $employee->email }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email
                            </a>
                            @if($employee->profile?->phone)
                                <a href="tel:{{ $employee->profile->phone }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Call
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status Card (Mobile/Sidebar Info) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Informasi Sistem</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">ID Karyawan</span>
                            <span class="text-sm font-mono font-medium text-gray-900">#{{ $employee->id }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">NIP</span>
                            <span class="text-sm font-medium text-gray-900">{{ $employee->profile?->nip ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Bergabung Sejak</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ $employee->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm text-gray-600">Role Sistem</span>
                            <div class="flex gap-1">
                                @foreach($employee->roles as $role)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 uppercase">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Detailed Info --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Contact Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Detail Kontak & Alamat</h3>
                    </div>
                    <div class="px-6 py-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 flex items-center mb-2">
                                    <div class="p-1.5 bg-indigo-50 rounded-lg mr-2 text-indigo-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    Email Resmi
                                </dt>
                                <dd class="text-base text-gray-900 pl-9">{{ $employee->email }}</dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 flex items-center mb-2">
                                    <div class="p-1.5 bg-green-50 rounded-lg mr-2 text-green-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    Nomor Handphone
                                </dt>
                                <dd class="text-base text-gray-900 pl-9">{{ $employee->profile?->phone ?? '-' }}</dd>
                            </div>

                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 flex items-center mb-2">
                                    <div class="p-1.5 bg-red-50 rounded-lg mr-2 text-red-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    Alamat Domisili
                                </dt>
                                <dd class="text-base text-gray-900 pl-9 leading-relaxed">
                                    {{ $employee->profile?->address ?? 'Alamat belum dilengkapi.' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Activity Section (Future Proofing) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Aktivitas Terakhir</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center p-4 bg-gray-50 rounded-full mb-4">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500">Belum ada aktivitas tercatat hari ini.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection