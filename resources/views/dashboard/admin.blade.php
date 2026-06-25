@extends('layouts.app')

@section('title', 'Administration')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Administration</h1>
        <p class="text-sm text-on-surface-variant mt-1">Gérez les utilisateurs, les cours et surveillez les statistiques globales.</p>
    </div>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Utilisateurs</p>
                <p class="text-3xl font-bold text-primary mt-1">{{ $totalUsers }}</p>
                <p class="text-xs text-primary/70 mt-1">{{ $activeStudents }} étudiants actifs</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                <i class="fas fa-users text-primary text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Cours</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalCourses }}</p>
                <p class="text-xs text-blue-600/70 mt-1">{{ $publishedCourses }} publiés</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fas fa-book-open text-blue-600 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Inscriptions</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalEnrollments }}</p>
                <p class="text-xs text-green-600/70 mt-1">Progression moy. {{ $avgProgress }}%</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-user-graduate text-green-600 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Quiz passés</p>
                <p class="text-3xl font-bold text-purple-600 mt-1">{{ $quizzesPassed }}</p>
                <p class="text-xs text-purple-600/70 mt-1">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA revenus</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                <i class="fas fa-brain text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Graphiques Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Inscriptions par mois -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-outline/20 shadow-sm p-5">
            <h2 class="text-md font-display font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-primary"></i> Inscriptions (6 derniers mois)
            </h2>
            <canvas id="enrollmentsChart" height="120"></canvas>
        </div>

        <!-- Répartition par rôle -->
        <div class="bg-white rounded-2xl border border-outline/20 shadow-sm p-5">
            <h2 class="text-md font-display font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-secondary"></i> Utilisateurs par rôle
            </h2>
            <canvas id="rolesChart" height="180"></canvas>
            <div class="flex flex-col gap-1 mt-3 text-xs text-on-surface-variant">
                <span><span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-1"></span> Étudiants : {{ $usersByRole[0] }}</span>
                <span><span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-1"></span> Instructeurs : {{ $usersByRole[1] }}</span>
                <span><span class="inline-block w-3 h-3 rounded-full bg-purple-500 mr-1"></span> Admins : {{ $usersByRole[2] }}</span>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Gestion des utilisateurs -->
        <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline/20 bg-surface-low/50 flex items-center justify-between">
                <h2 class="text-md font-display font-semibold flex items-center gap-2">
                    <i class="fas fa-users text-primary"></i> Utilisateurs
                </h2>
                <a href="{{ route(‘admin.users.create’) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-lg hover:bg-primary/90 transition">
                    <i class="fas fa-plus"></i> Ajouter
                </a>
            </div>
            <div class="p-5 space-y-3">
                <p class="text-sm text-on-surface-variant">Gérez les comptes, rôles et permissions des utilisateurs.</p>
                <a href="{{ route(‘admin.users.index’) }}" class="inline-flex items-center gap-2 text-primary hover:underline text-sm font-medium">
                    <i class="fas fa-arrow-right"></i> Voir tous les utilisateurs
                </a>
            </div>
        </div>

        <!-- Rapport d’analyse -->
        <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline/20 bg-surface-low/50">
                <h2 class="text-md font-display font-semibold flex items-center gap-2">
                    <i class="fas fa-chart-line text-primary"></i> Analyse rapide
                </h2>
            </div>
            <div class="p-5 space-y-3 text-sm">
                <div class="flex justify-between items-center py-1.5 border-b border-outline/10">
                    <span class="text-on-surface-variant">Taux de complétion moyen</span>
                    <span class="font-bold text-primary">{{ $avgProgress }}%</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-outline/10">
                    <span class="text-on-surface-variant">Quiz réussis</span>
                    <span class="font-bold text-green-600">{{ $quizzesPassed }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-on-surface-variant">Revenus totaux</span>
                    <span class="font-bold text-purple-600">{{ number_format($totalRevenue, 0, ‘,’, ‘ ‘) }} FCFA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières activités réelles -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-outline/20 bg-surface-low/50">
            <h2 class="text-md font-display font-semibold flex items-center gap-2">
                <i class="fas fa-history text-primary"></i> Dernières activités
            </h2>
        </div>
        <div class="divide-y divide-outline/20">
            @forelse($recentActivities as $activity)
                <div class="px-5 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                        @if($activity[‘color’] === ‘green’) bg-green-100
                        @elseif($activity[‘color’] === ‘blue’) bg-blue-100
                        @elseif($activity[‘color’] === ‘purple’) bg-purple-100
                        @else bg-primary/10 @endif">
                        <i class="fas {{ $activity[‘icon’] }} text-sm
                            @if($activity[‘color’] === ‘green’) text-green-600
                            @elseif($activity[‘color’] === ‘blue’) text-blue-600
                            @elseif($activity[‘color’] === ‘purple’) text-purple-600
                            @else text-primary @endif"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $activity[‘text’] }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $activity[‘time’]->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-on-surface-variant">
                    <i class="fas fa-inbox text-3xl mb-2 block opacity-30"></i>
                    Aucune activité récente.
                </div>
            @endforelse
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const enrollmentsCtx = document.getElementById('enrollmentsChart').getContext('2d');
    new Chart(enrollmentsCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyLabels),
            datasets: [{
                label: 'Inscriptions',
                data: @json($monthlyEnrollments),
                backgroundColor: 'rgba(0, 86, 210, 0.15)',
                borderColor: '#0056d2',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    const rolesCtx = document.getElementById('rolesChart').getContext('2d');
    new Chart(rolesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Étudiants', 'Instructeurs', 'Admins'],
            datasets: [{
                data: @json($usersByRole),
                backgroundColor: ['#3b82f6', '#22c55e', '#a855f7'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush

@endsection