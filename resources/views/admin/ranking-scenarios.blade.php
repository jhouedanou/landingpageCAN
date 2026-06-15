<x-layouts.app title="Admin - Scénarios de classement">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-soboa-blue hover:underline">&larr; Retour au dashboard</a>
                <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3 mt-2">
                    <span class="text-4xl">📊</span> Scénarios de classement
                </h1>
                <p class="text-gray-600 mt-2">
                    Deux recalculs alternatifs des bonus « visite point de vente » (PDV), pour décision finale.
                </p>
            </div>

            <!-- Bandeau DRY-RUN -->
            <div class="bg-amber-50 border-l-4 border-amber-400 rounded p-4 mb-6 text-sm text-amber-800">
                <strong>Mode lecture seule (DRY-RUN).</strong> Ces simulations ne modifient
                <strong>aucune donnée</strong> en base. Elles servent uniquement à comparer les classements
                et à exporter les fichiers (CSV / PDF) pour décision.
            </div>

            <!-- Filtre -->
            <form method="GET" class="bg-white rounded-xl shadow p-4 mb-6 flex items-center gap-3 flex-wrap">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="include_staff" value="1" onchange="this.form.submit()"
                           @checked($includeStaff) class="rounded border-gray-300 text-soboa-blue">
                    Inclure les comptes admin / soboa dans le classement
                </label>
            </form>

            <div x-data="{ tab: 'a' }">
                <!-- Tabs -->
                <div class="flex flex-wrap gap-2 border-b border-gray-200 mb-6">
                    <button type="button" @click="tab='a'"
                            :class="tab==='a' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 font-bold text-sm border-b-2 -mb-px transition-colors">
                        Scénario A — Reset total
                    </button>
                    <button type="button" @click="tab='b'"
                            :class="tab==='b' ? 'border-soboa-blue text-soboa-blue' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 font-bold text-sm border-b-2 -mb-px transition-colors">
                        Scénario B — Conditionnel
                    </button>
                    <button type="button" @click="tab='fraud'"
                            :class="tab==='fraud' ? 'border-red-600 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 font-bold text-sm border-b-2 -mb-px transition-colors flex items-center gap-2">
                        🚩 Patterns de fraude
                        <span class="bg-red-100 text-red-700 text-xs font-black px-2 py-0.5 rounded-full">{{ count($fraud) + count($speedFraud ?? []) + count($ipFraud ?? []) + count($coordFraud ?? []) }}</span>
                    </button>
                </div>

                <!-- Panneau Scénario A -->
                <div x-show="tab==='a'" x-cloak>
                    @include('admin.partials.ranking-scenario', ['s' => $a, 'includeStaff' => $includeStaff])
                </div>

                <!-- Panneau Scénario B -->
                <div x-show="tab==='b'" x-cloak>
                    @include('admin.partials.ranking-scenario', ['s' => $b, 'includeStaff' => $includeStaff])
                </div>

                <!-- Panneau Fraude -->
                <div x-show="tab==='fraud'" x-cloak>
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-5 bg-gradient-to-r from-red-700 to-red-600 text-white">
                            <h2 class="text-xl font-black flex items-center gap-2">🚩 Patterns de fraude (check-ins)</h2>
                            <p class="text-white/90 text-sm mt-1">
                                Profils avec check-ins suspects (visites PDV <strong>sans pronostic</strong>) —
                                {{ count($fraud) }} profil(s). Signaux : ≥3 PDV le même jour, ≥4 PDV au total, ou ≥8 check-ins.
                            </p>
                        </div>
                        <div class="p-6 overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700">
                                        <th class="border px-3 py-2 text-left">#</th>
                                        <th class="border px-3 py-2 text-left">Nom</th>
                                        <th class="border px-3 py-2 text-left">Téléphone</th>
                                        <th class="border px-3 py-2 text-left">Signaux</th>
                                        <th class="border px-3 py-2 text-right">Check-ins</th>
                                        <th class="border px-3 py-2 text-right">PDV</th>
                                        <th class="border px-3 py-2 text-right">PDV/jour (max)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($fraud as $f)
                                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                            <td class="border px-3 py-2 text-gray-400">{{ $f['user_id'] }}</td>
                                            <td class="border px-3 py-2 font-medium">{{ $f['name'] }}</td>
                                            <td class="border px-3 py-2 text-gray-500">{{ $f['phone'] }}</td>
                                            <td class="border px-3 py-2">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($f['reasons'] as $reason)
                                                        <span class="text-[10px] font-semibold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">{{ $reason }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="border px-3 py-2 text-right font-bold">{{ $f['checkins'] }}</td>
                                            <td class="border px-3 py-2 text-right">{{ $f['distinct_bars'] }}</td>
                                            <td class="border px-3 py-2 text-right font-black text-red-600">{{ $f['max_bars_day'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="border px-3 py-8 text-center text-gray-400">Aucun pattern suspect détecté.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if(count($fraud) > 0)
                                <p class="text-xs text-gray-400 mt-3">
                                    Ces bonus proviennent de check-ins <code>bar_visit</code> (sans pronostic). Le scénario B ne les conserve
                                    que si un pronostic accompagne le check-in ; le scénario A les retire tous.
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- B1 — Vitesse impossible entre deux check-ins -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6">
                        <div class="px-6 py-5 bg-gradient-to-r from-orange-600 to-amber-500 text-white">
                            <h2 class="text-xl font-black flex items-center gap-2">
                                🚗 Vitesse impossible
                                <span class="bg-white/25 text-xs font-black px-2 py-0.5 rounded-full">{{ count($speedFraud ?? []) }}</span>
                            </h2>
                            <p class="text-white/90 text-sm mt-1">
                                Deux check-ins géolocalisés trop éloignés en trop peu de temps (&gt; 120 km/h) — déplacement
                                physiquement impossible (compte piloté à distance ou GPS falsifié).
                            </p>
                        </div>
                        <div class="p-6 overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700">
                                        <th class="border px-3 py-2 text-left">#</th>
                                        <th class="border px-3 py-2 text-left">Nom</th>
                                        <th class="border px-3 py-2 text-left">Téléphone</th>
                                        <th class="border px-3 py-2 text-right">Vitesse</th>
                                        <th class="border px-3 py-2 text-right">Distance</th>
                                        <th class="border px-3 py-2 text-right">Durée</th>
                                        <th class="border px-3 py-2 text-left">Segment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($speedFraud ?? []) as $f)
                                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                            <td class="border px-3 py-2 text-gray-400">{{ $f['user_id'] }}</td>
                                            <td class="border px-3 py-2 font-medium">{{ $f['name'] }}</td>
                                            <td class="border px-3 py-2 text-gray-500">{{ $f['phone'] }}</td>
                                            <td class="border px-3 py-2 text-right font-black text-orange-600">
                                                {{ $f['speed_kmh'] >= 999999 ? '∞' : number_format($f['speed_kmh'], 0, ',', ' ') }} km/h
                                            </td>
                                            <td class="border px-3 py-2 text-right">{{ number_format($f['distance_km'], 2, ',', ' ') }} km</td>
                                            <td class="border px-3 py-2 text-right">{{ $f['minutes'] }} min</td>
                                            <td class="border px-3 py-2 text-gray-500">{{ $f['from'] }} → {{ $f['to'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="border px-3 py-8 text-center text-gray-400">Aucun déplacement impossible détecté.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- B3 — Multi-comptes même appareil / IP -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6">
                        <div class="px-6 py-5 bg-gradient-to-r from-purple-700 to-fuchsia-600 text-white">
                            <h2 class="text-xl font-black flex items-center gap-2">
                                📱 Multi-comptes même IP
                                <span class="bg-white/25 text-xs font-black px-2 py-0.5 rounded-full">{{ count($ipFraud ?? []) }}</span>
                            </h2>
                            <p class="text-white/90 text-sm mt-1">
                                Une même adresse IP utilisée par plusieurs comptes (pronostics + check-ins). Signal de
                                multi-comptes — à pondérer (un wifi de PDV ou la 4G d'un opérateur peut être partagé légitimement).
                            </p>
                        </div>
                        <div class="p-6 overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700">
                                        <th class="border px-3 py-2 text-left">IP</th>
                                        <th class="border px-3 py-2 text-right">Comptes</th>
                                        <th class="border px-3 py-2 text-right">Événements</th>
                                        <th class="border px-3 py-2 text-left">Utilisateurs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($ipFraud ?? []) as $f)
                                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                            <td class="border px-3 py-2 font-mono text-xs">{{ $f['ip'] }}</td>
                                            <td class="border px-3 py-2 text-right font-black text-purple-700">{{ $f['users_count'] }}</td>
                                            <td class="border px-3 py-2 text-right">{{ $f['events'] }}</td>
                                            <td class="border px-3 py-2">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($f['users'] as $u)
                                                        <span class="text-[10px] font-semibold bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                                                            {{ $u['name'] }} ({{ $u['count'] }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="border px-3 py-8 text-center text-gray-400">Aucune IP partagée détectée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- B4 — Coordonnées GPS identiques entre comptes -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6">
                        <div class="px-6 py-5 bg-gradient-to-r from-teal-700 to-emerald-600 text-white">
                            <h2 class="text-xl font-black flex items-center gap-2">
                                📍 Coordonnées GPS identiques
                                <span class="bg-white/25 text-xs font-black px-2 py-0.5 rounded-full">{{ count($coordFraud ?? []) }}</span>
                            </h2>
                            <p class="text-white/90 text-sm mt-1">
                                Des check-ins de plusieurs comptes au même point exact (à ~1 m près) — un même téléphone
                                utilisé pour piloter plusieurs comptes.
                            </p>
                        </div>
                        <div class="p-6 overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700">
                                        <th class="border px-3 py-2 text-left">Coordonnées</th>
                                        <th class="border px-3 py-2 text-right">Comptes</th>
                                        <th class="border px-3 py-2 text-right">Check-ins</th>
                                        <th class="border px-3 py-2 text-left">Utilisateurs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($coordFraud ?? []) as $f)
                                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                            <td class="border px-3 py-2 font-mono text-xs">
                                                <a href="https://maps.google.com/?q={{ $f['latitude'] }},{{ $f['longitude'] }}" target="_blank" rel="noopener" class="text-teal-700 hover:underline">
                                                    {{ $f['latitude'] }}, {{ $f['longitude'] }}
                                                </a>
                                            </td>
                                            <td class="border px-3 py-2 text-right font-black text-teal-700">{{ $f['users_count'] }}</td>
                                            <td class="border px-3 py-2 text-right">{{ $f['checkins'] }}</td>
                                            <td class="border px-3 py-2">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($f['users'] as $u)
                                                        <span class="text-[10px] font-semibold bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full">
                                                            {{ $u['name'] }} ({{ $u['count'] }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="border px-3 py-8 text-center text-gray-400">Aucune coordonnée partagée détectée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <p class="text-xs text-gray-400 mt-3">
                                Ces détections (vitesse, IP, GPS) s'appuient sur les check-ins géolocalisés persistés
                                (table <code>check_ins</code>) : elles ne couvrent que les présences enregistrées après la mise en service.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
