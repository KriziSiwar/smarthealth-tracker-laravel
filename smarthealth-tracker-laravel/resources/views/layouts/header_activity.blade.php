{{-- resources/views/layouts/header_activity.blade.php --}}
<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      {{-- LOGO MEDITRUST --}}
      <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto me-xl-0">
        {{-- Icône de bâtiment médical --}}
        <svg class="my-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #3fbbc0; width: 40px; height: 40px;">
          <g id="bgCarrier" stroke-width="0"></g>
          <g id="tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="iconCarrier">
            {{-- Icône d'hôpital/clinique --}}
            <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5" fill="none"/>
            <path d="M12 7V17M8 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </g>
        </svg>
        <h1 class="sitename">MediTrust</h1>
      </a>

      {{-- NAVIGATION MEDITRUST --}}
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
          <li><a href="{{ url('/about') }}">About</a></li>
          <li><a href="{{ url('/departments') }}">Departments</a></li>
          <li><a href="{{ url('/services') }}">Services</a></li>
          <li><a href="{{ url('/doctors') }}">Doctors</a></li>
          
          {{-- Menu déroulant More Pages --}}
          <li class="dropdown">
            <a href="#"><span>More Pages</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="{{ route('activities.index') }}">Activities</a></li>
              <li><a href="{{ route('challenges.index') }}">Challenges</a></li>
              @auth
              <li><a href="{{ route('profile.edit') }}">My Profile</a></li>
              @endauth
            </ul>
          </li>

          {{-- Menu déroulant Dropdown --}}
          <li class="dropdown">
            <a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Dropdown 1</a></li>
              <li class="dropdown">
                <a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="#">Dropdown 2</a></li>
              <li><a href="#">Dropdown 3</a></li>
              <li><a href="#">Dropdown 4</a></li>
            </ul>
          </li>

          <li><a href="{{ url('/contact') }}">Contact</a></li>

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
              <li><a href="{{ route('profile.edit') }}">👤 My Profile</a></li>
              <li><a href="{{ route('activities.index') }}">🏃‍♀️ My Activities</a></li>
              <li><a href="{{ route('user.challenges.index') }}">📈 My Challenges</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                  @csrf
                  <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #dc3545;">
                    🚪 Logout
                  </a>
                </form>
              </li>
            </ul>
          </li>
          @else
          <li><a href="{{ route('login') }}">🔐 Login</a></li>
          <li><a href="{{ route('register') }}">📝 Register</a></li>
          @endauth
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      {{-- BOUTON APPOINTMENT --}}
      <a class="btn-getstarted" href="{{ url('/appointment') }}">
        Appointment
      </a>

    </div>
</header>