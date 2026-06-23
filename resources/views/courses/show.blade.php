@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête du cours -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-display font-bold tracking-tight">{{ $course->title }}</h1>
                <p class="text-on-surface-variant mt-2 text-lg">{{ $course->description }}</p>
            </div>
            @if(!$isEnrolled && auth()->id() != $course->user_id)
                <form action="{{ route('courses.enroll', $course) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary flex items-center gap-2 whitespace-nowrap">
                        <i class="fas fa-user-plus"></i> S'inscrire ({{ $course->formatted_price }})
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Colonne principale : contenu du cours ou aperçu -->
        <div class="lg:col-span-2 space-y-8">
            @if($isEnrolled)
                <!-- Barre de progression pour les inscrits -->
                <div class="bg-white rounded-2xl shadow-sm p-5 border border-outline/20">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-on-surface-variant">Progression globale</span>
                        <span class="text-sm font-bold text-primary">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-primary h-2.5 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <!-- Contenu complet du cours (chapitres/leçons) -->
                <div>
                    <h2 class="text-2xl font-display font-semibold mb-5 flex items-center gap-2">
                        <i class="fas fa-book-open text-primary"></i> Contenu du cours
                    </h2>
                    <div class="space-y-4">
                        @foreach($course->chapters as $chapter)
                            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-outline/20">
                                <div class="bg-surface-low px-5 py-3 flex justify-between items-center">
                                    <div>
                                        <h3 class="font-display font-semibold text-lg">{{ $chapter->title }}</h3>
                                        @if($chapter->description)
                                            <p class="text-sm text-on-surface-variant">{{ $chapter->description }}</p>
                                        @endif
                                    </div>
                                    <span class="text-xs text-on-surface-variant">{{ $chapter->lessons->count() }} leçons</span>
                                </div>
                                <ul class="divide-y divide-outline/20">
                                    @foreach($chapter->lessons as $lesson)
                                        <li class="flex flex-wrap justify-between items-center gap-3 p-4 hover:bg-surface-low/50 transition">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-play-circle text-primary"></i>
                                                <a href="{{ route('lessons.show', $lesson) }}" class="font-medium hover:text-primary transition">
                                                    {{ $lesson->title }}
                                                </a>
                                                @if($lesson->is_free)
                                                    <span class="text-xs bg-secondary-fixed text-secondary px-2 py-0.5 rounded-full">Gratuit</span>
                                                @endif
                                            </div>
                                            @if($lesson->isCompletedBy(auth()->id()))
                                                <span class="flex items-center gap-1 text-xs text-secondary">
                                                    <i class="fas fa-check-circle"></i> Terminée
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>

                    <!-- Lien vers le forum -->
                    <div class="mt-8 text-center md:text-left">
                        <a href="{{ route('courses.forum.index', $course) }}" class="btn-secondary inline-flex items-center gap-2">
                            <i class="fas fa-comments"></i> Accéder au forum du cours
                        </a>
                    </div>
                </div>
            @else
                <!-- Aperçu pour les non-inscrits -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-outline/20">
                    <h2 class="text-2xl font-display font-semibold mb-4 flex items-center gap-2">
                        <i class="fas fa-graduation-cap text-primary"></i> À propos de ce cours
                    </h2>
                    <p class="text-on-surface-variant leading-relaxed">{{ $course->description }}</p>
                    
                    @if($course->chapters->count())
                        <div class="mt-6">
                            <h3 class="font-display font-semibold text-lg mb-3">Plan du cours</h3>
                            <div class="space-y-3">
                                @foreach($course->chapters as $chapter)
                                    <div class="border-l-2 border-primary/30 pl-4">
                                        <h4 class="font-medium">{{ $chapter->title }}</h4>
                                        <p class="text-xs text-on-surface-variant">{{ $chapter->lessons->count() }} leçons</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Leçons gratuites en aperçu -->
                    @php
                        $freeLessons = $course->lessons->where('is_free', true);
                    @endphp
                    @if($freeLessons->count())
                        <div class="mt-6">
                            <h3 class="font-display font-semibold text-lg mb-3 flex items-center gap-2">
                                <i class="fas fa-eye text-primary"></i> Leçons gratuites (aperçu)
                            </h3>
                            <ul class="space-y-2">
                                @foreach($freeLessons as $lesson)
                                    <li class="flex items-center gap-2 text-sm">
                                        <i class="fas fa-play-circle text-primary/70"></i>
                                        <span>{{ $lesson->title }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Colonne latérale : informations et appel à l'inscription -->
        <div class="space-y-6">
            <!-- Carte d'informations générales -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-outline/20">
                <h3 class="font-display font-semibold text-lg flex items-center gap-2 mb-4">
                    <i class="fas fa-info-circle text-primary"></i> Informations
                </h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex justify-between">
                        <span class="text-on-surface-variant">Niveau</span>
                        <span class="font-medium">
                            @if($course->level == 'beginner') 🌱 Débutant
                            @elseif($course->level == 'intermediate') 📘 Intermédiaire
                            @else 🚀 Avancé @endif
                        </span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-on-surface-variant">Langue</span>
                        <span class="font-medium">{{ strtoupper($course->language) }}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-on-surface-variant">Durée totale</span>
                        <span class="font-medium">{{ $course->duration_minutes }} minutes</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-on-surface-variant">Prix</span>
                        <span class="font-bold text-primary">{{ $course->formatted_price }}</span>
                    </li>
                </ul>
            </div>

            <!-- Carte de l'instructeur -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-outline/20">
                <div class="flex items-center gap-4">
                    <img src="{{ $course->instructor->avatar_url }}" class="w-14 h-14 rounded-full object-cover border-2 border-primary">
                    <div>
                        <h4 class="font-display font-semibold">{{ $course->instructor->name }}</h4>
                        <p class="text-xs text-on-surface-variant">Instructeur</p>
                    </div>
                </div>
                @if($course->instructor->bio)
                    <p class="text-sm text-on-surface-variant mt-4">{{ $course->instructor->bio }}</p>
                @endif
            </div>

            <!-- Message d'incitation à l'inscription (pour non-inscrits) -->
            @if(!$isEnrolled && auth()->id() != $course->user_id)
                <div class="bg-gradient-to-br from-primary/5 to-primary/10 rounded-2xl p-6 text-center border border-primary/20">
                    <i class="fas fa-lock-open text-primary text-3xl mb-3 block"></i>
                    <h3 class="font-display font-semibold text-lg">Accédez au contenu complet</h3>
                    <p class="text-sm text-on-surface-variant mt-1">Inscrivez-vous pour suivre toutes les leçons et passer les quiz.</p>
                    <form action="{{ route('courses.enroll', $course) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-primary w-full">S'inscrire maintenant</button>
                    </form>
                </div>
            @endif

            <!-- Message pour l'instructeur lui-même -->
            @if(auth()->check() && auth()->id() == $course->user_id)
                <div class="bg-amber-50 rounded-2xl p-5 text-center border border-amber-200">
                    <i class="fas fa-chalkboard-teacher text-amber-600 text-2xl mb-2 block"></i>
                    <p class="text-sm text-amber-800">Vous êtes l'instructeur de ce cours. Vous ne pouvez pas vous inscrire.</p>
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="mt-3 inline-block text-primary text-sm hover:underline">Modifier le cours</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection