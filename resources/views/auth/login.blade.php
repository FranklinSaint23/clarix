@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-display font-bold">Bienvenue</h1>
        <p class="text-on-surface-variant mt-2">Connectez-vous à votre sanctuaire intellectuel</p>
    </div>
    <div class="layer-lift p-8">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" class="input-field" value="{{ old('email') }}" required autofocus>
                @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mot de passe</label>
                <input type="password" name="password" class="input-field" required>
                @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-center justify-between mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-outline text-primary focus:ring-primary">
                    <span class="ml-2 text-sm">Se souvenir de moi</span>
                </label>
            </div>
            <button type="submit" class="btn-primary w-full text-center">Se connecter</button>
        </form>
        <div class="mt-6 text-center text-sm">
            <span class="text-on-surface-variant">Nouveau sur Skillora ?</span>
            <a href="{{ route('register') }}" class="text-primary font-medium ml-1">Créer un compte</a>
        </div>
    </div>
</div>
@endsection