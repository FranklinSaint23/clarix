@if($courses->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-7">
        @foreach($courses as $course)
            <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="relative h-48 overflow-hidden">
                    <img src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
                         alt="{{ $course->title }}" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                    <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-xs font-semibold px-2.5 py-1 rounded-full shadow-sm">
                        @if($course->level == 'beginner') 🌱 Débutant
                        @elseif($course->level == 'intermediate') 📘 Intermédiaire
                        @else 🚀 Avancé @endif
                    </span>
                    <span class="absolute top-3 right-3 bg-primary/95 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        {{ $course->formatted_price }}
                    </span>
                </div>
                <div class="p-5">
                    <h3 class="font-display font-bold text-xl line-clamp-1">{{ $course->title }}</h3>
                    <p class="text-on-surface-variant text-sm mt-2 line-clamp-2">{{ $course->description }}</p>
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center gap-1 text-sm text-on-surface-variant">
                            <i class="fas fa-user-graduate"></i>
                            <span>{{ $course->students->count() }} inscrits</span>
                        </div>
                        <a href="{{ route('courses.show', $course) }}" class="text-primary font-medium hover:underline inline-flex items-center gap-1 group/link">
                            Découvrir <i class="fas fa-arrow-right text-xs transition-transform group-hover/link:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination (si besoin, mais avec AJAX elle peut être désactivée ou gérée à part) -->
    @if($courses->hasPages())
        <div class="mt-12">
            {{ $courses->links() }}
        </div>
    @endif
@else
    <div class="text-center py-16 bg-surface-low rounded-2xl">
        <i class="fas fa-search text-5xl text-on-surface-variant/30 mb-4"></i>
        <h3 class="text-xl font-display font-semibold">Aucun cours trouvé</h3>
        <p class="text-on-surface-variant mt-1">Essayez de modifier vos filtres ou votre recherche.</p>
        <button id="reset-from-empty" class="btn-primary inline-block mt-6">Réinitialiser les filtres</button>
    </div>
@endif

<script>
    // Gestion du bouton de réinitialisation depuis la zone vide
    const resetBtn = document.getElementById('reset-from-empty');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            document.getElementById('search-input').value = '';
            document.getElementById('level-select').value = '';
            document.getElementById('price-select').value = '';
            fetchCourses();
        });
    }
</script>