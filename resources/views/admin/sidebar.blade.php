
@php
    $me = auth()->user();

    $showMain = $me->canView('dashboard') || $me->canView('step1');

    $showProcess = collect(['step2', 'step3', 'step4', 'step5', 'step6'])
        ->contains(fn ($m) => $me->canView($m));

    $showRecords = collect(['students', 'capacity', 'reports'])
        ->contains(fn ($m) => $me->canView($m));
@endphp

<aside class="admin-sidebar">

    {{-- BRAND --}}
    <div class="sidebar-brand">

        <div class="sidebar-brand-logo">
            <img src="{{ asset('logo_adzu.jpg') }}" alt="AdZU Logo">
        </div>

        <div class="sidebar-brand-text">
            <strong>ADMISSIONS</strong>
            <span>AdZU Senior High</span>
        </div>

        {{-- COLLAPSE SIDEBAR --}}
        <button type="button"
                class="sidebar-collapse-toggle"
                data-sidebar-toggle
                aria-pressed="false"
                aria-label="Collapse sidebar"
                title="Collapse sidebar">

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <rect x="3" y="4" width="18" height="16" rx="2"/>
                <path d="M9 4v16m6-10 2 2-2 2"/>
            </svg>

        </button>

    </div>

    {{-- NAVIGATION --}}
    <div class="sidebar-scroll">
        <nav class="sidebar-menu">

            {{-- MAIN MENU --}}
            @if($showMain)
                <div class="sidebar-label">MAIN MENU</div>

                @if($me->canView('dashboard'))
                    <a href="{{ route('admin.dashboard') }}"
                       data-tooltip="Dashboard"
                       class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="menu-icon">▦</span>
                        <span>Dashboard</span>
                    </a>
                @endif

                @if($me->canView('step1'))
                    <a href="{{ route('admin.index') }}"
                       data-tooltip="Step 1 - Enrollment"
                       class="{{ request()->routeIs('admin.index', 'admin.show', 'admin.step1*') ? 'active' : '' }}">
                        <span class="menu-icon">▤</span>
                        <span>Step 1 - Enrollment</span>
                    </a>
                @endif
            @endif

            {{-- ENROLLMENT PROCESS --}}
            @if($showProcess)
                <div class="sidebar-label">ENROLLMENT PROCESS</div>

                @if($me->canView('step2'))
                    <a href="{{ route('admin.step2.index') }}"
                       data-tooltip="Step 2 - Infirmary"
                       class="{{ request()->routeIs('admin.step2*') ? 'active' : '' }}">
                        <span class="menu-icon">✚</span>
                        <span>Step 2 - Infirmary</span>
                    </a>
                @endif

                @if($me->canView('step3'))
                    <a href="{{ route('admin.step3.index') }}"
                       data-tooltip="Step 3 - Admission"
                       class="{{ request()->routeIs('admin.step3*') ? 'active' : '' }}">
                        <span class="menu-icon">✚</span>
                        <span>Step 3 - Admission</span>
                    </a>
                @endif

                @if($me->canView('step4'))
                    <a href="{{ route('admin.step4.index') }}"
                       data-tooltip="Step 4 - ID Capturing"
                       class="{{ request()->routeIs('admin.step4*') ? 'active' : '' }}">
                        <span class="menu-icon">▣</span>
                        <span>Step 4 - ID Capturing</span>
                    </a>
                @endif

                @if($me->canView('step5'))
                    <a href="{{ route('admin.step5.index') }}"
                       data-tooltip="Step 5 - GCO Assessment"
                       class="{{ request()->routeIs('admin.step5*') ? 'active' : '' }}">
                        <span class="menu-icon">▥</span>
                        <span>Step 5 - GCO Assessment</span>
                    </a>
                @endif

                @if($me->canView('step6'))
                    <a href="{{ route('admin.step6.index') }}"
                       data-tooltip="Step 6 - Enrolled"
                       class="{{ request()->routeIs('admin.step6*') ? 'active' : '' }}">
                        <span class="menu-icon">▥</span>
                        <span>Step 6 - Enrolled</span>
                    </a>
                @endif
            @endif

            {{-- RECORDS --}}
            @if($showRecords)
                <div class="sidebar-label">RECORDS</div>

                @if($me->canView('students') && Route::has('admin.students.index'))
                    <a href="{{ route('admin.students.index') }}"
                       data-tooltip="Student Directory"
                       class="{{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                        <span class="menu-icon">▤</span>
                        <span>Student Directory</span>
                    </a>
                @endif

                @if($me->canView('capacity') && Route::has('admin.capacity.index'))
                    <a href="{{ route('admin.capacity.index') }}"
                       data-tooltip="Capacity Control"
                       class="{{ request()->routeIs('admin.capacity*') ? 'active' : '' }}">
                        <span class="menu-icon">◫</span>
                        <span>Capacity Control</span>
                    </a>
                @endif

                @if($me->canView('reports') && Route::has('admin.reports.index'))
                    <a href="{{ route('admin.reports.index') }}"
                       data-tooltip="Reports"
                       class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                        <span class="menu-icon">▧</span>
                        <span>Reports</span>
                    </a>
                @endif
            @endif

            {{-- SYSTEM --}}
            @if($me->isAdmin())
                <div class="sidebar-label">SYSTEM</div>

                @if(Route::has('admin.accounts.index'))
                    <a href="{{ route('admin.accounts.index') }}"
                       data-tooltip="Manage Account"
                       class="{{ request()->routeIs('admin.accounts*') ? 'active' : '' }}">
                        <span class="menu-icon">♙</span>
                        <span>Manage Account</span>
                    </a>
                @endif

                @if(Route::has('admin.access.index'))
                    <a href="{{ route('admin.access.index') }}"
                       data-tooltip="Access Rights"
                       class="{{ request()->routeIs('admin.access*') ? 'active' : '' }}">
                        <span class="menu-icon">⚿</span>
                        <span>Access Rights</span>
                    </a>
                @endif

                @if(Route::has('admin.settings.index'))
                    <a href="{{ route('admin.settings.index') }}"
                       data-tooltip="Settings"
                       class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                        <span class="menu-icon">⚙</span>
                        <span>Settings</span>
                    </a>
                @endif
            @endif

        </nav>
    </div>

    {{-- SIDEBAR FOOTER --}}
    <div class="sidebar-footer">

        {{-- USER PROFILE --}}
        <a href="{{ route('admin.profile') }}"
           class="sidebar-user"
           style="text-decoration:none;color:inherit;"
           title="My account">

            <div class="sidebar-avatar">
                {{ strtoupper(substr($me->name ?? 'A', 0, 1)) }}
            </div>

            <div class="sidebar-user-info">
                <strong>{{ $me->name ?? 'Administrator' }}</strong>
                <span>{{ $me->roleLabel() }}</span>
            </div>

        </a>

        {{-- LOGOUT --}}
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf

            <button type="submit"
                    class="sidebar-logout"
                    title="Logout"
                    aria-label="Logout">

                <span class="logout-icon">↪</span>
                <span>Logout</span>

            </button>
        </form>

    </div>

</aside>
