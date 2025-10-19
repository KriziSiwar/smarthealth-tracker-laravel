<!DOCTYPE html>
<html lang="fr">
<head>
    @include('layouts.head')
    <title>MediTrust – Activités</title>

    {{-- ✅ CSS MediTrust --}}
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
</head>
<body>

    {{-- ✅ Header MediTrust uniquement --}}
    @include('layouts.header_activity')

    <main class="py-5">
        @yield('content')
    </main>

    {{-- ✅ Scripts globaux --}}
    @include('layouts.scripts')

    {{-- ⚠️ On ne charge pas main.js pour éviter le bug addEventListener --}}
    {{-- <script src="{{ asset('js/main.js') }}"></script> --}}

    {{-- ✅ Petit script de secours pour activer le menu mobile si besoin --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.querySelector('.mobile-nav-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    const navbar = document.querySelector('#navbar');
                    if (navbar) {
                        navbar.classList.toggle('navbar-mobile');
                    }
                });
            }
        });
    </script>

</body>
</html>
