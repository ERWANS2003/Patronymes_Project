<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Répertoire des Patronymes') }}</title>

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
            min-height: 100vh;
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
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="animate__animated animate__fadeInLeft">
                        <h1 class="display-4 fw-bold text-white mb-4">
                            <i class="fas fa-users me-3"></i>
                            Répertoire des Patronymes
                        </h1>
                        <p class="lead text-white mb-4">
                            Découvrez l'histoire et la signification des patronymes du Burkina Faso.
                            Une base de données complète et interactive pour explorer notre patrimoine culturel.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('patronymes.index') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-search me-2"></i>Explorer
                            </a>
                            @guest
                                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>S'inscrire
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="animate__animated animate__fadeInRight">
                        <!-- Search Form -->
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

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold animate__animated animate__fadeInUp">
                    <i class="fas fa-star text-warning me-3"></i>
                    Fonctionnalités
                </h2>
                <p class="lead text-muted animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                    Une plateforme complète pour explorer le patrimoine patronymique
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-database fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Base de Données Complète</h5>
                            <p class="card-text">
                                Accédez à des milliers de patronymes avec leurs origines, significations et histoires.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Géolocalisation</h5>
                            <p class="card-text">
                                Explorez les patronymes par région, province et commune du Burkina Faso.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-heart fa-3x text-danger"></i>
                            </div>
                            <h5 class="card-title">Favoris Personnels</h5>
                            <p class="card-text">
                                Sauvegardez vos patronymes préférés et créez votre collection personnelle.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-chart-bar fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Statistiques</h5>
                            <p class="card-text">
                                Visualisez les données avec des graphiques et analyses détaillées.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Communauté</h5>
                            <p class="card-text">
                                Contribuez et partagez vos connaissances avec la communauté.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.6s">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-mobile-alt fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title">Responsive</h5>
                            <p class="card-text">
                                Interface adaptée à tous les appareils : mobile, tablette et desktop.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp">
                        <div class="stats-counter" data-count="{{ app()->environment('testing') ? 0 : \App\Models\Patronyme::count() }}">0</div>
                        <h5>Patronymes</h5>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="stats-counter" data-count="{{ app()->environment('testing') ? 0 : \App\Models\Region::count() }}">0</div>
                        <h5>Régions</h5>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="stats-counter" data-count="{{ app()->environment('testing') ? 0 : \App\Models\User::count() }}">0</div>
                        <h5>Utilisateurs</h5>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                        <div class="stats-counter" data-count="{{ app()->environment('testing') ? 0 : \DB::table('favorites')->count() }}">0</div>
                        <h5>Favoris</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5">
        <div class="container text-center">
            <div class="animate__animated animate__fadeInUp">
                <h2 class="display-5 fw-bold mb-4">Prêt à explorer ?</h2>
                <p class="lead text-muted mb-4">
                    Découvrez dès maintenant l'histoire fascinante des patronymes du Burkina Faso
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('patronymes.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Commencer l'exploration
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Rejoindre la communauté
                        </a>
                    @endguest
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
