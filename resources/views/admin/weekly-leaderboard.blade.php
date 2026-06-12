<x-layouts.app title="Classement Hebdomadaire - Admin">
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <div class="bg-gradient-to-r from-soboa-blue to-blue-800 text-white py-6">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">{{ ($isGlobal ?? false) ? 'Classement Global' : 'Classement Hebdomadaire' }}</h1>
                        <p class="text-blue-200 mt-1">
                            @if($isGlobal ?? false)
                                Cumul de tous les points du tournoi
                            @else
                                {{ $weekStart->format('d/m/Y') }} - {{ $weekEnd->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-6">
            <!-- Explication : différence Classement Global vs Hebdomadaire -->
            <div x-data="{ open: false }" class="bg-blue-50 border border-blue-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 text-left">
                    <span class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-soboa-blue flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold text-soboa-blue">Pourquoi le classement global diffère du classement par semaine ?</span>
                    </span>
                    <svg class="w-5 h-5 text-soboa-blue transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition x-cloak class="px-5 pb-5 text-sm text-gray-700 space-y-4">
                    <p>
                        Les deux classements mesurent des choses différentes et ne s'additionnent pas forcément.
                        Il est donc normal qu'un joueur soit 1<sup>er</sup> au global mais bas une semaine donnée (ou l'inverse).
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <h4 class="font-bold text-soboa-blue mb-2">🏆 Classement Global</h4>
                            <ul class="space-y-1 list-disc list-inside">
                                <li><strong>Période :</strong> tout le tournoi (cumul à vie)</li>
                                <li><strong>Source :</strong> total de points cumulé du joueur (<code>points_total</code>)</li>
                                <li><strong>Compte :</strong> tous les points jamais gagnés</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <h4 class="font-bold text-soboa-orange mb-2">📅 Classement par Semaine</h4>
                            <ul class="space-y-1 list-disc list-inside">
                                <li><strong>Période :</strong> uniquement les 7 jours de la semaine</li>
                                <li><strong>Source :</strong> points gagnés entre le lundi et le dimanche de la période</li>
                                <li><strong>Compte :</strong> remis à zéro à chaque nouvelle semaine</li>
                            </ul>
                        </div>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <p class="font-semibold text-amber-800 mb-1">En résumé</p>
                        <p class="text-amber-700">
                            Le global = <strong>somme de tous les points depuis le début</strong>.
                            La semaine = <strong>seulement les points gagnés cette semaine-là</strong>.
                            Un joueur très actif au lancement peut dominer le global sans gagner une semaine récente,
                            et la somme des classements hebdomadaires ne correspond pas toujours au total global
                            (ajustements manuels, points hors période ou recalculs).
                        </p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-blue-100">
                        <p class="font-semibold text-gray-800 mb-1">Règle de départage (égalité de points)</p>
                        <p>
                            En cas d'égalité, le joueur ayant fait son <strong>premier pronostic le plus tôt</strong> passe devant.
                            En dernier recours, tri par ordre alphabétique du nom.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filtres et statistiques -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <!-- Sélecteur de semaine -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sélectionner une période</label>
                        <form method="GET" action="{{ route('admin.weekly-leaderboard') }}">
                            <select name="period" onchange="this.form.submit()" class="w-full border-gray-300 rounded-lg focus:ring-soboa-orange focus:border-soboa-orange">
                                @foreach($availableWeeks as $periodValue => $periodLabel)
                                    <option value="{{ $periodValue }}" {{ $selectedWeek === $periodValue ? 'selected' : '' }}>
                                        {{ $periodLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="lg:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                        <div class="text-3xl font-black text-soboa-blue">{{ $weeklyStats['total_participants'] }}</div>
                        <div class="text-sm text-gray-500">Participants</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                        <div class="text-3xl font-black text-yellow-500">{{ number_format($weeklyStats['total_points']) }}</div>
                        <div class="text-sm text-gray-500">Points distribués</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                        <div class="text-3xl font-black text-green-500">{{ $weeklyStats['total_predictions'] }}</div>
                        <div class="text-sm text-gray-500">Pronostics</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                        <div class="text-3xl font-black text-purple-500">{{ $weeklyStats['total_checkins'] }}</div>
                        <div class="text-sm text-gray-500">Check-ins</div>
                    </div>
                </div>
            </div>

            <!-- Top 3 -->
            @if($weeklyStats['total_participants'] >= 3)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @foreach($weeklyData->take(3) as $index => $topUser)
                    @php
                        $podiumColors = [
                            0 => 'from-yellow-400 to-yellow-600',
                            1 => 'from-gray-300 to-gray-500',
                            2 => 'from-orange-400 to-orange-600',
                        ];
                        $medals = ['🥇', '🥈', '🥉'];
                    @endphp
                    <div class="bg-gradient-to-br {{ $podiumColors[$index] }} rounded-xl shadow-lg p-6 text-white {{ $index === 0 ? 'md:order-2' : ($index === 1 ? 'md:order-1' : 'md:order-3') }}">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-4xl">{{ $medals[$index] }}</span>
                            <span class="text-3xl font-black">{{ $topUser->weekly_points }} pts</span>
                        </div>
                        <h3 class="text-xl font-bold truncate">{{ $topUser->name }}</h3>
                        <p class="text-white/80 text-sm">{{ $topUser->phone }}</p>
                        <div class="mt-3 flex gap-4 text-sm">
                            <span>{{ $topUser->weekly_predictions }} pronostics</span>
                            <span>{{ $topUser->weekly_checkins }} check-ins</span>
                        </div>
                        <a href="{{ route('admin.weekly-leaderboard-user-details', $topUser->id) }}?period={{ $selectedWeek }}" 
                           class="mt-4 inline-block bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition">
                            Voir détails →
                        </a>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Actions -->
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.export-weekly-leaderboard') }}?period={{ $selectedWeek }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Exporter CSV
                </a>
            </div>

            <!-- Tableau complet -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Rang</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Utilisateur</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Contact</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">{{ ($isGlobal ?? false) ? 'Points Global' : 'Points Semaine' }}</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Points Total</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Pronostics</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Check-ins</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($weeklyData as $user)
                                <tr class="hover:bg-gray-50 {{ $user->rank <= 3 ? 'bg-yellow-50' : ($user->rank <= 15 ? 'bg-green-50/50' : '') }}">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($user->rank === 1)
                                                <span class="text-2xl">🥇</span>
                                            @elseif($user->rank === 2)
                                                <span class="text-2xl">🥈</span>
                                            @elseif($user->rank === 3)
                                                <span class="text-2xl">🥉</span>
                                            @elseif($user->rank <= 15)
                                                <span class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center font-bold text-white">
                                                    {{ $user->rank }}
                                                </span>
                                            @else
                                                <span class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-600">
                                                    {{ $user->rank }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-600">{{ $user->phone }}</div>
                                        @if($user->email)
                                            <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800">
                                            {{ $user->weekly_points }} pts
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-gray-600 font-medium">{{ number_format($user->points_total) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $user->weekly_predictions }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $user->weekly_checkins }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.weekly-leaderboard-user-details', $user->id) }}?period={{ $selectedWeek }}" 
                                           class="text-soboa-blue hover:text-blue-800 font-medium text-sm">
                                            Détails →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                        <div class="text-4xl mb-2">📭</div>
                                        Aucune activité cette semaine
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Légende -->
            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm">
                <span class="font-bold text-gray-700">Légende :</span>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-yellow-50 border border-yellow-200 rounded"></span>
                    <span class="text-gray-600">Top 3 (podium)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-green-50 border border-green-200 rounded"></span>
                    <span class="text-gray-600">Top 4-15 (gagnants)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-white border border-gray-200 rounded"></span>
                    <span class="text-gray-600">Autres participants</span>
                </div>
            </div>

            <!-- Info mise à jour -->
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Les données sont mises à jour en temps réel. Dernière consultation : {{ now()->format('d/m/Y à H:i') }}</p>
                <p class="mt-1 text-green-600 font-medium">{{ ($isGlobal ?? false) ? 'Classement cumulé sur tout le tournoi (mêmes règles que le classement public)' : 'Les 15 premiers de chaque semaine sont gagnants' }}</p>
            </div>
        </div>
    </div>
</x-layouts.app>
