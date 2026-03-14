<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-40 lg:hidden"></div>

<aside :class="sidebarOpen ? 'translate-x-0 lg:ml-0' : '-translate-x-full lg:translate-x-0 lg:-ml-72'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-black text-white transition-all duration-700 ease-in-out shadow-2xl flex flex-col border-r border-gray-800 lg:static">

    <div class="flex items-center justify-between px-6 h-20 bg-black border-b border-gray-800 shrink-0">
        <div class="flex items-center gap-3">
            <div
                class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 shadow-lg shadow-orange-500/20">
                <i class="fas fa-newspaper text-white text-lg"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-base font-bold text-white tracking-wide">SI-PEG</span>
                <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Media Sriwijaya</span>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-8 overflow-y-auto no-scrollbar scroll-smooth">

        <div>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('dashboard')
    ? 'bg-orange-600 text-white shadow-lg shadow-orange-500/30'
    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-th-large w-5 text-center transition-transform group-hover:scale-110"></i>
                <span class="font-medium text-sm">Dashboard</span>
            </a>
        </div>

        @if(Auth::user()->hasAnyRole(['admin', 'hrd', 'direktur']))
            <div>
                <h3 class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Kepegawaian</h3>
                <div class="space-y-1">
                    {{-- Manajemen Karyawan (Data Diri, Kontak, Jabatan) --}}
                    <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                            {{ request()->routeIs('employees.*')
            ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
            : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                        <i
                            class="fas fa-id-card w-5 text-center {{ request()->routeIs('employees.*') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                        <span class="font-medium text-sm">Data Karyawan</span>
                    </a>
                </div>
            </div>
        @endif

        <div>
            <h3 class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Operasional Harian</h3>
            <div class="space-y-1">

                @if(Auth::user()->hasAnyRole(['admin', 'pegawai', 'wartawan']))
                            <a href="{{ route('absensi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                                                                                                                                                                {{ request()->routeIs('absensi.*')
                    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                                <i
                                    class="fas fa-clock w-5 text-center {{ request()->routeIs('absensi.*') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                                <span class="font-medium text-sm">Absensi</span>
                            </a>
                @endif

                @if(Auth::user()->hasAnyRole(['direktur', 'pegawai', 'admin']))
                            <a href="{{ route('permohonan-izin.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                                                                                                                                                                {{ request()->routeIs('permohonan-izin.*')
                    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                                <i
                                    class="fas fa-calendar-alt w-5 text-center {{ request()->routeIs('permohonan-izin.*') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                                <span class="font-medium text-sm">Cuti & Izin</span>
                            </a>
                @endif

                @if(Auth::user()->hasAnyRole(['admin', 'direktur', 'wartawan', 'pegawai']))
                            <a href="{{ route('assignments.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                        {{ (request()->routeIs('assignments.*') && !request()->routeIs('assignments.published'))
                    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                                <i
                                    class="fas fa-clipboard-list w-5 text-center {{ (request()->routeIs('assignments.*') && !request()->routeIs('assignments.published')) ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                                <span class="font-medium text-sm">Penugasan Liputan</span>
                            </a>
                @endif

                @if(Auth::user()->hasAnyRole(['admin', 'direktur', 'wartawan', 'pegawai']))
                            <a href="{{ route('assignments.published') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                            {{ request()->routeIs('assignments.published')
                    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                                <i
                                    class="fas fa-newspaper w-5 text-center {{ request()->routeIs('assignments.published') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                                <span class="font-medium text-sm">Berita Terbit</span>
                            </a>
                @endif

                <a href="{{ route('reports.performance') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                    {{ request()->routeIs('reports.performance')
    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                    <i
                        class="fas fa-chart-line w-5 text-center {{ request()->routeIs('reports.performance') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                    <span class="font-medium text-sm">Laporan Kinerja</span>
                </a>

                @if(Auth::user()->hasAnyRole(['direktur', 'admin']))
                            <a href="{{ route('rekap-absensi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                                                                                    {{ request()->routeIs('rekap-absensi.*')
                    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                                <i
                                    class="fas fa-calendar-check w-5 text-center {{ request()->routeIs('rekap-absensi.*') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                                <span class="font-medium text-sm">Rekap Absensi</span>
                            </a>
                @endif

                @if(Auth::user()->hasAnyRole(['admin', 'editor', 'pegawai']))
                            <a href="{{ route('reviews.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                                                                        {{ request()->routeIs('reviews.*')
                    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                                <i
                                    class="fas fa-clipboard-check w-5 text-center {{ request()->routeIs('reviews.*') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                                <span class="font-medium text-sm">Review Liputan</span>
                            </a>
                @endif
            </div>
        </div>

        <div>
            <h3 class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Administrasi</h3>
            <div class="space-y-1">
                @if(Auth::user()->hasAnyRole(['admin', 'direktur']))
                            <a href="{{ route('archives.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                    {{ request()->routeIs('archives.*')
                    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                                <i
                                    class="fas fa-archive w-5 text-center {{ request()->routeIs('archives.*') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                                <span class="font-medium text-sm">Arsip & Dokumen</span>
                            </a>
                @endif

                <a href="{{ route('kerjasama.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                    {{ request()->routeIs('kerjasama.*')
    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                    <i
                        class="fas fa-handshake w-5 text-center {{ request()->routeIs('kerjasama.*') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                    <span class="font-medium text-sm">Kerjasama (MoU)</span>
                </a>

                @if(Auth::user()->hasAnyRole(['direktur', 'pegawai', 'editor', 'admin']))
                            <a href="{{ route('payrolls.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                        {{ request()->routeIs('payrolls.*')
                    ? 'bg-orange-500/10 text-orange-400 border-l-4 border-orange-500'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white border-l-4 border-transparent' }}">
                                <i
                                    class="fas fa-money-bill-wave w-5 text-center {{ request()->routeIs('payrolls.*') ? 'text-orange-400' : 'group-hover:text-white' }}"></i>
                                <span class="font-medium text-sm">Payroll / Penggajian</span>
                            </a>
                @endif
            </div>
        </div>

        @if(Auth::user()->hasRole('admin'))
            <div>
                <h3 class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">System</h3>
                <div class="space-y-1">
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                {{ request()->routeIs('users.*')
            ? 'bg-red-500/10 text-red-400 border-l-4 border-red-500'
            : 'text-gray-400 hover:bg-gray-800 hover:text-red-300 border-l-4 border-transparent' }}">
                        <i
                            class="fas fa-users-cog w-5 text-center {{ request()->routeIs('users.*') ? 'text-red-400' : 'group-hover:text-red-300' }}"></i>
                        <span class="font-medium text-sm">Manajemen User (Akun)</span>
                    </a>

                    <a href="{{ route('divisions.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                {{ request()->routeIs('divisions.*')
            ? 'bg-red-500/10 text-red-400 border-l-4 border-red-500'
            : 'text-gray-400 hover:bg-gray-800 hover:text-red-300 border-l-4 border-transparent' }}">
                        <i
                            class="fas fa-sitemap w-5 text-center {{ request()->routeIs('divisions.*') ? 'text-red-400' : 'group-hover:text-red-300' }}"></i>
                        <span class="font-medium text-sm">Data Divisi</span>
                    </a>

                    <a href="{{ route('positions.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors group
                                                                                                {{ request()->routeIs('positions.*')
            ? 'bg-red-500/10 text-red-400 border-l-4 border-red-500'
            : 'text-gray-400 hover:bg-gray-800 hover:text-red-300 border-l-4 border-transparent' }}">
                        <i
                            class="fas fa-briefcase w-5 text-center {{ request()->routeIs('positions.*') ? 'text-red-400' : 'group-hover:text-red-300' }}"></i>
                        <span class="font-medium text-sm">Data Jabatan</span>
                    </a>
                </div>
            </div>
        @endif
    </nav>

    <div class="p-4 border-t border-gray-800 bg-gray-900/50">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-gray-300 font-bold border border-gray-600">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-slate-500 truncate capitalize">
                    {{ Auth::user()->getRoleNames()->join(', ') ?: 'User' }}
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors"
                    title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>