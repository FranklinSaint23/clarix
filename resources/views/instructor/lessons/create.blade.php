@extends('layouts.app')

@section('title', 'Ajouter une leçon')

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
            <li class="text-primary font-medium">Ajouter une leçon</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="mb-10">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Ajouter une leçon</h1>
        <p class="text-sm text-on-surface-variant mt-2">Créez une nouvelle leçon pour le chapitre <span class="font-medium text-primary">{{ $chapter->title }}</span>.</p>
    </div>

    <!-- Carte formulaire -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm">
        <form action="{{ route('instructor.chapters.lessons.store', $chapter) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
            @csrf

            <!-- Titre -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Titre de la leçon <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="input-field w-full" placeholder="Ex: Introduction à Laravel" required autofocus>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Description (optionnelle)</label>
                <textarea name="description" rows="3" class="input-field w-full resize-none" placeholder="Décrivez brièvement le contenu...">{{ old('description') }}</textarea>
            </div>

            <!-- Type de leçon + champs dynamiques (Alpine.js) -->
            <div x-data="lessonFormHandler()" x-init="initLessonType('{{ old('type', 'video') }}')">
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

                <!-- Partie vidéo -->
                <div x-show="lessonType === 'video' || lessonType === 'mixed'" class="space-y-6 transition">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-2">Fichier vidéo (optionnel)</label>
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
                            <div x-show="videoFile" class="mt-3 p-3 bg-surface-low rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-video text-primary"></i>
                                    <span class="text-sm font-medium truncate" x-text="videoFile.name"></span>
                                    <span class="text-xs text-on-surface-variant" x-text="formatFileSize(videoFile.size)"></span>
                                </div>
                                <button type="button" @click="clearVideo()" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                        <div x-show="videoPreviewUrl" class="mt-3">
                            <p class="text-xs text-on-surface-variant mb-1">Aperçu :</p>
                            <video controls class="w-full max-w-md rounded-xl border shadow-sm" :src="videoPreviewUrl"></video>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-2">URL vidéo (YouTube, Vimeo)</label>
                        <input type="url" name="video_url" value="{{ old('video_url') }}" class="input-field w-full" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                </div>

                <!-- Partie texte avec Quill -->
                <div x-show="lessonType === 'text' || lessonType === 'mixed'" class="transition mt-6">
                    <label class="block text-sm font-semibold text-on-surface mb-2">Contenu de la leçon</label>
                    <div class="border border-outline/20 rounded-xl overflow-hidden">
                        <div id="quill-editor" style="height: 400px;"></div>
                        <textarea name="content" id="content-input" style="display:none;"></textarea>
                    </div>
                    <p class="text-xs text-on-surface-variant mt-2">Utilisez l'éditeur pour structurer votre texte (titres, listes, gras, etc.). Le contenu collé depuis Word ou Google Docs sera automatiquement nettoyé.</p>
                </div>

                <!-- Ressources (fichiers) -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-on-surface mb-2">Ressources (fichiers à télécharger)</label>
                    <div class="relative">
                        <div @dragover.prevent="resourcesDragover = true" @dragleave.prevent="resourcesDragover = false" @drop.prevent="handleResourcesDrop($event)"
                             class="border-2 border-dashed rounded-2xl p-6 text-center transition cursor-pointer"
                             :class="resourcesDragover ? 'border-primary bg-primary/5' : 'border-outline/40 bg-surface-low hover:bg-surface-high'"
                             @click="$refs.resourcesInput.click()">
                            <i class="fas fa-file-alt text-3xl text-on-surface-variant/60 mb-2 block"></i>
                            <p class="text-sm text-on-surface-variant">Glissez-déposez vos fichiers (PDF, DOC, ZIP) ou <span class="text-primary font-medium">parcourez</span></p>
                            <p class="text-xs text-on-surface-variant mt-1">Taille max : 50 Mo par fichier</p>
                            <input type="file" name="resources[]" multiple accept=".pdf,.doc,.docx,.zip,.rar,.txt" class="hidden" x-ref="resourcesInput" @change="handleResourcesSelect($event)">
                        </div>
                        <div x-show="resourcesList.length" class="mt-3 space-y-2">
                            <template x-for="(file, idx) in resourcesList" :key="idx">
                                <div class="p-2 bg-surface-low rounded-xl flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-paperclip text-primary"></i>
                                        <span class="text-sm truncate max-w-[200px]" x-text="file.name"></span>
                                        <span class="text-xs text-on-surface-variant" x-text="formatFileSize(file.size)"></span>
                                    </div>
                                    <button type="button" @click="removeResource(idx)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </template>
                        </div>
                    </div>
                    <p class="text-xs text-on-surface-variant mt-2">Ajoutez des documents, exercices ou fichiers complémentaires.</p>
                </div>
            </div>

            <!-- Durée et Ordre -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Durée (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 0) }}" class="input-field w-full" min="0" step="1" placeholder="15">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Ordre d'affichage</label>
                    <div class="flex items-center gap-4 flex-wrap">
                        <input type="number" name="order" value="{{ old('order', $chapter->lessons->count() + 1) }}" class="input-field w-32" min="1" step="1">
                        <span class="text-sm text-on-surface-variant">Position actuelle : <span class="font-medium text-primary">{{ $chapter->lessons->count() + 1 }}</span></span>
                    </div>
                </div>
            </div>

            <!-- Leçon gratuite -->
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_free" value="1" {{ old('is_free') ? 'checked' : '' }} class="w-5 h-5 text-primary rounded border-outline/30 focus:ring-primary">
                    <span class="text-sm text-on-surface">Leçon gratuite (prévisualisable sans inscription)</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('instructor.courses.edit', $chapter->course) }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-105 transition-all duration-200 shadow-md">
                    <i class="fas fa-save"></i> Créer la leçon
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Styles et scripts pour Quill -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>

<script>
    function lessonFormHandler() {
        return {
            lessonType: 'video',
            videoFile: null,
            videoPreviewUrl: null,
            dragover: false,
            resourcesList: [],
            resourcesDragover: false,

            initLessonType(type) {
                this.lessonType = type;
                // Initialiser Quill une seule fois si le div existe
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
                    alert('Le fichier dépasse 200 Mo.');
                    return;
                }
                this.videoFile = file;
                if (this.videoPreviewUrl) URL.revokeObjectURL(this.videoPreviewUrl);
                this.videoPreviewUrl = URL.createObjectURL(file);
                const dt = new DataTransfer();
                dt.items.add(file);
                document.querySelector('input[name="video_file"]').files = dt.files;
            },
            clearVideo() {
                this.videoFile = null;
                if (this.videoPreviewUrl) URL.revokeObjectURL(this.videoPreviewUrl);
                this.videoPreviewUrl = null;
                document.querySelector('input[name="video_file"]').value = '';
            },
            handleResourcesSelect(event) {
                const files = Array.from(event.target.files);
                this.addResources(files);
            },
            handleResourcesDrop(event) {
                this.resourcesDragover = false;
                const files = Array.from(event.dataTransfer.files);
                this.addResources(files);
            },
            addResources(files) {
                for (let file of files) {
                    if (file.size > 50 * 1024 * 1024) {
                        alert(`Fichier ${file.name} dépasse 50 Mo.`);
                        continue;
                    }
                    if (!this.resourcesList.some(f => f.name === file.name && f.size === file.size)) {
                        this.resourcesList.push(file);
                    }
                }
                this.updateResourcesInput();
            },
            removeResource(index) {
                this.resourcesList.splice(index, 1);
                this.updateResourcesInput();
            },
            updateResourcesInput() {
                const dt = new DataTransfer();
                this.resourcesList.forEach(file => dt.items.add(file));
                document.querySelector('input[name="resources[]"]').files = dt.files;
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
        Alpine.data('lessonFormHandler', lessonFormHandler);
    });
</script>
@endsection