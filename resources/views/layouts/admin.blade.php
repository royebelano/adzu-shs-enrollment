
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') | AdZU Senior High</title>

    {{-- Theme script: load early to prevent theme flashing --}}
    <script src="{{ asset('js/admin/theme.js') }}"></script>

    {{-- Main styles --}}
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/theme.css') }}">

    {{-- Page-specific styles --}}
    @stack('styles')
</head>
<body>

    <div class="admin-wrapper">

        @include('admin.sidebar')

        <div class="admin-main">

            @include('admin.navbar')

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
