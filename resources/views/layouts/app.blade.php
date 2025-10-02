{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
    
<!-- Head Section -->
@include('layouts.head')

<body>
    <!-- Header Section -->
    @include('layouts.header')

    <!-- Main Content Section -->
    <main id="main">
        @yield('content')
    </main>

    <!-- Footer Section -->
    @include('layouts.footer')

    <!-- Scripts Section -->
    @include('layouts.scripts')
    
    <!-- Page-specific Scripts -->
    @yield('scripts')
</body>
</html>