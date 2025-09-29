<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <i class="mr-2 fas fa-file-import"></i> Importer des patronymes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.import.run') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Fichier (.xlsx ou .csv)</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i> Lancer l'import
                        </button>
                        <a href="{{ route('admin.export') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-download me-2"></i> Exporter les patronymes
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
