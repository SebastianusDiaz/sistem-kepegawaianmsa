<header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="flex items-center">
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 hidden md:block ml-4">
            @yield('page_title', 'Dashboard')
        </h2>
    </div>

    <div class="flex items-center gap-4">

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                <img class="h-9 w-9 rounded-full object-cover border border-gray-300" 
                     src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" 
                     alt="Avatar">
                <div class="text-left hidden md:block">
                    <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 uppercase">{{ Auth::user()->role }}</p>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 ml-1"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-user mr-2 w-4"></i> Profil
                </a>
                <div class="border-t border-gray-100"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt mr-2 w-4"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>