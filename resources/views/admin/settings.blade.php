<x-layouts.app title="Admin - Paramètres">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-3xl mx-auto px-4">
            
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('admin.dashboard') }}" class="text-soboa-orange hover:underline font-bold mb-2 inline-block">
                    ← Retour au dashboard
                </a>
                <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3">
                    <span class="text-4xl">⚙️</span> Paramètres
                </h1>
                <p class="text-gray-600 mt-2">Configurez les paramètres généraux de l'application</p>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.update-settings') }}" method="POST">
                @csrf

                <!-- Paramètres Généraux -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span>🌐</span> Paramètres Généraux
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Nom du site</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'CAN 2025' }}"
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" 
                                   {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}
                                   class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <label for="maintenance_mode" class="text-gray-700">
                                <span class="font-bold">Mode Maintenance</span>
                                <span class="block text-sm text-gray-500">Activez pour bloquer l'accès au site</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Paramètres Geofencing -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span>📍</span> Geofencing
                    </h2>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Rayon de geofencing (mètres)</label>
                        <input type="number" name="geofencing_radius" value="{{ $settings['geofencing_radius'] ?? 200 }}"
                               min="10" max="5000" step="10"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                        <p class="text-gray-500 text-sm mt-1">Distance maximale pour valider la position d'un utilisateur près d'un point de vente</p>
                    </div>
                </div>

                <!-- Paramètres Points -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span>🏆</span> Attribution des Points
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Points pour score exact</label>
                            <input type="number" name="points_exact_score" value="{{ $settings['points_exact_score'] ?? 10 }}"
                                   min="0" max="100"
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                            <p class="text-gray-500 text-sm mt-1">Points attribués quand le score prédit est exact</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Points pour bon vainqueur</label>
                            <input type="number" name="points_correct_winner" value="{{ $settings['points_correct_winner'] ?? 5 }}"
                                   min="0" max="100"
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                            <p class="text-gray-500 text-sm mt-1">Points attribués quand le vainqueur prédit est correct</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Points pour match nul prédit</label>
                            <input type="number" name="points_correct_draw" value="{{ $settings['points_correct_draw'] ?? 3 }}"
                                   min="0" max="100"
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                            <p class="text-gray-500 text-sm mt-1">Points attribués quand un match nul est correctement prédit</p>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif -->
                <div class="bg-soboa-blue/10 border border-soboa-blue/20 rounded-xl p-6 mb-6">
                    <h3 class="font-bold text-soboa-blue mb-3">📋 Récapitulatif de l'attribution des points</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-center gap-2">
                            <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs">✓</span>
                            <span>Score exact = <strong id="recap-exact">{{ $settings['points_exact_score'] ?? 10 }}</strong> points</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-6 h-6 bg-soboa-orange text-white rounded-full flex items-center justify-center text-xs">✓</span>
                            <span>Bon vainqueur = <strong id="recap-winner">{{ $settings['points_correct_winner'] ?? 5 }}</strong> points</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-6 h-6 bg-gray-500 text-white rounded-full flex items-center justify-center text-xs">✓</span>
                            <span>Match nul prédit = <strong id="recap-draw">{{ $settings['points_correct_draw'] ?? 3 }}</strong> points</span>
                        </li>
                    </ul>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-soboa-blue hover:bg-soboa-blue/90 text-white font-bold py-3 px-8 rounded-lg transition">
                        💾 Enregistrer les paramètres
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        // Mise à jour dynamique du récapitulatif
        document.querySelector('[name="points_exact_score"]').addEventListener('input', function() {
            document.getElementById('recap-exact').textContent = this.value;
        });
        document.querySelector('[name="points_correct_winner"]').addEventListener('input', function() {
            document.getElementById('recap-winner').textContent = this.value;
        });
        document.querySelector('[name="points_correct_draw"]').addEventListener('input', function() {
            document.getElementById('recap-draw').textContent = this.value;
        });
    </script>
</x-layouts.app>
