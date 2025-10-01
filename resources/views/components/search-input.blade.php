@props([
    'name' => 'search',
    'placeholder' => 'Rechercher...',
    'value' => '',
    'suggestions' => true,
    'autocomplete' => 'off'
])

<div class="position-relative" x-data="searchInput()">
    <div class="input-group">
        <input
            type="text"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            class="form-control form-control-lg"
            x-model="query"
            @input="handleInput"
            @keydown.arrow-down="navigateSuggestions(1)"
            @keydown.arrow-up="navigateSuggestions(-1)"
            @keydown.enter="selectSuggestion"
            @keydown.escape="hideSuggestions"
            @blur="hideSuggestions"
            {{ $attributes }}
        >
        <button class="btn btn-primary btn-lg" type="submit">
            <i class="fas fa-search"></i>
        </button>
    </div>

    @if($suggestions)
    <div
        x-show="showSuggestions"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
        style="z-index: 1000; max-height: 300px; overflow-y: auto;"
    >
        <template x-for="(suggestion, index) in suggestions" :key="index">
            <div
                class="p-2 border-bottom cursor-pointer hover:bg-light"
                :class="{ 'bg-primary text-white': selectedIndex === index }"
                @click="selectSuggestion"
                @mouseenter="selectedIndex = index"
            >
                <div class="d-flex align-items-center">
                    <i :class="getSuggestionIcon(suggestion.type)" class="me-2"></i>
                    <div>
                        <div x-text="suggestion.label" class="fw-bold"></div>
                        <small x-show="suggestion.description" x-text="suggestion.description" class="text-muted"></small>
                    </div>
                </div>
            </div>
        </template>

        <div x-show="suggestions.length === 0 && query.length >= 2" class="p-3 text-center text-muted">
            <i class="fas fa-search me-2"></i>
            Aucune suggestion trouvée
        </div>
    </div>
    @endif
</div>

<script>
function searchInput() {
    return {
        query: '{{ $value }}',
        suggestions: [],
        showSuggestions: false,
        selectedIndex: -1,
        debounceTimer: null,

        handleInput() {
            clearTimeout(this.debounceTimer);

            if (this.query.length < 2) {
                this.hideSuggestions();
                return;
            }

            this.debounceTimer = setTimeout(() => {
                this.fetchSuggestions();
            }, 300);
        },

        async fetchSuggestions() {
            try {
                const response = await fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(this.query)}`);
                const data = await response.json();
                this.suggestions = data;
                this.showSuggestions = data.length > 0;
                this.selectedIndex = -1;
            } catch (error) {
                console.error('Erreur lors de la récupération des suggestions:', error);
                this.hideSuggestions();
            }
        },

        navigateSuggestions(direction) {
            if (!this.showSuggestions || this.suggestions.length === 0) return;

            this.selectedIndex += direction;

            if (this.selectedIndex < 0) {
                this.selectedIndex = this.suggestions.length - 1;
            } else if (this.selectedIndex >= this.suggestions.length) {
                this.selectedIndex = 0;
            }
        },

        selectSuggestion() {
            if (this.selectedIndex >= 0 && this.suggestions[this.selectedIndex]) {
                this.query = this.suggestions[this.selectedIndex].value;
            }
            this.hideSuggestions();

            // Soumettre le formulaire si c'est une recherche
            const form = this.$el.closest('form');
            if (form) {
                form.submit();
            }
        },

        hideSuggestions() {
            setTimeout(() => {
                this.showSuggestions = false;
                this.selectedIndex = -1;
            }, 200);
        },

        getSuggestionIcon(type) {
            const icons = {
                'patronyme': 'fas fa-user',
                'region': 'fas fa-globe',
                'groupe': 'fas fa-users',
                'similar': 'fas fa-search'
            };
            return icons[type] || 'fas fa-question';
        }
    }
}
</script>
