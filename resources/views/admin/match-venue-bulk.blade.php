<x-layouts.app title="Admin - Ajout en masse PDV / Matchs">
    <div class="bg-gray-100 min-h-screen py-8" x-data="bulkAssign()">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3">
                        <span class="text-4xl">➕</span> Ajout en masse PDV / Matchs
                    </h1>
                    <p class="text-gray-600 mt-2">Sélectionnez plusieurs points de vente et plusieurs matchs : toutes les combinaisons seront créées d'un coup.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.match-venue-matrix') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition">
                        📊 Matrice
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition">
                        ← Retour
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-6 font-medium">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filtres -->
            <div class="bg-white rounded-xl shadow-lg p-5 mb-6">
                <form method="GET" action="{{ route('admin.match-venue-bulk') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="phase" class="block text-sm font-bold text-gray-700 mb-2">Phase du tournoi</label>
                        <select id="phase" name="phase" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-soboa-orange">
                            <option value="">Toutes les phases</option>
                            @foreach($phases as $phaseKey => $phaseName)
                                <option value="{{ $phaseKey }}" {{ $phase === $phaseKey ? 'selected' : '' }}>{{ $phaseName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="zone" class="block text-sm font-bold text-gray-700 mb-2">Zone (PDV)</label>
                        <select id="zone" name="zone" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-soboa-orange">
                            <option value="">Toutes les zones</option>
                            @foreach($zones as $zoneName)
                                <option value="{{ $zoneName }}" {{ $zone === $zoneName ? 'selected' : '' }}>{{ $zoneName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="flex items-center gap-2 px-4 py-2 border border-blue-300 rounded-lg bg-blue-50 cursor-pointer hover:bg-blue-100">
                        <input type="checkbox" name="upcoming_only" value="1" {{ $upcomingOnly ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm text-gray-700">Matchs non terminés uniquement</span>
                    </label>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.match-venue-bulk-store') }}" @submit="onSubmit($event)">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Colonne PDV -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
                        <div class="px-5 py-4 bg-soboa-blue text-white flex items-center justify-between">
                            <h2 class="font-bold flex items-center gap-2">🏪 Points de vente
                                <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full" x-text="venueCount + ' / {{ $bars->count() }}'"></span>
                            </h2>
                            <div class="flex gap-2 text-xs">
                                <button type="button" @click="toggleAll('venue', true)" class="bg-white/20 hover:bg-white/30 px-2 py-1 rounded">Tout cocher</button>
                                <button type="button" @click="toggleAll('venue', false)" class="bg-white/20 hover:bg-white/30 px-2 py-1 rounded">Tout décocher</button>
                            </div>
                        </div>
                        <div class="p-3">
                            <input type="text" x-model="venueSearch" placeholder="Rechercher un PDV..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2">
                        </div>
                        <div class="max-h-[28rem] overflow-y-auto px-3 pb-3 space-y-1">
                            @forelse($bars as $bar)
                                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer venue-row"
                                       data-search="{{ strtolower($bar->name . ' ' . $bar->zone) }}">
                                    <input type="checkbox" name="venue_ids[]" value="{{ $bar->id }}" class="venue-cb w-4 h-4 text-soboa-orange rounded" @change="recount()">
                                    <span class="flex-1">
                                        <span class="font-medium text-gray-900">{{ $bar->name }}</span>
                                        @if($bar->zone)<span class="text-xs text-gray-500 ml-2">{{ $bar->zone }}</span>@endif
                                    </span>
                                </label>
                            @empty
                                <p class="text-center text-gray-500 py-8">Aucun PDV pour ces filtres.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Colonne Matchs -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
                        <div class="px-5 py-4 bg-soboa-orange text-white flex items-center justify-between">
                            <h2 class="font-bold flex items-center gap-2">⚽ Matchs
                                <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full" x-text="matchCount + ' / {{ $matches->count() }}'"></span>
                            </h2>
                            <div class="flex gap-2 text-xs">
                                <button type="button" @click="toggleAll('match', true)" class="bg-white/20 hover:bg-white/30 px-2 py-1 rounded">Tout cocher</button>
                                <button type="button" @click="toggleAll('match', false)" class="bg-white/20 hover:bg-white/30 px-2 py-1 rounded">Tout décocher</button>
                            </div>
                        </div>
                        <div class="p-3">
                            <input type="text" x-model="matchSearch" placeholder="Rechercher un match..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2">
                        </div>
                        <div class="max-h-[28rem] overflow-y-auto px-3 pb-3 space-y-1">
                            @forelse($matches as $match)
                                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer match-row"
                                       data-search="{{ strtolower($match->home_name_fr . ' ' . $match->away_name_fr) }}">
                                    <input type="checkbox" name="match_ids[]" value="{{ $match->id }}" class="match-cb w-4 h-4 text-soboa-orange rounded" @change="recount()">
                                    <span class="flex-1">
                                        <span class="font-medium text-gray-900">{{ $match->home_name_fr }} <span class="text-gray-400">vs</span> {{ $match->away_name_fr }}</span>
                                        <span class="block text-xs text-gray-500">
                                            {{ $match->match_date->format('d/m/Y H:i') }}
                                            @if($match->animations_count > 0)
                                                · <span class="text-green-600">{{ $match->animations_count }} PDV déjà</span>
                                            @endif
                                        </span>
                                    </span>
                                </label>
                            @empty
                                <p class="text-center text-gray-500 py-8">Aucun match pour ces filtres.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Barre d'action -->
                <div class="sticky bottom-4 mt-6">
                    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 px-5 py-4 flex items-center justify-between">
                        <p class="text-gray-700">
                            <span class="font-bold text-soboa-blue" x-text="venueCount"></span> PDV ×
                            <span class="font-bold text-soboa-orange" x-text="matchCount"></span> matchs =
                            <span class="font-black text-lg" x-text="venueCount * matchCount"></span> assignation(s)
                        </p>
                        <button type="submit"
                                :disabled="venueCount === 0 || matchCount === 0"
                                :class="(venueCount === 0 || matchCount === 0) ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                                class="text-white font-bold py-2.5 px-6 rounded-lg transition">
                            ✅ Créer les assignations
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bulkAssign() {
            return {
                venueCount: 0,
                matchCount: 0,
                venueSearch: '',
                matchSearch: '',
                init() {
                    this.recount();
                    this.$watch('venueSearch', v => this.filterRows('venue', v));
                    this.$watch('matchSearch', v => this.filterRows('match', v));
                },
                recount() {
                    this.venueCount = document.querySelectorAll('.venue-cb:checked').length;
                    this.matchCount = document.querySelectorAll('.match-cb:checked').length;
                },
                toggleAll(type, state) {
                    // ne coche que les lignes visibles (respect du filtre de recherche)
                    document.querySelectorAll('.' + type + '-row').forEach(row => {
                        if (row.style.display === 'none') return;
                        const cb = row.querySelector('input[type=checkbox]');
                        if (cb) cb.checked = state;
                    });
                    this.recount();
                },
                filterRows(type, term) {
                    term = (term || '').toLowerCase();
                    document.querySelectorAll('.' + type + '-row').forEach(row => {
                        row.style.display = row.dataset.search.includes(term) ? '' : 'none';
                    });
                },
                onSubmit(e) {
                    const total = this.venueCount * this.matchCount;
                    if (total > 200 && !confirm(total + ' assignations vont être créées. Continuer ?')) {
                        e.preventDefault();
                    }
                }
            }
        }
    </script>
</x-layouts.app>
