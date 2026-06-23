@php
    $isOwn = auth()->id() == $post->user_id;
    $canDelete = $isOwn || auth()->user()->isAdmin() || auth()->user()->isInstructor();
@endphp
<div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} group" id="post-{{ $post->id }}" data-post-id="{{ $post->id }}">
    @if(!$isOwn)
        <img src="{{ $post->author->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-1 mr-3">
    @endif
    <div class="{{ $isOwn ? 'max-w-[85%] md:max-w-[75%]' : 'max-w-[85%] md:max-w-[75%]' }}">
        <div class="rounded-2xl px-5 py-3 shadow-sm {{ $isOwn ? 'bg-primary-fixed/20 border border-primary/30' : 'bg-white border border-outline/20' }}">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="font-semibold text-sm">{{ $post->author->name }}</span>
                @if($post->author->role === 'instructor')
                    <span class="text-[10px] bg-primary-fixed text-primary px-1.5 py-0.5 rounded-full">Instructeur</span>
                @endif
                @if($post->is_solution)
                    <span class="text-[10px] bg-secondary-fixed text-secondary px-1.5 py-0.5 rounded-full">
                        <i class="fas fa-check-circle"></i> Solution
                    </span>
                @endif
                <span class="text-xs text-on-surface-variant ml-auto">{{ $post->created_at->format('d M Y, H:i') }}</span>
            </div>
            <p class="text-sm text-on-surface whitespace-pre-line break-words">{{ $post->body }}</p>
            <div class="flex items-center justify-between mt-2 text-xs text-on-surface-variant">
                <div class="flex items-center gap-4">
                    @if(!$thread->locked)
                        @if($thread->user_id == auth()->id() && !$post->is_solution && $post->user_id != auth()->id())
                            <form action="{{ route('courses.forum.posts.solution', [$course, $post]) }}" method="POST" class="inline solution-form">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-secondary hover:underline">✓ Solution</button>
                            </form>
                        @endif
                        <button onclick="replyTo('{{ $post->author->name }}')" class="text-primary hover:underline">Répondre</button>
                    @endif
                    @if($post->likes > 0)
                        <span><i class="fas fa-heart text-red-400"></i> {{ $post->likes }}</span>
                    @endif
                </div>
                @if($canDelete)
                    <button class="delete-post text-red-500 hover:text-red-700 transition text-xs" data-post-id="{{ $post->id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                @endif
            </div>
        </div>
        @if($isOwn)
            <div class="text-right text-xs text-on-surface-variant mt-1 opacity-0 group-hover:opacity-100 transition">
                Vous
            </div>
        @endif
    </div>
    @if($isOwn)
        <img src="{{ $post->author->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-1 ml-3">
    @endif
</div>