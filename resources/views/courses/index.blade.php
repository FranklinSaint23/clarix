@extends('layouts.app')

@section('title', 'Catalogue des cours')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Hero section -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-display font-bold tracking-tight">Explorez notre catalogue</h1>
        <p class="text-on-surface-variant text-lg mt-3 max-w-2xl mx-auto">Des formations conçues par des experts pour vous aider à atteindre vos objectifs.</p>
    </div>

    <!-- Barre de recherche et filtres (sans rechargement) -->
    <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-outline/20 p-5 mb-10">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Champ recherche -->
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60"></i>
                    <input type="text" id="search-input" placeholder="Rechercher un cours..." 
                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-outline/30 bg-surface-low focus:bg-white focus:border-primary/50 transition">
                </div>
            </div>

            <!-- Filtre niveau -->
            <div class="md:w-52">
                <select id="level-select" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-low focus:bg-white focus:border-primary/50 transition cursor-pointer">
                    <option value="">📚 Tous niveaux</option>
                    <option value="beginner">🌱 Débutant</option>
                    <option value="intermediate">📘 Intermédiaire</option>
                    <option value="advanced">🚀 Avancé</option>
                </select>
            </div>

            <!-- Filtre prix -->
            <div class="md:w-48">
                <select id="price-select" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-low focus:bg-white focus:border-primary/50 transition cursor-pointer">
                    <option value="">💰 Tous prix</option>
                    <option value="free">🎁 Gratuit</option>
                    <option value="paid">💎 Payant</option>
                </select>
            </div>

            <!-- Bouton réinitialiser -->
            <button id="reset-filters" class="flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition">
                <i class="fas fa-times"></i> Réinitialiser
            </button>
        </div>
    </div>

    <!-- Conteneur des résultats (mis à jour via AJAX) -->
    <div id="courses-container">
        @include('courses._partials.course_grid', ['courses' => $courses])
    </div>
</div>

<script>
    function fetchCourses() {
        const search = document.getElementById('search-input').value;
        const level = document.getElementById('level-select').value;
        const price = document.getElementById('price-select').value;

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (level) params.append('level', level);
        if (price) params.append('price', price);

        fetch(`{{ route('courses.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('courses-container').innerHTML = html;
            // Mettre à jour l'URL sans recharger (optionnel)
            window.history.pushState({}, '', `{{ route('courses.index') }}?${params.toString()}`);
        })
        .catch(error => console.error('Erreur:', error));
    }

    // Événements
    document.getElementById('search-input').addEventListener('input', () => fetchCourses());
    document.getElementById('level-select').addEventListener('change', () => fetchCourses());
    document.getElementById('price-select').addEventListener('change', () => fetchCourses());
    document.getElementById('reset-filters').addEventListener('click', () => {
        document.getElementById('search-input').value = '';
        document.getElementById('level-select').value = '';
        document.getElementById('price-select').value = '';
        fetchCourses();
    });

    // Debounce pour la recherche (évite trop d'appels)
    let timeout;
    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fetchCourses(), 300);
    });
</script>
@endsection