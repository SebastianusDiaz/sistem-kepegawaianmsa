@extends('layouts.app')

@section('title', 'Rekap Absensi Karyawan')
@section('page_title', 'Rekap Absensi Karyawan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Rekap Absensi Karyawan</h1>
        <p class="mt-2 text-sm text-gray-500">
            Ringkasan kehadiran karyawan periode {{ $startDate->translatedFormat('F Y') }}.
        </p>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <form action="{{ route('rekap-absensi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="month" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="year" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Karyawan</dt>
            <dd class="mt-1 text-3xl font-bold text-gray-900">{{ $rekapData->count() }}</dd>
        </div>
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5">
            <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Kehadiran</dt>
            <dd class="mt-1 text-3xl font-bold text-green-600">
                @php
                    $avgHadir = $rekapData->count() > 0 ? round($rekapData->avg('hadir'), 1) : 0;
                @endphp
                {{ $avgHadir }} Hari
            </dd>
        </div>
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Jam Kerja</dt>
            <dd class="mt-1 text-3xl font-bold text-orange-600">{{ $rekapData->sum('total_hours') }} Jam</dd>
        </div>
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Alpha</dt>
            <dd class="mt-1 text-3xl font-bold text-red-600">{{ $rekapData->sum('alpha') }}</dd>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sakit</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alpha</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rekapData as $data)
                        @php
                            $percentage = $data->working_days > 0 ? round(($data->hadir / $data->working_days) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ substr($data->user->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $data->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $data->user->profile->position->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                {{ $data->user->profile->division->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-sm font-bold text-green-700 bg-green-100 rounded-full">{{ $data->hadir }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-sm font-medium text-orange-700 bg-orange-100 rounded-full">{{ $data->izin }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-sm font-medium text-pink-700 bg-pink-100 rounded-full">{{ $data->sakit }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-sm font-bold {{ $data->alpha > 0 ? 'text-red-700 bg-red-100' : 'text-gray-600 bg-gray-100' }} rounded-full">{{ $data->alpha }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                {{ $data->total_hours }} Jam
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    <span class="text-sm font-bold {{ $percentage >= 80 ? 'text-green-600' : ($percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $percentage }}%
                                    </span>
                                    <div class="w-16 h-1.5 bg-gray-200 rounded-full ml-2 overflow-hidden">
                                        <div class="h-full {{ $percentage >= 80 ? 'bg-green-500' : ($percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                Tidak ada data karyawan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
