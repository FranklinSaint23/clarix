@if($courses->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
            @php
                $studentsCount = $course->enrollments_count ?? $course->students->count();
                $avgProgress = 0;
                if ($studentsCount > 0 && isset($course->enrollments)) {
                    $total = 0;
                    foreach ($course->enrollments as $enrollment) {
                        $total += $enrollment->progress_percent;
                    }
                    $avgProgress = round($total / $studentsCount);
                }
            @endphp
            <div class="group bg-white rounded-2xl border border-outline/20 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <!-- Image et badges -->
                <div class="relative h-48 overflow-hidden">
                    <img src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $course->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                    <div class="absolute top-3 right-3">
                        @if($course->published)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-circle text-[6px]"></i> Publié
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-pen-ruler text-[10px]"></i> Brouillon
                            </span>
                        @endif
                    </div>
                    <div class="absolute bottom-3 left-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-white/90 text-gray-700 backdrop-blur-sm">
                            @if($course->level == 'beginner') 🌱 Débutant
                            @elseif($course->level == 'intermediate') 📘 Intermédiaire
                            @else 🚀 Avancé @endif
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-display font-bold text-lg line-clamp-1">{{ $course->title }}</h3>
                    <p class="text-on-surface-variant text-sm mt-1 line-clamp-2">{{ Str::limit($course->description, 80) }}</p>
                    <div class="flex items-center justify-between mt-3 text-sm text-on-surface-variant">
                        <div class="flex items-center gap-1">
                            <i class="fas fa-user-graduate"></i>
                            <span>{{ $studentsCount }} étudiant(s)</span>
                        </div>
                        @if($studentsCount > 0)
                            <div class="flex items-center gap-1">
                                <i class="fas fa-chart-line"></i>
                                <span>{{ $avgProgress }}% moy.</span>
                            </div>
                        @endif
                    </div>
                    @if($studentsCount > 0)
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-primary h-1.5 rounded-full" style="width: {{ $avgProgress }}%"></div>
                        </div>
                    @endif
                    <div class="flex items-center justify-end gap-3 mt-4 pt-2 border-t border-outline/20">
                        <a href="{{ route('instructor.courses.edit', $course) }}" class="text-primary hover:text-primary/80 text-sm font-medium transition flex items-center gap-1">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <button type="button" class="delete-course-btn text-red-500 hover:text-red-700 text-sm font-medium transition flex items-center gap-1"
                                data-course-id="{{ $course->id }}" data-course-title="{{ $course->title }}">
                            <i class="fas fa-trash-alt"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-10 flex justify-center">
        {{ $courses->appends(request()->query())->links() }}
    </div>
@else
    <div class="text-center py-16 bg-white rounded-2xl border border-outline/20 shadow-sm">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-fixed/30 mb-4">
            <i class="fas fa-chalkboard text-2xl text-primary"></i>
        </div>
        <h3 class="text-xl font-display font-semibold">Aucun cours trouvé</h3>
        <p class="text-on-surface-variant mt-1">Aucun cours ne correspond à vos critères.</p>
    </div>
@endif