<x-layouts.app title="Admin - Dashboard">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4">
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3">
                    <span class="text-4xl">⚙️</span> Dashboard Administrateur
                </h1>
                <p class="text-gray-600 mt-2">Gérez les matchs, les scores, les utilisateurs et plus encore</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <span class="text-3xl">👥</span>
                    <p class="text-3xl font-black text-soboa-blue mt-2">{{ $stats['totalUsers'] }}</p>
                    <p class="text-gray-500 text-sm">Utilisateurs</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <span class="text-3xl">⚽</span>
                    <p class="text-3xl font-black text-soboa-blue mt-2">{{ $stats['totalMatches'] }}</p>
                    <p class="text-gray-500 text-sm">Total Matchs</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <span class="text-3xl">📍</span>
                    <p class="text-3xl font-black text-green-600 mt-2">{{ $stats['activeBars'] }}/{{ $stats['totalBars'] }}</p>
                    <p class="text-gray-500 text-sm">Points de Vente</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <span class="text-3xl">🎯</span>
                    <p class="text-3xl font-black text-soboa-orange mt-2">{{ $stats['totalPredictions'] }}</p>
                    <p class="text-gray-500 text-sm">Pronostics</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <a href="{{ route('admin.matches') }}" class="bg-soboa-blue hover:bg-soboa-blue/90 text-white rounded-xl p-4 shadow-lg flex flex-col items-center gap-2 transition-all hover:scale-105">
                    <span class="text-3xl">⚽</span>
                    <span class="font-bold text-sm text-center">Matchs</span>
                </a>
                <a href="{{ route('admin.users') }}" class="bg-soboa-orange hover:bg-soboa-orange/90 text-white rounded-xl p-4 shadow-lg flex flex-col items-center gap-2 transition-all hover:scale-105">
                    <span class="text-3xl">👥</span>
                    <span class="font-bold text-sm text-center">Utilisateurs</span>
                </a>
                <a href="{{ route('admin.bars') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-xl p-4 shadow-lg flex flex-col items-center gap-2 transition-all hover:scale-105">
                    <span class="text-3xl">�</span>
                    <span class="font-bold text-sm text-center">Points de Vente</span>
                </a>
                <a href="{{ route('admin.teams') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-xl p-4 shadow-lg flex flex-col items-center gap-2 transition-all hover:scale-105">
                    <span class="text-3xl">🏳️</span>
                    <span class="font-bold text-sm text-center">Équipes</span>
                </a>
                <a href="{{ route('admin.predictions') }}" class="bg-pink-600 hover:bg-pink-700 text-white rounded-xl p-4 shadow-lg flex flex-col items-center gap-2 transition-all hover:scale-105">
                    <span class="text-3xl">�</span>
                    <span class="font-bold text-sm text-center">Pronostics</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="bg-gray-700 hover:bg-gray-800 text-white rounded-xl p-4 shadow-lg flex flex-col items-center gap-2 transition-all hover:scale-105">
                    <span class="text-3xl">⚙️</span>
                    <span class="font-bold text-sm text-center">Paramètres</span>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Matches -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-soboa-blue mb-4 flex items-center gap-2">
                        <span>⚽</span> Matchs Récents
                    </h2>
                    <div class="space-y-3">
                        @foreach($recentMatches as $match)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                @if($match->homeTeam)
                                <img src="https://flagcdn.com/w40/{{ $match->homeTeam->iso_code }}.png" class="w-8 h-6 rounded">
                                @endif
                                <span class="font-medium text-sm">{{ $match->team_a }}</span>
                                <span class="font-bold">
                                    @if($match->status === 'finished')
                                    {{ $match->score_a }} - {{ $match->score_b }}
                                    @else
                                    <span class="text-gray-400">vs</span>
                                    @endif
                                </span>
                                <span class="font-medium text-sm">{{ $match->team_b }}</span>
                                @if($match->awayTeam)
                                <img src="https://flagcdn.com/w40/{{ $match->awayTeam->iso_code }}.png" class="w-8 h-6 rounded">
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if($match->status === 'finished')
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded">Terminé</span>
                                @else
                                <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-1 rounded">À venir</span>
                                @endif
                                <a href="{{ route('admin.edit-match', $match->id) }}" class="text-soboa-orange hover:underline text-sm font-bold">Modifier</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.matches') }}" class="block mt-4 text-center text-soboa-orange font-bold hover:underline">
                        Voir tous les matchs →
                    </a>
                </div>

                <!-- Top Users -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-soboa-blue mb-4 flex items-center gap-2">
                        <span>🏆</span> Top 10 Joueurs
                    </h2>
                    <div class="space-y-2">
                        @foreach($topUsers as $index => $user)
                        <div class="flex items-center justify-between p-3 {{ $index < 3 ? 'bg-soboa-orange/10' : 'bg-gray-50' }} rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-lg w-8 text-center">
                                    @if($index == 0) 🥇 @elseif($index == 1) 🥈 @elseif($index == 2) 🥉 @else {{ $index + 1 }} @endif
                                </span>
                                <div class="w-10 h-10 bg-soboa-blue/20 rounded-full flex items-center justify-center font-bold text-soboa-blue">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->phone }}</p>
                                </div>
                            </div>
                            <span class="font-black text-soboa-orange text-lg">{{ $user->points_total }} pts</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.users') }}" class="block mt-4 text-center text-soboa-orange font-bold hover:underline">
                        Voir tous les utilisateurs →
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
