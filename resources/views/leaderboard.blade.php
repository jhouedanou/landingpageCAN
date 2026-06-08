<x-layouts.app title="Classement">
    <div class="space-y-6">
        <!-- Header -->
        <div class="relative py-8 px-6 rounded-2xl overflow-hidden mb-6 shadow-2xl text-center">
            <div class="absolute inset-0 z-0">
                <img src="/images/sen.webp" class="w-full h-full object-cover" alt="Background">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"></div>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl md:text-4xl font-black text-white drop-shadow-2xl">🏆 Classement</h1>
                <p class="text-white/90 font-bold mt-2 uppercase tracking-widest text-xs">
                    {{ $period_label }}
                </p>
            </div>
        </div>

        <!-- Sélecteur de période -->
        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">📅</span>
                <span class="font-bold text-gray-700">Période</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('leaderboard', ['period' => 'global']) }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition {{ $selected_period === 'global' ? 'bg-soboa-orange text-black' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Général
                </a>
                @foreach($available_periods as $key => $period)
                    <a href="{{ route('leaderboard', ['period' => $key]) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition {{ $selected_period === $key ? 'bg-soboa-orange text-black' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $period['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Info sur les gains -->
        @if(str_starts_with($selected_period, 'week_'))
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center gap-3">
                <span class="text-3xl">🎁</span>
                <div>
                    <p class="font-bold">Classement Hebdomadaire - Top 15</p>
                    <p class="text-sm text-white/90">Les 15 premiers de cette semaine sont gagnants !</p>
                </div>
            </div>
        </div>
        @elseif($selected_period === 'global')
        <div class="bg-gradient-to-r from-soboa-blue to-soboa-blue-dark rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center gap-3">
                <span class="text-3xl">🏆</span>
                <div>
                    <p class="font-bold">Classement National — Top 50</p>
                    <p class="text-sm text-white/80">Les 50 meilleurs pronostiqueurs depuis le début de la compétition</p>
                </div>
            </div>
        </div>
        @endif

        @php
            $isWeekly = str_starts_with($selected_period, 'week_');
            $topData = $isWeekly ? $top15 : ($top50 ?? $top20 ?? []);
            $topLimit = $isWeekly ? 15 : 50;
            $topLabel = $isWeekly ? 'TOP 15 Hebdomadaire' : 'TOP 50 National';
        @endphp

        <!-- Classement principal -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-soboa-blue to-soboa-blue/80 p-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span>🏅</span> {{ $topLabel }}
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    @if($isWeekly)
                        Points gagnés cette semaine
                    @else
                        Classement général depuis le 21 décembre 2025
                    @endif
                </p>
            </div>

            @if(count($topData) > 0)
                <!-- Podium visuel (Top 3) -->
                <div class="bg-soboa-orange p-6 pb-8">
                    <div class="flex justify-center items-end gap-4">
                        <!-- 2ème place -->
                        @if(isset($topData[1]))
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-full border-4 border-gray-300 bg-gray-700 flex items-center justify-center text-lg font-bold text-white mb-2">
                                    {{ substr($topData[1]['name'], 0, 1) }}
                                </div>
                                <div class="text-center">
                                    <div class="font-bold text-sm text-black">{{ $topData[1]['name'] }}</div>
                                    <div class="text-black/60 text-xs">{{ $topData[1]['points'] }} pts</div>
                                </div>
                                <div class="h-16 w-14 bg-gradient-to-b from-gray-300 to-gray-400 mt-2 rounded-t-lg flex items-center justify-center text-xl font-bold text-gray-800 shadow-lg">
                                    2
                                </div>
                            </div>
                        @endif

                        <!-- 1ère place -->
                        @if(isset($topData[0]))
                            <div class="flex flex-col items-center z-10">
                                <div class="w-18 h-18 rounded-full border-4 border-yellow-400 bg-gray-700 flex items-center justify-center text-2xl font-bold text-yellow-400 mb-2" style="width: 4.5rem; height: 4.5rem;">
                                    {{ substr($topData[0]['name'], 0, 1) }}
                                </div>
                                <div class="text-center">
                                    <div class="font-bold text-base text-black">{{ $topData[0]['name'] }}</div>
                                    <div class="text-black/70 text-sm">{{ $topData[0]['points'] }} pts</div>
                                </div>
                                <div class="h-24 w-18 bg-gradient-to-b from-yellow-300 to-yellow-500 mt-2 rounded-t-lg flex items-center justify-center text-3xl font-bold text-yellow-900 shadow-lg" style="width: 4.5rem;">
                                    👑
                                </div>
                            </div>
                        @endif

                        <!-- 3ème place -->
                        @if(isset($topData[2]))
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-full border-4 border-orange-400/50 bg-gray-700 flex items-center justify-center text-lg font-bold text-white mb-2">
                                    {{ substr($topData[2]['name'], 0, 1) }}
                                </div>
                                <div class="text-center">
                                    <div class="font-bold text-sm text-black">{{ $topData[2]['name'] }}</div>
                                    <div class="text-black/60 text-xs">{{ $topData[2]['points'] }} pts</div>
                                </div>
                                <div class="h-12 w-14 bg-gradient-to-b from-orange-300 to-orange-500 mt-2 rounded-t-lg flex items-center justify-center text-xl font-bold text-orange-900 shadow-lg">
                                    3
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Positions 4 à N (selon période) -->
                @if(count($topData) > 3)
                    <div class="divide-y divide-gray-100">
                        @foreach(array_slice($topData, 3) as $entry)
                            @if($entry)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition {{ ($isWeekly && $entry['rank'] <= 15) ? 'bg-green-50/30' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-full {{ $entry['rank'] <= 10 ? 'bg-soboa-blue text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold text-sm">
                                            {{ $entry['rank'] }}
                                        </span>
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center font-bold text-gray-700">
                                            {{ substr($entry['name'], 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $entry['name'] }}</span>
                                        @if($isWeekly && $entry['rank'] <= 15)
                                            <span class="text-xs bg-green-500 text-white px-2 py-0.5 rounded-full">Gagnant</span>
                                        @endif
                                    </div>
                                    <span class="font-bold text-gray-700">{{ $entry['points'] }} pts</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @else
                <div class="p-8 text-center text-gray-500">
                    <span class="text-4xl mb-2 block">📊</span>
                    <p>Aucun classement disponible pour cette période.</p>
                </div>
            @endif
        </div>

        {{-- Position personnelle --}}
        @if($user_position)
            @php
                $topThreshold = $isWeekly ? 15 : 50;
                $userInTopDisplay = $user_position['rank'] <= $topThreshold;
                $topLabel2 = $isWeekly ? 'TOP 15' : 'TOP 50';
                $pointsToNext = $userInTopDisplay ? null : null;
            @endphp

            @if(!$userInTopDisplay)
                {{-- Bloc "hors top" valorisant --}}
                <div class="relative overflow-hidden rounded-2xl shadow-lg border-2 border-soboa-orange/40">
                    <div class="absolute inset-0 bg-gradient-to-br from-soboa-blue via-soboa-blue-dark to-soboa-text-dark opacity-95"></div>
                    <div class="relative z-10 p-6">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-16 h-16 rounded-full bg-soboa-orange flex items-center justify-center text-2xl font-black text-white shadow-lg">
                                        {{ $user_position['rank'] }}
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-soboa-cream rounded-full flex items-center justify-center text-xs">📍</div>
                                </div>
                                <div>
                                    <p class="text-soboa-cream/70 text-xs font-bold uppercase tracking-widest mb-0.5">Votre position</p>
                                    <p class="text-white font-black text-xl leading-none">{{ $user_position['rank'] }}<span class="text-soboa-cream/50 text-sm">e</span> place</p>
                                    <p class="text-soboa-orange font-black text-2xl mt-1">{{ $user_position['points'] }} pts</p>
                                    <p class="text-white/50 text-xs mt-0.5">sur {{ $user_position['total_users'] ?? '—' }} participants</p>
                                </div>
                            </div>
                            <div class="bg-soboa-orange/20 border border-soboa-orange/40 rounded-xl p-4 text-center min-w-[140px]">
                                <p class="text-soboa-orange-light text-xs font-bold uppercase tracking-wider mb-1">Pour atteindre le Top 50</p>
                                <p class="text-white/70 text-xs">Continuez à pronostiquer chaque jour</p>
                                <a href="{{ route('matches') }}" class="mt-3 inline-block bg-soboa-orange hover:bg-soboa-orange-secondary text-white text-xs font-black px-4 py-2 rounded-lg transition">
                                    Pronostiquer →
                                </a>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/10 grid grid-cols-3 gap-3 text-center">
                            <div class="bg-white/5 rounded-xl p-3">
                                <p class="text-soboa-orange font-black text-lg">+1</p>
                                <p class="text-white/50 text-[10px] uppercase tracking-wide">par connexion/jour</p>
                            </div>
                            <div class="bg-white/5 rounded-xl p-3">
                                <p class="text-soboa-orange font-black text-lg">+4</p>
                                <p class="text-white/50 text-[10px] uppercase tracking-wide">en point partenaire</p>
                            </div>
                            <div class="bg-white/5 rounded-xl p-3">
                                <p class="text-soboa-orange font-black text-lg">+6</p>
                                <p class="text-white/50 text-[10px] uppercase tracking-wide">score exact</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Dans le top --}}
                <div class="bg-gradient-to-r from-soboa-blue to-soboa-blue-dark rounded-2xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <span>📍</span> Votre position
                    </h3>
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-soboa-orange flex items-center justify-center text-2xl font-black text-white shadow-xl">
                                #{{ $user_position['rank'] }}
                            </div>
                            <div>
                                <div class="text-xl font-black">{{ $user_position['points'] }} points</div>
                                <div class="text-soboa-orange font-bold text-sm mt-0.5">
                                    🎉 Vous êtes dans le {{ $topLabel2 }} !
                                    @if($isWeekly)<span class="text-green-300 ml-1">— Gagnant</span>@endif
                                </div>
                                <div class="text-white/50 text-xs mt-0.5">sur {{ $user_position['total_users'] ?? '—' }} participants</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-gray-100 rounded-xl p-6 text-center">
                <span class="text-4xl mb-2 block">🔒</span>
                <p class="text-gray-600 mb-4">Connectez-vous pour voir votre position dans le classement</p>
                <a href="{{ route('login') }}" class="inline-block bg-soboa-orange text-white font-bold px-6 py-3 rounded-full hover:bg-soboa-orange-secondary transition">
                    Se connecter
                </a>
            </div>
        @endif

        <!-- Légende des points -->
        <div class="bg-gradient-to-r from-soboa-blue/5 to-soboa-orange/5 rounded-xl p-4 border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span>💡</span> Comment gagner des points ?
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                <span class="bg-white px-3 py-2 rounded-lg border border-gray-200 flex items-center gap-2">
                    <span>🔑</span> +1 pt/connexion/jour
                </span>
                <span class="bg-white px-3 py-2 rounded-lg border border-gray-200 flex items-center gap-2">
                    <span>⚽</span> +1 pt/pronostic
                </span>
                <span class="bg-white px-3 py-2 rounded-lg border border-gray-200 flex items-center gap-2">
                    <span>🎯</span> +3 pts/bon vainqueur
                </span>
                <span class="bg-white px-3 py-2 rounded-lg border border-gray-200 flex items-center gap-2">
                    <span>🏆</span> +3 pts/score exact
                </span>
                <span class="bg-white px-3 py-2 rounded-lg border border-gray-200 flex items-center gap-2">
                    <span>📍</span> +4 pts/visite lieu
                </span>
            </div>
        </div>
    </div>
</x-layouts.app>