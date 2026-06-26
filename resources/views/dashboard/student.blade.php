@extends('layouts.app')

@section('title', 'Mon espace')

@section('content')
<div class="mb-10">
    <h1 class="text-3xl font-display font-bold">Bienvenue, {{ auth()->user()->name }}.</h1>
    <p class="text-on-surface-variant mt-2">Votre parcours intellectuel continue. Vous avez <span class="font-semibold text-primary">{{ $pendingModules ?? 0 }}</span> modules à terminer cette semaine.</p>
</div>

<!-- Statistiques globales -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
    <div class="layer-lift p-4 text-center">
        <div class="text-2xl font-bold text-primary">{{ $enrollments->count() }}</div>
        <div class="text-xs text-on-surface-variant uppercase">Cours inscrits</div>
    </div>
    <div class="layer-lift p-4 text-center">
        <div class="text-2xl font-bold text-primary">{{ $totalLessonsCompleted }}</div>
        <div class="text-xs text-on-surface-variant uppercase">Leçons terminées</div>
    </div>
    <div class="layer-lift p-4 text-center">
        <div class="text-2xl font-bold text-primary">{{ $enrollments->where('status', 'completed')->count() }}</div>
        <div class="text-xs text-on-surface-variant uppercase">Cours finis</div>
    </div>
    <div class="layer-lift p-4 text-center">
        <div class="text-2xl font-bold text-primary">{{ $currentStreak }}</div>
        <div class="text-xs text-on-surface-variant uppercase">Jours consécutifs</div>
        <div class="flex justify-center mt-2 space-x-1">
            @for($i = 0; $i < 7; $i++)
                <div class="w-2 h-2 rounded-full {{ $i < $currentStreak ? 'bg-secondary' : 'bg-surface-high' }}"></div>
            @endfor
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-8">
    <!-- Colonne principale : cours en cours et liste -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Cours en cours (premiers) -->
        <div>
            <h2 class="text-xl font-display font-semibold mb-4 flex items-center">
                <i class="fas fa-play-circle text-primary mr-2"></i> Poursuivre l'apprentissage
            </h2>
            @if($enrollments->where('status', 'active')->count() > 0)
                @foreach($enrollments->take(2) as $enrollment)
                    <div class="layer-lift p-5 mb-4 transition hover:shadow-ambient">
                        <div class="flex flex-wrap justify-between items-start gap-4">
                            <div class="flex-1">
                                <h3 class="font-display font-bold text-lg">{{ $enrollment->course->title }}</h3>
                                <p class="text-sm text-on-surface-variant mt-1">{{ $enrollment->course->chapters->first()->title ?? 'Module 1' }} • {{ $enrollment->progress_percent }}% terminé</p>
                            </div>
                            <a href="{{ route('courses.show', $enrollment->course) }}" class="btn-primary py-2 px-4 text-sm whitespace-nowrap">Continuer</a>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-secondary-fixed rounded-full h-2">
                                <div class="progress-gradient h-2 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="layer-lift p-8 text-center">
                    <i class="fas fa-book-open text-4xl text-primary/30 mb-3 block"></i>
                    <p class="text-on-surface-variant">Vous n'avez aucun cours en cours pour le moment.</p>
                    <a href="{{ route('courses.index') }}" class="inline-block mt-4 text-primary hover:underline">Explorer le catalogue →</a>
                </div>
            @endif
        </div>

        <!-- Tous mes cours -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-display font-semibold flex items-center">
                    <i class="fas fa-book-open text-primary mr-2"></i> Mes cours
                </h2>
                <a href="{{ route('courses.index') }}" class="text-primary text-sm hover:underline">Voir le catalogue →</a>
            </div>

            @if($enrollments->count() > 0)
                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($enrollments as $enrollment)
                        <div class="layer-lift p-4 transition hover:shadow-ambient">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-xs font-medium bg-secondary-fixed text-secondary rounded-full px-2 py-0.5">
                                        {{ $enrollment->course->level == 'beginner' ? 'Débutant' : ($enrollment->course->level == 'intermediate' ? 'Intermédiaire' : 'Avancé') }}
                                    </span>
                                    <h3 class="font-display font-semibold mt-2 line-clamp-1">{{ $enrollment->course->title }}</h3>
                                </div>
                                <span class="text-sm font-bold text-primary">{{ $enrollment->progress_percent }}%</span>
                            </div>
                            <div class="mt-3 w-full bg-secondary-fixed rounded-full h-1.5">
                                <div class="progress-gradient h-1.5 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                            <div class="mt-3 flex justify-end gap-3">
                                @if($enrollment->progress_percent >= 100 || $enrollment->status === 'completed')
                                    <a href="{{ route('certificate.download', $enrollment) }}"
                                       class="text-green-600 text-sm hover:underline flex items-center gap-1"
                                       title="Télécharger le certificat">
                                        <i class="fas fa-certificate"></i> Certificat
                                    </a>
                                @endif
                                <a href="{{ route('courses.show', $enrollment->course) }}" class="text-primary text-sm hover:underline">Reprendre →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="layer-lift p-8 text-center">
                    <i class="fas fa-folder-open text-4xl text-primary/30 mb-3 block"></i>
                    <p class="text-on-surface-variant">Vous n'êtes encore inscrit à aucun cours.</p>
                    <a href="{{ route('courses.index') }}" class="inline-block mt-4 btn-primary">Découvrir les cours</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Colonne latérale (inchangée) -->
    <div class="space-y-6">
        <!-- Carte de profil -->
        <div class="layer-lift p-5 text-center">
            <img src="{{ auth()->user()->avatar_url }}" class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-primary object-cover">
            <h3 class="font-display font-semibold text-lg">{{ auth()->user()->name }}</h3>
            <p class="text-sm text-on-surface-variant">{{ auth()->user()->email }}</p>
            <div class="mt-3 flex justify-center gap-2">
                <span class="bg-primary-fixed text-primary text-xs px-2 py-1 rounded-full">{{ auth()->user()->role === 'student' ? 'Apprenant' : 'Instructeur' }}</span>
                <span class="bg-secondary-fixed text-secondary text-xs px-2 py-1 rounded-full">Pro Learner</span>
            </div>
            <a href="{{ route('profile.edit') }}" class="mt-4 inline-block text-primary text-sm hover:underline">Modifier le profil →</a>
        </div>

        <!-- Badges / Achievements -->
        <div class="layer-lift p-5">
            <h3 class="font-display font-semibold flex items-center">
                <i class="fas fa-award text-primary mr-2"></i> Récompenses
            </h3>
            <div class="flex flex-wrap gap-3 mt-3">
                <div class="bg-surface-low rounded-full px-3 py-1 text-xs flex items-center">
                    <i class="fas fa-bolt text-secondary mr-1"></i> Quick Learner
                </div>
                <div class="bg-surface-low rounded-full px-3 py-1 text-xs flex items-center">
                    <i class="fas fa-users text-primary mr-1"></i> Top Contributor
                </div>
                <div class="bg-surface-low rounded-full px-3 py-1 text-xs flex items-center">
                    <i class="fas fa-rocket text-primary mr-1"></i> 100+ réponses
                </div>
            </div>
        </div>

        <!-- Insights personnalisés -->
        <div class="layer-lift p-5">
            <h3 class="font-display font-semibold flex items-center">
                <i class="fas fa-chart-line text-primary mr-2"></i> Perspectives
            </h3>
            <p class="text-sm text-on-surface-variant mt-2">
                Votre concentration a augmenté de <span class="text-primary font-bold">22%</span> ce mois. 
                Vous êtes le plus productif entre 8h et 10h.
            </p>
            <div class="mt-3 flex justify-between items-center">
                <span class="text-xs text-on-surface-variant">Heures apprises cette semaine :</span>
                <span class="font-bold text-primary">12h</span>
            </div>
            <div class="w-full bg-secondary-fixed rounded-full h-1.5 mt-1">
                <div class="bg-primary h-1.5 rounded-full" style="width: 65%"></div>
            </div>
            <a href="#" class="mt-4 inline-block text-primary text-sm hover:underline">Télécharger le rapport détaillé →</a>
        </div>

        <!-- Citation / motivation -->
        <div class="layer-lift p-5 bg-primary-fixed/20 border-l-4 border-primary">
            <p class="italic text-sm text-on-surface-variant">“La connaissance est la seule richesse qui augmente quand on la partage.”</p>
            <p class="text-right text-xs text-primary mt-2">— Proverbe africain</p>
        </div>
    </div>
</div>
@endsection