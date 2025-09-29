<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Répertoire des Patronymes') }} - Créer un Utilisateur</title>

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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 40vh;
            display: flex;
            align-items: center;
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
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-1"></i>Utilisateurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.roles') }}">
                            <i class="fas fa-users-cog me-1"></i>Gestion des rôles
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
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
                            <i class="fas fa-user-plus me-3"></i>
                            Créer un Utilisateur
                        </h1>
                        <p class="lead text-white mb-4">
                            Ajoutez un nouvel utilisateur à la plateforme avec des rôles et permissions spécifiques.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card animate__animated animate__fadeInUp">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-plus text-primary me-2"></i>
                                Informations de l'utilisateur
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.users.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nom complet *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Mot de passe *</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                               id="password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe *</label>
                                        <input type="password" class="form-control"
                                               id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">Rôle *</label>
                                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                            <option value="">Sélectionner un rôle</option>
                                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Utilisateur</option>
                                            <option value="contributeur" {{ old('role') == 'contributeur' ? 'selected' : '' }}>Contributeur</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="mb-3">Permissions spéciales</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="can_contribute" id="can_contribute"
                                               value="1" {{ old('can_contribute') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_contribute">
                                            Peut contribuer (ajouter/modifier des patronymes)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="can_manage_roles" id="can_manage_roles"
                                               value="1" {{ old('can_manage_roles') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_manage_roles">
                                            Peut gérer les rôles et permissions
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Créer l'utilisateur
                                    </button>
                                </div>
                            </form>
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
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
