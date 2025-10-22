<!-- ======= Register Section ======= -->
<section id="register" class="register py-5 bg-light">
    <div class="container" data-aos="fade-up">
        
        <div class="text-center mb-5">
            <h2 class="fw-bold">Créer un compte</h2>
            <p class="text-muted">Inscrivez-vous pour accéder à votre espace personnel</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- ======= Register Form Card ======= -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="card-title mb-4 text-center">Inscription</h4>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom complet</label>
                                <input id="name" type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required autofocus
                                       placeholder="Votre nom complet">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Adresse email</label>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required
                                       placeholder="Votre adresse email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="mb-3">
                                <label for="role" class="form-label">Rôle</label>
                                <select id="role" name="role" 
                                        class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">-- Sélectionnez un rôle --</option>
                                    <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required placeholder="Entrez un mot de passe">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input id="password_confirmation" type="password" 
                                       class="form-control" name="password_confirmation" required
                                       placeholder="Confirmez votre mot de passe">
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-plus me-2"></i> S'inscrire
                                </button>
                            </div>

                            <!-- Login Link -->
                            <p class="text-center text-muted">
                                Déjà inscrit ? 
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Se connecter</a>
                            </p>

                        </form>
                    </div>
                </div><!-- End Card -->

            </div>
        </div>

    </div>
</section>
