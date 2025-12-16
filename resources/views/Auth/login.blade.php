<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- @vite('resources/css/app.css') --}}
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Left Side -->
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-orange-400 via-orange-500 to-orange-700 justify-center items-center p-8">
            <div class="text-center text-white">
            <h1 class="text-4xl font-bold mb-4">Welcome Back</h1>
            <p class="text-lg text-blue-100">Sign in to access your account</p>
            <h2 class="text-2xl font-semibold mt-4">Sistem Informasi Kepegawaian PT. Media Sriwijaya Anugrah</h2>
            </div>
        </div>

        <!-- Right Side -->
        <div class="w-full md:w-1/2 flex justify-center items-center p-8">
            <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl p-8 backdrop-blur-lg">
                <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome</h2>
                <p class="text-gray-600">Sign in to your account</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-3">Email Address</label>
                    <input type="email" name="email" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" placeholder="Enter your email" required>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-3">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" placeholder="Enter your password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3 px-4 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg">
                    Sign In
                </button>
                </form>

            </div>
            </div>
        </div>
    </div>
</body>
</html>