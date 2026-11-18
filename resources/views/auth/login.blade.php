<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &bull; AdZU SHS e-Admissions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 font-sans antialiased flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-slate-800 border border-slate-700/80 rounded-2xl shadow-xl overflow-hidden p-8">

        <!-- Header / Logo Area -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-xl text-white font-bold text-xl mb-3 shadow-lg">
                AdZU
            </div>
            <h1 class="text-xl font-bold text-white tracking-wide">e-Admissions Portal</h1>
            <p class="text-xs text-slate-400 mt-1">Senior High School &bull; Admin & Staff Access</p>
        </div>

        <!-- Display Errors if any -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-xs text-red-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="e.g. admissions@adzu.edu.ph">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-0">
                    <span class="ml-2">Remember me</span>
                </label>
            </div>

            <button type="submit"
                class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs uppercase tracking-wider rounded-lg transition duration-200 shadow-md">
                Sign In to Dashboard
            </button>
        </form>

        <!-- Footer Note -->
        <div class="mt-8 text-center border-t border-slate-700/60 pt-4">
            <p class="text-[11px] text-slate-500">Ateneo de Zamboanga University &copy; 2026</p>
        </div>

    </div>

</body>
</html>
