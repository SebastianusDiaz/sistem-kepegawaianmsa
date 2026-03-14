@extends('layouts.app')

@section('title', 'Direktori Karyawan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Modern Header & Stats --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Karyawan & Tim</h1>
            <p class="mt-2 text-gray-500">Kelola dan pantau data seluruh personil perusahaan.</p>
            
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Stat Card 1 --}}
                <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-100 p-5 flex items-center">
                    <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                        <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Karyawan</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $totalEmployees }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Stat Card 2 --}}
                <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-100 p-5 flex items-center">
                    <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Aktif Bekerja</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $activeEmployees }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toolbar (Search & Filter) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-8">
            <form action="{{ route('employees.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative flex-1 max-w-lg">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-shadow"
                        placeholder="Cari berdasarkan nama, email, atau NIP...">
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select name="department" onchange="this.form.submit()" 
                            class="appearance-none block w-full pl-3 pr-10 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm cursor-pointer">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    
                    {{-- Reset Filter Button --}}
                    @if(request()->has('search') || request()->has('department'))
                        <a href="{{ route('employees.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Employee Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Karyawan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Divisi & NIP
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kontak
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($employees as $employee)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">
                                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $employee->roles->pluck('name')->join(', ') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $employee->profile?->department ?? 'General' }}</div>
                                    @if($employee->profile?->nip)
                                        <div class="text-xs text-gray-500">NIP: {{ $employee->profile->nip }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $employee->email }}</div>
                                    @if($employee->profile?->phone)
                                        <div class="text-sm text-gray-500">{{ $employee->profile->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($employee->profile?->is_active ?? true)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('employees.show', $employee->id) }}" class="text-orange-600 hover:text-orange-900 font-medium">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada karyawan ditemukan</h3>
                                    <p class="mt-1 text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($employees->hasPages())
            <div class="mt-8">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
@endsection