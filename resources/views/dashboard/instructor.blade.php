@extends('layouts.app')

@section('title', 'Espace instructeur')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 space-y-4 sm:space-y-6 pb-12">

    <!-- EN-TÊTE -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-4">
        <div>
            <h1 class="text-2xl sm:text-2xl md:text-3xl font-display font-bold tracking-tight">
                Bonjour, {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5 sm:mt-1">Voici ce qui se passe dans votre espace instructeur.</p>
        </div>
    </div>

    <!-- CARTES STATISTIQUES -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">

        <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-5 shadow-sm border border-outline/20 hover:shadow-md transition">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <p class="text-[9px] sm:text-xs font-medium text-on-surface-variant uppercase tracking-wide truncate">Cours</p>
                    <p class="text-xl sm:text-3xl font-bold text-primary mt-1 leading-tight">{{ $totalCourses }}</p>
                    <p class="text-[9px] sm:text-xs text-primary/70 mt-0.5">{{ $publishedCourses }} publiés</p>
                </div>
                <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chalkboard text-primary text-[10px] sm:text-base"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-5 shadow-sm border border-outline/20 hover:shadow-md transition">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <p class="text-[9px] sm:text-xs font-medium text-on-surface-variant uppercase tracking-wide truncate">Étudiants</p>
                    <p class="text-xl sm:text-3xl font-bold text-blue-600 mt-1 leading-tight">{{ $totalStudents }}</p>
                </div>
                <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-blue-600 text-[10px] sm:text-base"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-5 shadow-sm border border-outline/20 hover:shadow-md transition">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <p class="text-[9px] sm:text-xs font-medium text-on-surface-variant uppercase tracking-wide truncate">Revenus</p>
                    <p class="text-base sm:text-2xl font-bold text-green-600 mt-1 leading-tight">
                        {{ number_format($totalRevenue, 0, ',', ' ') }}
                        <span class="text-[9px] sm:text-xs font-normal">FCFA</span>
                    </p>
                </div>
                <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-money-bill-wave text-green-600 text-[10px] sm:text-base"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-5 shadow-sm border border-outline/20 hover:shadow-md transition">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <p class="text-[9px] sm:text-xs font-medium text-on-surface-variant uppercase tracking-wide truncate">Progression</p>
                    <p class="text-xl sm:text-3xl font-bold text-purple-600 mt-1 leading-tight">{{ $averageProgress }}%</p>
                </div>
                <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-line text-purple-600 text-[10px] sm:text-base"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- DERNIERS INSCRITS -->
    <div class="bg-white rounded-xl sm:rounded-2xl border border-outline/20 overflow-hidden shadow-sm">
        <div class="px-3 sm:px-5 py-2 sm:py-4 border-b border-outline/20 bg-surface-low/30">
            <h2 class="text-md sm:text-sm md:text-base font-display font-semibold flex items-center gap-2">
                <i class="fas fa-user-plus text-primary text-xs sm:text-sm"></i> Derniers inscrits
            </h2>
        </div>
        @if($recentEnrollments->count())
            <div class="divide-y divide-outline/20">
                @foreach($recentEnrollments as $enrollment)
                    <div class="flex items-center justify-between px-3 sm:px-5 py-2 sm:py-4 hover:bg-surface-low/50 transition">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <img src="{{ $enrollment->user->avatar_url }}"
                                 class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover ring-2 ring-primary/20 flex-shrink-0">
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-medium truncate">{{ $enrollment->user->name }}</p>
                                <p class="text-[10px] sm:text-xs text-on-surface-variant truncate">{{ $enrollment->course->title }}</p>
                            </div>
                        </div>
                        <span class="text-[9px] sm:text-xs text-on-surface-variant whitespace-nowrap ml-2 flex-shrink-0">
                            {{ $enrollment->created_at->diffForHumans() }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-3 sm:px-5 py-8 text-center text-xs sm:text-sm text-on-surface-variant">Aucun inscrit pour le moment.</div>
        @endif
    </div>

    <!-- TABLEAU DES COURS -->
    <div class="bg-white rounded-xl sm:rounded-2xl border border-outline/20 overflow-hidden shadow-sm">
        <div class="flex justify-between items-center px-3 sm:px-5 py-2 sm:py-4 border-b border-outline/20 bg-surface-low/30">
            <h2 class="text-md sm:text-sm md:text-base font-display font-semibold flex items-center gap-2">
                <i class="fas fa-book-open text-primary text-xs sm:text-sm"></i> Mes formations
            </h2>
            <a href="{{ route('instructor.courses.index') }}"
               class="inline-flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 sm:py-1.5 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition text-[10px] sm:text-xs md:text-sm font-medium">
                <i class="fas fa-plus-circle text-[10px] sm:text-xs"></i>
                
                <span class="inline">Mes cours</span>
            </a>
        </div>

        {{-- Tableau visible md+ --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-low text-on-surface-variant text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Cours</th>
                        <th class="px-5 py-3 text-center font-medium">Étudiants</th>
                        <th class="px-5 py-3 text-center font-medium">Progression</th>
                        <th class="px-5 py-3 text-center font-medium">Revenu</th>
                        <th class="px-5 py-3 text-center font-medium">Statut</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline/20">
                    @foreach($courses as $course)
                        @php
                            $courseProgress = 0;
                            if ($course->enrollments_count > 0) {
                                $total = 0;
                                foreach ($course->enrollments as $enrollment) {
                                    $total += $enrollment->progress_percent;
                                }
                                $courseProgress = round($total / $course->enrollments_count);
                            }
                            $courseRevenue = $course->price * $course->enrollments_count;
                        @endphp
                        <tr class="hover:bg-surface-low/30 transition">
                            <td class="px-5 py-4 font-medium text-sm max-w-[200px] truncate">{{ $course->title }}</td>
                            <td class="px-5 py-4 text-center text-sm">{{ $course->enrollments_count }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-20 h-1.5 bg-gray-200 rounded-full">
                                        <div class="bg-primary h-1.5 rounded-full" style="width: {{ $courseProgress }}%"></div>
                                    </div>
                                    <span class="text-xs">{{ $courseProgress }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center text-sm">{{ number_format($courseRevenue, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-4 text-center">
                                @if($course->published)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">
                                        <i class="fas fa-circle text-[6px]"></i> Publié
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">
                                        <i class="fas fa-pen-ruler text-[10px]"></i> Brouillon
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3 text-xs">
                                    <a href="{{ route('instructor.courses.edit', $course) }}" class="text-primary hover:underline">Modifier</a>
                                    <button type="button"
                                            class="delete-course-btn text-red-500 hover:text-red-700 transition"
                                            data-course-id="{{ $course->id }}"
                                            data-course-title="{{ $course->title }}">
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cards mobiles (< md) --}}
        <div class="md:hidden divide-y divide-outline/20">
            @foreach($courses as $course)
                @php
                    $courseProgress = 0;
                    if ($course->enrollments_count > 0) {
                        $total = 0;
                        foreach ($course->enrollments as $enrollment) {
                            $total += $enrollment->progress_percent;
                        }
                        $courseProgress = round($total / $course->enrollments_count);
                    }
                    $courseRevenue = $course->price * $course->enrollments_count;
                @endphp
                <div class="p-3 space-y-2 hover:bg-surface-low/30 transition">

                    {{-- Titre + statut --}}
                    <div class="flex justify-between items-start gap-2">
                        <p class="text-xs font-semibold leading-tight flex-1">{{ $course->title }}</p>
                        @if($course->published)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] bg-green-100 text-green-700 flex-shrink-0">
                                <i class="fas fa-circle text-[5px]"></i> Publié
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] bg-gray-100 text-gray-600 flex-shrink-0">
                                <i class="fas fa-pen-ruler text-[8px]"></i> Brouillon
                            </span>
                        @endif
                    </div>

                    {{-- Méta : étudiants + revenu --}}
                    <div class="flex items-center gap-4 text-[10px] text-on-surface-variant">
                        <span><i class="fas fa-users mr-1"></i>{{ $course->enrollments_count }} étudiants</span>
                        <span><i class="fas fa-money-bill-wave mr-1"></i>{{ number_format($courseRevenue, 0, ',', ' ') }} FCFA</span>
                    </div>

                    {{-- Barre de progression --}}
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-1 bg-gray-200 rounded-full">
                            <div class="bg-primary h-1 rounded-full" style="width: {{ $courseProgress }}%"></div>
                        </div>
                        <span class="text-[10px] text-on-surface-variant">{{ $courseProgress }}%</span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 text-[10px] pt-0.5">
                        <a href="{{ route('instructor.courses.edit', $course) }}" class="text-primary hover:underline font-medium">Modifier</a>
                        <button type="button"
                                class="delete-course-btn text-red-500 hover:text-red-700 font-medium"
                                data-course-id="{{ $course->id }}"
                                data-course-title="{{ $course->title }}">
                            Supprimer
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @if($courses->hasPages())
            <div class="px-3 sm:px-5 py-2 sm:py-3 border-t border-outline/20 bg-surface-low/30">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODALE DE SUPPRESSION -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-5 sm:p-6 transform transition-all">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-trash-alt text-red-500 text-sm"></i>
            </div>
            <h3 class="text-sm sm:text-lg font-display font-semibold">Confirmer la suppression</h3>
        </div>
        <p class="text-on-surface-variant text-xs sm:text-sm mb-5 sm:mb-6">
            Êtes-vous sûr de vouloir supprimer le cours <strong id="deleteCourseTitle"></strong> ?<br>
            Cette action est irréversible.
        </p>
        <div class="flex justify-end gap-2 sm:gap-3">
            <button id="cancelDeleteBtn" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition text-xs sm:text-sm">
                Annuler
            </button>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 transition text-xs sm:text-sm">
                    Supprimer définitivement
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteCourseTitleSpan = document.getElementById('deleteCourseTitle');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    document.querySelectorAll('.delete-course-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            deleteCourseTitleSpan.textContent = this.dataset.courseTitle;
            deleteForm.action = `/instructeur/courses/${this.dataset.courseId}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
</script>
@endsection