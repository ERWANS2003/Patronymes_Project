<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Répertoire des Patronymes') }} - Administration</title>

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
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
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
            color: #dc3545;
        }
        .admin-card {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }
        .admin-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-cog me-2"></i>Administration
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('patronymes.index') }}">
                            <i class="fas fa-search me-1"></i>Patronymes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('patronymes.create') }}">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.import') }}">
                            <i class="fas fa-upload me-1"></i>Import
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.export') }}">
                            <i class="fas fa-download me-1"></i>Export
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-arrow-left me-1"></i>Retour au dashboard
                        </a>
                    </li>
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
                            <i class="fas fa-cog me-3"></i>
                            Tableau de bord Administrateur
                        </h1>
                        <p class="lead text-white mb-4">
                            Gérez et administrez le répertoire des patronymes. 
                            Surveillez les statistiques, modérez le contenu et maintenez la qualité de la base de données.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('patronymes.create') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-plus me-2"></i>Ajouter un patronyme
                            </a>
                            <a href="{{ route('admin.import') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-upload me-2"></i>Importer des données
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="animate__animated animate__fadeInRight">
                        <!-- Quick Stats -->
                        <div class="card shadow-lg">
                            <div class="card-body p-4">
                                <h3 class="card-title text-center mb-4">
                                    <i class="fas fa-chart-pie text-danger"></i> Statistiques Rapides
                                </h3>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-3">
                                            <i class="fas fa-users text-primary fa-2x mb-2"></i>
                                            <h4 class="mb-1">{{ \App\Models\User::count() }}</h4>
                                            <small class="text-muted">Utilisateurs</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-3">
                                            <i class="fas fa-book text-success fa-2x mb-2"></i>
                                            <h4 class="mb-1">{{ \App\Models\Patronyme::count() }}</h4>
                                            <small class="text-muted">Patronymes</small>
                                        </div>
                                    </div>
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
                        <h5><i class="fas fa-book me-2"></i>Patronymes</h5>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="stats-counter" data-count="{{ \App\Models\User::count() }}">0</div>
                        <h5><i class="fas fa-users me-2"></i>Utilisateurs</h5>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="stats-counter" data-count="{{ \App\Models\Region::count() }}">0</div>
                        <h5><i class="fas fa-map-marker-alt me-2"></i>Régions</h5>
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
                        Actions Administratives
                    </h2>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-plus fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Ajouter un Patronyme</h5>
                            <p class="card-text">
                                Créez de nouveaux patronymes avec toutes les informations détaillées.
                            </p>
                            <a href="{{ route('patronymes.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Ajouter
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-upload fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Importer des Données</h5>
                            <p class="card-text">
                                Importez des patronymes en masse depuis des fichiers Excel ou CSV.
                            </p>
                            <a href="{{ route('admin.import') }}" class="btn btn-info">
                                <i class="fas fa-upload me-2"></i>Importer
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-download fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Exporter les Données</h5>
                            <p class="card-text">
                                Exportez la base de données complète ou des sélections spécifiques.
                            </p>
                            <a href="{{ route('admin.export') }}" class="btn btn-warning">
                                <i class="fas fa-download me-2"></i>Exporter
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="row">
                <!-- Recent Patronymes -->
                <div class="col-lg-8 mb-4">
                    <div class="card animate__animated animate__fadeInLeft">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Derniers Patronymes Ajoutés
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(\App\Models\Patronyme::latest()->take(5)->get()->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach(\App\Models\Patronyme::latest()->take(5)->get() as $patronyme)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $patronyme->nom }}</strong>
                                                @if($patronyme->region)
                                                    <br><small class="text-muted">{{ $patronyme->region->name }}</small>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('patronymes.show', $patronyme) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('patronymes.edit', $patronyme) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucun patronyme disponible</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="col-lg-4 mb-4">
                    <div class="card animate__animated animate__fadeInRight">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie text-success me-2"></i>
                                Statistiques Détaillées
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-eye text-primary fa-2x mb-2"></i>
                                        <h4 class="mb-1">{{ \App\Models\Patronyme::sum('views_count') ?? 0 }}</h4>
                                        <small class="text-muted">Vues totales</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-heart text-danger fa-2x mb-2"></i>
                                        <h4 class="mb-1">{{ \DB::table('favorites')->count() }}</h4>
                                        <small class="text-muted">Favoris</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-comments text-info fa-2x mb-2"></i>
                                        <h4 class="mb-1">{{ \App\Models\Commentaire::count() }}</h4>
                                        <small class="text-muted">Commentaires</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-users text-warning fa-2x mb-2"></i>
                                        <h4 class="mb-1">{{ \App\Models\User::where('role', 'admin')->count() }}</h4>
                                        <small class="text-muted">Admins</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Admin Features -->
            <div class="row">
                <div class="col-12">
                    <h2 class="display-6 fw-bold text-center mb-5 animate__animated animate__fadeInUp">
                        <i class="fas fa-star text-warning me-3"></i>
                        Outils d'Administration
                    </h2>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-chart-bar fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Statistiques Avancées</h5>
                            <p class="card-text">
                                Consultez des analyses détaillées et des rapports sur l'utilisation.
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
                                <i class="fas fa-search fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Gérer les Patronymes</h5>
                            <p class="card-text">
                                Recherchez, modifiez et gérez tous les patronymes de la base de données.
                            </p>
                            <a href="{{ route('patronymes.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Gérer
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
                        &copy; {{ date('Y') }} Répertoire des Patronymes - Administration. Tous droits réservés.
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