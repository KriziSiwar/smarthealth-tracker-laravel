{{-- resources/views/layouts/header.blade.php --}}
<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      {{-- LOGO CHALLENGE SANTÉ --}}
      <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto me-xl-0">
        <svg class="my-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #2c7873;">
          <g id="bgCarrier" stroke-width="0"></g>
          <g id="tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="iconCarrier">
            {{-- Icône modifiée pour représenter les challenges --}}
            <path d="M12 2L14 7L19 7L15 10L16 15L12 12L8 15L9 10L5 7L10 7Z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
            <path d="M12 22V19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path opacity="0.5" d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path opacity="0.5" d="M10 15H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </g>
        </svg>
        <h1 class="sitename">ChallengeSanté</h1>
      </a>

      {{-- NAVIGATION CHALLENGES --}}
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Accueil</a></li>
          
          {{-- MENU CHALLENGES --}}
          <li class="dropdown">
            <a href="#"><span>Challenges</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="{{ route('challenges.index') }}">Tous les Challenges</a></li>
              @auth
              <li><a href="{{ route('challenges.create') }}">Créer un Challenge</a></li>
              @endauth
            </ul>
          </li>

          {{-- CATÉGORIES --}}
          <li class="dropdown">
            <a href="#"><span>Catégories</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="{{ route('challenges.index', ['category' => 'sport']) }}">🏃 Sport & Fitness</a></li>
              <li><a href="{{ route('challenges.index', ['category' => 'nutrition']) }}">🥗 Nutrition</a></li>
              <li><a href="{{ route('challenges.index', ['category' => 'mental']) }}">🧠 Bien-être Mental</a></li>
              <li><a href="{{ route('challenges.index', ['category' => 'ecologie']) }}">🌱 Écologie</a></li>
              <li><a href="{{ route('challenges.index', ['category' => 'apprentissage']) }}">📚 Apprentissage</a></li>
            </ul>
          </li>

          {{-- NUTRITION --}}
          <li class="dropdown">
            <a href="#"><span>🍎 Nutrition</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="{{ route('nutrition.dashboard') }}">Tableau de bord</a></li>
              <li><a href="{{ route('nutrition.food.log') }}">Journal alimentaire</a></li>
              <li><a href="{{ route('nutrition.meal-plans.index') }}">Plans de repas</a></li>
              <li><a href="{{ route('nutrition.water-intake') }}">Suivi d'hydratation</a></li>
            </ul>
          </li>

          {{-- STATISTIQUES --}}
          @auth
          <li class="dropdown">
            <a href="#"><span>Mes Progrès</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="{{ route('user.challenges.index') }}">📊 Tableau de Bord</a></li>
              <li><a href="{{ route('user.challenges.index', ['status' => 'completed']) }}">✅ Challenges Complétés</a></li>
              <li><a href="{{ route('user.challenges.index', ['status' => 'in_progress']) }}">🔄 En Cours</a></li>
            </ul>
          </li>
          @endauth

          {{-- MENU UTILISATEUR --}}
          @auth
          <li class="dropdown">
            <a href="#">
              <span>
                <i class="bi bi-person-circle me-1"></i>
                {{ Auth::user()->name }}
              </span> 
              <i class="bi bi-chevron-down toggle-dropdown"></i>
            </a>
            <ul>
              <li><a href="{{ route('profile.edit') }}">👤 Mon Profil</a></li>
              <li><a href="{{ route('user.challenges.index') }}">📈 Mes Statistiques</a></li>
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
          <li><a href="{{ route('login') }}">🔐 Connexion</a></li>
          <li><a href="{{ route('register') }}">📝 Inscription</a></li>
          @endauth
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      {{-- BOUTON ACTION PRINCIPAL --}}
      @auth
        <a class="btn-getstarted" href="{{ route('challenges.create') }}">
          <i class="bi bi-plus-circle me-1"></i>
          Nouveau Challenge
        </a>
      @else
        <a class="btn-getstarted" href="{{ route('register') }}">
          <i class="bi bi-rocket-takeoff me-1"></i>
          Commencer
        </a>
      @endauth

    </div>
</header>



<style>
    /* Décale le contenu pour ne pas être caché par le navbar */
    body {
        padding-top: 70px; /* adapte selon la hauteur exacte du navbar */
    }

    /* Optionnel : si ton main a déjà du padding, ajuste légèrement */
    main#main {
        min-height: calc(100vh - 70px);
    }
</style>
