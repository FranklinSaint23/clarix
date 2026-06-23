@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-10">
        <h1 class="text-3xl md:text-4xl font-display font-bold tracking-tight">Mon profil</h1>
        <p class="text-on-surface-variant mt-2">Gérez vos informations personnelles, votre sécurité et vos préférences.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Colonne gauche : Avatar + infos clés -->
        <div class="lg:col-span-1 space-y-6">
            <div class="layer-lift p-6 text-center">
                <div class="relative inline-block cursor-pointer group" onclick="openLightbox()">
                    <img src="{{ $user->avatar_url }}" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg" id="avatar-preview">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition">
                        <i class="fas fa-search-plus text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <label for="avatar" class="text-xs text-primary hover:underline cursor-pointer">
                        <i class="fas fa-upload mr-1"></i> Changer l'avatar
                    </label>
                    <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" form="profile-form">
                </div>
                <h2 class="font-display font-bold text-xl mt-4">{{ $user->name }}</h2>
                <p class="text-on-surface-variant text-sm">{{ $user->email }}</p>
                <div class="mt-3 inline-flex items-center gap-2 flex-wrap justify-center">
                    <span class="bg-primary-fixed text-primary text-xs px-3 py-1 rounded-full">
                        {{ $user->role === 'student' ? 'Apprenant' : ($user->role === 'instructor' ? 'Instructeur' : 'Administrateur') }}
                    </span>
                    <span class="bg-secondary-fixed text-secondary text-xs px-3 py-1 rounded-full">
                        Membre depuis {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>

            <div class="layer-lift p-5">
                <h3 class="font-display font-semibold flex items-center gap-2 mb-3">
                    <i class="fas fa-chart-simple text-primary"></i> Activité
                </h3>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div>
                        <div class="text-2xl font-bold text-primary">{{ $user->enrollments->count() }}</div>
                        <div class="text-xs text-on-surface-variant uppercase">Cours suivis</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-primary">{{ $user->quizSubmissions->count() }}</div>
                        <div class="text-xs text-on-surface-variant uppercase">Quiz complétés</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Formulaire infos personnelles + avatar -->
            <div class="layer-lift p-6">
                <h3 class="font-display font-semibold text-lg flex items-center gap-2 mb-5">
                    <i class="fas fa-user-pen text-primary"></i> Informations personnelles
                </h3>
                <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required>
                            @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Adresse email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
                            @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Bio</label>
                        <textarea name="bio" rows="3" class="input-field" placeholder="Parlez-nous un peu de vous...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>

            <!-- Formulaire changement de mot de passe -->
            <div class="layer-lift p-6">
                <h3 class="font-display font-semibold text-lg flex items-center gap-2 mb-5">
                    <i class="fas fa-lock text-primary"></i> Sécurité
                </h3>
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Mot de passe actuel</label>
                            <input type="password" name="current_password" class="input-field" autocomplete="off" required>
                            @error('current_password') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Nouveau mot de passe</label>
                                <input type="password" name="password" class="input-field" autocomplete="new-password" required>
                                @error('password') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Confirmation</label>
                                <input type="password" name="password_confirmation" class="input-field" required>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="btn-primary">Mettre à jour le mot de passe</button>
                    </div>
                </form>
            </div>

            <!-- Zone dangereuse -->
            <div class="layer-lift p-6 border border-red-200 bg-red-50/30">
                <h3 class="font-display font-semibold text-red-700 flex items-center gap-2 mb-3">
                    <i class="fas fa-triangle-exclamation"></i> Zone dangereuse
                </h3>
                <p class="text-sm text-on-surface-variant mb-4">Supprimer définitivement votre compte et toutes vos données. Cette action est irréversible.</p>
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Êtes-vous absolument sûr ? Toutes vos données seront effacées.')">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-1">Mot de passe actuel</label>
                            <input type="password" name="password" class="input-field" required>
                        </div>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-5 rounded-md transition">
                            Supprimer mon compte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center" onclick="closeLightbox()">
    <div class="relative max-w-3xl max-h-full p-4">
        <img id="lightbox-img" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl">
        <button class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">&times;</button>
    </div>
</div>

<script>
    // Lightbox functions
    function openLightbox() {
        const img = document.getElementById('avatar-preview');
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        lightboxImg.src = img.src;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
    }
    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
    }
    // Aperçu de l'avatar avant upload
    document.getElementById('avatar')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('avatar-preview');
                if (preview) preview.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection