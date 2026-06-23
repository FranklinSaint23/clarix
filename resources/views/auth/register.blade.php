@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-display font-bold">Rejoindre la communauté</h1>
        <p class="text-on-surface-variant mt-2">Choisissez votre rôle et commencez votre voyage</p>
    </div>
    <div class="layer-lift p-8">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nom complet</label>
                <input type="text" name="name" class="input-field" value="{{ old('name') }}" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" class="input-field" value="{{ old('email') }}" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mot de passe</label>
                <input type="password" name="password" class="input-field" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="input-field" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Je suis :</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center p-3 border border-outline/30 rounded-lg cursor-pointer hover:bg-primary-fixed transition">
                        <input type="radio" name="role" value="student" class="mr-2" required> Étudiant
                    </label>
                    <label class="flex items-center justify-center p-3 border border-outline/30 rounded-lg cursor-pointer hover:bg-primary-fixed transition">
                        <input type="radio" name="role" value="instructor" class="mr-2"> Instructeur
                    </label>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full text-center">Créer mon compte</button>
        </form>
        <div class="mt-6 text-center text-sm">
            <span class="text-on-surface-variant">Déjà membre ?</span>
            <a href="{{ route('login') }}" class="text-primary font-medium ml-1">Se connecter</a>
        </div>
    </div>
</div>
@endsection