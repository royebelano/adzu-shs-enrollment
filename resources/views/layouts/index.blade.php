<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') | SHS Enrollment</title>

    <script src="{{ asset('js/admin/theme.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/navbar.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/theme.css') }}">
</head>
<body class="legacy-admin">
    <div class="admin-wrapper">
        @include('admin.sidebar')

        <div class="admin-main">
            @include('admin.navbar')

            <main class="admin-content">
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>

            <footer class="legacy-footer">SHS Enrollment Management System</footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
