{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Inscription - Mon App Santé')

@section('content')
<!-- ======= Register Section ======= -->
<section id="register" class="register section">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>Inscription</h2>
            <p>Créez votre compte personnel</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- ======= Register Form ======= -->
                <form method="POST" action="{{ route('register') }}" class="php-email-form">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-12 form-group">
                            <label for="name" class="form-label">{{ __('Nom complet') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
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
                            <label for="email" class="form-label">{{ __('Adresse Email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email"
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
                                   name="password" required autocomplete="new-password"
                                   placeholder="Votre mot de passe">
                            @error('password')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12 form-group">
                            <label for="password-confirm" class="form-label">{{ __('Confirmer le mot de passe') }}</label>
                            <input id="password-confirm" type="password" class="form-control" 
                                   name="password_confirmation" required autocomplete="new-password"
                                   placeholder="Confirmez votre mot de passe">
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-2"></i>
                                {{ __('Créer mon compte') }}
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <p class="mt-2">
                                Déjà un compte ? 
                                <a href="{{ route('login') }}" class="text-primary">Se connecter</a>
                            </p>
                        </div>
                    </div>

                </form>
                <!-- End Register Form -->

            </div>
        </div>

    </div>
</section><!-- End Register Section -->
@endsection

@section('styles')
<style>
.register.section {
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