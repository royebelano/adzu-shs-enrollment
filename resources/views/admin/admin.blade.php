
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | Enrollment System</title>

    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">

    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">

        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('logo_adzu.jpg') }}" alt="AdZU Logo">
                <div>
                    <strong>ADMISSIONS COMMAND CENTER</strong>
                    <span>AdZU Senior High</span>
                </div>
            </div>

            <nav>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.index') }}">Step 1 - Enrollment</a>
            </nav>
        </aside>

        <div class="admin-main">
            <header class="admin-navbar">
                <h3>@yield('page-heading', 'Admissions Dashboard')</h3>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </header>

            <main class="admin-content">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
