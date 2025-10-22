<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-indigo-100 px-6 py-12">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
            
            <!-- En-tête -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-indigo-600">Connexion</h1>
                <p class="text-gray-500 mt-2">Accédez à votre espace personnel</p>
            </div>

            <!-- Statut de session -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Adresse e-mail')" />
                    <x-text-input id="email"
                        class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Mot de passe -->
                <div>
                    <x-input-label for="password" :value="__('Mot de passe')" />
                    <x-text-input id="password"
                        class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Souvenir -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('Mot de passe oublié ?') }}
                        </a>
                    @endif
                </div>

                <!-- Bouton -->
                <div>
                    <x-primary-button
                        class="w-full justify-center py-2 text-lg font-semibold bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
                        {{ __('Se connecter') }}
                    </x-primary-button>
                </div>

                <!-- Lien d'inscription -->
                <p class="text-center text-sm text-gray-600 mt-4">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Créer un compte</a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
