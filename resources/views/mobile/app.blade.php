<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#3b82f6">

    <title>Répertoire des Patronymes - Mobile</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-16x16.png">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        /* Styles spécifiques mobile */
        .mobile-container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem;
        }

        .mobile-content {
            margin-top: 80px;
            padding: 1rem;
            min-height: calc(100vh - 80px);
        }

        .mobile-search {
            position: sticky;
            top: 80px;
            z-index: 40;
            background: white;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .mobile-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .mobile-button {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            transition: all 0.2s;
        }

        .mobile-button-primary {
            background: #3b82f6;
            color: white;
        }

        .mobile-button-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .mobile-fab {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            z-index: 60;
        }

        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 0.5rem;
            z-index: 50;
        }

        .mobile-nav-item {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.75rem;
        }

        .mobile-nav-item.active {
            color: #3b82f6;
        }

        .mobile-nav-icon {
            width: 24px;
            height: 24px;
            margin: 0 auto 0.25rem;
        }

        /* Animations */
        .mobile-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .mobile-slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .mobile-content {
                padding: 0.5rem;
            }

            .mobile-card {
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="mobile-container">
        <!-- Header Mobile -->
        <header class="mobile-header">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-users text-2xl text-blue-600 mr-2"></i>
                    <h1 class="text-lg font-bold text-gray-900">Patronymes BF</h1>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="search-toggle" class="p-2 text-gray-600">
                        <i class="fas fa-search"></i>
                    </button>
                    <button id="menu-toggle" class="p-2 text-gray-600">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Search Mobile -->
        <div id="mobile-search" class="mobile-search" style="display: none;">
            <div class="relative">
                <input type="text"
                       id="mobile-search-input"
                       placeholder="Rechercher un patronyme..."
                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <button id="search-clear" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>
        </div>

        <!-- Content Mobile -->
        <main class="mobile-content">
            <!-- Loading State -->
            <div id="loading" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-4"></i>
                <p class="text-gray-600">Chargement...</p>
            </div>

            <!-- Search Results -->
            <div id="search-results" class="hidden">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Résultats de recherche</h2>
                    <button id="clear-search" class="text-blue-600 text-sm">Effacer</button>
                </div>
                <div id="search-results-list"></div>
            </div>

            <!-- Popular Patronymes -->
            <div id="popular-section" class="hidden">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-fire text-orange-500 mr-2"></i>
                    Patronymes populaires
                </h2>
                <div id="popular-list"></div>
            </div>

            <!-- Recent Patronymes -->
            <div id="recent-section" class="hidden">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-clock text-green-500 mr-2"></i>
                    Patronymes récents
                </h2>
                <div id="recent-list"></div>
            </div>

            <!-- Patronymes by Letter -->
            <div id="letter-section" class="hidden">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-list text-purple-500 mr-2"></i>
                    Patronymes par lettre
                </h2>
                <div class="grid grid-cols-6 gap-2 mb-4">
                    @foreach(range('A', 'Z') as $letter)
                        <button class="letter-btn mobile-button mobile-button-secondary" data-letter="{{ $letter }}">
                            {{ $letter }}
                        </button>
                    @endforeach
                </div>
                <div id="letter-results"></div>
            </div>
        </main>

        <!-- Floating Action Button -->
        <button id="mobile-fab" class="mobile-fab">
            <i class="fas fa-plus"></i>
        </button>

        <!-- Bottom Navigation -->
        <nav class="mobile-nav">
            <div class="flex">
                <a href="#" class="mobile-nav-item active" data-section="popular">
                    <i class="fas fa-fire mobile-nav-icon"></i>
                    <div>Populaires</div>
                </a>
                <a href="#" class="mobile-nav-item" data-section="recent">
                    <i class="fas fa-clock mobile-nav-icon"></i>
                    <div>Récents</div>
                </a>
                <a href="#" class="mobile-nav-item" data-section="letter">
                    <i class="fas fa-list mobile-nav-icon"></i>
                    <div>Par lettre</div>
                </a>
                <a href="#" class="mobile-nav-item" data-section="search">
                    <i class="fas fa-search mobile-nav-icon"></i>
                    <div>Recherche</div>
                </a>
            </div>
        </nav>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Mobile App JavaScript
        class MobileApp {
            constructor() {
                this.currentSection = 'popular';
                this.searchQuery = '';
                this.init();
            }

            init() {
                this.registerServiceWorker();
                this.bindEvents();
                this.loadInitialData();
            }

            registerServiceWorker() {
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('/sw.js')
                        .then(registration => {
                            console.log('Service Worker enregistré:', registration);
                        })
                        .catch(error => {
                            console.log('Erreur Service Worker:', error);
                        });
                }
            }

            bindEvents() {
                // Search toggle
                document.getElementById('search-toggle').addEventListener('click', () => {
                    this.toggleSearch();
                });

                // Search input
                document.getElementById('mobile-search-input').addEventListener('input', (e) => {
                    this.handleSearch(e.target.value);
                });

                // Search clear
                document.getElementById('search-clear').addEventListener('click', () => {
                    this.clearSearch();
                });

                // Navigation
                document.querySelectorAll('.mobile-nav-item').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.switchSection(item.dataset.section);
                    });
                });

                // Letter buttons
                document.querySelectorAll('.letter-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        this.loadPatronymesByLetter(btn.dataset.letter);
                    });
                });

                // FAB
                document.getElementById('mobile-fab').addEventListener('click', () => {
                    this.showAddPatronyme();
                });
            }

            async loadInitialData() {
                this.showLoading();
                try {
                    await Promise.all([
                        this.loadPopularPatronymes(),
                        this.loadRecentPatronymes()
                    ]);
                    this.hideLoading();
                    this.showSection('popular');
                } catch (error) {
                    console.error('Erreur chargement initial:', error);
                    this.hideLoading();
                }
            }

            async loadPopularPatronymes() {
                try {
                    const response = await fetch('/api/mobile/patronymes/popular?limit=10');
                    const data = await response.json();

                    if (data.success) {
                        this.renderPatronymesList('popular-list', data.data);
                    }
                } catch (error) {
                    console.error('Erreur chargement populaires:', error);
                }
            }

            async loadRecentPatronymes() {
                try {
                    const response = await fetch('/api/mobile/patronymes/recent?limit=10');
                    const data = await response.json();

                    if (data.success) {
                        this.renderPatronymesList('recent-list', data.data);
                    }
                } catch (error) {
                    console.error('Erreur chargement récents:', error);
                }
            }

            async loadPatronymesByLetter(letter) {
                try {
                    const response = await fetch(`/api/mobile/patronymes/by-letter/${letter}?limit=20`);
                    const data = await response.json();

                    if (data.success) {
                        this.renderPatronymesList('letter-results', data.data);
                    }
                } catch (error) {
                    console.error('Erreur chargement par lettre:', error);
                }
            }

            async handleSearch(query) {
                if (query.length < 2) return;

                this.searchQuery = query;

                try {
                    const response = await fetch(`/api/mobile/patronymes/search?q=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    if (data.success) {
                        this.renderPatronymesList('search-results-list', data.data);
                        this.showSection('search');
                    }
                } catch (error) {
                    console.error('Erreur recherche:', error);
                }
            }

            renderPatronymesList(containerId, patronymes) {
                const container = document.getElementById(containerId);
                if (!container) return;

                container.innerHTML = patronymes.map(patronyme => `
                    <div class="mobile-card mobile-fade-in">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">${patronyme.nom}</h3>
                                <p class="text-sm text-gray-600 mb-2">${patronyme.signification || 'Aucune signification'}</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <span>${patronyme.region?.name || 'N/A'}</span>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-eye mr-1"></i>
                                    <span>${patronyme.views_count || 0} vues</span>
                                </div>
                            </div>
                            <button class="ml-2 p-2 text-blue-600" onclick="mobileApp.viewPatronyme(${patronyme.id})">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            }

            switchSection(section) {
                this.currentSection = section;

                // Update navigation
                document.querySelectorAll('.mobile-nav-item').forEach(item => {
                    item.classList.toggle('active', item.dataset.section === section);
                });

                this.showSection(section);
            }

            showSection(section) {
                // Hide all sections
                document.querySelectorAll('[id$="-section"]').forEach(el => {
                    el.classList.add('hidden');
                });

                // Show target section
                const targetSection = document.getElementById(`${section}-section`);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }
            }

            toggleSearch() {
                const searchDiv = document.getElementById('mobile-search');
                const isVisible = searchDiv.style.display !== 'none';
                searchDiv.style.display = isVisible ? 'none' : 'block';

                if (!isVisible) {
                    document.getElementById('mobile-search-input').focus();
                }
            }

            clearSearch() {
                document.getElementById('mobile-search-input').value = '';
                document.getElementById('search-results').classList.add('hidden');
                this.searchQuery = '';
            }

            showLoading() {
                document.getElementById('loading').classList.remove('hidden');
            }

            hideLoading() {
                document.getElementById('loading').classList.add('hidden');
            }

            viewPatronyme(id) {
                window.location.href = `/patronymes/${id}`;
            }

            showAddPatronyme() {
                // Rediriger vers la page d'ajout
                window.location.href = '/patronymes/create';
            }
        }

        // Initialize mobile app
        const mobileApp = new MobileApp();
    </script>
</body>
</html>
