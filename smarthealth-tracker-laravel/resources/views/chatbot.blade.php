@extends('layouts.app')

@section('title', 'Chatbot Santé')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">🤖 Bienvenue chez Dalanda</h2>

    {{-- ✅ Intégration correcte du chatbot --}}
    <iframe
        src="https://www.chatbase.co/chatbot/8pO8s8wW9Qn92qo7LZqsl"
        width="100%"
        height="600"
        frameborder="0"
        title="Chatbot Santé"
        allow="microphone;"
    ></iframe>
</div>

{{-- Script Chatbase --}}
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
@endsection
