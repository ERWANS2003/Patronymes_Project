<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    <i class="mr-2 fas fa-user-circle"></i>{{ __('Détail du patronyme') }} : {{ $patronyme->nom }}
                </h2>
                @if($patronyme->views_count)
                    <p class="mt-1 text-sm text-gray-500">
                        <i class="fas fa-eye"></i> {{ number_format($patronyme->views_count) }} vues
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                @auth
                    <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors favorite-btn"
                            data-patronyme-id="{{ $patronyme->id }}"
                            data-favorited="{{ $patronyme->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}">
                        <i class="mr-2 {{ $patronyme->isFavoritedBy(auth()->id()) ? 'fas' : 'far' }} fa-heart"></i>
                        {{ $patronyme->isFavoritedBy(auth()->id()) ? 'Favori' : 'Ajouter aux favoris' }}
                    </button>

                    <button onclick="sharePatronyme()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="mr-2 fas fa-share"></i> Partager
                    </button>

                    <button onclick="printPatronyme()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="mr-2 fas fa-print"></i> Imprimer
                    </button>

                    @if(Auth::user()->canContribute())
                        <a href="{{ route('patronymes.edit', $patronyme) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-yellow-600 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                            <i class="mr-2 fas fa-edit"></i> Modifier
                        </a>
                    @endif

                    @if(Auth::user()->isAdmin())
                        <form action="{{ route('patronymes.destroy', $patronyme) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patronyme ?')">
                                <i class="mr-2 fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    @endif
                @endauth

                <a href="{{ route('patronymes.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="mr-2 fas fa-arrow-left"></i> Retour à la liste
                </a>

                <a href="{{ route('statistics.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="mr-2 fas fa-chart-bar"></i> Statistiques
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 patronyme-content">
                    <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Informations générales</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patronyme->nom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Origine</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patronyme->origine ?? 'Non spécifiée' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Région</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patronyme->region->name ?? 'Non spécifiée' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Groupe ethnique</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patronyme->groupeEthnique->nom ?? 'Non spécifié' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Langue</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patronyme->langue->nom ?? 'Non spécifiée' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Mode de transmission</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patronyme->modeTransmission->type ?? 'Non spécifié' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Signification</h3>
                            <p class="p-4 text-gray-700 rounded-md bg-gray-50">
                                {{ $patronyme->signification ?? 'Aucune information disponible' }}
                            </p>

                            <h3 class="mt-6 mb-4 text-lg font-medium text-gray-900">Histoire</h3>
                            <p class="p-4 text-gray-700 rounded-md bg-gray-50">
                                {{ $patronyme->histoire ?? 'Aucune information historique disponible' }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    @auth
                        @if(Auth::user()->isAdmin())
                            <div class="flex justify-end pt-6 mt-6 space-x-3 border-t border-gray-200">
                                <a href="{{ route('patronymes.edit', $patronyme) }}" class="flex items-center px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                    <i class="mr-2 fas fa-edit"></i> Modifier
                                </a>
                                <form action="{{ route('patronymes.destroy', $patronyme) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patronyme ?')">
                                        <i class="mr-2 fas fa-trash"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Section Commentaires -->
            <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Commentaires</h3>

                    @auth
                        <!-- Formulaire d'ajout de commentaire -->
                        <form action="{{ route('commentaires.store') }}" method="POST" class="mb-6">
                            @csrf
                            <input type="hidden" name="patronyme_id" value="{{ $patronyme->id }}">
                            <div>
                                <label for="contenu" class="block mb-2 text-sm font-medium text-gray-700">Ajouter un commentaire</label>
                                <textarea name="contenu" id="contenu" rows="3" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Partagez vos connaissances sur ce patronyme..."></textarea>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                    <i class="mr-2 fas fa-comment"></i> Publier
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="p-4 mb-6 border border-blue-200 rounded-md bg-blue-50">
                            <p class="text-blue-800">
                                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-800">Connectez-vous</a>
                                pour ajouter un commentaire.
                            </p>
                        </div>
                    @endauth

                    <!-- Liste des commentaires -->
                    <div class="space-y-4">
                        @forelse($patronyme->commentaires as $commentaire)
                            <div class="p-4 border border-gray-200 rounded-md">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $commentaire->utilisateur->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $commentaire->date_commentaire->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @auth
                                        @if(Auth::user()->isAdmin() || Auth::user()->id === $commentaire->utilisateur_id)
                                            <form action="{{ route('commentaires.destroy', $commentaire) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                                <p class="mt-2 text-gray-700">{{ $commentaire->contenu }}</p>
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <i class="mb-4 text-4xl text-gray-400 fas fa-comments"></i>
                                <p class="text-gray-500">Aucun commentaire pour le moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle favorite toggle
            document.querySelectorAll('.favorite-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const patronymeId = this.dataset.patronymeId;
                    const isFavorited = this.dataset.favorited === 'true';

                    fetch(`/patronymes/${patronymeId}/favorite`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.isFavorited) {
                            this.innerHTML = '<i class="mr-2 fas fa-heart"></i>Favori';
                            this.classList.remove('text-red-600', 'bg-red-50', 'border-red-200', 'hover:bg-red-100');
                            this.classList.add('text-red-700', 'bg-red-100', 'border-red-300', 'hover:bg-red-200');
                            this.dataset.favorited = 'true';
                        } else {
                            this.innerHTML = '<i class="mr-2 far fa-heart"></i>Favoris';
                            this.classList.remove('text-red-700', 'bg-red-100', 'border-red-300', 'hover:bg-red-200');
                            this.classList.add('text-red-600', 'bg-red-50', 'border-red-200', 'hover:bg-red-100');
                            this.dataset.favorited = 'false';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de la mise à jour des favoris.');
                    });
                });
            });
        });

        // Fonction pour partager un patronyme
        function sharePatronyme() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $patronyme->nom }} - Répertoire des Patronymes',
                    text: 'Découvrez l\'origine et la signification du patronyme {{ $patronyme->nom }}',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback pour les navigateurs qui ne supportent pas l'API Web Share
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    alert('Lien copié dans le presse-papiers !');
                }).catch(() => {
                    // Fallback pour les navigateurs plus anciens
                    const textArea = document.createElement('textarea');
                    textArea.value = url;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    alert('Lien copié dans le presse-papiers !');
                });
            }
        }

        // Fonction pour imprimer un patronyme
        function printPatronyme() {
            const printContent = document.querySelector('.patronyme-content');
            const printWindow = window.open('', '_blank');

            printWindow.document.write(`
                <html>
                    <head>
                        <title>{{ $patronyme->nom }} - Répertoire des Patronymes</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            .header { text-align: center; margin-bottom: 30px; }
                            .section { margin-bottom: 20px; }
                            .section h3 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
                            .info { margin: 10px 0; }
                            .label { font-weight: bold; color: #666; }
                            .value { margin-left: 10px; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1>{{ $patronyme->nom }}</h1>
                            <p>Répertoire des Patronymes du Burkina Faso</p>
                        </div>
                        ${printContent.innerHTML}
                    </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.print();
        }
    </script>
</x-app-layout>
