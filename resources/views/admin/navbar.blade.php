
<header class="admin-navbar">

    {{-- PAGE HEADING --}}
    <div class="navbar-heading">

        <span class="navbar-eyebrow">
            ADMISSIONS COMMAND CENTER
        </span>

        <div class="navbar-title-row">
            <h1>@yield('page-heading', 'Dashboard')</h1>
            <span class="navbar-divider">/</span>
            <span class="navbar-section">Administration</span>
        </div>

        <div class="navbar-date">
            <span class="date-indicator"></span>
            {{ now()->format('l, F d, Y') }}
        </div>

    </div>

    {{-- NAVBAR ACTIONS --}}
    <div class="navbar-actions">

        {{-- DARK / LIGHT MODE ICON --}}
        <button type="button"
                class="theme-toggle navbar-theme-toggle"
                data-theme-toggle
                aria-pressed="false"
                aria-label="Switch to dark mode"
                title="Switch to dark mode">

            {{-- MOON ICON --}}
            <svg class="theme-icon-moon"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M20.2 15.4A8.2 8.2 0 0 1 8.6 3.8 8.3 8.3 0 1 0 20.2 15.4Z"/>
            </svg>

            {{-- SUN ICON --}}
            <svg class="theme-icon-sun"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <circle cx="12" cy="12" r="4"/>
                <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/>
            </svg>

        </button>

        {{-- ADMINISTRATOR INFORMATION --}}
        <div class="navbar-admin">

            <div class="navbar-admin-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>

            <div class="navbar-admin-info">
                <strong>
                    {{ auth()->user()->name ?? 'Administrator' }}
                </strong>

                <span>
                    {{ auth()->user()->roleLabel() }}
                </span>
            </div>

            <span class="admin-status-dot"></span>

        </div>

    </div>

</header>
