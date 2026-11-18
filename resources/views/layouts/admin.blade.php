<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Napakahalaga nito para sa AJAX/Fetch PUT requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard &bull; AdZU SHS e-Admissions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js para sa smooth collapse/hide animation -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 min-h-screen flex selection:bg-blue-600 selection:text-white" x-data="{ sidebarOpen: true }">

    <!-- Dark Navy Sidebar with Animation -->
    <aside :class="sidebarOpen ? 'w-72' : 'w-20'" class="bg-[#080d1a] border-r border-slate-800/80 flex flex-col justify-between transition-all duration-300 ease-in-out relative shrink-0 overflow-hidden shadow-xl">
        <div class="p-5 overflow-y-auto space-y-6">

            <!-- Logo Header & Collapse Button -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div class="flex items-center space-x-3" x-show="sidebarOpen" x-transition.opacity.duration.200ms>
                    <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-xl flex items-center justify-center font-bold text-white shadow-md">
                        AdZU
                    </div>
                    <div>
                        <h1 class="text-xs font-bold text-white tracking-wide">AdZU SHS</h1>
                        <p class="text-[10px] text-slate-400">e-Admissions Portal</p>
                    </div>
                </div>
                <!-- Mini Logo pag naka-hide -->
                <div x-show="!sidebarOpen" x-cloak class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-xl flex items-center justify-center font-bold text-white shadow-md mx-auto">
                    Ad
                </div>

                <!-- Arrow Toggle Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800/80 transition transform active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- User Profile Box -->
            <div x-show="sidebarOpen" x-transition.opacity.duration.200ms class="p-3 bg-slate-900/90 border border-slate-800 rounded-xl flex items-center space-x-3 shadow-inner">
                <div class="w-9 h-9 bg-amber-400 rounded-lg flex items-center justify-center font-bold text-slate-950 text-xs shadow-sm">
                    {{ substr(Auth::user()->name ?? 'Maria Santos', 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <h3 class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Maria Santos' }}</h3>
                    <p class="text-[10px] text-amber-400 font-medium">{{ Auth::user()->department ?? 'Admissions' }} Admin</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-6 text-xs font-medium">
                <div>
                    <p x-show="sidebarOpen" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 px-3">Overview</p>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>
                </div>

                <div>
                    <p x-show="sidebarOpen" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 px-3">Enrollment Steps</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.step1') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition group {{ request()->routeIs('admin.step1*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.step1*') ? 'text-amber-400' : 'text-slate-500 group-hover:text-blue-400' }} transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span x-show="sidebarOpen">Step 1 &bull; Enrollment Form</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.step2') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.step2*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} transition group">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span x-show="sidebarOpen">Step 2 &bull; Infirmary</span>
                        </a>

                        <a href="{{ route('admin.step3') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.step3*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} transition group">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-show="sidebarOpen">Step 3 &bull; Admission</span>
                        </a>

                        <a href="{{ route('admin.step4') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.step4*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} transition group">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            <span x-show="sidebarOpen">Step 4 &bull; ID Capturing</span>
                        </a>

                        <a href="{{ route('admin.step5') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.step5*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} transition group">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span x-show="sidebarOpen">Step 5 &bull; GCO Assessment</span>
                        </a>

                        <a href="{{ route('admin.step6') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.step6*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} transition group">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-show="sidebarOpen">Step 6 &bull; Enrolled</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p x-show="sidebarOpen" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 px-3">Records</p>
                    <div class="space-y-1 text-slate-400">
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-slate-900 hover:text-white transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span x-show="sidebarOpen">Student Directory</span>
                        </a>
<a href="{{ route('admin.enrollment-forms') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-slate-900 hover:text-white transition">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <span x-show="sidebarOpen">Enrollment Forms</span>
</a>
                        <a href="{{ route('admin.capacity') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.capacity*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} transition group">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-amber-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            </svg>
                            <span x-show="sidebarOpen">Capacity Control</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-slate-900 hover:text-white transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-show="sidebarOpen">Reports</span>
                        </a>
                    </div>
                </div>

                <!-- ADMINISTRATION SECTION -->
                <div>
                    <p x-show="sidebarOpen" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 px-3">Administration</p>
                    <div class="space-y-1 text-slate-400">
                        <!-- Manage Accounts -->
                        <a href="{{ route('admin.accounts') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.accounts*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20 font-semibold shadow-2xs' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} transition group">
    <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.654 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span x-show="sidebarOpen">Manage Accounts</span>
</a>

                        <!-- Access Rights -->
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-slate-900 hover:text-white transition group">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span x-show="sidebarOpen">Access Rights</span>
                        </a>

                        <!-- Settings -->
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-slate-900 hover:text-white transition group">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="sidebarOpen">Settings</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Logout Button -->
        <div class="p-4 border-t border-slate-800">
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="flex items-center space-x-3 w-full px-3 py-2 text-xs font-semibold text-red-400 hover:bg-red-500/10 rounded-lg transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span x-show="sidebarOpen">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen bg-slate-50 transition-all duration-300">

        <!-- Top Navigation Bar -->
        <header class="h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-8 shadow-xs sticky top-0 z-10">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600">Dashboard Overview</h2>
            <div class="text-xs text-slate-600 font-medium">
                Logged in as: <strong class="text-slate-900">{{ Auth::user()->name ?? 'Test User' }}</strong>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

    </div>

</body>
</html>
