{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')


@section('content')
<!-- ======= Login Section ======= -->
<section id="login" class="login section">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>Connexion</h2>
            <p>Accédez à votre espace personnel</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- ======= Login Form ======= -->
                <div class="php-email-form">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="email" class="form-label">{{ __('Adresse Email') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
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
                                <label for="password" class="form-label">{{ __('Mot de passe') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="current-password"
                                       placeholder="Votre mot de passe">
                                @error('password')
                                    <div class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Se souvenir de moi') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    {{ __('Se connecter') }}
                                </button>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-center">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Mot de passe oublié ?') }}
                                    </a>
                                @endif
                                
                                @if (Route::has('register'))
                                    <p class="mt-2">
                                        Pas encore de compte ? 
                                        <a href="{{ route('register') }}" class="text-primary">Créer un compte</a>
                                    </p>
                                @endif
                            </div>
                        </div>

                    </form>
                </div><!-- End Login Form -->

            </div>
        </div>

    </div>
</section><!-- End Login Section -->
@endsection

@section('styles')
<style>
.login.section {
    padding: 60px 0;
    background: #f8f9fa;
}

.php-email-form {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
}

.php-email-form .form-group {
    margin-bottom: 20px;
}

.php-email-form .form-control {
    height: 50px;
    border-radius: 5px;
    border: 1px solid #ddd;
    padding: 10px 15px;
}

.php-email-form .btn-primary {
    background: #1977cc;
    border: 0;
    padding: 12px 40px;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.4s;
}

.php-email-form .btn-primary:hover {
    background: #1c84e3;
}
</style>
@endsection