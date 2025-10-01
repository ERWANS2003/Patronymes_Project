<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-upload text-green-600 mr-2"></i>
                    Import de Patronymes
                </h1>
                <p class="text-gray-600 mt-1">
                    Importez des patronymes en masse depuis des fichiers Excel ou CSV
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Retour au dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Format Requirements -->
        <div class="card mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Format requis pour l'import
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-2"></i>
                            <span class="text-sm text-gray-700">Fichier .xlsx ou .csv</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-2"></i>
                            <span class="text-sm text-gray-700">Colonnes: nom, origine, signification</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-2"></i>
                            <span class="text-sm text-gray-700">Encodage UTF-8</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-2"></i>
                            <span class="text-sm text-gray-700">Maximum 1000 lignes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="card">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    <i class="fas fa-file-upload text-green-600 mr-2"></i>
                    Sélectionner un fichier à importer
                </h3>

                <form action="{{ route('admin.import.run') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <!-- Upload Area -->
                    <div class="upload-area border-2 border-dashed border-green-300 rounded-lg p-8 text-center hover:border-green-400 hover:bg-green-50 transition-colors cursor-pointer" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt text-4xl text-green-600 mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Glissez-déposez votre fichier ici</h4>
                        <p class="text-gray-600 mb-4">ou cliquez pour sélectionner un fichier</p>
                        <input type="file" name="file" id="fileInput" class="hidden" accept=".xlsx,.csv" required>
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-folder-open mr-2"></i>Parcourir les fichiers
                        </button>
                    </div>

                    <!-- File Info -->
                    <div id="fileInfo" class="mt-4 hidden">
                        <div class="alert alert-info flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-file mr-2"></i>
                                <span id="fileName"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="clearFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center space-x-4 mt-6">
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="fas fa-upload mr-2"></i>Lancer l'import
                        </button>
                        <a href="{{ route('admin.export') }}" class="btn btn-outline">
                            <i class="fas fa-download mr-2"></i>Télécharger un modèle
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .upload-area.dragover {
            border-color: #10b981;
            background-color: #ecfdf5;
        }
    </style>

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
                fileInfo.classList.remove('hidden');
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
                fileInfo.classList.add('hidden');
                submitBtn.disabled = true;
            };

            // Form submission
            document.getElementById('importForm').addEventListener('submit', function(e) {
                if (!fileInput.files.length) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un fichier à importer.');
                    return;
                }

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Import en cours...';
                submitBtn.disabled = true;
            });
        });
    </script>
</x-app-layout>
