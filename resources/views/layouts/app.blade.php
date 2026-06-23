<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CLARIX – @yield('title', 'Plateforme e-learning')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Tailwind CSS compilé localement -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      body { font-family: 'Inter', sans-serif; background-color: #f8f9fb; color: #191c1e; }
      h1,h2,h3,h4,h5,h6 { font-family: 'Manrope', sans-serif; }
      .progress-gradient { background: linear-gradient(90deg, #0040a1 0%, #0056d2 100%); }
      .layer-lift { background: white; border-radius: 1rem; box-shadow: 0 20px 40px rgba(25,28,30,0.06); }
      .btn-primary { background: #0056d2; color: white; font-weight: 500; padding: 0.75rem 1.5rem; border-radius: 0.5rem; transition: all 0.2s; display: inline-block; }
      .btn-primary:hover { background: #0040a1; transform: translateY(-1px); }
      .btn-secondary { background: rgba(255,255,255,0.5); backdrop-filter: blur(4px); color: #0056d2; border: 1px solid rgba(195,198,214,0.3); padding: 0.5rem 1.25rem; border-radius: 0.5rem; transition: all 0.2s; }
      .btn-secondary:hover { background: #e8f0fe; }
      .input-field { width: 100%; border-radius: 0.5rem; border: none; background: #f3f4f6; padding: 0.75rem; color: #191c1e; outline: none; }
      .input-field:focus { ring: 2px solid #0056d2; }
      .glass-nav { background: rgba(248,249,251,0.8); backdrop-filter: blur(12px); box-shadow: 0 8px 20px rgba(0,0,0,0.03); border-bottom: 1px solid rgba(195,198,214,0.2); }
      .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
      @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
      .nav-link { position: relative; color: #424654; font-weight: 500; transition: 0.3s; }
      .nav-link:hover { color: #0056d2; }
      .nav-link::after { content: ""; position: absolute; left: 0; bottom: -4px; width: 0%; height: 2px; background: #0056d2; transition: 0.3s; }
      .nav-link:hover::after { width: 100%; }
      /* Correction affichage vidéo */
        video {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }
        video::-webkit-media-controls {
            transform: none !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex flex-col">

    <div x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen">
        <!-- Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition.opacity></div>

        <!-- Sidebar (glisse depuis la droite) -->
        <aside x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 z-50 w-64 h-full bg-surface-lowest shadow-lg lg:hidden">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between p-4 border-b border-outline/20">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('images/logo.svg') }}" class="w-8 h-8" alt="CLARIX">
                        <span class="font-display font-bold text-xl text-on-surface">CLARIX</span>
                    </div>
                    <button @click="sidebarOpen = false" class="text-on-surface-variant hover:text-primary">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <nav class="flex-1 py-6 px-4 space-y-4">
                    <a href="{{ route('courses.index') }}" class="flex items-center space-x-3 text-on-surface-variant hover:text-primary transition">
                        <i class="fas fa-book-open w-5"></i>
                        <span>Catalogue</span>
                    </a>
                    @auth
                        @if(auth()->user()->isStudent())
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 text-on-surface-variant hover:text-primary transition">
                                <i class="fas fa-chalkboard-user w-5"></i>
                                <span>Mon espace</span>
                            </a>
                        @endif
                        @if(auth()->user()->isInstructor())
                            <a href="{{ route('instructor.dashboard') }}" class="flex items-center space-x-3 text-on-surface-variant hover:text-primary transition">
                                <i class="fas fa-chalkboard w-5"></i>
                                <span>Studio</span>
                            </a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 text-on-surface-variant hover:text-primary transition">
                                <i class="fas fa-shield-haltered w-5"></i>
                                <span>Administration</span>
                            </a>
                        @endif
                        <hr class="my-2 border-outline/20">
                        <!-- Lien Notifications dans la sidebar -->
                        <a href="{{ route('notifications.index') }}" class="flex items-center space-x-3 text-on-surface-variant hover:text-primary transition">
                            <i class="far fa-bell w-5"></i>
                            <span>Notifications</span>
                            @if(auth()->user()->unreadNotifications->count())
                                <span class="ml-auto bg-secondary text-white text-xs rounded-full px-1.5 py-0.5">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 text-on-surface-variant hover:text-primary transition">
                            <i class="fas fa-user w-5"></i>
                            <span>Mon profil</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 w-full text-left text-on-surface-variant hover:text-red-600 transition">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center space-x-3 text-primary font-medium hover:underline">
                            <i class="fas fa-sign-in-alt w-5"></i>
                            <span>Connexion</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center space-x-3 text-primary font-medium hover:underline">
                            <i class="fas fa-user-plus w-5"></i>
                            <span>Inscription</span>
                        </a>
                    @endauth
                </nav>
            </div>
        </aside>

        <!-- Barre de navigation principale -->
        <nav class="glass-nav sticky top-0 z-30">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo à gauche -->
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <img src="{{ asset('images/logo.svg') }}" class="w-8 h-8" alt="CLARIX">
                        <span class="font-display font-bold text-xl text-on-surface">CLARIX</span>
                    </a>

                    <!-- Liens centraux (desktop) -->
                    <div class="hidden lg:flex space-x-8">
                        <a href="{{ route('courses.index') }}" class="text-on-surface-variant hover:text-primary transition">Catalogue</a>
                        @auth
                            @if(auth()->user()->isStudent())
                                <a href="{{ route('dashboard') }}" class="text-on-surface-variant hover:text-primary">Mon espace</a>
                            @endif
                            @if(auth()->user()->isInstructor())
                                <a href="{{ route('instructor.dashboard') }}" class="text-on-surface-variant hover:text-primary">Studio</a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="text-on-surface-variant hover:text-primary">Administration</a>
                            @endif
                        @endauth
                    </div>

                    <!-- Zone droite : recherche, notifications, utilisateur, hamburger -->
                    <div class="flex items-center space-x-4">

                        <!-- Barre de recherche (desktop) -->
                        <form action="{{ route('search') }}" method="GET" class="hidden lg:flex items-center">
                            <div class="relative">
                                <input type="text" name="q" value="{{ request('q') }}"
                                       placeholder="Rechercher cours, forum, leçons"
                                       class="w-64 pl-9 pr-4 py-1.5 text-sm rounded-lg bg-gray-100 border border-transparent focus:outline-none focus:border-primary focus:bg-white transition">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            </div>
                        </form>

                        @auth
                            <!-- Icône notifications avec compteur positionné absolument -->
                            <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center justify-center text-on-surface-variant hover:text-primary">
                                <i class="far fa-bell text-xl"></i>
                                @php $count = auth()->user()->unreadNotifications->count(); @endphp
                                @if($count)
                                    <span class="notification-badge" style="position: absolute; top: -0.25rem; right: -0.5rem; background-color: #006c47; color: white; font-size: 0.7rem; font-weight: bold; width: 1.25rem; height: 1.25rem; display: flex; align-items: center; justify-content: center; border-radius: 9999px;">
                                        {{ $count }}
                                    </span>
                                @endif
                            </a>

                            <!-- Menu utilisateur (desktop) -->
                            <div x-data="{ open: false }" class="relative hidden lg:block">
                                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                    <img src="{{ auth()->user()->avatar_url ?? asset('images/avatar-placeholder.png') }}" class="w-8 h-8 rounded-full object-cover">
                                    <span class="hidden md:inline text-sm font-medium">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs text-on-surface-variant"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-ambient z-50 py-1">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-surface-low">Mon profil</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-surface-low">Déconnexion</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Boutons Connexion & Inscription (desktop) -->
                            <div class="hidden lg:flex items-center gap-3">
                                <a href="{{ route('login') }}" 
                                   class="px-5 py-2 rounded-lg border border-primary text-primary font-medium hover:bg-primary-fixed transition">
                                    Connexion
                                </a>
                                <a href="{{ route('register') }}" 
                                   class="px-5 py-2 rounded-lg bg-primary text-white font-medium hover:bg-primary-container transition">
                                    Inscription
                                </a>
                            </div>
                        @endauth

                        <!-- Bouton hamburger (visible sur mobile) -->
                        <button @click="sidebarOpen = true" class="lg:hidden text-on-surface-variant hover:text-primary focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Contenu principal (flex-1 pour pousser le footer) -->
        <main class="flex-1 py-8">
            <div class="container mx-auto px-4">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-4 mb-6 rounded">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-600 text-red-800 p-4 mb-6 rounded">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-surface-low border-t border-outline/20 py-12">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <img src="{{ asset('images/logo.svg') }}" class="w-8 h-8" alt="CLARIX">
                            <span class="font-display font-bold text-xl text-on-surface">CLARIX</span>
                        </div>
                        <p class="text-sm text-on-surface-variant">Le sanctuaire intellectuel pour les esprits curieux.</p>
                    </div>
                    <div>
                        <h4 class="font-display font-semibold mb-3">Explorer</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('courses.index') }}" class="text-on-surface-variant hover:text-primary">Catalogue</a></li>
                            <li><a href="#" class="text-on-surface-variant hover:text-primary">Devenir instructeur</a></li>
                            <li><a href="#" class="text-on-surface-variant hover:text-primary">Entreprise</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-display font-semibold mb-3">Ressources</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-on-surface-variant hover:text-primary">Aide</a></li>
                            <li><a href="#" class="text-on-surface-variant hover:text-primary">Confidentialité</a></li>
                            <li><a href="#" class="text-on-surface-variant hover:text-primary">Conditions générales</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-display font-semibold mb-3">Suivez-nous</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-on-surface-variant hover:text-primary"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-on-surface-variant hover:text-primary"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-on-surface-variant hover:text-primary"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-outline/20 mt-8 pt-6 text-center text-xs text-on-surface-variant">
                    &copy; {{ date('Y') }} CLARIX. Tous droits réservés.
                </div>
            </div>
        </footer>
    </div>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
    </script>
    @stack('scripts')
</body>
</html>