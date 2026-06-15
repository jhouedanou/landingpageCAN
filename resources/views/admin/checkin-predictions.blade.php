<x-layouts.app title="Check-ins × Pronostics - Admin">
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 text-white py-6">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">Check-ins × Pronostics</h1>
                        <p class="text-indigo-200 mt-1">Croisement des présences géolocalisées avec les pronostics effectués + statut du bonus +4</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.checkins') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">Check-ins</a>
                        <a href="{{ route('admin.dashboard') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">Retour</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-6">
            <!-- Note règle métier -->
            <div class="bg-amber-50 border-l-4 border-amber-400 rounded p-4 mb-6 text-sm text-amber-800">
                <strong>Règle :</strong> le bonus <strong>+4</strong> n'est attribué que pour un pronostic effectué
                <strong>depuis un point de vente</strong> (présence GPS vérifiée côté serveur), plafonné à un seul +4
                par (utilisateur, PDV, jour). Un check-in hors PDV ne donne jamais de bonus.
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                    <div class="text-3xl font-black text-indigo-600">{{ number_format($stats['total']) }}</div>
                    <div class="text-sm text-gray-500">Check-ins enregistrés</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                    <div class="text-3xl font-black text-green-600">{{ number_format($stats['with_prediction']) }}</div>
                    <div class="text-sm text-gray-500">Avec pronostic lié</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                    <div class="text-3xl font-black text-gray-500">{{ number_format($stats['without_prediction']) }}</div>
                    <div class="text-sm text-gray-500">Sans pronostic</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                    <div class="text-3xl font-black text-orange-500">{{ number_format($stats['from_pos']) }}</div>
                    <div class="text-sm text-gray-500">Depuis un PDV</div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" action="{{ route('admin.checkin-predictions') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateur</label>
                        <select name="user_id" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PDV</label>
                        <select name="bar_id" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous</option>
                            @foreach($bars as $b)
                                <option value="{{ $b->id }}" @selected(request('bar_id') == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                        <select name="source" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Toutes</option>
                            <option value="prediction" @selected(request('source') === 'prediction')>Pronostic sur place</option>
                            <option value="checkin" @selected(request('source') === 'checkin')>Check-in simple</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lien pronostic</label>
                        <select name="link" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous</option>
                            <option value="with" @selected(request('link') === 'with')>Avec pronostic</option>
                            <option value="without" @selected(request('link') === 'without')>Sans pronostic</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div class="lg:col-span-6 flex gap-2 justify-end">
                        <a href="{{ route('admin.checkin-predictions') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Réinitialiser</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Filtrer</button>
                    </div>
                </form>
            </div>

            <!-- Tableau -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b bg-gray-50">
                    <h3 class="font-bold text-gray-700">Croisement ({{ $checkins->total() }} check-ins)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Heure</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PDV</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">GPS / distance</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pronostic lié</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bonus +4</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($checkins as $checkin)
                                @php
                                    $bar = $checkin->bar;
                                    $pred = $checkin->prediction;
                                    $bonusKey = $checkin->user_id . '|' . $checkin->bar_id . '|' . $checkin->created_at->toDateString();
                                    $bonusGranted = $checkin->bar_id && ($bonusMap[$bonusKey] ?? false);
                                    $mapsUrl = "https://www.openstreetmap.org/?mlat={$checkin->latitude}&mlon={$checkin->longitude}&zoom=18";
                                @endphp
                                <tr class="hover:bg-gray-50 align-top">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $checkin->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $checkin->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($checkin->user)
                                            <div class="text-sm font-medium text-gray-900">{{ $checkin->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $checkin->user->phone }}</div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($checkin->source === 'prediction')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">📍 Sur place</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Check-in</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($bar)
                                            <div class="text-sm font-medium text-gray-900">{{ $bar->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $bar->zone }}</div>
                                        @else
                                            <span class="text-red-500 text-xs font-medium">Hors PDV</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="text-xs font-mono text-green-700 hover:underline">
                                            {{ number_format($checkin->latitude, 6) }},<br>{{ number_format($checkin->longitude, 6) }}
                                        </a>
                                        <div class="text-[11px] text-gray-500 mt-1">
                                            @if($checkin->distance_m !== null)<span>{{ $checkin->distance_m }} m du PDV</span>@endif
                                            @if($checkin->gps_accuracy !== null)<span class="block">±{{ (int) $checkin->gps_accuracy }} m</span>@endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($pred)
                                            @php
                                                $home = $pred->match?->homeTeam?->name ?? '?';
                                                $away = $pred->match?->awayTeam?->name ?? '?';
                                            @endphp
                                            <div class="text-sm text-gray-900">{{ $home }} <span class="font-mono font-bold">{{ $pred->score_a }}-{{ $pred->score_b }}</span> {{ $away }}</div>
                                            <div class="text-xs text-gray-500">
                                                Pronostic #{{ $pred->id }}
                                                @if($pred->points_earned > 0)
                                                    · <span class="text-green-600 font-semibold">{{ $pred->points_earned }} pt(s)</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">Aucun pronostic lié</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if(!$checkin->bar_id)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Hors PDV</span>
                                        @elseif($bonusGranted)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">+4 attribué</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600" title="Plafond déjà atteint ce jour pour ce PDV, ou pas de pronostic donnant droit au bonus">Pas de +4</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                        <p class="text-lg font-medium">Aucun check-in géolocalisé enregistré</p>
                                        <p class="text-sm mt-1">La table <code>check_ins</code> se remplit à partir des nouveaux check-ins / pronostics sur place.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($checkins->hasPages())
                    <div class="px-4 py-3 border-t bg-gray-50">{{ $checkins->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
