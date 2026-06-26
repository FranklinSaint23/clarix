@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="max-w-7xl mx-auto px-4">

    <!-- HERO ULTRA MODERNE -->
    <section class="text-center py-20" data-aos="fade-up">
        <h1 class="text-4xl md:text-6xl font-bold tracking-tight leading-tight">
            Apprenez sans limites avec 
            <span class="text-primary">CLARIX</span>
        </h1>

        <p class="mt-6 text-lg text-gray-500 max-w-2xl mx-auto">
            Des formations modernes, des quiz interactifs et un suivi intelligent pour booster vos compétences.
        </p>

        <div class="mt-10 flex justify-center gap-4">
            <a href="{{ route('courses.index') }}" 
               class="px-6 py-3 bg-primary text-white rounded-xl shadow hover:shadow-lg hover:scale-105 transition duration-300">
                Explorer les cours
            </a>

            <a href="{{ route('register') }}" 
               class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 hover:scale-105 transition duration-300">
                Commencer gratuitement
            </a>
        </div>
    </section>


    <!-- FEATURES (PLUS CLEAN) -->
    <section class="grid md:grid-cols-3 gap-8 mb-20">

        <div data-aos="fade-up" data-aos-delay="100"
             class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-2 transition duration-300">
            <i class="fas fa-video text-primary text-2xl mb-4"></i>
            <h3 class="font-semibold text-lg">Cours vidéo</h3>
            <p class="text-gray-500 text-sm mt-2">
                Accédez à des contenus premium créés par des experts.
            </p>
        </div>

        <div data-aos="fade-up" data-aos-delay="200"
             class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-2 transition duration-300">
            <i class="fas fa-check-circle text-primary text-2xl mb-4"></i>
            <h3 class="font-semibold text-lg">Quiz intelligents</h3>
            <p class="text-gray-500 text-sm mt-2">
                Testez vos connaissances avec correction automatique.
            </p>
        </div>

        <div data-aos="fade-up" data-aos-delay="300"
             class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-2 transition duration-300">
            <i class="fas fa-chart-line text-primary text-2xl mb-4"></i>
            <h3 class="font-semibold text-lg">Suivi progression</h3>
            <p class="text-gray-500 text-sm mt-2">
                Visualisez votre évolution en temps réel.
            </p>
        </div>

    </section>


    <!-- COURS -->
    <section class="mb-20">
        <div class="flex justify-between items-center mb-8" data-aos="fade-up">
            <h2 class="text-2xl font-bold">Formations populaires</h2>

            <a href="{{ route('courses.index') }}" 
               class="text-primary text-sm hover:underline">
                Voir tout →
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            @foreach($courses as $course)
                <div data-aos="zoom-in"
                     class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-2 transition duration-300">

                    <!-- IMAGE -->
                    <div class="h-48 overflow-hidden relative">
                        <img src="{{ $course->thumbnail_url }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                        <!-- BADGES -->
                        <div class="absolute top-3 left-3 text-xs px-3 py-1 bg-white rounded-full shadow">
                            {{ ucfirst($course->level) }}
                        </div>

                        <div class="absolute top-3 right-3 text-xs px-3 py-1 bg-primary text-white rounded-full shadow">
                            {{ $course->formatted_price }}
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div class="p-5">

                        <h3 class="font-semibold text-lg line-clamp-1">
                            {{ $course->title }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                            {{ \Illuminate\Support\Str::limit($course->description, 100) }}
                        </p>

                        <!-- FOOTER -->
                        <div class="flex justify-between items-center mt-4 text-sm text-gray-500">
                            <span>
                                👨‍🎓 {{ $course->students->count() }} inscrits
                            </span>

                            <a href="{{ route('courses.show', $course) }}" 
                               class="text-primary font-medium hover:underline">
                                Voir →
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach

        </div>
    </section>


    <!-- CTA FINAL -->
    <section data-aos="fade-up"
             class="text-center py-16 bg-gradient-to-r from-blue-500 to-green-500 rounded-3xl text-white">
        <h2 class="text-3xl font-bold">
            Prêt à apprendre ?
        </h2>

        <p class="mt-4 text-white/80">
            Rejoignez des milliers d'apprenants dès aujourd'hui.
        </p>

        <a href="{{ route('register') }}" 
           class="inline-block mt-6 px-6 py-3 bg-white text-primary font-semibold rounded-xl shadow hover:shadow-lg hover:scale-105 transition duration-300">
            Créer un compte
        </a>
    </section>

</div>
@endsection