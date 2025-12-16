<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-xl flex flex-col">
    
    <div class="flex items-center justify-between px-4 h-16 bg-gradient-to-r from-slate-950 to-slate-900 shadow-lg border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center shadow-md">
                <i class="fas fa-newspaper text-white text-lg"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-blue-400">SI Kepegawaian</span>
                <span class="text-xs text-gray-400 leading-none">PT. Media Sriwijaya Anugrah</span>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-home w-6"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <p class="px-4 mt-6 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Operasional</p>

        @if(in_array(Auth::user()->role, ['admin', 'pegawai', 'wartawan']))
        <a href="{{ route('absensi.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-gray-400 hover:bg-slate-800 hover:text-white">
            <i class="fas fa-clock w-6"></i>
            <span>Absensi</span>
        </a>
        @endif

        @if(in_array(Auth::user()->role, ['admin', 'direktur', 'wartawan']))
        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-gray-400 hover:bg-slate-800 hover:text-white">
            <i class="fas fa-tasks w-6"></i>
            <span>Penugasan Liputan</span>
        </a>
        @endif

        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-gray-400 hover:bg-slate-800 hover:text-white">
            <i class="fas fa-chart-line w-6"></i>
            <span>Kinerja</span>
        </a>

        <p class="px-4 mt-6 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Administrasi</p>

        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-gray-400 hover:bg-slate-800 hover:text-white">
            <i class="fas fa-money-bill-wave w-6"></i>
            <span>Penggajian</span>
        </a>

        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-gray-400 hover:bg-slate-800 hover:text-white">
            <i class="fas fa-file-alt w-6"></i>
            <span>Pelaporan</span>
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'direktur']))
        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-gray-400 hover:bg-slate-800 hover:text-white">
            <i class="fas fa-handshake w-6"></i>
            <span>Kerjasama</span>
        </a>
        @endif

        @if(Auth::user()->role === 'admin')
        <div class="mt-6 border-t border-slate-700 pt-4">
            <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-red-400 hover:bg-red-900/50 hover:text-red-200">
                <i class="fas fa-users-cog w-6"></i>
                <span>Manajemen Pengguna</span>
            </a>
        </div>
        @endif

    </nav>
</aside>