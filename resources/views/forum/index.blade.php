@extends('layouts.app')

@section('title', 'Forum - ' . $course->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6">

    <!-- HEADER avec bouton retour -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5 mb-10 animate-fade-in">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-primary">
                Forum
            </h1>
            <p class="text-sm text-on-surface-variant mt-1">
                {{ $course->title }}
            </p>
        </div>

        <!-- Boutons d'action groupés -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Retour au cours -->
            <a href="{{ route('courses.show', $course) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition">
                <i class="fas fa-arrow-left text-sm"></i>
                <span>Retour au cours</span>
            </a>

            <!-- Nouveau sujet -->
            <a href="{{ route('courses.forum.create', $course) }}" 
               class="group relative inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-medium shadow-md overflow-hidden transition">
                <span class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition"></span>
                <i class="fas fa-plus text-sm group-hover:rotate-90 transition-transform duration-300"></i>
                <span>Nouveau sujet</span>
            </a>
        </div>
    </div>

    <!-- THREAD LIST (identique, non modifié) -->
    <div class="space-y-3">
        @forelse($threads as $thread)
            <div class="group bg-white rounded-xl border border-outline/20 hover:border-primary/40 hover:shadow-md transition">
                <a href="{{ route('courses.forum.show', [$course, $thread]) }}" 
                   class="flex items-center justify-between gap-4 p-4">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <img src="{{ $thread->author->avatar_url }}" 
                             class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                @if($thread->pinned)
                                    <span class="text-[10px] px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">📌 Épinglé</span>
                                @endif
                                @if($thread->locked)
                                    <span class="text-[10px] px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">🔒 Verrouillé</span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-[15px] text-on-surface truncate group-hover:text-primary transition">
                                {{ $thread->title }}
                            </h3>
                            <div class="flex items-center gap-2 text-xs text-on-surface-variant mt-1 flex-wrap">
                                <span class="font-medium text-on-surface">{{ $thread->author->name }}</span>
                                <span>•</span>
                                <span>{{ $thread->created_at->diffForHumans() }}</span>
                                <span>•</span>
                                <span>💬 {{ $thread->posts->count() }}</span>
                                <span>👁 {{ $thread->views }}</span>
                            </div>
                        </div>
                    </div>
                    @if($thread->latestPost)
                        <div class="hidden md:flex flex-col items-end text-xs text-on-surface-variant min-w-[110px]">
                            <span class="text-[10px] uppercase tracking-wide opacity-60">Dernier</span>
                            <span class="font-medium text-on-surface truncate max-w-[100px]">{{ $thread->latestPost->author->name }}</span>
                            <span class="opacity-70">{{ $thread->latestPost->created_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </a>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-primary/10 mb-4">
                    <i class="fas fa-comments text-primary text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold">Aucun sujet</h3>
                <p class="text-sm text-on-surface-variant mt-1">Lance la première discussion 🚀</p>
                <a href="{{ route('courses.forum.create', $course) }}"
                   class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-container transition shadow-sm">
                    <i class="fas fa-plus"></i> Créer un sujet
                </a>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    @if($threads->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $threads->links() }}
        </div>
    @endif
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
</style>
@endsection