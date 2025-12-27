<x-layouts.app title="Calendrier CAN 2025"><x-layouts.app title="Calendrier CAN 2025">

    <div class="min-h-screen bg-gradient-to-br from-soboa-blue via-blue-800 to-blue-900 py-4 md:py-6 px-2 md:px-4" x-data="calendarApp()">    <div class="min-h-screen bg-gradient-to-br from-soboa-blue via-blue-800 to-blue-900 py-6 px-4" x-data="calendarApp()">

                

        <!-- Header -->        <!-- Header -->

        <div class="max-w-7xl mx-auto mb-4 md:mb-6">        <div class="max-w-6xl mx-auto mb-6">

            <div class="text-center">            <div class="text-center">

                <h1 class="text-2xl md:text-4xl font-black text-white mb-2">                <h1 class="text-3xl md:text-4xl font-black text-white mb-2">

                    📅 Calendrier CAN 2025                    Calendrier CAN 2025

                </h1>                </h1>

                <p class="text-blue-200 text-sm">                <p class="text-blue-200 text-sm md:text-base">

                    {{ $totalMatches }} matchs • {{ $finishedMatches }} terminés • {{ $upcomingMatches }} à venir                    {{ $totalMatches }} matchs - {{ $finishedMatches }} termines - {{ $upcomingMatches }} a venir

                </p>                </p>

            </div>            </div>

        </div>        </div>



        <!-- Navigation mois -->        <!-- Navigation mois -->

        <div class="max-w-7xl mx-auto mb-4">        <div class="max-w-6xl mx-auto mb-6">

            <div class="flex items-center justify-between bg-white/10 backdrop-blur-sm rounded-xl p-2 md:p-3">            <div class="flex items-center justify-between bg-white/10 backdrop-blur-sm rounded-xl p-3">

                <button type="button" @click="previousMonth()" class="p-2 text-white hover:bg-white/20 rounded-lg transition">                <button type="button" @click="previousMonth()" class="p-2 text-white hover:bg-white/20 rounded-lg transition">

                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>

                    </svg>                    </svg>

                </button>                </button>

                <h2 class="text-lg md:text-xl font-bold text-white" x-text="monthNames[currentMonth] + ' ' + currentYear"></h2>                <h2 class="text-xl font-bold text-white" x-text="monthNames[currentMonth] + ' ' + currentYear"></h2>

                <button type="button" @click="nextMonth()" class="p-2 text-white hover:bg-white/20 rounded-lg transition">                <button type="button" @click="nextMonth()" class="p-2 text-white hover:bg-white/20 rounded-lg transition">

                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>

                    </svg>                    </svg>

                </button>                </button>

            </div>            </div>

        </div>        </div>



        <!-- Calendrier -->        <!-- Calendrier Grid -->

        <div class="max-w-7xl mx-auto">        <div class="max-w-6xl mx-auto">

            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

                                

                <!-- En-tête jours de la semaine -->                <!-- Jours de la semaine -->

                <div class="bg-gradient-to-r from-soboa-blue to-blue-700" style="display: grid; grid-template-columns: repeat(7, 1fr);">                <div class="grid grid-cols-7 bg-gradient-to-r from-soboa-blue to-blue-700">

                    <div class="py-2 md:py-3 text-center text-white font-bold text-xs md:text-sm border-r border-white/20">Lun</div>                    <template x-for="day in dayNames" :key="day">

                    <div class="py-2 md:py-3 text-center text-white font-bold text-xs md:text-sm border-r border-white/20">Mar</div>                        <div class="py-3 text-center text-white font-bold text-xs md:text-sm border-r border-white/20 last:border-r-0" x-text="day"></div>

                    <div class="py-2 md:py-3 text-center text-white font-bold text-xs md:text-sm border-r border-white/20">Mer</div>                    </template>

                    <div class="py-2 md:py-3 text-center text-white font-bold text-xs md:text-sm border-r border-white/20">Jeu</div>                </div>

                    <div class="py-2 md:py-3 text-center text-white font-bold text-xs md:text-sm border-r border-white/20">Ven</div>

                    <div class="py-2 md:py-3 text-center text-white font-bold text-xs md:text-sm border-r border-white/20">Sam</div>                <!-- Grille des jours -->

                    <div class="py-2 md:py-3 text-center text-white font-bold text-xs md:text-sm">Dim</div>                <div class="grid grid-cols-7">

                </div>                    <template x-for="(day, index) in calendarDays" :key="index">

                        <div 

                <!-- Grille des jours -->                            class="min-h-[100px] md:min-h-[140px] border-r border-b border-gray-200 last:border-r-0 p-1 md:p-2 transition-all"

                <div style="display: grid; grid-template-columns: repeat(7, 1fr);">                            :class="{

                    <template x-for="(day, index) in calendarDays" :key="index">                                'bg-gray-50': !day.isCurrentMonth,

                        <div                                 'bg-white': day.isCurrentMonth,

                            class="border-r border-b border-gray-200 p-1 md:p-2 transition-all"                                'bg-soboa-orange/10': day.isToday

                            style="min-height: 80px;"                            }"

                            :class="{                        >

                                'bg-gray-100': !day.isCurrentMonth,                            <!-- Numero du jour -->

                                'bg-white': day.isCurrentMonth && !day.isToday,                            <div class="flex items-center justify-between mb-1">

                                'bg-orange-50 ring-2 ring-soboa-orange ring-inset': day.isToday                                <span 

                            }"                                    class="text-xs md:text-sm font-bold w-6 h-6 flex items-center justify-center rounded-full"

                        >                                    :class="{

                            <!-- Numéro du jour -->                                        'text-gray-400': !day.isCurrentMonth,

                            <div class="flex items-center justify-between mb-1">                                        'text-gray-700': day.isCurrentMonth && !day.isToday,

                                <span                                         'bg-soboa-orange text-white': day.isToday

                                    class="text-xs md:text-sm font-bold rounded-full flex items-center justify-center"                                    }"

                                    style="width: 24px; height: 24px;"                                    x-text="day.date"

                                    :class="{                                ></span>

                                        'text-gray-400': !day.isCurrentMonth,                                <span 

                                        'text-gray-700': day.isCurrentMonth && !day.isToday,                                    x-show="day.matches.length > 0" 

                                        'bg-soboa-orange text-white': day.isToday                                    class="text-[10px] bg-soboa-blue text-white px-1.5 py-0.5 rounded-full"

                                    }"                                    x-text="day.matches.length + ' match' + (day.matches.length > 1 ? 's' : '')"

                                    x-text="day.date"                                ></span>

                                ></span>                            </div>

                                <span 

                                    x-show="day.matches.length > 0"                             <!-- Matchs du jour -->

                                    class="text-[9px] md:text-[10px] bg-soboa-blue text-white px-1 py-0.5 rounded-full font-bold"                            <div class="space-y-1 overflow-y-auto max-h-[70px] md:max-h-[100px]">

                                    x-text="day.matches.length"                                <template x-for="match in day.matches" :key="match.id">

                                ></span>                                    <div 

                            </div>                                        @click="openMatchModal(match)"

                                        class="cursor-pointer rounded-lg p-1.5 text-[10px] md:text-xs transition-all hover:scale-[1.02] hover:shadow-md"

                            <!-- Matchs du jour -->                                        :class="{

                            <div class="space-y-0.5 overflow-y-auto" style="max-height: 60px;">                                            'bg-green-100 border border-green-300': match.status === 'finished',

                                <template x-for="match in day.matches" :key="match.id">                                            'bg-blue-50 border border-blue-200': match.status !== 'finished'

                                    <div                                         }"

                                        @click="openMatchModal(match)"                                    >

                                        class="cursor-pointer rounded p-0.5 md:p-1 text-[8px] md:text-[10px] transition-all hover:opacity-80"                                        <div class="flex items-center justify-between gap-1">

                                        :class="{                                            <div class="flex items-center gap-1 flex-1 min-w-0">

                                            'bg-green-100 border-l-2 border-green-500': match.status === 'finished',                                                <template x-if="match.homeIso">

                                            'bg-blue-50 border-l-2 border-blue-400': match.status !== 'finished'                                                    <img :src="'https://flagcdn.com/w20/' + match.homeIso + '.png'" 

                                        }"                                                         class="w-3 h-2 md:w-4 md:h-3 object-cover rounded-sm shadow-sm flex-shrink-0">

                                    >                                                </template>

                                        <div class="flex items-center gap-0.5">                                                <span class="truncate font-medium" x-text="match.homeTeam.substring(0, 8)"></span>

                                            <template x-if="match.homeIso">                                            </div>

                                                <img :src="'https://flagcdn.com/w20/' + match.homeIso + '.png'"                                             

                                                     class="w-3 h-2 object-cover rounded-sm flex-shrink-0">                                            <template x-if="match.status === 'finished'">

                                            </template>                                                <span class="font-bold text-green-700 flex-shrink-0" x-text="match.homeScore + '-' + match.awayScore"></span>

                                            <span class="font-medium truncate" x-text="match.homeTeam.substring(0, 3)"></span>                                            </template>

                                            <template x-if="match.status === 'finished'">                                            <template x-if="match.status !== 'finished'">

                                                <span class="font-bold text-green-700" x-text="match.homeScore + '-' + match.awayScore"></span>                                                <span class="text-gray-500 flex-shrink-0" x-text="match.time"></span>

                                            </template>                                            </template>

                                            <template x-if="match.status !== 'finished'">                                            

                                                <span class="text-gray-400">vs</span>                                            <div class="flex items-center gap-1 flex-1 min-w-0 justify-end">

                                            </template>                                                <span class="truncate font-medium text-right" x-text="match.awayTeam.substring(0, 8)"></span>

                                            <span class="font-medium truncate" x-text="match.awayTeam.substring(0, 3)"></span>                                                <template x-if="match.awayIso">

                                            <template x-if="match.awayIso">                                                    <img :src="'https://flagcdn.com/w20/' + match.awayIso + '.png'" 

                                                <img :src="'https://flagcdn.com/w20/' + match.awayIso + '.png'"                                                          class="w-3 h-2 md:w-4 md:h-3 object-cover rounded-sm shadow-sm flex-shrink-0">

                                                     class="w-3 h-2 object-cover rounded-sm flex-shrink-0">                                                </template>

                                            </template>                                            </div>

                                        </div>                                        </div>

                                    </div>                                    </div>

                                </template>                                </template>

                            </div>                            </div>

                        </div>                        </div>

                    </template>                    </template>

                </div>                </div>

            </div>            </div>

        </div>        </div>



        <!-- Légende -->        <!-- Legende -->

        <div class="max-w-7xl mx-auto mt-4">        <div class="max-w-6xl mx-auto mt-6">

            <div class="flex flex-wrap justify-center gap-3 md:gap-6 text-xs md:text-sm">            <div class="flex flex-wrap justify-center gap-4 text-sm">

                <div class="flex items-center gap-2 text-white">                <div class="flex items-center gap-2 text-white">

                    <div class="w-4 h-4 bg-green-100 border-l-2 border-green-500 rounded"></div>                    <div class="w-4 h-4 bg-green-100 border border-green-300 rounded"></div>

                    <span>Terminé</span>                    <span>Match termine</span>

                </div>                </div>

                <div class="flex items-center gap-2 text-white">                <div class="flex items-center gap-2 text-white">

                    <div class="w-4 h-4 bg-blue-50 border-l-2 border-blue-400 rounded"></div>                    <div class="w-4 h-4 bg-blue-50 border border-blue-200 rounded"></div>

                    <span>À venir</span>                    <span>Match a venir</span>

                </div>                </div>

                <div class="flex items-center gap-2 text-white">                <div class="flex items-center gap-2 text-white">

                    <div class="w-4 h-4 bg-soboa-orange rounded-full"></div>                    <div class="w-4 h-4 bg-soboa-orange rounded-full"></div>

                    <span>Aujourd'hui</span>                    <span>Aujourd hui</span>

                </div>                </div>

            </div>            </div>

        </div>        </div>



        <!-- Modal détails match -->        <!-- Modal details match -->

        <div         <div 

            x-show="showModal"             x-show="showModal" 

            x-transition:enter="transition ease-out duration-200"            x-transition:enter="transition ease-out duration-300"

            x-transition:enter-start="opacity-0"            x-transition:enter-start="opacity-0"

            x-transition:enter-end="opacity-100"            x-transition:enter-end="opacity-100"

            x-transition:leave="transition ease-in duration-150"            x-transition:leave="transition ease-in duration-200"

            x-transition:leave-start="opacity-100"            x-transition:leave-start="opacity-100"

            x-transition:leave-end="opacity-0"            x-transition:leave-end="opacity-0"

            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"

            @click.self="showModal = false"            @click.self="showModal = false"

            x-cloak            x-cloak

            style="display: none;"        >

        >            <div 

            <div                 class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"

                class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"                x-transition:enter="transition ease-out duration-300"

                x-transition:enter="transition ease-out duration-200"                x-transition:enter-start="opacity-0 scale-95"

                x-transition:enter-start="opacity-0 scale-95"                x-transition:enter-end="opacity-100 scale-100"

                x-transition:enter-end="opacity-100 scale-100"                @click.stop

                @click.stop            >

            >                <!-- Header Modal -->

                <!-- Header Modal -->                <div class="bg-gradient-to-r from-soboa-blue to-blue-700 p-4 text-white">

                <div class="bg-gradient-to-r from-soboa-blue to-blue-700 p-4 text-white">                    <div class="flex items-center justify-between">

                    <div class="flex items-center justify-between">                        <div>

                        <div>                            <span class="text-xs uppercase tracking-wide opacity-80" x-text="selectedMatch?.group || selectedMatch?.phase || 'CAN 2025'"></span>

                            <span class="text-xs uppercase tracking-wide opacity-80" x-text="'Groupe ' + (selectedMatch?.group || '') + ' • ' + (selectedMatch?.phase === 'group_stage' ? 'Phase de poules' : selectedMatch?.phase || 'CAN 2025')"></span>                            <h3 class="text-lg font-bold" x-text="selectedMatch?.date"></h3>

                            <h3 class="text-lg font-bold" x-text="formatDate(selectedMatch?.date)"></h3>                        </div>

                        </div>                        <button type="button" @click="showModal = false" class="p-2 hover:bg-white/20 rounded-full transition">

                        <button type="button" @click="showModal = false" class="p-2 hover:bg-white/20 rounded-full transition">                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>                            </svg>

                            </svg>                        </button>

                        </button>                    </div>

                    </div>                </div>

                </div>

                <!-- Corps Modal -->

                <!-- Corps Modal -->                <div class="p-6">

                <div class="p-6">                    <div class="flex items-center justify-between mb-6">

                    <!-- Équipes -->                        <div class="text-center flex-1">

                    <div class="flex items-center justify-between mb-6">                            <template x-if="selectedMatch?.homeIso">

                        <!-- Équipe domicile -->                                <img :src="'https://flagcdn.com/w80/' + selectedMatch.homeIso + '.png'" 

                        <div class="text-center flex-1">                                     class="w-16 h-12 object-cover rounded-lg shadow-md mx-auto mb-2">

                            <template x-if="selectedMatch?.homeIso">                            </template>

                                <img :src="'https://flagcdn.com/w80/' + selectedMatch.homeIso + '.png'"                             <p class="font-bold text-gray-800" x-text="selectedMatch?.homeTeam"></p>

                                     class="w-16 h-12 object-cover rounded-lg shadow-md mx-auto mb-2">                        </div>

                            </template>

                            <p class="font-bold text-gray-800 text-sm" x-text="selectedMatch?.homeTeam"></p>                        <div class="px-4">

                        </div>                            <template x-if="selectedMatch?.status === 'finished'">

                                <div class="text-center">

                        <!-- Score ou VS -->                                    <div class="text-3xl font-black text-soboa-blue" x-text="selectedMatch?.homeScore + ' - ' + selectedMatch?.awayScore"></div>

                        <div class="px-4">                                    <span class="text-xs text-green-600 font-medium">Termine</span>

                            <template x-if="selectedMatch?.status === 'finished'">                                </div>

                                <div class="text-center">                            </template>

                                    <div class="text-3xl font-black text-soboa-blue" x-text="selectedMatch?.homeScore + ' - ' + selectedMatch?.awayScore"></div>                            <template x-if="selectedMatch?.status !== 'finished'">

                                    <span class="text-xs text-green-600 font-medium">Terminé</span>                                <div class="text-center">

                                </div>                                    <div class="text-2xl font-bold text-gray-400">VS</div>

                            </template>                                    <span class="text-sm text-soboa-orange font-bold" x-text="selectedMatch?.time"></span>

                            <template x-if="selectedMatch?.status !== 'finished'">                                </div>

                                <div class="text-center">                            </template>

                                    <div class="text-2xl font-bold text-gray-400">VS</div>                        </div>

                                    <span class="text-sm text-soboa-orange font-bold" x-text="selectedMatch?.time"></span>

                                </div>                        <div class="text-center flex-1">

                            </template>                            <template x-if="selectedMatch?.awayIso">

                        </div>                                <img :src="'https://flagcdn.com/w80/' + selectedMatch.awayIso + '.png'" 

                                     class="w-16 h-12 object-cover rounded-lg shadow-md mx-auto mb-2">

                        <!-- Équipe extérieur -->                            </template>

                        <div class="text-center flex-1">                            <p class="font-bold text-gray-800" x-text="selectedMatch?.awayTeam"></p>

                            <template x-if="selectedMatch?.awayIso">                        </div>

                                <img :src="'https://flagcdn.com/w80/' + selectedMatch.awayIso + '.png'"                     </div>

                                     class="w-16 h-12 object-cover rounded-lg shadow-md mx-auto mb-2">

                            </template>                    <div class="space-y-2 text-sm text-gray-600 border-t pt-4">

                            <p class="font-bold text-gray-800 text-sm" x-text="selectedMatch?.awayTeam"></p>                        <div class="flex items-center gap-2">

                        </div>                            <span>🏟️</span>

                    </div>                            <span x-text="selectedMatch?.stadium || 'Stade CAN 2025'"></span>

                        </div>

                    <!-- Infos supplémentaires -->                        <div class="flex items-center gap-2">

                    <div class="space-y-2 text-sm text-gray-600 border-t pt-4">                            <span>📅</span>

                        <div class="flex items-center gap-2">                            <span x-text="selectedMatch?.date + ' a ' + selectedMatch?.time"></span>

                            <span>🏟️</span>                        </div>

                            <span x-text="selectedMatch?.stadium || 'Stade CAN 2025'"></span>                    </div>

                        </div>

                        <div class="flex items-center gap-2">                    <template x-if="selectedMatch?.status !== 'finished'">

                            <span>📅</span>                        <div class="mt-6">

                            <span x-text="formatDate(selectedMatch?.date) + ' à ' + selectedMatch?.time"></span>                            <a :href="'/matches#match-' + selectedMatch?.id"

                        </div>                               class="block w-full bg-gradient-to-r from-soboa-orange to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white text-center font-bold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">

                    </div>                                Faire un pronostic

                            </a>

                    <!-- Bouton pronostic -->                        </div>

                    <template x-if="selectedMatch?.status !== 'finished'">                    </template>

                        <div class="mt-6">                </div>

                            <a :href="'/matches#match-' + selectedMatch?.id"            </div>

                               class="block w-full bg-gradient-to-r from-soboa-orange to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white text-center font-bold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">        </div>

                                🎯 Faire un pronostic    </div>

                            </a>

                        </div>    <script>

                    </template>        function calendarApp() {

                </div>            const matchesData = @json($matchesJson);

            </div>            const today = new Date();

        </div>

    </div>            return {

                currentMonth: today.getMonth(),

    <script>                currentYear: today.getFullYear(),

        function calendarApp() {                showModal: false,

            const matchesData = @json($matchesJson);                selectedMatch: null,

            const today = new Date();                matches: matchesData,

                

            return {                dayNames: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],

                currentMonth: today.getMonth(),                monthNames: ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'],

                currentYear: today.getFullYear(),

                showModal: false,                get calendarDays() {

                selectedMatch: null,                    const days = [];

                matches: matchesData,                    const firstDay = new Date(this.currentYear, this.currentMonth, 1);

                                    const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);

                monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],                    

                    let startDay = firstDay.getDay() - 1;

                get calendarDays() {                    if (startDay < 0) startDay = 6;

                    const days = [];

                    const firstDay = new Date(this.currentYear, this.currentMonth, 1);                    const prevMonth = new Date(this.currentYear, this.currentMonth, 0);

                    const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);                    for (let i = startDay - 1; i >= 0; i--) {

                                            const date = prevMonth.getDate() - i;

                    // Lundi = 0, Dimanche = 6                        days.push({

                    let startDay = firstDay.getDay() - 1;                            date: date,

                    if (startDay < 0) startDay = 6;                            isCurrentMonth: false,

                            isToday: false,

                    // Jours du mois précédent                            matches: this.getMatchesForDate(this.currentYear, this.currentMonth - 1, date)

                    const prevMonth = new Date(this.currentYear, this.currentMonth, 0);                        });

                    for (let i = startDay - 1; i >= 0; i--) {                    }

                        const date = prevMonth.getDate() - i;

                        days.push({                    const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

                            date: date,                    for (let date = 1; date <= lastDay.getDate(); date++) {

                            isCurrentMonth: false,                        const dateStr = this.currentYear + '-' + String(this.currentMonth + 1).padStart(2, '0') + '-' + String(date).padStart(2, '0');

                            isToday: false,                        days.push({

                            matches: this.getMatchesForDate(this.currentYear, this.currentMonth - 1, date)                            date: date,

                        });                            isCurrentMonth: true,

                    }                            isToday: dateStr === todayStr,

                            matches: this.getMatchesForDate(this.currentYear, this.currentMonth, date)

                    // Jours du mois courant                        });

                    const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');                    }

                    for (let date = 1; date <= lastDay.getDate(); date++) {

                        const dateStr = this.currentYear + '-' + String(this.currentMonth + 1).padStart(2, '0') + '-' + String(date).padStart(2, '0');                    const remaining = 42 - days.length;

                        days.push({                    for (let date = 1; date <= remaining; date++) {

                            date: date,                        days.push({

                            isCurrentMonth: true,                            date: date,

                            isToday: dateStr === todayStr,                            isCurrentMonth: false,

                            matches: this.getMatchesForDate(this.currentYear, this.currentMonth, date)                            isToday: false,

                        });                            matches: this.getMatchesForDate(this.currentYear, this.currentMonth + 1, date)

                    }                        });

                    }

                    // Jours du mois suivant pour compléter 6 semaines

                    const remaining = 42 - days.length;                    return days;

                    for (let date = 1; date <= remaining; date++) {                },

                        days.push({

                            date: date,                getMatchesForDate(year, month, date) {

                            isCurrentMonth: false,                    if (month < 0) {

                            isToday: false,                        month = 11;

                            matches: this.getMatchesForDate(this.currentYear, this.currentMonth + 1, date)                        year--;

                        });                    } else if (month > 11) {

                    }                        month = 0;

                        year++;

                    return days;                    }

                },                    

                    const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(date).padStart(2, '0');

                getMatchesForDate(year, month, date) {                    return this.matches.filter(function(m) { return m.date === dateStr; });

                    if (month < 0) { month = 11; year--; }                },

                    else if (month > 11) { month = 0; year++; }

                                    previousMonth() {

                    const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(date).padStart(2, '0');                    if (this.currentMonth === 0) {

                    return this.matches.filter(function(m) { return m.date === dateStr; });                        this.currentMonth = 11;

                },                        this.currentYear--;

                    } else {

                formatDate(dateStr) {                        this.currentMonth--;

                    if (!dateStr) return '';                    }

                    const parts = dateStr.split('-');                },

                    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

                    return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];                nextMonth() {

                },                    if (this.currentMonth === 11) {

                        this.currentMonth = 0;

                previousMonth() {                        this.currentYear++;

                    if (this.currentMonth === 0) {                    } else {

                        this.currentMonth = 11;                        this.currentMonth++;

                        this.currentYear--;                    }

                    } else {                },

                        this.currentMonth--;

                    }                openMatchModal(match) {

                },                    this.selectedMatch = match;

                    this.showModal = true;

                nextMonth() {                }

                    if (this.currentMonth === 11) {            };

                        this.currentMonth = 0;        }

                        this.currentYear++;    </script>

                    } else {</x-layouts.app>

                        this.currentMonth++;
                    }
                },

                openMatchModal(match) {
                    this.selectedMatch = match;
                    this.showModal = true;
                }
            };
        }
    </script>
</x-layouts.app>
