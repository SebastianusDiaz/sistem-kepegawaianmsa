@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Profil Saya
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi akun dan data kepegawaian Anda.
                </p>
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
            {{-- Profile Header --}}
            <div class="px-6 py-8 bg-gradient-to-r from-indigo-600 to-indigo-700 sm:px-8">
                {{-- Success Message --}}
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex items-center space-x-5">
                    {{-- Photo with Edit Overlay --}}
                    <div class="flex-shrink-0 relative group" x-data="{ showModal: false }">
                        @php
                            $photoUrl = $user->photo
                                ? asset('storage/' . $user->photo)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=128';
                        @endphp
                        <img class="h-24 w-24 rounded-full ring-4 ring-white shadow-lg object-cover" src="{{ $photoUrl }}"
                            alt="{{ $user->name }}">

                        {{-- Edit Button Overlay --}}
                        <button type="button" @click="showModal = true"
                            class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>

                        {{-- Upload Modal --}}
                        <div x-show="showModal" x-cloak @click.away="showModal = false"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4" @click.stop>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ganti Foto Profil</h3>
                                <form action="{{ route('profile.updatePhoto') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Foto Baru</label>
                                        <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" required
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <p class="mt-1 text-xs text-gray-500">JPG, JPEG, atau PNG. Maksimal 2MB.</p>
                                        @error('photo')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" @click="showModal = false"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                            Upload
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="text-white">
                        <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                        <p class="text-indigo-100">{{ $user->email }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($user->getRoleNames() as $role)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white">
                                    {{ ucfirst($role) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Details --}}
            <div class="px-6 py-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    Data Kepegawaian
                </h3>

                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    {{-- NIP --}}
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Nomor Induk Pegawai (NIP)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->profile->nip ?? '-' }}</dd>
                    </div>

                    {{-- Phone --}}
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Nomor Handphone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->profile->phone ?? '-' }}</dd>
                    </div>

                    {{-- Gender & Birth Info --}}
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $user->profile->gender === 'male' ? 'Laki-laki' : ($user->profile->gender === 'female' ? 'Perempuan' : '-') }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Tempat, Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $user->profile->birth_place ?? '-' }},
                            {{ $user->profile->birth_date ? \Carbon\Carbon::parse($user->profile->birth_date)->translatedFormat('d F Y') : '-' }}
                        </dd>
                    </div>

                    {{-- Division --}}
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Divisi</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->profile->division->name ?? '-' }}</dd>
                    </div>

                    {{-- Position --}}
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Jabatan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->profile->position->name ?? '-' }}</dd>
                    </div>

                    {{-- Address --}}
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->profile->address ?? '-' }}</dd>
                    </div>

                    {{-- Account Status --}}
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Status Akun</dt>
                        <dd class="mt-1">
                            @if($user->is_active)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Non-Aktif
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Edit Profile Form --}}
        <div class="mt-8 bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
            <div class="px-6 py-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profil
                </h3>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                        {{-- Name --}}
                        <div class="sm:col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        {{-- Email --}}
                        <div class="sm:col-span-1">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        {{-- Phone --}}
                        <div class="sm:col-span-1">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Handphone</label>
                            <input type="text" name="phone" id="phone"
                                value="{{ old('phone', $user->profile->phone ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        {{-- Birth Place --}}
                        <div class="sm:col-span-1">
                            <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <input type="text" name="birth_place" id="birth_place"
                                value="{{ old('birth_place', $user->profile->birth_place ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        {{-- Birth Date --}}
                        <div class="sm:col-span-1">
                            <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="birth_date"
                                value="{{ old('birth_date', $user->profile->birth_date ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        {{-- Gender --}}
                        <div class="sm:col-span-1">
                            <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select name="gender" id="gender"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">-- Pilih --</option>
                                <option value="male" {{ (old('gender', $user->profile->gender ?? '') == 'male') ? 'selected' : '' }}>Laki-laki</option>
                                <option value="female" {{ (old('gender', $user->profile->gender ?? '') == 'female') ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        {{-- Signature --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan Digital</label>

                            @if($user->profile && $user->profile->signature_path)
                                <div class="mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Tanda tangan saat ini:</p>
                                    <img src="{{ asset('storage/' . $user->profile->signature_path) }}" alt="Signature"
                                        class="h-20 border border-gray-200 rounded bg-white">
                                </div>
                            @endif

                            <div class="flex items-center justify-center w-full">
                                <label for="signature"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk
                                                upload</span> tanda tangan (transparan lebih baik)</p>
                                        <p class="text-xs text-gray-500">PNG / JPG (Max. 2MB)</p>
                                    </div>
                                    <input id="signature" name="signature" type="file" class="hidden"
                                        accept="image/png, image/jpeg" />
                                </label>
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea name="address" id="address" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('address', $user->profile->address ?? '') }}</textarea>
                        </div>

                        <div class="sm:col-span-2 border-t border-gray-100 pt-6 mt-2">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Ubah Password (Opsional)</h4>
                        </div>

                        {{-- Password --}}
                        <div class="sm:col-span-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>

                        {{-- Confirm Password --}}
                        <div class="sm:col-span-1">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi
                                Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
@endsection