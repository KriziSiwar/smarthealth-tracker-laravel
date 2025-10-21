{{-- resources/views/layouts/header.blade.php --}}
<header id="header" class="header d-flex align-items-center fixed-top">
  <div class="header-container container-fluid container-xl d-flex align-items-center justify-content-between">

    {{-- === LOGO === --}}
    <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto me-xl-0">
      <svg class="my-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
        style="color: #2c7873; width: 36px; height: 36px;">
        <path d="M12 2L14 7L19 7L15 10L16 15L12 12L8 15L9 10L5 7L10 7Z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
        <path d="M12 22V19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
      <h1 class="sitename ms-2">SmartHealth</h1>
    </a>

    {{-- === NAVIGATION PRINCIPALE === --}}
    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="{{ route('dashboardV') }}" >🏠 Accueil</a></li>

        {{-- === CHALLENGES === --}}
        <li class="dropdown">
          <a href="#"><span>🔥 Challenges</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li><a href="{{ route('challenges.index') }}">Tous les Challenges</a></li>
            @auth
              <li><a href="{{ route('challenges.create') }}">➕ Créer un Challenge</a></li>
              <li><a href="{{ route('user.challenges.index') }}">📈 Mes Challenges</a></li>
            @endauth
          </ul>
        </li>
         {{-- NUTRITION --}}
          <li class="dropdown">
            <a href="#"><span>🍎 Nutrition</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="{{ route('nutrition.dashboard') }}">Tableau de bord</a></li>
              <li><a href="{{ route('nutrition.food.log') }}">Journal alimentaire</a></li>
              <li><a href="{{ route('nutrition.meal-plans.index') }}">Plans de repas</a></li>
            </ul>
          </li>

        {{-- === ACTIVITÉS === --}}
        @auth
        <li class="dropdown">
          <a href="#"><span>🏃 Activités</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li><a href="{{ route('activities.index') }}">📋 Mes Activités</a></li>
            <li><a href="{{ route('activities.create') }}">➕ Nouvelle Activité</a></li>
            <li><a href="{{ route('activities.stats') }}">📊 Statistiques</a></li>
          </ul>
        </li>
        @endauth

        {{-- === UTILISATEUR CONNECTÉ === --}}
        @auth
        <li class="dropdown">
          <a href="#">
            <i class="bi bi-person-circle me-1"></i>
            <span>{{ Auth::user()->name }}</span>
            <i class="bi bi-chevron-down toggle-dropdown"></i>
          </a>
          <ul>
            <li><a href="{{ route('profile.edit') }}">👤 Mon Profil</a></li>
            <li><a href="{{ route('user.challenges.index') }}">📈 Mes Challenges</a></li>
            <li><a href="{{ route('activities.index') }}">🏃 Mes Activités</a></li>
            <li><a href="{{ route('chatbot') }}">💬 Chatbot</a></li>

            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #dc3545;">
                  🚪 Déconnexion
                </a>
              </form>
            </li>
          </ul>
        </li>
        @else
        {{-- === MENU INVITÉ === --}}
        <li><a href="{{ route('login') }}">🔐 Connexion</a></li>
        <li><a href="{{ route('register') }}">📝 Inscription</a></li>
        @endauth
      </ul>

      {{-- BOUTON RESPONSIVE MOBILE --}}
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    {{-- === BOUTON ACTION PRINCIPAL === --}}
    @auth
      <a class="btn-getstarted" href="{{ route('activities.create') }}">
        <i class="bi bi-plus-circle me-1"></i> Nouvelle Activité
      </a>
    @else
      <a class="btn-getstarted" href="{{ route('register') }}">
        🚀 Commencer
      </a>
    @endauth

  </div>
</header>
