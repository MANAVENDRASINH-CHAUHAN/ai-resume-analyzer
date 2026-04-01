<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AI Resume Analyzer System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @php
        $isAdminArea = auth()->check() && auth()->user()->role === 'admin' && request()->routeIs('admin.*');
    @endphp

    @include('partials.navbar')

    <main class="site-main py-4 py-lg-5">
        @if ($isAdminArea)
            <div class="container-fluid px-3 px-lg-4">
                @include('partials.flash-messages')

                <div class="row g-4">
                    <div class="col-xl-3 col-lg-4">
                        @include('partials.admin-sidebar')
                    </div>
                    <div class="col-xl-9 col-lg-8">
                        @yield('content')
                    </div>
                </div>
            </div>
        @else
            <div class="container-fluid px-3 px-lg-4">
                @include('partials.flash-messages')
                @yield('content')
            </div>
        @endif
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/notifications.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-live.js') }}"></script>
    <script src="{{ asset('assets/js/resume-status.js') }}"></script>
    @stack('scripts')
</body>
</html>
