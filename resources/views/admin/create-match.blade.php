<x-layouts.app title="Admin - Nouveau Match">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4">
            
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('admin.matches') }}" class="text-soboa-orange hover:underline font-bold mb-2 inline-block">
                    ← Retour aux matchs
                </a>
                <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3">
                    <span class="text-4xl">⚽</span> Nouveau Match
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
                <form action="{{ route('admin.store-match') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Équipe domicile *</label>
                                <select name="home_team_id" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                                    <option value="">Sélectionner...</option>
                                    @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('home_team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Équipe extérieur *</label>
                                <select name="away_team_id" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                                    <option value="">Sélectionner...</option>
                                    @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('away_team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Date et heure du match *</label>
                            <input type="datetime-local" name="match_date" value="{{ old('match_date') }}" required
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Groupe / Phase</label>
                            <input type="text" name="group_name" value="{{ old('group_name') }}"
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue"
                                   placeholder="Ex: Groupe A, Quarts de finale, etc.">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Statut *</label>
                            <select name="status" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-soboa-blue focus:border-soboa-blue">
                                <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Programmé</option>
                                <option value="live" {{ old('status') === 'live' ? 'selected' : '' }}>En cours</option>
                                <option value="finished" {{ old('status') === 'finished' ? 'selected' : '' }}>Terminé</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-4 pt-4 border-t">
                            <a href="{{ route('admin.matches') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition">
                                Annuler
                            </a>
                            <button type="submit" class="bg-soboa-blue hover:bg-soboa-blue/90 text-white font-bold py-3 px-6 rounded-lg transition">
                                Créer le match
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layouts.app>
