<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Répertoire des Patronymes') }} - Import</title>

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
            min-height: 50vh;
            display: flex;
            align-items: center;
        }
        .upload-area {
            border: 2px dashed #28a745;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #20c997;
            background-color: #f8f9fa;
        }
        .upload-area.dragover {
            border-color: #20c997;
            background-color: #e8f5e8;
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
                        <a class="nav-link" href="{{ route('patronymes.index') }}">
                            <i class="fas fa-search me-1"></i>Patronymes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.import') }}">
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
                            <i class="fas fa-upload me-3"></i>
                            Import de Patronymes
                        </h1>
                        <p class="lead text-white mb-4">
                            Importez des patronymes en masse depuis des fichiers Excel ou CSV.
                            Assurez-vous que vos fichiers respectent le format requis pour un import optimal.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="animate__animated animate__fadeInRight">
                        <div class="card shadow-lg">
                            <div class="card-body p-4">
                                <h3 class="card-title text-center mb-4">
                                    <i class="fas fa-info-circle text-success"></i> Format Requis
                                </h3>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Fichier .xlsx ou .csv</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Colonnes: nom, origine, signification</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Encodage UTF-8</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Maximum 1000 lignes</li>
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
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card animate__animated animate__fadeInUp">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-upload text-success me-2"></i>
                                Sélectionner un fichier à importer
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.import.run') }}" method="POST" enctype="multipart/form-data" id="importForm">
                                @csrf
                                
                                <!-- Upload Area -->
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-success mb-3"></i>
                                    <h5>Glissez-déposez votre fichier ici</h5>
                                    <p class="text-muted">ou cliquez pour sélectionner un fichier</p>
                                    <input type="file" name="file" id="fileInput" class="d-none" accept=".xlsx,.csv" required>
                                    <button type="button" class="btn btn-outline-success" onclick="document.getElementById('fileInput').click()">
                                        <i class="fas fa-folder-open me-2"></i>Parcourir les fichiers
                                    </button>
                                </div>

                                <!-- File Info -->
                                <div id="fileInfo" class="mt-3 d-none">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file me-2"></i>
                                        <span id="fileName"></span>
                                        <span class="float-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                        <i class="fas fa-upload me-2"></i>Lancer l'import
                                    </button>
                                    <a href="{{ route('admin.export') }}" class="btn btn-outline-primary btn-lg ms-3">
                                        <i class="fas fa-download me-2"></i>Télécharger un modèle
                                    </a>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const submitBtn = document.getElementById('submitBtn');

            // Drag and drop functionality
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFile(files[0]);
                }
            });

            // Click to select file
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });

            // File input change
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFile(e.target.files[0]);
                }
            });

            function handleFile(file) {
                // Validate file type
                const allowedTypes = ['.xlsx', '.csv'];
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                
                if (!allowedTypes.includes(fileExtension)) {
                    alert('Veuillez sélectionner un fichier .xlsx ou .csv');
                    return;
                }

                // Validate file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux. Maximum 10MB.');
                    return;
                }

                fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                fileInfo.classList.remove('d-none');
                submitBtn.disabled = false;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            window.clearFile = function() {
                fileInput.value = '';
                fileInfo.classList.add('d-none');
                submitBtn.disabled = true;
            };

            // Form submission
            document.getElementById('importForm').addEventListener('submit', function(e) {
                if (!fileInput.files.length) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un fichier à importer.');
                    return;
                }

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Import en cours...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>
