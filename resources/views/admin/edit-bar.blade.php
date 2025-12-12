<x-layouts.app title="Admin - Modifier Point de Vente">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4">
            
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('admin.bars') }}" class="text-soboa-orange hover:underline font-bold mb-2 inline-block">
                    ← Retour aux points de vente
                </a>
                <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3">
                    <span class="text-4xl">📍</span> Modifier Point de Vente
                </h1>
            </div>

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg p-6">
                <form action="{{ route('admin.update-bar', $bar->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Nom du point de vente *</label>
                            <input type="text" name="name" value="{{ old('name', $bar->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue"
                                   placeholder="Ex: Maquis Chez Tantie">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Adresse complète *</label>
                            <input type="text" name="address" value="{{ old('address', $bar->address) }}" required
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue"
                                   placeholder="Ex: Cocody, Rue des Jardins, Abidjan">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Latitude *</label>
                                <input type="number" name="latitude" value="{{ old('latitude', $bar->latitude) }}" required
                                       step="0.00000001" min="-90" max="90"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue"
                                       placeholder="Ex: 5.35837443">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Longitude *</label>
                                <input type="number" name="longitude" value="{{ old('longitude', $bar->longitude) }}" required
                                       step="0.00000001" min="-180" max="180"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue"
                                       placeholder="Ex: -3.94398784">
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-700 text-sm">
                                💡 <strong>Astuce :</strong> Pour obtenir les coordonnées GPS, ouvrez Google Maps, faites un clic droit sur l'emplacement souhaité et copiez les coordonnées.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ $bar->is_active ? 'checked' : '' }}
                                   class="w-5 h-5 text-soboa-blue border-gray-300 rounded focus:ring-soboa-blue">
                            <label for="is_active" class="text-gray-700 font-medium">Point de vente actif</label>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t">
                            <form action="{{ route('admin.delete-bar', $bar->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce point de vente ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-bold">
                                    🗑️ Supprimer
                                </button>
                            </form>
                            <div class="flex gap-4">
                                <a href="{{ route('admin.bars') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition">
                                    Annuler
                                </a>
                                <button type="submit" class="bg-soboa-blue hover:bg-soboa-blue/90 text-white font-bold py-3 px-6 rounded-lg transition">
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layouts.app>
