<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Kepegawaian')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        
        @include('layouts.sidebar')

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            @include('layouts.header')

            <main class="w-full flex-grow p-6">
                @yield('content')
            </main>
            
            <footer class="bg-white p-4 text-center text-xs text-gray-500 border-t">
                &copy; {{ date('Y') }} Perusahaan Media & Teknologi. All rights reserved.
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Intercept elements with data-confirm
            document.body.addEventListener('click', function(e) {
                const trigger = e.target.closest('[data-confirm]');
                if (!trigger) return;

                e.preventDefault();

                const message = trigger.getAttribute('data-confirm');
                
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (trigger.tagName === 'A') {
                            window.location.href = trigger.href;
                        } else if (trigger.tagName === 'BUTTON' || trigger.tagName === 'INPUT') {
                            trigger.closest('form').submit();
                        }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>