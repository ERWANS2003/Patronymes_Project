<div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-search text-blue-600 mr-2"></i>
        Recherche Avancée
    </h3>

    <!-- Recherche par lettre -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Recherche par lettre</label>
        <div class="flex flex-wrap gap-2">
            @foreach(range('A', 'Z') as $letter)
                <button type="button"
                        class="letter-btn px-3 py-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-blue-50 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        data-letter="{{ $letter }}"
                        onclick="searchByLetter('{{ $letter }}')">
                    {{ $letter }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Recherche par popularité -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Patronymes populaires</label>
        <button type="button"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                onclick="showPopularPatronymes()">
            <i class="fas fa-fire mr-2"></i>
            Voir les plus populaires
        </button>
    </div>

    <!-- Résultats de recherche par lettre -->
    <div id="letter-results" class="hidden">
        <h4 class="text-md font-semibold text-gray-900 mb-3">
            <i class="fas fa-list mr-2"></i>
            Résultats par lettre
        </h4>
        <div id="letter-results-content" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Les résultats seront chargés ici -->
        </div>
    </div>

    <!-- Résultats populaires -->
    <div id="popular-results" class="hidden">
        <h4 class="text-md font-semibold text-gray-900 mb-3">
            <i class="fas fa-fire mr-2"></i>
            Patronymes populaires
        </h4>
        <div id="popular-results-content" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Les résultats seront chargés ici -->
        </div>
    </div>
</div>

<script>
function searchByLetter(letter) {
    // Mettre à jour l'état visuel des boutons
    document.querySelectorAll('.letter-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-white', 'text-gray-700');
    });

    event.target.classList.add('bg-blue-600', 'text-white');
    event.target.classList.remove('bg-white', 'text-gray-700');

    // Afficher le loader
    const resultsDiv = document.getElementById('letter-results');
    const contentDiv = document.getElementById('letter-results-content');

    resultsDiv.classList.remove('hidden');
    contentDiv.innerHTML = '<div class="col-span-full text-center py-4"><i class="fas fa-spinner fa-spin text-blue-600"></i> Chargement...</div>';

    // Masquer les autres résultats
    document.getElementById('popular-results').classList.add('hidden');

    // Charger les données
    fetch(`/api/patronymes/letter/${letter}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                contentDiv.innerHTML = data.map(patronyme => `
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <h5 class="font-semibold text-gray-900">${patronyme.nom}</h5>
                        <p class="text-sm text-gray-600 mt-1">${patronyme.signification || 'Aucune signification disponible'}</p>
                        <div class="flex items-center mt-2 text-xs text-gray-500">
                            <i class="fas fa-eye mr-1"></i>
                            <span>${patronyme.views_count || 0} vues</span>
                        </div>
                    </div>
                `).join('');
            } else {
                contentDiv.innerHTML = '<div class="col-span-full text-center py-4 text-gray-500">Aucun patronyme trouvé pour la lettre ' + letter + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="col-span-full text-center py-4 text-red-500">Erreur lors du chargement des données</div>';
        });
}

function showPopularPatronymes() {
    // Afficher le loader
    const resultsDiv = document.getElementById('popular-results');
    const contentDiv = document.getElementById('popular-results-content');

    resultsDiv.classList.remove('hidden');
    contentDiv.innerHTML = '<div class="col-span-full text-center py-4"><i class="fas fa-spinner fa-spin text-blue-600"></i> Chargement...</div>';

    // Masquer les autres résultats
    document.getElementById('letter-results').classList.add('hidden');

    // Charger les données
    fetch('/api/popular-patronymes')
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                contentDiv.innerHTML = data.map(patronyme => `
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 hover:from-blue-100 hover:to-indigo-100 transition-colors">
                        <h5 class="font-semibold text-gray-900">${patronyme.nom}</h5>
                        <p class="text-sm text-gray-600 mt-1">${patronyme.signification || 'Aucune signification disponible'}</p>
                        <div class="flex items-center mt-2 text-xs text-gray-500">
                            <i class="fas fa-fire mr-1 text-orange-500"></i>
                            <span>${patronyme.views_count || 0} vues</span>
                        </div>
                    </div>
                `).join('');
            } else {
                contentDiv.innerHTML = '<div class="col-span-full text-center py-4 text-gray-500">Aucun patronyme populaire trouvé</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="col-span-full text-center py-4 text-red-500">Erreur lors du chargement des données</div>';
        });
}
</script>

