@extends('layouts.app')

@section('title', 'Mes cours')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Mes cours</h1>
            <p class="text-sm text-on-surface-variant mt-1">Gérez votre bibliothèque de formations.</p>
        </div>
        <a href="{{ route('instructor.courses.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-3 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-105 transition-all duration-200 shadow-md font-medium">
            <i class="fas fa-plus-circle"></i> Nouveau cours
        </a>
    </div>

    <!-- Barre de filtres (sans rechargement) -->
    <div class="bg-white rounded-2xl border border-outline/20 p-4 mb-8 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Recherche -->
            <div class="flex-1">
                <div class="relative">
                    <!-- <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm"></i> -->
                    <input type="text" id="search-input" value="{{ request('search') }}" 
                           class="input-field w-full pl-9 py-2" 
                           placeholder="Rechercher un cours...">
                </div>
            </div>
            <!-- Niveau -->
            <div class="sm:w-48">
                <select id="level-select" class="input-field w-full py-2">
                    <option value="">Tous niveaux</option>
                    <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>🌱 Débutant</option>
                    <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>📘 Intermédiaire</option>
                    <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>🚀 Avancé</option>
                </select>
            </div>
            <!-- Statut -->
            <div class="sm:w-48">
                <select id="status-select" class="input-field w-full py-2">
                    <option value="">Tous statuts</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publié</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                </select>
            </div>
            <!-- Bouton réinitialiser -->
            <div>
                <button id="reset-filters" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-outline/30 text-on-surface-variant hover:bg-surface-low transition text-sm">
                    <i class="fas fa-times"></i> Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- Conteneur de la grille (sera mis à jour via AJAX) -->
    <div id="courses-grid">
        @include('instructor.courses._grid', ['courses' => $courses])
    </div>
</div>

<!-- Modale de suppression (inchangée) -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <!-- ... contenu identique à avant ... -->
</div>

<script>
    const gridContainer = document.getElementById('courses-grid');
    const searchInput = document.getElementById('search-input');
    const levelSelect = document.getElementById('level-select');
    const statusSelect = document.getElementById('status-select');
    const resetBtn = document.getElementById('reset-filters');

    let timeout = null;

    function fetchCourses() {
        const params = new URLSearchParams();
        if (searchInput.value) params.append('search', searchInput.value);
        if (levelSelect.value) params.append('level', levelSelect.value);
        if (statusSelect.value) params.append('status', statusSelect.value);

        fetch(`{{ route('instructor.courses.index') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            gridContainer.innerHTML = html;
            // Mettre à jour l'URL sans rechargement
            const newUrl = `{{ route('instructor.courses.index') }}?${params.toString()}`;
            window.history.pushState({}, '', newUrl);
            // Réattacher les événements de suppression (car la modale a besoin des nouveaux boutons)
            attachDeleteEvents();
        })
        .catch(error => console.error('Erreur:', error));
    }

    function attachDeleteEvents() {
        // Réattacher les événements des boutons supprimer (même code que dans la modale)
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteCourseTitleSpan = document.getElementById('deleteCourseTitle');
        const cancelBtn = document.getElementById('cancelDeleteBtn');

        document.querySelectorAll('.delete-course-btn').forEach(btn => {
            if (btn.hasAttribute('data-listener')) return;
            btn.setAttribute('data-listener', 'true');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const courseId = this.dataset.courseId;
                const courseTitle = this.dataset.courseTitle;
                deleteCourseTitleSpan.textContent = courseTitle;
                deleteForm.action = `/instructeur/courses/${courseId}`;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });
    }

    // Événements
    searchInput.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(fetchCourses, 400);
    });
    levelSelect.addEventListener('change', fetchCourses);
    statusSelect.addEventListener('change', fetchCourses);
    resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        levelSelect.value = '';
        statusSelect.value = '';
        fetchCourses();
    });

    // Initialiser les événements de suppression
    attachDeleteEvents();

    // Gestion de la modale (identique à avant)
    const modal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
</script>
@endsection