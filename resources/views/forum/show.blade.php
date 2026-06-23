@extends('layouts.app')

@section('title', $thread->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Fil d'Ariane -->
    <div class="mb-6 text-sm text-on-surface-variant">
        <a href="{{ route('courses.forum.index', $course) }}" class="hover:text-primary transition inline-flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs"></i> Retour au forum
        </a>
    </div>

   <!-- SUJET PRINCIPAL AMÉLIORÉ -->
    <div class="relative mb-10">

        <!-- Glow léger -->
        <div class="absolute -inset-1 bg-gradient-to-r from-primary/20 to-secondary/20 blur-xl opacity-30 rounded-3xl"></div>

        <div class="relative bg-white border border-outline/20 rounded-3xl shadow-md overflow-hidden">

            <!-- HEADER -->
            <div class="p-5 md:p-6 border-b border-outline/10 space-y-4">

                <!-- BADGES -->
                <div class="flex items-center gap-2">
                    @if($thread->pinned)
                        <span class="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                            📌 Épinglé
                        </span>
                    @endif

                    @if($thread->locked)
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">
                            🔒 Verrouillé
                        </span>
                    @endif
                </div>

                <!-- TITLE -->
                <h1 class="text-xl md:text-2xl font-bold leading-snug text-on-surface">
                    {{ $thread->title }}
                </h1>

                <!-- META -->
                <div class="flex flex-wrap items-center justify-between gap-4">

                    <!-- USER -->
                    <div class="flex items-center gap-3">

                        <!-- AVATAR PARFAITEMENT ROND -->
                        <div class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-primary/20">
                            <img src="{{ $thread->author->avatar_url }}"
                                class="w-full h-full object-cover">
                        </div>

                        <div class="leading-tight">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-semibold text-on-surface">
                                    {{ $thread->author->name }}
                                </span>

                                @if($thread->author->role === 'instructor')
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-primary/10 text-primary">
                                        Pro
                                    </span>
                                @endif
                            </div>

                            <span class="text-xs text-gray-500">
                                {{ $thread->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <!-- STATS -->
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            👁 <span class="font-medium text-gray-700">{{ $thread->views }}</span>
                        </span>

                        <span class="flex items-center gap-1">
                            💬 <span class="font-medium text-gray-700 reply-count">{{ $thread->posts->count() }}</span>
                        </span>
                    </div>

                </div>
            </div>

            <!-- BODY -->
            <div class="px-5 md:px-6 py-5 text-sm md:text-[15px] leading-relaxed text-gray-700 bg-white">
                <p class="whitespace-pre-line">
                    {{ $thread->body }}
                </p>
            </div>

            <!-- FOOTER -->
            <div class="px-5 md:px-6 py-3 bg-gray-50 border-t border-outline/10 flex justify-end">

                @if(!$thread->locked)
                    <button onclick="replyTo('{{ $thread->author->name }}')" 
                        class="text-xs flex items-center gap-1 text-primary font-medium hover:opacity-80 transition">
                        ↩ Répondre
                    </button>
                @endif

            </div>
        </div>
    </div>

    <!-- Zone des réponses (conversation) -->
    <div id="posts-container" class="space-y-5 mb-8">
        @foreach($posts as $post)
            @include('forum._partials.post', ['post' => $post, 'thread' => $thread, 'course' => $course])
        @endforeach
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $posts->links() }}
        </div>
    @endif

    <!-- Zone de réponse (formulaire AJAX, collée en bas) -->
    @if(!$thread->locked)
        <div class="sticky bottom-4 bg-white rounded-2xl shadow-lg border border-outline/20 p-4 backdrop-blur-sm bg-white/95 mt-6">
            <form id="reply-form" data-thread-id="{{ $thread->id }}">
                @csrf
                <div class="flex items-start gap-3">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-1">
                    <div class="flex-1">
                        <textarea name="body" id="reply-body" rows="2" class="input-field w-full text-sm resize-none py-2" placeholder="Écrivez votre réponse..." required></textarea>
                        <div class="flex justify-end mt-2">
                            <button type="submit" class="btn-primary py-2 px-5 rounded-xl flex items-center gap-2 text-sm">
                                <i class="fas fa-paper-plane"></i> Envoyer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="text-center text-sm text-on-surface-variant py-4 mt-6 border-t border-outline/20">
            <i class="fas fa-lock"></i> Sujet verrouillé – plus de réponses possibles.
        </div>
    @endif
</div>

<x-modal />

<script>
    const courseId = {{ $course->id }};

    function confirmAction(message, confirmText = 'Supprimer', cancelText = 'Annuler') {
        return new Promise((resolve) => {
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: {
                    type: 'confirm',
                    message: message,
                    resolve: resolve,
                    confirmText: confirmText,
                    cancelText: cancelText
                }
            }));
        });
    }

    function showError(message) {
        window.dispatchEvent(new CustomEvent('open-modal', {
            detail: {
                type: 'error',
                message: message,
                resolve: null,
                confirmText: 'OK'
            }
        }));
    }

    // Envoi de réponse (AJAX)
    document.getElementById('reply-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const textarea = document.getElementById('reply-body');
        const body = textarea.value.trim();
        if (!body) return;

        const threadId = form.dataset.threadId;
        const url = `/cours/${courseId}/forum/${threadId}/repondre`;

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ body })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const container = document.getElementById('posts-container');
                container.insertAdjacentHTML('beforeend', data.html);
                textarea.value = '';
                textarea.style.height = 'auto';
                const countSpan = document.querySelector('.reply-count');
                if (countSpan) countSpan.innerText = parseInt(countSpan.innerText) + 1;
                container.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'start' });
                attachDeleteEvents();
            } else {
                showError(data.message || 'Une erreur est survenue.');
            }
        } catch (error) {
            console.error(error);
            showError('Erreur réseau. Veuillez réessayer.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Auto-resize
    const replyBody = document.getElementById('reply-body');
    if (replyBody) {
        replyBody.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
        });
    }

    window.replyTo = function(username) {
        const textarea = document.getElementById('reply-body');
        textarea.value = `@${username} ` + textarea.value;
        textarea.focus();
        textarea.dispatchEvent(new Event('input'));
    };

    // Suppression de message (AJAX)
    async function attachDeleteEvents() {
        document.querySelectorAll('.delete-post').forEach(btn => {
            if (btn.dataset.listener) return;
            btn.dataset.listener = 'true';
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const postId = btn.dataset.postId;
                const postDiv = document.getElementById(`post-${postId}`);
                if (!postDiv) return;

                const confirmed = await confirmAction('Supprimer ce message ?');
                if (!confirmed) return;

                const url = `/cours/${courseId}/forum/posts/${postId}`;

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        postDiv.remove();
                        const countSpan = document.querySelector('.reply-count');
                        if (countSpan) countSpan.innerText = parseInt(countSpan.innerText) - 1;
                    } else {
                        showError('Erreur lors de la suppression.');
                    }
                } catch (error) {
                    console.error(error);
                    showError('Erreur réseau.');
                }
            });
        });
    }

    attachDeleteEvents();
</script>
@endsection