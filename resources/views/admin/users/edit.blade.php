@extends('layouts.app')

@section('title', 'Modifier utilisateur')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <!-- Fil d'Ariane -->
    <nav class="mb-6 text-sm text-on-surface-variant">
        <ol class="flex flex-wrap items-center gap-1">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition">Administration</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('admin.users.index') }}" class="hover:text-primary transition">Utilisateurs</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li class="text-primary font-medium">Modifier</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Modifier l’utilisateur</h1>
        <p class="text-sm text-on-surface-variant mt-1">{{ $user->name }}</p>
    </div>

    <!-- Carte formulaire -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf @method('PUT')

            <!-- Nom -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field w-full" required>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Adresse email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field w-full" required>
            </div>

            <!-- Rôle -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Rôle</label>
                <select name="role" class="input-field w-full">
                    <option value="student" @selected($user->role == 'student')>👨‍🎓 Étudiant</option>
                    <option value="instructor" @selected($user->role == 'instructor')>👨‍🏫 Instructeur</option>
                    <option value="admin" @selected($user->role == 'admin')>🔧 Administrateur</option>
                </select>
            </div>

            <!-- Bio -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Bio</label>
                <textarea name="bio" rows="4" class="input-field w-full resize-none" placeholder="Biographie de l’utilisateur...">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap justify-between items-center gap-3 pt-4">
                <button type="button" id="deleteUserBtn" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition font-medium text-sm">
                    <i class="fas fa-trash-alt"></i> Supprimer l’utilisateur
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-[1.02] transition shadow-md font-medium">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modale de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6 transform transition-all">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fas fa-trash-alt text-red-500"></i>
            </div>
            <h3 class="text-lg font-display font-semibold">Confirmer la suppression</h3>
        </div>
        <p class="text-on-surface-variant text-sm mb-6">
            Êtes-vous sûr de vouloir supprimer l’utilisateur <strong>{{ $user->name }}</strong> ?<br>
            Cette action est irréversible.
        </p>
        <div class="flex justify-end gap-3">
            <button id="cancelDeleteBtn" class="px-4 py-2 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition">
                Annuler
            </button>
            <form id="deleteForm" method="POST" action="{{ route('admin.users.destroy', $user) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 transition">
                    Supprimer définitivement
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('deleteModal');
    const deleteBtn = document.getElementById('deleteUserBtn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    deleteBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
</script>
@endsection