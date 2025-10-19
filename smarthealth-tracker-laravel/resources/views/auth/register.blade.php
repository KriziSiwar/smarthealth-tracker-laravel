
<!-- ======= Register Section ======= -->
<section id="register" class="register section">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>Créer un compte</h2>
            <p>Inscrivez-vous pour accéder à votre espace personnel</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- ======= Register Form ======= -->
                <div class="php-email-form">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="name" class="form-label">{{ __('Nom complet') }}</label>
                                <input id="name" type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required autofocus
                                       placeholder="Votre nom complet">
                                @error('name')
                                    <div class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="email" class="form-label">{{ __('Adresse email') }}</label>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required
                                       placeholder="Votre adresse email">
                                @error('email')
                                    <div class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="role" class="form-label">{{ __('Rôle') }}</label>
                                <select id="role" name="role" 
                                        class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">-- Sélectionnez un rôle --</option>
                                    <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="password" class="form-label">{{ __('Mot de passe') }}</label>
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required placeholder="Entrez un mot de passe">
                                @error('password')
                                    <div class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="password_confirmation" class="form-label">{{ __('Confirmer le mot de passe') }}</label>
                                <input id="password_confirmation" type="password" 
                                       class="form-control" name="password_confirmation" required
                                       placeholder="Confirmez votre mot de passe">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-plus me-2"></i>
                                    {{ __('S\'inscrire') }}
                                </button>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-center">
                                <p>Déjà inscrit ? 
                                    <a href="{{ route('login') }}" class="text-primary">Se connecter</a>
                                </p>
                            </div>
                        </div>

                    </form>
                </div><!-- End Register Form -->

            </div>
        </div>

    </div>
</section><!-- End Register Section -->
