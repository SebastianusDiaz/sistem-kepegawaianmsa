@extends('layouts.app')

@section('title', 'Dashboard Utama')
@section('page_title', 'Dashboard Overview')

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Welcome Board --}}
    <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border-l-4 border-orange-600 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-gray-600 mt-1">Anda login sebagai <span
                    class="font-bold uppercase text-orange-600">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>.
            </p>
        </div>
        <div class="hidden sm:block">
            <span
                class="bg-orange-100 text-orange-800 text-xs font-medium px-3 py-1 rounded-full">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    {{-- SHORTCUTS / QUICK ACTIONS --}}
    <div class="mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-500"></i> Akses Cepat
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">

            {{-- Kepegawaian --}}
            @if(Auth::user()->hasAnyRole(['admin', 'hrd', 'direktur']))
            <a href="{{ route('employees.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-id-card"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Data Karyawan</span>
            </a>
            @endif

            {{-- Operasional Harian --}}
            @if(Auth::user()->hasAnyRole(['admin', 'pegawai', 'wartawan']))
            <a href="{{ route('absensi.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Absensi</span>
            </a>
            @endif

            @if(Auth::user()->hasAnyRole(['direktur', 'pegawai', 'admin', 'wartawan']))
            <a href="{{ route('permohonan-izin.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Cuti & Izin</span>
            </a>
            @endif

            @if(Auth::user()->hasAnyRole(['admin', 'direktur', 'wartawan', 'pegawai']))
            <a href="{{ route('assignments.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Penugasan</span>
            </a>
            
            <a href="{{ route('assignments.published') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-newspaper"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Berita Terbit</span>
            </a>
            @endif

            <a href="{{ route('reports.performance') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Laporan Kinerja</span>
            </a>

            @if(Auth::user()->hasAnyRole(['direktur', 'admin']))
            <a href="{{ route('rekap-absensi.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Rekap Absensi</span>
            </a>
            @endif

            @if(Auth::user()->hasAnyRole(['admin', 'editor', 'pegawai']))
            <a href="{{ route('reviews.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Review Liputan</span>
            </a>
            @endif

            {{-- Administrasi --}}
            @if(Auth::user()->hasAnyRole(['admin', 'direktur']))
            <a href="{{ route('archives.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-archive"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Arsip</span>
            </a>
            @endif

            <a href="{{ route('kerjasama.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-handshake"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Kerjasama</span>
            </a>

            @if(Auth::user()->hasAnyRole(['direktur', 'pegawai', 'editor', 'admin']))
            <a href="{{ route('payrolls.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Slip Gaji</span>
            </a>
            @endif
            
            {{-- System (Admin Only) --}}
            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('users.index') }}" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users-cog"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">User</span>
            </a>
            @endif

        </div>
    </div>


    {{-- DASHBOARD CONTENT --}}
    @if(Auth::user()->hasRole('admin'))
        {{-- ADMIN DASHBOARD --}}
        <h3 class="text-lg font-bold text-gray-800 mb-4">Statistik Internal</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-5 rounded-2xl shadow-lg text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-white/20 rounded-lg">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold">{{ $data['totalUsers'] ?? 0 }}</p>
                <p class="text-sm opacity-80">Total User Aktif</p>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-green-100 text-green-600 rounded-lg">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Penugasan Aktif</span>
                </div>
                <p class="text-2xl font-bold text-gray-800 ml-1">{{ $data['activeAssignments'] ?? 0 }}</p>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Cuti Pending</span>
                </div>
                <p class="text-2xl font-bold text-gray-800 ml-1">{{ $data['pendingLeaves'] ?? 0 }}</p>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                        <i class="fas fa-server"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Server</span>
                </div>
                <p
                    class="text-xl font-bold {{ ($data['serverStatus'] ?? '') === 'Online' ? 'text-green-600' : 'text-red-600' }} ml-1">
                    {{ $data['serverStatus'] ?? 'Offline' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-700 mb-4">Distribusi Status Penugasan</h4>
                <div id="chart-admin"></div>
            </div>
        </div>

    @elseif(Auth::user()->hasRole('direktur'))
        {{-- DIREKTUR DASHBOARD --}}
        <h3 class="text-lg font-bold text-gray-800 mb-4">Overview Kinerja</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-r from-orange-600 to-red-500 text-white p-6 rounded-2xl shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold opacity-90">Kerjasama Aktif</h3>
                        <p class="text-4xl font-bold mt-2">{{ $data['totalKerjasama'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg">
                        <i class="fas fa-handshake text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-sm opacity-90">Pending Approval: {{ $data['kerjasamaPending'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-gray-500 text-sm font-semibold">Kinerja Pegawai (Avg)</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['performanceAvg'] ?? 0 }}%</p>
                    </div>
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $data['performanceAvg'] ?? 0 }}%"></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-semibold">Approval Pending</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['permohonanIzinPending'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4">Permohonan Izin/Cuti baru</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-700 mb-4">Statistik Status Kerjasama</h4>
                <div id="chart-direktur"></div>
            </div>
        </div>

    @elseif(Auth::user()->hasRole('wartawan'))
        {{-- WARTAWAN DASHBOARD --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fas fa-camera text-6xl text-orange-600"></i>
                </div>
                <p class="text-gray-500 text-sm font-medium mb-1">Tugas Aktif</p>
                <p class="text-3xl font-bold text-gray-800">{{ $data['activeTasks'] ?? 0 }}</p>
                <a href="{{ route('assignments.index') }}"
                    class="text-xs text-orange-600 font-medium mt-2 inline-block hover:underline">Lihat Semua &rarr;</a>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-gray-500 text-sm font-medium mb-1">Selesai (Bulan Ini)</p>
                <p class="text-3xl font-bold text-green-600">{{ $data['completedTasks'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-2">Berita terbit & submitted</p>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Status Absensi</p>
                    @if(isset($data['todayAttendance']))
                        <p class="text-2xl font-bold text-green-600 mt-1">Hadir</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($data['todayAttendance']->clock_in)->format('H:i') }} WIB</p>
                    @else
                        <p class="text-2xl font-bold text-red-500 mt-1">Belum Absen</p>
                        <a href="{{ route('absensi.index') }}" class="text-xs text-blue-600 mt-1 inline-block hover:underline">Absen
                            Sekarang</a>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center">
                    <i class="fas fa-fingerprint text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Penugasan Terbaru</h3>
                    <a href="{{ route('assignments.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua</a>
                </div>
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-800 font-semibold">
                        <tr>
                            <th class="p-4 pl-6">Judul</th>
                            <th class="p-4">Lokasi</th>
                            <th class="p-4 pr-6">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data['myAssignments'] ?? [] as $task)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 pl-6 font-medium text-gray-800">{{ Str::limit($task->title, 50) }}</td>
                                <td class="p-4">{{ $task->location_name ?? '-' }}</td>
                                <td class="p-4 pr-6">
                                    <span class="px-2.5 py-1 text-xs rounded-full font-medium
                                                    @if($task->status === 'on_site') bg-blue-100 text-blue-700
                                                    @elseif($task->status === 'accepted') bg-yellow-100 text-yellow-700
                                                    @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-400">
                                    <i class="fas fa-clipboard-list text-3xl mb-2 opacity-20"></i>
                                    <p>Belum ada penugasan aktif.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-700 mb-4">Tren Penyelesaian Tugas</h4>
                <div id="chart-wartawan"></div>
            </div>
        </div>

    @elseif(Auth::user()->hasAnyRole(['editor', 'pegawai']))
        {{-- EDITOR / PEGAWAI DASHBOARD --}}
        <h3 class="text-lg font-bold text-gray-800 mb-4">Statistik Kerja</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <i class="fas fa-edit text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pending Review</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $data['pendingReviews'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                        <i class="fas fa-check-double text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Berita Terbit</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $data['publishedMonth'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Absensi Hari Ini</p>
                    @if(isset($data['todayAttendance']))
                        <p class="text-2xl font-bold text-green-600 mt-1">Hadir</p>
                    @else
                        <p class="text-2xl font-bold text-red-500 mt-1">Belum Absen</p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center">
                    <i class="fas fa-fingerprint text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-700 mb-4">Tren Berita Terbit</h4>
                <div id="chart-editor"></div>
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded-xl shadow-sm text-center">
            <i class="fas fa-user-shield text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-bold text-gray-800">Akses Terbatas</h3>
            <p class="text-gray-500 mt-2">Anda tidak memiliki role yang spesifik untuk dashboard ini. Silahkan gunakan menu di
                samping.</p>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart Data
            const chartData = @json($data['chart'] ?? null);

            if (!chartData) return;

            // Admin Chart
            if (document.getElementById('chart-admin')) {
                const options = {
                    series: chartData.series,
                    labels: chartData.labels,
                    chart: {
                        type: 'donut',
                        height: 350
                    },
                    colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#6366F1'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        position: 'bottom'
                    }
                };
                new ApexCharts(document.getElementById('chart-admin'), options).render();
            }

            // Direktur Chart
            if (document.getElementById('chart-direktur')) {
                const options = {
                    series: chartData.series,
                    labels: chartData.labels,
                    chart: {
                        type: 'pie',
                        height: 350
                    },
                    colors: ['#F97316', '#84CC16', '#06B6D4', '#64748B'],
                    legend: {
                        position: 'bottom'
                    }
                };
                new ApexCharts(document.getElementById('chart-direktur'), options).render();
            }

            // Wartawan Chart
            if (document.getElementById('chart-wartawan')) {
                const options = {
                    series: [{
                        name: 'Tugas Selesai',
                        data: chartData.series
                    }],
                    chart: {
                        height: 300,
                        type: 'bar',
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '50%',
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: chartData.labels,
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    colors: ['#F97316'],
                    grid: {
                        borderColor: '#f1f1f1',
                    }
                };
                new ApexCharts(document.getElementById('chart-wartawan'), options).render();
            }

            // Editor Chart
            if (document.getElementById('chart-editor')) {
                const options = {
                    series: [{
                        name: 'Berita Terbit',
                        data: chartData.series
                    }],
                    chart: {
                        height: 300,
                        type: 'area', // Area chart for better visual
                        toolbar: { show: false }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        categories: chartData.labels,
                    },
                    colors: ['#10B981'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.2,
                            stops: [0, 90, 100]
                        }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                    }
                };
                new ApexCharts(document.getElementById('chart-editor'), options).render();
            }
        });
    </script>

@endsection