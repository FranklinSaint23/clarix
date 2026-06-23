@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-display font-bold mb-4">{{ $lesson->title }}</h1>
    <p class="text-on-surface-variant mb-6">{{ $lesson->description }}</p>

    {{-- Vidéo --}}
    @if(in_array($lesson->type, ['video', 'mixed']) && ($lesson->video_path || $lesson->video_url))
        <div class="aspect-video rounded-2xl overflow-hidden shadow-lg mb-6 bg-black">
            @if($lesson->video_path)
                <video controls class="w-full h-full object-contain">
                    <source src="{{ asset('storage/'.$lesson->video_path) }}" type="video/mp4">
                    Votre navigateur ne supporte pas la vidéo.
                </video>
            @elseif($lesson->video_url)
                @php
                    $videoId = null;
                    $url = $lesson->video_url;
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
                        $videoId = $matches[1];
                    } elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                        $videoId = $matches[1];
                    }
                    $embedUrl = $videoId ? "https://www.youtube-nocookie.com/embed/{$videoId}" : null;
                @endphp
                @if($embedUrl)
                    <iframe class="w-full h-full" src="{{ $embedUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                @else
                    <p class="text-red-600 p-4">Lien vidéo invalide.</p>
                @endif
            @endif
        </div>
    @endif

    {{-- Contenu texte structuré --}}
    @if(in_array($lesson->type, ['text', 'mixed']) && $lesson->content)
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 lesson-content">
            {!! $lesson->content !!}
        </div>
    @endif

    {{-- Quiz --}}
    @if($lesson->quiz)
        <div class="bg-primary-fixed/20 rounded-2xl p-5 mb-6">
            <div class="flex flex-wrap justify-between items-center gap-3">
                <div>
                    <p class="font-semibold">Quiz disponible</p>
                    <p class="text-sm text-on-surface-variant">Testez vos connaissances sur cette leçon.</p>
                </div>
                <a href="{{ route('quizzes.take', $lesson->quiz) }}" class="btn-primary py-2 px-5 text-sm">Passer le quiz</a>
            </div>
        </div>
    @endif

    {{-- Ressources --}}
    @if($lesson->resources->count())
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">
            <h3 class="font-display font-semibold mb-3">Ressources</h3>
            <ul class="divide-y divide-outline/20">
                @foreach($lesson->resources as $resource)
                    <li class="py-2 flex justify-between items-center">
                        <span>{{ $resource->title }}</span>
                        <a href="{{ $resource->url }}" class="text-primary hover:underline text-sm" target="_blank">
                            Télécharger ({{ $resource->formatted_size }})
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Marquer terminée --}}
    <form action="{{ route('lessons.complete', $lesson) }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary w-full md:w-auto">
            <i class="fas fa-check-circle mr-2"></i> Marquer comme terminée
        </button>
    </form>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     ASSISTANT IA — Section sous le contenu de la leçon
════════════════════════════════════════════════════════════════ --}}
<div class="max-w-4xl mx-auto mt-10"
     x-data="aiAssistant()"
     x-init="init()">

    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100 overflow-hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-blue-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <div>
                    <h3 class="font-display font-semibold text-sm">Assistant IA CLARIX</h3>
                    <p class="text-xs text-on-surface-variant">Propulsé par Groq · Contexte : cette leçon uniquement</p>
                </div>
            </div>
            <button @click="open = !open" class="text-on-surface-variant hover:text-primary text-sm">
                <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas"></i>
            </button>
        </div>

        <!-- Corps -->
        <div x-show="open" x-transition>

            <!-- Boutons d'action -->
            <div class="px-6 py-4 flex flex-wrap gap-3 border-b border-blue-100">
                <button @click="action('summarize')"
                        :disabled="loading"
                        class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg bg-white border border-blue-200 hover:border-primary hover:text-primary transition disabled:opacity-50">
                    <i class="fas fa-list-ul text-primary"></i> Résumer le chapitre
                </button>
                <button @click="action('explain')"
                        :disabled="loading"
                        class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg bg-white border border-blue-200 hover:border-primary hover:text-primary transition disabled:opacity-50">
                    <i class="fas fa-lightbulb text-yellow-500"></i> Expliquer simplement
                </button>
                <button @click="action('questions')"
                        :disabled="loading"
                        class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg bg-white border border-blue-200 hover:border-primary hover:text-primary transition disabled:opacity-50">
                    <i class="fas fa-question-circle text-green-600"></i> Questions de révision
                </button>
                <button @click="mode = mode === 'chat' ? 'result' : 'chat'"
                        class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg bg-primary text-white hover:bg-primary/90 transition">
                    <i class="fas fa-comments"></i> Chatbot IA
                </button>
            </div>

            <!-- Zone de résultat (résumé / explication / questions) -->
            <div x-show="mode === 'result'" class="px-6 py-4">
                <div x-show="loading" class="flex items-center gap-3 text-sm text-on-surface-variant">
                    <div class="w-5 h-5 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                    <span>L'IA analyse la leçon…</span>
                </div>
                <div x-show="!loading && result"
                     class="bg-white rounded-xl p-4 text-sm text-on-surface leading-relaxed whitespace-pre-wrap"
                     x-text="result"></div>
                <div x-show="!loading && error"
                     class="bg-red-50 text-red-700 rounded-xl p-4 text-sm"
                     x-text="error"></div>
            </div>

            <!-- Zone de chat -->
            <div x-show="mode === 'chat'" class="px-6 py-4">

                <!-- Messages -->
                <div class="bg-white rounded-xl p-3 h-64 overflow-y-auto flex flex-col gap-3 mb-3" id="chatBox">
                    <template x-if="messages.length === 0">
                        <div class="flex items-center gap-2 text-sm text-on-surface-variant m-auto">
                            <i class="fas fa-robot text-primary"></i>
                            <span>Posez-moi une question sur cette leçon !</span>
                        </div>
                    </template>
                    <template x-for="(msg, i) in messages" :key="i">
                        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.role === 'user'
                                ? 'bg-primary text-white rounded-2xl rounded-tr-sm px-4 py-2 text-sm max-w-xs'
                                : 'bg-gray-100 text-on-surface rounded-2xl rounded-tl-sm px-4 py-2 text-sm max-w-sm whitespace-pre-wrap'"
                                 x-text="msg.content">
                            </div>
                        </div>
                    </template>
                    <div x-show="loading" class="flex justify-start">
                        <div class="bg-gray-100 rounded-2xl px-4 py-2 flex gap-1">
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0s"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:.15s"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:.3s"></span>
                        </div>
                    </div>
                </div>

                <!-- Saisie -->
                <div class="flex gap-2">
                    <input x-model="chatInput"
                           @keydown.enter.prevent="sendChat()"
                           :disabled="loading"
                           type="text"
                           placeholder="Votre question…"
                           class="flex-1 px-4 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:border-primary">
                    <button @click="sendChat()"
                            :disabled="loading || !chatInput.trim()"
                            class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-primary/90 disabled:opacity-50 transition">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function aiAssistant() {
    return {
        open: true,
        mode: 'result',
        loading: false,
        result: '',
        error: '',
        messages: [],
        chatInput: '',
        lessonContent: '',

        init() {
            const el = document.querySelector('.lesson-content');
            const domText = el ? el.innerText.trim() : '';
            // Fallback PHP si la leçon est vidéo sans texte
            const phpContent = @json(trim(strip_tags(($lesson->content ?? '') . ' ' . $lesson->title . ' ' . ($lesson->description ?? ''))));
            this.lessonContent = domText || phpContent;
        },

        async action(type) {
            this.mode = 'result';
            this.result = '';
            this.error = '';
            this.loading = true;

            const endpoints = {
                summarize: '{{ route('ai.summarize') }}',
                explain:   '{{ route('ai.explain') }}',
                questions: '{{ route('ai.questions') }}',
            };

            try {
                const res = await fetch(endpoints[type], {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ content: this.lessonContent }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erreur');
                this.result = data.result;
            } catch (e) {
                this.error = 'Erreur : ' + e.message;
            } finally {
                this.loading = false;
            }
        },

        async sendChat() {
            if (!this.chatInput.trim()) return;
            const userMsg = this.chatInput.trim();
            this.chatInput = '';
            this.messages.push({ role: 'user', content: userMsg });
            this.loading = true;
            this.$nextTick(() => {
                const box = document.getElementById('chatBox');
                if (box) box.scrollTop = box.scrollHeight;
            });

            try {
                const res = await fetch('{{ route('ai.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        content: this.lessonContent,
                        messages: this.messages,
                    }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erreur');
                this.messages.push({ role: 'assistant', content: data.result });
            } catch (e) {
                this.messages.push({ role: 'assistant', content: '❌ ' + e.message });
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    const box = document.getElementById('chatBox');
                    if (box) box.scrollTop = box.scrollHeight;
                });
            }
        }
    };
}
</script>
@endpush

{{-- Styles CSS pour structurer le contenu HTML (fallback si Typography non installé) --}}
<style>
    .lesson-content {
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
        color: #191c1e;
    }
    .lesson-content h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-family: 'Manrope', sans-serif;
    }
    .lesson-content h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
        font-family: 'Manrope', sans-serif;
    }
    .lesson-content h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }
    .lesson-content p {
        margin-bottom: 1rem;
    }
    .lesson-content ul, .lesson-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .lesson-content li {
        margin-bottom: 0.25rem;
    }
    .lesson-content pre {
        background-color: #f3f4f6;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-bottom: 1rem;
        font-family: monospace;
        font-size: 0.875rem;
    }
    .lesson-content code {
        background-color: #eef0f4;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-family: monospace;
        font-size: 0.875rem;
    }
    .lesson-content hr {
        margin: 1.5rem 0;
        border-color: #d1d4e0;
    }
    .lesson-content blockquote {
        border-left: 4px solid #0056d2;
        padding-left: 1rem;
        color: #424654;
        margin: 1rem 0;
    }
    .lesson-content strong {
        font-weight: 600;
    }
</style>
@endsection