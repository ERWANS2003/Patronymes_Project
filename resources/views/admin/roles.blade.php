<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Répertoire des Patronymes') }} - Gestion des Rôles</title>

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
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
            min-height: 50vh;
            display: flex;
            align-items: center;
        }
        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .role-user { background-color: #6c757d; color: white; }
        .role-contributeur { background-color: #28a745; color: white; }
        .role-admin { background-color: #dc3545; color: white; }
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
                        <a class="nav-link active" href="{{ route('admin.roles') }}">
                            <i class="fas fa-users-cog me-1"></i>Gestion des rôles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('patronymes.index') }}">
                            <i class="fas fa-search me-1"></i>Patronymes
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-arrow-left me-1"></i>Retour au dashboard
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
                            <i class="fas fa-users-cog me-3"></i>
                            Gestion des Rôles
                        </h1>
                        <p class="lead text-white mb-4">
                            Gérez les rôles et permissions des utilisateurs. 
                            Seuls les administrateurs peuvent modifier les rôles et autoriser les contributions.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="animate__animated animate__fadeInRight">
                        <div class="card shadow-lg">
                            <div class="card-body p-4">
                                <h3 class="card-title text-center mb-4">
                                    <i class="fas fa-info-circle text-primary"></i> Rôles Disponibles
                                </h3>
                                <ul class="list-unstyled">
                                    <li><span class="badge role-user me-2">Utilisateur</span> Lecture seule</li>
                                    <li><span class="badge role-contributeur me-2">Contributeur</span> Peut contribuer</li>
                                    <li><span class="badge role-admin me-2">Admin</span> Accès complet</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-5 bg-light">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card animate__animated animate__fadeInUp">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users text-primary me-2"></i>
                        Liste des Utilisateurs
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <span class="text-white fw-bold">{{ substr($user->name, 0, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge role-{{ $user->role }} role-badge">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @if($user->can_contribute)
                                                    <span class="badge bg-success">Peut contribuer</span>
                                                @endif
                                                @if($user->can_manage_roles)
                                                    <span class="badge bg-warning">Peut gérer les rôles</span>
                                                @endif
                                                @if(!$user->can_contribute && !$user->can_manage_roles)
                                                    <span class="text-muted">Aucune permission spéciale</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $user->id }}">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </button>
                                                @if($user->id !== Auth::id())
                                                    <a href="{{ route('admin.roles.toggle-contribution', $user) }}" class="btn btn-sm btn-outline-{{ $user->can_contribute ? 'warning' : 'success' }}">
                                                        <i class="fas fa-{{ $user->can_contribute ? 'ban' : 'check' }}"></i>
                                                        {{ $user->can_contribute ? 'Désactiver' : 'Activer' }} contribution
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal pour modifier le rôle -->
                                    <div class="modal fade" id="editRoleModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modifier le rôle de {{ $user->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.roles.update', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="role" class="form-label">Rôle</label>
                                                            <select name="role" id="role" class="form-select" required>
                                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                                                <option value="contributeur" {{ $user->role === 'contributeur' ? 'selected' : '' }}>Contributeur</option>
                                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="can_contribute" id="can_contribute{{ $user->id }}" {{ $user->can_contribute ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="can_contribute{{ $user->id }}">
                                                                Peut contribuer
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="can_manage_roles" id="can_manage_roles{{ $user->id }}" {{ $user->can_manage_roles ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="can_manage_roles{{ $user->id }}">
                                                                Peut gérer les rôles
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
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
</body>
</html>
