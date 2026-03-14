@extends('layouts.app')

@section('title', 'Laporan Kinerja')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Laporan Kinerja</h1>
            <p class="mt-2 text-sm text-gray-500">
                Laporan aktivitas dan kinerja Wartawan (Penugasan) dan Pegawai (Approval).
            </p>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
            <form action="{{ route('reports.performance') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                </div>

                {{-- Division Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                    <select name="division_id"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            {{-- Total Assignments --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Penugasan</dt>
                <dd class="mt-1 text-3xl font-bold text-gray-900">{{ $reportData->sum('total') }}</dd>
            </div>

            {{-- Completed --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Selesai / Disetujui</dt>
                <dd class="mt-1 text-3xl font-bold text-green-600">{{ $reportData->sum('completed') }}</dd>
            </div>

            {{-- Pending --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Dalam Proses</dt>
                <dd class="mt-1 text-3xl font-bold text-yellow-600">{{ $reportData->sum('pending') }}</dd>
            </div>

            {{-- Avg Rate --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Penyelesaian</dt>
                <dd class="mt-1 text-3xl font-bold text-orange-600">
                    {{ $reportData->count() > 0 ? round($reportData->avg('rate'), 1) : 0 }}%
                </dd>
            </div>
        </div>

        {{-- Detailed Table --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama / Role</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Tugas</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Selesai / Acc</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Proses</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Batal</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reportData as $data)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white font-bold text-sm">
                                                {{ substr($data->user->name, 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $data->user->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $data->role_type == 'Wartawan' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $data->role_type }}
                                                </span>
                                                <span class="text-gray-400 mx-1">|</span>
                                                {{ $data->user->profile->division->name ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                    {{ $data->total }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-green-600">
                                    {{ $data->completed }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-yellow-600">
                                    {{ $data->pending }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-red-600">
                                    {{ $data->canceled }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <span
                                            class="text-sm font-bold {{ $data->rate >= 80 ? 'text-green-600' : ($data->rate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $data->rate }}%
                                        </span>
                                        {{-- Progress Bar --}}
                                        <div class="w-16 h-1.5 bg-gray-200 rounded-full ml-2 overflow-hidden">
                                            <div class="h-full {{ $data->rate >= 80 ? 'bg-green-500' : ($data->rate >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                style="width: {{ $data->rate }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Tidak ada data untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection