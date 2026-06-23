@extends('layouts.app')

@section('title', 'Recherche')

@section('content')
<div class="max-w-4xl mx-auto">

    <h1 class="text-2xl font-display font-bold mb-2">Recherche</h1>

    <form action="{{ route('search') }}" method="GET" class="mb-8">
        <div class="flex gap-3">
            <input type="text" name="q" value="{{ $q }}"
                   placeholder="Rechercher cours, forum, leçons"
                   class="flex-1 px-4 py-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm">
            <button type="submit" class="btn-primary px-6">
                <i class="fas fa-search mr-2"></i> Rechercher
            </button>
        </div>
    </form>

    @if(strlen($q) < 2)
        <div class="text-center py-16 text-on-surface-variant">
            <i class="fas fa-search text-5xl mb-4 opacity-20"></i>
            <p>Entrez au moins 2 caractères pour lancer une recherche.</p>
        </div>
    @else
        @php $total = $courses->count() + $lessons->count() + $threads->count(); @endphp

        <p class="text-sm text-on-surface-variant mb-6">
            <strong>{{ $total }}</strong> résultat(s) pour « {{ $q }} »
        </p>

        {{-- Cours --}}
        @if($courses->count())
        <section class="mb-8">
            <h2 class="text-lg font-display font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-book-open text-primary"></i> Cours ({{ $courses->count() }})
            </h2>
            <div class="space-y-3">
                @foreach($courses as $course)
                <a href="{{ route('courses.show', $course) }}"
                   class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
                    <img src="{{ $course->thumbnail_url }}" class="w-16 h-12 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm line-clamp-1">{{ $course->title }}</p>
                        <p class="text-xs text-on-surface-variant mt-0.5 line-clamp-2">{{ Str::limit($course->description, 120) }}</p>
                    </div>
                    <span class="text-xs bg-primary/10 text-primary rounded-full px-2 py-0.5 whitespace-nowrap">{{ $course->formatted_price }}</span>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Leçons --}}
        @if($lessons->count())
        <section class="mb-8">
            <h2 class="text-lg font-display font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-play-circle text-secondary"></i> Leçons ({{ $lessons->count() }})
            </h2>
            <div class="space-y-3">
                @foreach($lessons as $lesson)
                <a href="{{ route('lessons.show', $lesson) }}"
                   class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
                    <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $lesson->type === 'video' ? 'video' : ($lesson->type === 'text' ? 'file-alt' : 'layer-group') }} text-secondary"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm line-clamp-1">{{ $lesson->title }}</p>
                        @if($lesson->chapter && $lesson->chapter->course)
                            <p class="text-xs text-on-surface-variant mt-0.5">{{ $lesson->chapter->course->title }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Forum --}}
        @if($threads->count())
        <section class="mb-8">
            <h2 class="text-lg font-display font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-comments text-green-600"></i> Forum ({{ $threads->count() }})
            </h2>
            <div class="space-y-3">
                @foreach($threads as $thread)
                <a href="{{ route('courses.forum.show', [$thread->course, $thread]) }}"
                   class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-comments text-green-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm line-clamp-1">{{ $thread->title }}</p>
                        <p class="text-xs text-on-surface-variant mt-0.5">{{ $thread->course->title ?? '' }} • par {{ $thread->author->name ?? '' }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        @if($total === 0)
        <div class="text-center py-16 text-on-surface-variant">
            <i class="fas fa-search text-5xl mb-4 opacity-20"></i>
            <p class="font-medium">Aucun résultat pour « {{ $q }} »</p>
            <p class="text-sm mt-2">Essayez d'autres mots-clés.</p>
        </div>
        @endif
    @endif

</div>
@endsection
