<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AdZU Senior High School Admissions Portal</title>

        <!-- Fonts & Tailwind CSS -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-900 text-white font-sans antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

        <!-- Background Overlay with Image effect -->
        <div class="absolute inset-0 z-0 bg-cover bg-center opacity-40" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1920&auto=format&fit=crop');"></div>
        <div class="absolute inset-0 z-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>

        <!-- Header / Navigation -->
        <header class="relative z-10 w-full px-6 py-6 flex items-center justify-between max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <!-- Logo placeholder -->
                <div class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center font-bold text-white border border-yellow-500">
                    AdZU
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-wide uppercase">Ateneo de Zamboanga University</h1>
                    <p class="text-xs text-gray-300">SENIOR HIGH SCHOOL ADMISSIONS</p>
                </div>
            </div>

            <nav class="flex items-center gap-4">
                <a href="{{ route('tracking.form') }}" class="text-sm text-gray-300 hover:text-white transition">Track application</a>
                <a href="{{ route('login') }}" class="text-xs font-semibold px-4 py-2 border border-gray-400 rounded-full hover:bg-white hover:text-black transition">STAFF PORTAL</a>
            </nav>
        </header>

        <!-- Main Hero Section -->
        <main class="relative z-10 max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12 items-center flex-grow">

            <!-- Left Side Content -->
            <div class="lg:col-span-2 space-y-6">
                <div class="inline-block px-3 py-1 rounded-full bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 text-xs tracking-wider uppercase font-semibold">
                    • SY 2026-2027 Applications Are Open
                </div>

                <h2 class="text-5xl lg:text-7xl font-bold tracking-tight leading-none">
                    Your Ateneo journey <br>
                    <span class="text-yellow-400">starts here.</span>
                </h2>

                <p class="text-gray-300 text-base lg:text-lg max-w-xl">
                    A clear, guided path from your first appointment to official enrollment at AdZU Senior High School.
                </p>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="{{ route('enrollment.form') }}" class="px-6 py-3.5 bg-yellow-500 hover:bg-yellow-400 text-black font-semibold rounded-lg shadow-lg transition flex items-center gap-2">
                        Book an appointment &rarr;
                    </a>
                    <a href="{{ route('tracking.form') }}" class="px-6 py-3.5 bg-gray-800/80 hover:bg-gray-700 text-white font-semibold rounded-lg border border-gray-600 transition">
                        Track my application
                    </a>
                </div>
            </div>

            <!-- Right Side: Requirements Box -->
            <div class="bg-black/40 backdrop-blur-md p-6 lg:p-8 rounded-2xl border border-white/10 space-y-4">
                <p class="text-xs uppercase tracking-widest text-yellow-400 font-semibold">Before You Begin</p>
                <h3 class="text-xl font-bold">Prepare your documents</h3>

                <ul class="space-y-3 text-sm text-gray-300">
                    <li class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-xs">✓</span> Form 138 / Report Card
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-xs">✓</span> Good Moral Certificate
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-xs">✓</span> PSA Birth Certificate
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-xs">✓</span> Valid contact and email
                    </li>
                </ul>
            </div>

        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full py-6 text-center text-xs text-gray-500 border-t border-gray-800">
            Ateneo de Zamboanga University &copy; {{ date('Y') }}
        </footer>

    </body>
</html>
