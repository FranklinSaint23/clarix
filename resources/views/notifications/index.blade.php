@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Notifications</h1>
                <p class="text-sm text-on-surface-variant mt-1">Restez informé de votre activité sur Skillora.</p>
            </div>
            <div class="flex gap-3">
                @if(auth()->user()->unreadNotifications->count())
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-outline/30 text-primary hover:bg-primary-fixed transition text-sm font-medium">
                            <i class="fas fa-check-double"></i> Tout marquer comme lu
                        </button>
                    </form>
                @endif
                @if($notifications->count())
                    <button type="button" id="deleteAllBtn" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-outline/30 text-red-600 hover:bg-red-50 transition text-sm font-medium">
                        <i class="fas fa-trash-alt"></i> Tout supprimer
                    </button>
                @endif
            </div>
        </div>

        <!-- Liste des notifications -->
        @if($notifications->count())
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="group bg-white rounded-2xl border border-outline/20 shadow-sm hover:shadow-md transition-all duration-200 {{ $notification->read_at ? 'opacity-80 hover:opacity-100' : 'bg-primary-fixed/5 border-primary/20' }}">
                        <div class="p-5">
                            <div class="flex flex-wrap justify-between items-start gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                            <i class="fas fa-bell text-primary text-sm"></i>
                                        </div>
                                        <span class="text-xs font-medium text-primary uppercase tracking-wide">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-base text-on-surface leading-relaxed">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                    @if(isset($notification->data['course_id']))
                                        <div class="mt-3">
                                            <a href="{{ route('courses.show', $notification->data['course_id']) }}" class="inline-flex items-center gap-1 text-primary text-sm hover:underline">
                                                <i class="fas fa-arrow-right"></i> Voir le cours
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(!$notification->read_at)
                                        <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-primary hover:underline flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> Marquer comme lue
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="delete-notif-btn text-xs text-red-600 hover:text-red-700 flex items-center gap-1" data-url="{{ route('notifications.destroy', $notification) }}" data-name="cette notification">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border border-outline/20 shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-fixed/30 mb-4">
                    <i class="fas fa-bell-slash text-2xl text-primary"></i>
                </div>
                <h3 class="text-lg font-display font-semibold mb-1">Aucune notification</h3>
                <p class="text-sm text-on-surface-variant">Vous serez informé des nouvelles leçons, réponses au forum, etc.</p>
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
            Êtes-vous sûr de vouloir supprimer <strong id="deleteItemName"></strong> ?<br>
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
    const deleteItemNameSpan = document.getElementById('deleteItemName');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    // Suppression individuelle
    document.querySelectorAll('.delete-notif-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            const name = this.dataset.name;
            deleteItemNameSpan.textContent = name;
            deleteForm.action = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    // Suppression de toutes les notifications
    const deleteAllBtn = document.getElementById('deleteAllBtn');
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', function() {
            deleteItemNameSpan.textContent = 'toutes vos notifications';
            deleteForm.action = '{{ route("notifications.destroy-all") }}';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    }

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