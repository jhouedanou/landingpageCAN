<x-layouts.app title="Admin - Pronostics">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3">
                        <span class="text-4xl">🎯</span> Pronostics
                    </h1>
                    <p class="text-gray-600 mt-2">Consultez tous les pronostics des utilisateurs</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition">
                    ← Retour
                </a>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
            @endif

            <!-- Filtres -->
            <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-gray-700 font-bold mb-2 text-sm">Filtrer par match</label>
                        <select name="match_id" class="w-full border border-gray-300 rounded-lg p-2">
                            <option value="">Tous les matchs</option>
                            @foreach($matches as $match)
                            <option value="{{ $match->id }}" {{ request('match_id') == $match->id ? 'selected' : '' }}>
                                {{ $match->team_a }} vs {{ $match->team_b }} ({{ $match->match_date->format('d/m') }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-gray-700 font-bold mb-2 text-sm">Filtrer par utilisateur</label>
                        <select name="user_id" class="w-full border border-gray-300 rounded-lg p-2">
                            <option value="">Tous les utilisateurs</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->phone }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-soboa-blue hover:bg-soboa-blue/90 text-white font-bold py-2 px-4 rounded-lg">
                        Filtrer
                    </button>
                    <a href="{{ route('admin.predictions') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg">
                        Réinitialiser
                    </a>
                </form>
            </div>

            <!-- Liste -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left p-4 font-bold text-gray-700">Utilisateur</th>
                            <th class="text-left p-4 font-bold text-gray-700">Match</th>
                            <th class="text-center p-4 font-bold text-gray-700">Pronostic</th>
                            <th class="text-center p-4 font-bold text-gray-700">Score Réel</th>
                            <th class="text-center p-4 font-bold text-gray-700">Points</th>
                            <th class="text-center p-4 font-bold text-gray-700">Date</th>
                            <th class="text-center p-4 font-bold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($predictions as $prediction)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-soboa-blue/20 rounded-full flex items-center justify-center font-bold text-soboa-blue text-sm">
                                        {{ mb_substr($prediction->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">{{ $prediction->user->name ?? 'Inconnu' }}</p>
                                        <p class="text-xs text-gray-500">{{ $prediction->user->phone ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    @if($prediction->match->homeTeam)
                                    <img src="https://flagcdn.com/w20/{{ $prediction->match->homeTeam->iso_code }}.png" class="w-5 h-4 rounded">
                                    @endif
                                    <span class="text-sm">{{ $prediction->match->team_a }}</span>
                                    <span class="text-gray-400 text-xs">vs</span>
                                    <span class="text-sm">{{ $prediction->match->team_b }}</span>
                                    @if($prediction->match->awayTeam)
                                    <img src="https://flagcdn.com/w20/{{ $prediction->match->awayTeam->iso_code }}.png" class="w-5 h-4 rounded">
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="font-bold text-lg text-soboa-blue">
                                    {{ $prediction->predicted_score_a }} - {{ $prediction->predicted_score_b }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($prediction->match->status === 'finished')
                                <span class="font-bold text-lg text-green-600">
                                    {{ $prediction->match->score_a }} - {{ $prediction->match->score_b }}
                                </span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($prediction->points_earned !== null)
                                <span class="font-black text-lg {{ $prediction->points_earned > 0 ? 'text-soboa-orange' : 'text-gray-400' }}">
                                    {{ $prediction->points_earned }} pts
                                </span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-center text-gray-500 text-sm">
                                {{ $prediction->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.delete-prediction', $prediction->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce pronostic ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm font-bold">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                Aucun pronostic trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $predictions->withQueryString()->links() }}
            </div>

        </div>
    </div>
</x-layouts.app>
