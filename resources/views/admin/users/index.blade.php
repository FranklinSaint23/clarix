@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Gestion des utilisateurs</h1>
        <p class="text-sm text-on-surface-variant mt-1">Gérez les comptes, les rôles et les permissions des utilisateurs.</p>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
        
        <!-- 🔥 scroll horizontal mobile -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-sm">
                <thead class="bg-surface-low text-on-surface-variant">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold">Nom</th>
                        <th class="px-5 py-3 text-left font-semibold">Email</th>
                        <th class="px-5 py-3 text-left font-semibold">Rôle</th>
                        <th class="px-5 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline/20">
                    @foreach($users as $user)
                        <tr class="hover:bg-surface-low/30 transition">
                            <td class="px-5 py-4 font-medium">{{ $user->name }}</td>
                            <td class="px-5 py-4 text-on-surface-variant">{{ $user->email }}</td>
                            <td class="px-5 py-4">
                                @if($user->role == 'admin')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-700">
                                        <i class="fas fa-shield-alt"></i> Administrateur
                                    </span>
                                @elseif($user->role == 'instructor')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                        <i class="fas fa-chalkboard-teacher"></i> Instructeur
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                        <i class="fas fa-user-graduate"></i> Étudiant
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline/30 text-on-surface-variant hover:bg-primary-fixed hover:text-primary transition text-sm">
                                        <i class="fas fa-edit text-xs"></i> Modifier
                                    </a>
                                    <button type="button" 
                                            class="delete-user-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline/30 text-red-600 hover:bg-red-50 transition text-sm"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}">
                                        <i class="fas fa-trash-alt text-xs"></i> Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-outline/20 bg-surface-low/30">
                {{ $users->links() }}
            </div>
        @endif
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
            Êtes-vous sûr de vouloir supprimer l’utilisateur <strong id="deleteUserName"></strong> ?<br>
            Cette action est irréversible.
        </p>
        <div class="flex justify-end gap-3">
            <button id="cancelDeleteBtn" class="px-4 py-2 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition">
                Annuler
            </button>
            <form id="deleteForm" method="POST" action="">
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
    const deleteForm = document.getElementById('deleteForm');
    const deleteUserNameSpan = document.getElementById('deleteUserName');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            deleteUserNameSpan.textContent = userName;
            deleteForm.action = `/admin/utilisateurs/${userId}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
</script>
@endsection