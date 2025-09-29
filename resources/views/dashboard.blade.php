<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Répertoire des Patronymes') }} - Tableau de bord</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 60vh;
            display: flex;
            align-items: center;
        }
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .stats-counter {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
        }
        .quick-action-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .quick-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .activity-item {
            border-left: 4px solid #667eea;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        .activity-item.success {
            border-left-color: #28a745;
        }
        .activity-item.info {
            border-left-color: #17a2b8;
        }
        .activity-item.warning {
            border-left-color: #ffc107;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-book-open me-2"></i>Répertoire des Patronymes
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('patronymes.index') }}">
                            <i class="fas fa-search me-1"></i>Patronymes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('favorites.index') }}">
                            <i class="fas fa-heart me-1"></i>Favoris
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('statistics.index') }}">
                            <i class="fas fa-chart-bar me-1"></i>Statistiques
                        </a>
                    </li>
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-cog me-1"></i>Administration
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="fas fa-user me-2"></i>Mon profil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="animate__animated animate__fadeInLeft">
                        <h1 class="display-4 fw-bold text-white mb-4">
                            <i class="fas fa-tachometer-alt me-3"></i>
                            Bienvenue sur votre tableau de bord
                        </h1>
                        <p class="lead text-white mb-4">
                            Explorez, contribuez et découvrez l'histoire fascinante des patronymes du Burkina Faso.
                            Votre espace personnel pour gérer vos favoris et suivre vos contributions.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('patronymes.index') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-search me-2"></i>Explorer les patronymes
                            </a>
                            <a href="{{ route('patronymes.create') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-plus me-2"></i>Ajouter un patronyme
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="animate__animated animate__fadeInRight">
                        <!-- Quick Search -->
                        <div class="card shadow-lg">
                            <div class="card-body p-4">
                                <h3 class="card-title text-center mb-4">
                                    <i class="fas fa-search text-primary"></i> Recherche Rapide
                                </h3>
                                <form action="{{ route('patronymes.index') }}" method="GET">
                                    <div class="input-group input-group-lg mb-3">
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Entrez un nom de patronyme..."
                                               value="{{ request('search') }}">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Recherchez par nom, origine ou signification
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp">
                        <div class="stats-counter" data-count="{{ \App\Models\Patronyme::count() }}">0</div>
                        <h5><i class="fas fa-users me-2"></i>Patronymes</h5>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="stats-counter" data-count="{{ \App\Models\Region::count() }}">0</div>
                        <h5><i class="fas fa-map-marker-alt me-2"></i>Régions</h5>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="stats-counter" data-count="{{ \App\Models\User::count() }}">0</div>
                        <h5><i class="fas fa-user me-2"></i>Utilisateurs</h5>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                        <div class="stats-counter" data-count="{{ \DB::table('favorites')->count() }}">0</div>
                        <h5><i class="fas fa-heart me-2"></i>Favoris</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5 bg-light">
        <div class="container">
            <!-- Quick Actions -->
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="display-6 fw-bold text-center mb-5 animate__animated animate__fadeInUp">
                        <i class="fas fa-bolt text-warning me-3"></i>
                        Actions Rapides
                    </h2>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-search fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Rechercher</h5>
                            <p class="card-text">
                                Explorez notre base de données de patronymes avec des outils de recherche avancés.
                            </p>
                            <a href="{{ route('patronymes.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Commencer la recherche
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-plus fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Contribuer</h5>
                            <p class="card-text">
                                Ajoutez de nouveaux patronymes et enrichissez notre patrimoine culturel.
                            </p>
                            <a href="{{ route('patronymes.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Ajouter un patronyme
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-heart fa-3x text-danger"></i>
                            </div>
                            <h5 class="card-title">Mes Favoris</h5>
                            <p class="card-text">
                                Consultez et gérez vos patronymes favoris sauvegardés.
                            </p>
                            <a href="{{ route('favorites.index') }}" class="btn btn-danger">
                                <i class="fas fa-heart me-2"></i>Voir mes favoris
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="row">
                <!-- Recent Activity -->
                <div class="col-lg-8 mb-4">
                    <div class="card animate__animated animate__fadeInLeft">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Activité Récente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="activity-item success">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Contribution validée</h6>
                                        <p class="mb-1 text-muted">Votre contribution pour "Diallo" a été approuvée et est maintenant visible</p>
                                        <small class="text-muted"><i class="fas fa-clock me-1"></i>Il y a 2 heures</small>
                                    </div>
                                </div>
                            </div>

                            <div class="activity-item info">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-comment text-info fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Nouveau commentaire</h6>
                                        <p class="mb-1 text-muted">Vous avez commenté le patronyme "Traoré"</p>
                                        <small class="text-muted"><i class="fas fa-clock me-1"></i>Il y a 1 jour</small>
                                    </div>
                                </div>
                            </div>

                            <div class="activity-item warning">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-search text-warning fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Recherche effectuée</h6>
                                        <p class="mb-1 text-muted">Vous avez recherché "patronymes bambara"</p>
                                        <small class="text-muted"><i class="fas fa-clock me-1"></i>Il y a 2 jours</small>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <a href="{{ route('patronymes.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-2"></i>Voir toute l'activité
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="col-lg-4 mb-4">
                    <div class="card animate__animated animate__fadeInRight">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie text-success me-2"></i>
                                Mes Statistiques
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-eye text-primary fa-2x mb-2"></i>
                                        <h4 class="mb-1">{{ Auth::user()->patronymes()->count() ?? 0 }}</h4>
                                        <small class="text-muted">Patronymes consultés</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-edit text-success fa-2x mb-2"></i>
                                        <h4 class="mb-1">{{ Auth::user()->contributions()->count() ?? 0 }}</h4>
                                        <small class="text-muted">Contributions</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-comments text-info fa-2x mb-2"></i>
                                        <h4 class="mb-1">{{ Auth::user()->commentaires()->count() ?? 0 }}</h4>
                                        <small class="text-muted">Commentaires</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-heart text-danger fa-2x mb-2"></i>
                                        <h4 class="mb-1">{{ Auth::user()->favorites()->count() ?? 0 }}</h4>
                                        <small class="text-muted">Favoris</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="row">
                <div class="col-12">
                    <h2 class="display-6 fw-bold text-center mb-5 animate__animated animate__fadeInUp">
                        <i class="fas fa-star text-warning me-3"></i>
                        Fonctionnalités Disponibles
                    </h2>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-chart-bar fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Statistiques Détaillées</h5>
                            <p class="card-text">
                                Visualisez des analyses complètes et des graphiques sur les patronymes.
                            </p>
                            <a href="{{ route('statistics.index') }}" class="btn btn-info">
                                <i class="fas fa-chart-bar me-2"></i>Voir les statistiques
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-user-cog fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title">Mon Profil</h5>
                            <p class="card-text">
                                Gérez vos informations personnelles et vos préférences.
                            </p>
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-user-cog me-2"></i>Gérer mon profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; {{ date('Y') }} Répertoire des Patronymes. Tous droits réservés.
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-3">
                        <a href="#" class="text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-white">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Counter Animation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stats-counter');

            const animateCounter = (counter) => {
                const target = parseInt(counter.getAttribute('data-count'));
                const increment = target / 100;
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current);
                }, 20);
            };

            // Intersection Observer for counter animation
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });

            counters.forEach(counter => observer.observe(counter));
        });
    </script>
</body>
</html>