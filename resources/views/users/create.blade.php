@extends('layouts.app')

@section('title', 'Tambah User Baru')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb Navigation --}}
        <nav class="flex items-center text-sm font-medium text-gray-500 mb-6">
            <a href="{{ route('users.index') }}" class="hover:text-gray-700 transition-colors">Users</a>
            <svg class="h-4 w-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-900">Tambah Baru</span>
        </nav>

        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Buat Akun Pengguna
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Isi formulir berikut untuk mendaftarkan user baru ke dalam sistem.
                </p>
            </div>
        </div>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                
                {{-- CARD 1: INFORMASI AKUN & ROLE (UI BARU) --}}
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Kredensial & Hak Akses
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Informasi login dan tingkatan akses pengguna.</p>
                    </div>

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            
                            {{-- Nama --}}
                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <div class="mt-1">
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5"
                                        placeholder="Contoh: Budi Santoso">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                                <div class="mt-1">
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5"
                                        placeholder="budi@mediasriwijaya.com">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="sm:col-span-3">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <div class="mt-1 relative">
                                    <input type="password" name="password" id="password" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="sm:col-span-3">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                <div class="mt-1">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5">
                                </div>
                            </div>

                            {{-- SECTION ROLE (UI BARU: SELECTABLE CARDS) --}}
                            <div class="sm:col-span-6 mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Role / Hak Akses</label>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    @foreach($roles as $role)
                                        @php
                                            // Logic Sederhana untuk Ikon & Warna berdasarkan nama role
                                            $icon = match(strtolower($role)) {
                                                'admin' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
                                                'wartawan' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />',
                                                'direktur' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                                                default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                                            };
                                            
                                            $isChecked = collect(old('roles'))->contains($role);
                                        @endphp

                                        <label class="relative cursor-pointer group">
                                            {{-- Input Checkbox (Hidden) --}}
                                            <input type="checkbox" name="roles[]" value="{{ $role }}" class="peer sr-only" {{ $isChecked ? 'checked' : '' }}>
                                            
                                            {{-- Card Design --}}
                                            <div class="h-full p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 transition-all duration-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:shadow-sm">
                                                <div class="flex flex-col items-center text-center space-y-2">
                                                    {{-- Icon Wrapper --}}
                                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 peer-checked:bg-indigo-200 peer-checked:text-indigo-700 transition-colors">
                                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            {!! $icon !!}
                                                        </svg>
                                                    </div>
                                                    
                                                    {{-- Role Name --}}
                                                    <span class="font-bold text-gray-700 peer-checked:text-indigo-900 capitalize block">
                                                        {{ ucfirst($role) }}
                                                    </span>
                                                </div>

                                                {{-- Checkmark Icon (Absolute Top Right) --}}
                                                <div class="absolute top-3 right-3 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('roles')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Klik pada kartu untuk memilih role (bisa pilih lebih dari satu).</p>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- CARD 2: DATA KEPEGAWAIAN (PROFILE) --}}
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            Data Profil & Kepegawaian
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Detail tambahan untuk keperluan administrasi.</p>
                    </div>

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">

                            {{-- Division --}}
                            <div class="sm:col-span-3">
                                <label for="division_id" class="block text-sm font-medium text-gray-700">Divisi / Departemen</label>
                                <div class="mt-1">
                                    <select name="division_id" id="division_id" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5">
                                        <option value="">-- Pilih Divisi --</option>
                                        @foreach($divisions as $div)
                                            <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                                                {{ $div->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('division_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Position --}}
                            <div class="sm:col-span-3">
                                <label for="position_id" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <div class="mt-1">
                                    <select name="position_id" id="position_id" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5">
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach($positions as $pos)
                                            <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                                {{ $pos->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- NIP --}}
                            <div class="sm:col-span-3">
                                <label for="nip" class="block text-sm font-medium text-gray-700">Nomor Induk Pegawai (NIP)</label>
                                <div class="mt-1">
                                    <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5"
                                        placeholder="Contoh: 2024001">
                                    @error('nip')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="sm:col-span-3">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Handphone</label>
                                <div class="mt-1">
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5"
                                        placeholder="0812...">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="sm:col-span-6">
                                <label for="address" class="block text-sm font-medium text-gray-700">Alamat Domisili</label>
                                <div class="mt-1">
                                    <textarea id="address" name="address" rows="3"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                        placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-x-3 pt-4">
                    <a href="{{ route('users.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all transform hover:-translate-y-0.5">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Data User
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection