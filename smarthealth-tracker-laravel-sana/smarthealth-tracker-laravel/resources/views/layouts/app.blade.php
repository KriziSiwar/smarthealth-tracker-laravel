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
<script>
(function() {
    const script = document.createElement("script");
    script.src = "https://www.chatbase.co/embed.min.js";
    script.id = "chatbase-script";
    script.setAttribute("chatbotId", "8pO8s8wW9Qn92qo7LZqsl");
    script.setAttribute("domain", "www.chatbase.co");
    script.async = true;
    document.head.appendChild(script);
})();
</script>
    <!-- Footer Section -->
    @include('layouts.footer')

    <!-- Scripts Section -->
    @include('layouts.scripts')
    
    <!-- Page-specific Scripts -->
    @yield('scripts')
</body>
</html>