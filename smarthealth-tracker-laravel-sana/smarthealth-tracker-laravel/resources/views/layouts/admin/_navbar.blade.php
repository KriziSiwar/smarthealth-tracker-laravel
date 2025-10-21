<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Logo / Nom du site -->
    <a class="navbar-brand ps-3" href="{{ route('admin.dashboard') }}">Admin Panel</a>

    <!-- Bouton pour réduire le menu -->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Barre de recherche -->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Rechercher..." aria-label="Search for..." />
            <button class="btn btn-primary" id="btnNavbarSearch" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    <!-- Menu utilisateur -->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.challenges.index') }}">Challenges</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.user-challenges.index') }}">Participations</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="dropdown-item text-danger">
        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
    </button>
</form>

                </li>
            </ul>
        </li>
    </ul>
</nav>
