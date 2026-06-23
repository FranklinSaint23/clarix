@extends('layouts.app')

@section('title', 'Modifier une leçon')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

    <!-- Fil d'Ariane -->
    <nav class="mb-8 text-sm text-on-surface-variant">
        <ol class="flex flex-wrap items-center gap-1">
            <li><a href="{{ route('instructor.courses.index') }}" class="hover:text-primary transition">Mes cours</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('instructor.courses.edit', $chapter->course) }}" class="hover:text-primary transition">{{ Str::limit($chapter->course->title, 30) }}</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('instructor.courses.edit', $chapter->course) }}" class="hover:text-primary transition">{{ $chapter->title }}</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li class="text-primary font-medium">Modifier la leçon</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="mb-10">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Modifier la leçon</h1>
        <p class="text-sm text-on-surface-variant mt-2">Mettez à jour le contenu de <span class="font-medium text-primary">{{ $lesson->title }}</span>.</p>
    </div>

    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm">
        <form action="{{ route('instructor.chapters.lessons.update', [$chapter, $lesson]) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
            @csrf @method('PUT')

            <!-- Titre -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $lesson->title) }}" class="input-field w-full" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Description (optionnelle)</label>
                <textarea name="description" rows="3" class="input-field w-full resize-none">{{ old('description', $lesson->description) }}</textarea>
            </div>

            <!-- Zone Alpine -->
            <div x-data="lessonEditHandler({{ Js::from($lesson->video_path) }}, {{ Js::from($lesson->video_url) }}, {{ Js::from($lesson->content) }}, {{ Js::from($lesson->resources) }})" x-init="initLessonType('{{ old('type', $lesson->type) }}')">
                <!-- Type de leçon -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-on-surface mb-2">Type de leçon</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="video" x-model="lessonType" class="text-primary">
                            <span>📹 Vidéo seule</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="text" x-model="lessonType" class="text-primary">
                            <span>📝 Texte seul</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="mixed" x-model="lessonType" class="text-primary">
                            <span>📹 + 📝 Texte et vidéo</span>
                        </label>
                    </div>
                </div>

                <!-- Partie vidéo (inchangée) -->
                <div x-show="lessonType === 'video' || lessonType === 'mixed'" class="space-y-6 transition">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-2">Nouveau fichier vidéo (optionnel)</label>
                        <div class="relative">
                            <div @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleVideoDrop($event)"
                                 class="border-2 border-dashed rounded-2xl p-6 text-center transition cursor-pointer"
                                 :class="dragover ? 'border-primary bg-primary/5' : 'border-outline/40 bg-surface-low hover:bg-surface-high'"
                                 @click="$refs.videoInput.click()">
                                <i class="fas fa-cloud-upload-alt text-3xl text-on-surface-variant/60 mb-2 block"></i>
                                <p class="text-sm text-on-surface-variant">Glissez-déposez votre vidéo ici ou <span class="text-primary font-medium">parcourez</span></p>
                                <p class="text-xs text-on-surface-variant mt-1">MP4, MOV, AVI (max 200 Mo)</p>
                                <input type="file" name="video_file" accept="video/*" class="hidden" x-ref="videoInput" @change="handleVideoSelect($event)">
                            </div>
                            <template x-if="videoFile">
                                <div class="mt-3 p-3 bg-surface-low rounded-xl flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-video text-primary"></i>
                                        <span class="text-sm font-medium truncate" x-text="videoFile.name"></span>
                                        <span class="text-xs text-on-surface-variant" x-text="formatFileSize(videoFile.size)"></span>
                                    </div>
                                    <button type="button" @click="clearVideo()" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </template>
                        </div>
                        <template x-if="videoPreviewUrl">
                            <div class="mt-3">
                                <p class="text-xs text-on-surface-variant mb-1">Aperçu nouvelle vidéo :</p>
                                <video controls class="w-full max-w-md rounded-xl border shadow-sm">
                                    <source :src="videoPreviewUrl" type="video/mp4">
                                </video>
                            </div>
                        </template>
                        <template x-if="!videoFile && existingVideoPath">
                            <div class="mt-3 p-3 bg-surface-low rounded-xl">
                                <p class="text-xs text-on-surface-variant mb-1">Vidéo actuelle :</p>
                                <video controls class="w-full max-w-md rounded-xl border shadow-sm">
                                    <source :src="existingVideoPath" type="video/mp4">
                                </video>
                            </div>
                        </template>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-2">URL vidéo</label>
                        <input type="url" name="video_url" x-model="videoUrl" class="input-field w-full" placeholder="https://...">
                    </div>
                </div>

                <!-- Partie texte avec Quill -->
                <div x-show="lessonType === 'text' || lessonType === 'mixed'" class="transition mt-6">
                    <label class="block text-sm font-semibold text-on-surface mb-2">Contenu de la leçon</label>
                    <div class="border border-outline/20 rounded-xl overflow-hidden">
                        <div id="quill-editor" style="height: 400px;"></div>
                        <textarea name="content" id="content-input" style="display:none;">{{ old('content', $lesson->content) }}</textarea>
                    </div>
                    <p class="text-xs text-on-surface-variant mt-2">Utilisez l'éditeur pour structurer votre texte (titres, listes, gras, etc.). Le contenu collé depuis Word ou Google Docs sera automatiquement nettoyé.</p>
                </div>

                <!-- Ressources existantes et ajout de nouvelles (inchangées) -->
                <template x-if="existingResources.length > 0">
                    <div class="mt-6">
                        <label class="block text-sm font-semibold text-on-surface mb-2">Ressources actuelles</label>
                        <div class="space-y-2">
                            <template x-for="(res, idx) in existingResources" :key="res.id">
                                <div class="p-2 bg-surface-low rounded-xl flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-paperclip text-primary"></i>
                                        <span x-text="res.title"></span>
                                        <a :href="res.url" target="_blank" class="text-primary text-sm hover:underline">Télécharger</a>
                                    </div>
                                    <button type="button" @click="removeExistingResource(idx, res.id)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="mt-6">
                    <label class="block text-sm font-semibold text-on-surface mb-2">Ajouter des ressources</label>
                    <div class="relative">
                        <div @dragover.prevent="resourcesDragover = true" @dragleave.prevent="resourcesDragover = false" @drop.prevent="handleResourcesDrop($event)"
                             class="border-2 border-dashed rounded-2xl p-6 text-center transition cursor-pointer"
                             :class="resourcesDragover ? 'border-primary bg-primary/5' : 'border-outline/40 bg-surface-low hover:bg-surface-high'"
                             @click="$refs.resourcesInput.click()">
                            <i class="fas fa-file-upload text-3xl text-on-surface-variant/60 mb-2 block"></i>
                            <p class="text-sm text-on-surface-variant">Glissez-déposez ou <span class="text-primary font-medium">parcourez</span></p>
                            <p class="text-xs text-on-surface-variant mt-1">PDF, DOC, ZIP (max 50 Mo par fichier)</p>
                            <input type="file" name="new_resources[]" multiple accept=".pdf,.doc,.docx,.zip,.rar,.txt" class="hidden" x-ref="resourcesInput" @change="handleResourcesSelect($event)">
                        </div>
                        <template x-if="resourcesList.length">
                            <div class="mt-3 space-y-2">
                                <template x-for="(file, idx) in resourcesList" :key="idx">
                                    <div class="p-2 bg-surface-low rounded-xl flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-paperclip text-primary"></i>
                                            <span class="text-sm truncate max-w-[200px]" x-text="file.name"></span>
                                            <span class="text-xs text-on-surface-variant" x-text="formatFileSize(file.size)"></span>
                                        </div>
                                        <button type="button" @click="removeNewResource(idx)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Durée et Ordre -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Durée (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" class="input-field w-full" min="0" step="1">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Ordre d'affichage</label>
                    <div class="flex items-center gap-4 flex-wrap">
                        <input type="number" name="order" value="{{ old('order', $lesson->order) }}" class="input-field w-32" min="1" step="1">
                        <span class="text-sm text-on-surface-variant">Position actuelle : <span class="font-medium text-primary">{{ $lesson->order }}</span></span>
                    </div>
                </div>
            </div>

            <!-- Leçon gratuite -->
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_free" value="1" {{ old('is_free', $lesson->is_free) ? 'checked' : '' }} class="w-5 h-5 text-primary rounded border-outline/30 focus:ring-primary">
                    <span class="text-sm text-on-surface">Leçon gratuite (prévisualisable sans inscription)</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('instructor.courses.edit', $chapter->course) }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-105 transition-all duration-200 shadow-md">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Styles et scripts Quill -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>

<script>
    function lessonEditHandler(existingVideoPath, existingVideoUrl, existingContent, existingResourcesCollection) {
        return {
            lessonType: 'video',
            videoFile: null,
            videoPreviewUrl: null,
            dragover: false,
            existingVideoPath: existingVideoPath ? '/storage/' + existingVideoPath : null,
            videoUrl: existingVideoUrl || '',
            existingResources: existingResourcesCollection || [],
            resourcesList: [],
            resourcesDragover: false,
            deletedResources: [],

            initLessonType(type) {
                this.lessonType = type;
                // Initialiser Quill une seule fois (si pas déjà fait)
                if (!window.quill && document.getElementById('quill-editor')) {
                    window.quill = new Quill('#quill-editor', {
                        theme: 'snow',
                        placeholder: 'Rédigez votre contenu...',
                        modules: {
                            toolbar: [
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline'],
                                ['link', 'blockquote', 'code-block'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                ['clean']
                            ]
                        }
                    });
                    // Charger le contenu existant
                    const existing = document.getElementById('content-input').value;
                    if (existing) {
                        window.quill.root.innerHTML = existing;
                    }
                    // Synchronisation avant soumission
                    const form = document.querySelector('form');
                    const hiddenInput = document.getElementById('content-input');
                    form.addEventListener('submit', () => {
                        hiddenInput.value = window.quill.root.innerHTML;
                    });
                }
            },
            handleVideoSelect(event) {
                this.processVideoFile(event.target.files[0]);
            },
            handleVideoDrop(event) {
                this.dragover = false;
                this.processVideoFile(event.dataTransfer.files[0]);
            },
            processVideoFile(file) {
                if (!file) return;
                if (!file.type.startsWith('video/')) {
                    alert('Veuillez sélectionner un fichier vidéo.');
                    return;
                }
                if (file.size > 200 * 1024 * 1024) {
                    alert('Fichier trop volumineux (max 200 Mo).');
                    return;
                }
                this.videoFile = file;
                if (this.videoPreviewUrl) URL.revokeObjectURL(this.videoPreviewUrl);
                this.videoPreviewUrl = URL.createObjectURL(file);
                const dt = new DataTransfer();
                dt.items.add(file);
                document.querySelector('input[name="video_file"]').files = dt.files;
                this.videoUrl = '';
            },
            clearVideo() {
                this.videoFile = null;
                if (this.videoPreviewUrl) URL.revokeObjectURL(this.videoPreviewUrl);
                this.videoPreviewUrl = null;
                document.querySelector('input[name="video_file"]').value = '';
            },
            handleResourcesSelect(event) {
                this.addNewResources(Array.from(event.target.files));
            },
            handleResourcesDrop(event) {
                this.resourcesDragover = false;
                this.addNewResources(Array.from(event.dataTransfer.files));
            },
            addNewResources(files) {
                for (let file of files) {
                    if (file.size > 50 * 1024 * 1024) {
                        alert(`${file.name} dépasse 50 Mo.`);
                        continue;
                    }
                    if (!this.resourcesList.some(f => f.name === file.name && f.size === file.size)) {
                        this.resourcesList.push(file);
                    }
                }
                this.updateNewResourcesInput();
            },
            removeNewResource(index) {
                this.resourcesList.splice(index, 1);
                this.updateNewResourcesInput();
            },
            updateNewResourcesInput() {
                const dt = new DataTransfer();
                this.resourcesList.forEach(file => dt.items.add(file));
                document.querySelector('input[name="new_resources[]"]').files = dt.files;
            },
            removeExistingResource(idx, id) {
                this.existingResources.splice(idx, 1);
                this.deletedResources.push(id);
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_resources[]';
                input.value = id;
                document.querySelector('form').appendChild(input);
            },
            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'Ko', 'Mo', 'Go'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        };
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('lessonEditHandler', lessonEditHandler);
    });
</script>
@endsection