@extends('layouts.app')

@section('title', $employee->name . ' - Profil Karyawan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center text-sm font-medium text-gray-500 mb-8">
            <a href="{{ route('employees.index') }}" class="hover:text-orange-600 transition-colors flex items-center">
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
                    <div class="h-32 bg-gradient-to-r from-orange-500 to-red-600"></div>

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
                        <p class="text-orange-600 font-medium mt-1">{{ $employee->profile?->department ?? 'General Staff' }}
                        </p>

                        <div class="mt-6 flex justify-center gap-2">
                            <a href="mailto:{{ $employee->email }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email
                            </a>
                            @if($employee->profile?->phone)
                                <a href="tel:{{ $employee->profile->phone }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
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

                {{-- Personal Info Section --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Informasi Pribadi</h3>
                    </div>
                    <div class="px-6 py-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 flex items-center mb-2">
                                    <div class="p-1.5 bg-blue-50 rounded-lg mr-2 text-blue-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    Jenis Kelamin
                                </dt>
                                <dd class="text-base text-gray-900 pl-9">
                                    {{ $employee->profile?->gender === 'male' ? 'Laki-laki' : ($employee->profile?->gender === 'female' ? 'Perempuan' : '-') }}
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 flex items-center mb-2">
                                    <div class="p-1.5 bg-purple-50 rounded-lg mr-2 text-purple-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
                                        </svg>
                                    </div>
                                    Tempat, Tanggal Lahir
                                </dt>
                                <dd class="text-base text-gray-900 pl-9">
                                    {{ $employee->profile?->birth_place ?? '-' }},
                                    {{ $employee->profile?->birth_date ? \Carbon\Carbon::parse($employee->profile->birth_date)->translatedFormat('d F Y') : '-' }}
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 flex items-center mb-2">
                                    <div class="p-1.5 bg-amber-50 rounded-lg mr-2 text-amber-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    Divisi / Departemen
                                </dt>
                                <dd class="text-base text-gray-900 pl-9">
                                    {{ $employee->profile?->division->name ?? ($employee->profile?->department ?? '-') }}
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 flex items-center mb-2">
                                    <div class="p-1.5 bg-teal-50 rounded-lg mr-2 text-teal-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    Jabatan
                                </dt>
                                <dd class="text-base text-gray-900 pl-9">
                                    {{ $employee->profile?->position->name ?? '-' }}
                                </dd>
                            </div>

                        </dl>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection