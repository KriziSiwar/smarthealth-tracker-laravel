<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- Section tableau de bord -->
                <div class="sb-sidenav-menu-heading">Tableau de bord</div>
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <!-- Section gestion utilisateurs -->
                <div class="sb-sidenav-menu-heading">Gestion</div>

                {{-- Gestion des utilisateurs (si tu les gères côté admin) --}}
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseUsers"
                   aria-expanded="false" aria-controls="collapseUsers">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Utilisateurs
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseUsers" aria-labelledby="headingOne"
                     data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        {{-- ⚠️ Ces routes doivent exister --}}
                        <a class="nav-link" href="{{ route('admin.users.index') }}">Liste des utilisateurs</a>
                        <a class="nav-link" href="{{ route('admin.roles.index') }}">Rôles & permissions</a>
                    </nav>
                </div>

                <!-- Section challenges -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseChallenges"
                   aria-expanded="false" aria-controls="collapseChallenges">
                    <div class="sb-nav-link-icon"><i class="fas fa-bolt"></i></div>
                    Challenges
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseChallenges" aria-labelledby="headingTwo"
                     data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('admin.challenges.index') }}">Tous les challenges</a>
                        <a class="nav-link" href="{{ route('admin.user-challenges.index') }}">Participations</a>
                    </nav>
                </div>

                <!-- Section autres -->
                <div class="sb-sidenav-menu-heading">Autres</div>
                {{-- Ces routes sont optionnelles --}}
                <a class="nav-link" href="{{ route('admin.reports.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                    Rapports
                </a>
                <a class="nav-link" href="{{ route('admin.settings') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                    Paramètres
                </a>
            </div>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small">Connecté en tant que :</div>
            {{ Auth::user()->name ?? 'Admin' }}
        </div>
    </nav>
</div>
