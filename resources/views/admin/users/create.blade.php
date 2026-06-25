@extends('layouts.app')

@section('title', 'Créer un utilisateur')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">

    <nav class="mb-6 text-sm text-on-surface-variant">
        <ol class="flex flex-wrap items-center gap-1">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition">Administration</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('admin.users.index') }}" class="hover:text-primary transition">Utilisateurs</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li class="text-primary font-medium">Créer un compte</li>
        </ol>
    </nav>

    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Créer un utilisateur</h1>
        <p class="text-sm text-on-surface-variant mt-1">Le compte sera immédiatement actif et vérifié.</p>
    </div>

    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm">
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="input-field w-full" placeholder="Jean Dupont" required autofocus>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Adresse e-mail <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="input-field w-full" placeholder="jean@exemple.com" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Rôle <span class="text-red-500">*</span></label>
                <select name="role" class="input-field w-full" required>
                    <option value="student"    @selected(old('role', 'student') === 'student')>Étudiant</option>
                    <option value="instructor" @selected(old('role') === 'instructor')>Instructeur</option>
                    <option value="admin"      @selected(old('role') === 'admin')>Administrateur</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Mot de passe <span class="text-red-500">*</span></label>
                <input type="password" name="password"
                       class="input-field w-full" placeholder="Minimum 8 caractères" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation"
                       class="input-field w-full" placeholder="Répétez le mot de passe" required>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit"
                        class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 transition shadow-md font-medium">
                    <i class="fas fa-user-plus"></i> Créer le compte
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
