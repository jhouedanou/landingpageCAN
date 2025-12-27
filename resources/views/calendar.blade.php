@php
use Carbon\Carbon;

$currentDate = Carbon::now();
$year = request('year', $currentDate->year);
$month = request('month', $currentDate->month);

$startOfMonth = Carbon::create($year, $month, 1);
$endOfMonth = $startOfMonth->copy()->endOfMonth();
$startDayOfWeek = $startOfMonth->dayOfWeekIso;
$prevMonthDays = $startDayOfWeek - 1;
$prevMonth = $startOfMonth->copy()->subMonth();
$daysInPrevMonth = $prevMonth->daysInMonth;

$calendarDays = [];

for ($i = $prevMonthDays; $i > 0; $i--) {
    $day = $daysInPrevMonth - $i + 1;
    $date = $prevMonth->copy()->day($day);
    $calendarDays[] = ['date' => $day, 'fullDate' => $date->format('Y-m-d'), 'isCurrentMonth' => false, 'isToday' => false];
}

for ($day = 1; $day <= $endOfMonth->day; $day++) {
    $date = Carbon::create($year, $month, $day);
    $calendarDays[] = ['date' => $day, 'fullDate' => $date->format('Y-m-d'), 'isCurrentMonth' => true, 'isToday' => $date->isToday()];
}

$remainingDays = 42 - count($calendarDays);
$nextMonth = $startOfMonth->copy()->addMonth();
for ($day = 1; $day <= $remainingDays; $day++) {
    $date = $nextMonth->copy()->day($day);
    $calendarDays[] = ['date' => $day, 'fullDate' => $date->format('Y-m-d'), 'isCurrentMonth' => false, 'isToday' => false];
}

$matchesByDate = $matches->groupBy(function($match) {
    return Carbon::parse($match->match_date)->format('Y-m-d');
});

// Pour le mobile : liste des jours avec matchs seulement
$daysWithMatches = collect($calendarDays)->filter(function($day) use ($matchesByDate) {
    return $matchesByDate->has($day['fullDate']) && $day['isCurrentMonth'];
});

$prevMonthLink = route('calendar', ['year' => $prevMonth->year, 'month' => $prevMonth->month]);
$nextMonthLink = route('calendar', ['year' => $nextMonth->year, 'month' => $nextMonth->month]);
$monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
$dayNames = ['', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
@endphp

<x-layouts.app title="Calendrier des matchs">
<div class="min-h-screen bg-gradient-to-br from-soboa-blue via-blue-800 to-blue-900 py-4 px-2 md:px-4">

{{-- Header --}}
<div class="max-w-7xl mx-auto mb-4">
    <div class="text-center">
        <h1 class="text-xl md:text-4xl font-black text-white mb-2">Calendrier des matchs</h1>
        <p class="text-blue-200 text-xs md:text-sm">{{ $totalMatches }} matchs - {{ $finishedMatches }} terminés - {{ $upcomingMatches }} à venir</p>
    </div>
</div>

{{-- Navigation Mois --}}
<div class="max-w-7xl mx-auto mb-4">
    <div class="flex items-center justify-between bg-white/10 backdrop-blur-sm rounded-xl p-2">
        <a href="{{ $prevMonthLink }}" class="p-2 text-white hover:bg-white/20 rounded-lg">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-lg md:text-xl font-bold text-white">{{ $monthNames[$month] }} {{ $year }}</h2>
        <a href="{{ $nextMonthLink }}" class="p-2 text-white hover:bg-white/20 rounded-lg">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</div>

{{-- VERSION MOBILE : Liste des matchs par jour --}}
<div class="md:hidden max-w-7xl mx-auto space-y-3">
    @forelse($daysWithMatches as $day)
        @php 
            $dayMatches = $matchesByDate->get($day['fullDate'], collect());
            $dateObj = Carbon::parse($day['fullDate']);
        @endphp
        <div class="bg-white rounded-xl shadow-lg overflow-hidden @if($day['isToday']) ring-2 ring-soboa-orange @endif">
            {{-- En-tête du jour --}}
            <div class="bg-gradient-to-r from-soboa-blue to-blue-700 px-4 py-2 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black text-white">{{ $day['date'] }}</span>
                    <span class="text-white/80 text-sm">{{ $dayNames[$dateObj->dayOfWeekIso] }}</span>
                </div>
                @if($day['isToday'])
                    <span class="bg-soboa-orange text-white text-xs px-2 py-1 rounded-full font-bold">Aujourd'hui</span>
                @endif
                <span class="bg-white/20 text-white text-xs px-2 py-1 rounded-full">{{ $dayMatches->count() }} match{{ $dayMatches->count() > 1 ? 's' : '' }}</span>
            </div>
            
            {{-- Liste des matchs --}}
            <div class="divide-y divide-gray-100">
                @foreach($dayMatches as $match)
                    <a href="{{ route('matches') }}#match-{{ $match->id }}" class="block p-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            {{-- Équipe domicile --}}
                            <div class="flex items-center gap-2 flex-1">
                                @if($match->homeTeam && $match->homeTeam->iso_code)
                                    <img src="https://flagcdn.com/w40/{{ $match->homeTeam->iso_code }}.png" class="w-8 h-5 object-cover rounded shadow-sm" alt="">
                                @endif
                                <span class="font-semibold text-gray-800 text-sm truncate">{{ $match->homeTeam->name ?? $match->team_a ?? '?' }}</span>
                            </div>
                            
                            {{-- Score ou heure --}}
                            <div class="flex-shrink-0 mx-2">
                                @if($match->status === 'finished')
                                    <div class="flex flex-col items-center">
                                        <div class="flex items-center gap-1 bg-green-100 px-3 py-1 rounded-lg">
                                            <span class="text-lg font-black text-green-700">{{ $match->score_a }}</span>
                                            <span class="text-gray-400 font-bold">-</span>
                                            <span class="text-lg font-black text-green-700">{{ $match->score_b }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400 mt-0.5">{{ Carbon::parse($match->match_date)->format('H:i') }}</span>
                                    </div>
                                @else
                                    <div class="bg-blue-50 px-3 py-1 rounded-lg">
                                        <span class="text-blue-600 font-semibold text-sm">{{ Carbon::parse($match->match_date)->format('H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Équipe extérieur --}}
                            <div class="flex items-center gap-2 flex-1 justify-end">
                                <span class="font-semibold text-gray-800 text-sm truncate text-right">{{ $match->awayTeam->name ?? $match->team_b ?? '?' }}</span>
                                @if($match->awayTeam && $match->awayTeam->iso_code)
                                    <img src="https://flagcdn.com/w40/{{ $match->awayTeam->iso_code }}.png" class="w-8 h-5 object-cover rounded shadow-sm" alt="">
                                @endif
                            </div>
                        </div>
                        {{-- Infos supplémentaires --}}
                        @if($match->group)
                            <div class="mt-2 flex items-center justify-center gap-3 text-xs text-gray-500">
                                <span class="bg-gray-100 px-2 py-0.5 rounded">Groupe {{ $match->group }}</span>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-8 text-center">
            <p class="text-white/80">Aucun match prévu ce mois</p>
        </div>
    @endforelse
</div>

{{-- VERSION DESKTOP : Grille calendrier --}}
<div class="hidden md:block max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        
        {{-- En-tête jours de la semaine --}}
        <div style="display: grid; grid-template-columns: repeat(7, 1fr);" class="bg-gradient-to-r from-soboa-blue to-blue-700">
            <div class="py-3 text-center text-white font-bold text-sm border-r border-white/20">Lun</div>
            <div class="py-3 text-center text-white font-bold text-sm border-r border-white/20">Mar</div>
            <div class="py-3 text-center text-white font-bold text-sm border-r border-white/20">Mer</div>
            <div class="py-3 text-center text-white font-bold text-sm border-r border-white/20">Jeu</div>
            <div class="py-3 text-center text-white font-bold text-sm border-r border-white/20">Ven</div>
            <div class="py-3 text-center text-white font-bold text-sm border-r border-white/20">Sam</div>
            <div class="py-3 text-center text-white font-bold text-sm">Dim</div>
        </div>
        
        {{-- Grille des jours --}}
        <div style="display: grid; grid-template-columns: repeat(7, 1fr);">
            @foreach($calendarDays as $day)
                @php $dayMatches = $matchesByDate->get($day['fullDate'], collect()); @endphp
                <div class="border-r border-b border-gray-200 p-2 min-h-[100px] @if(!$day['isCurrentMonth']) bg-gray-50 @elseif($day['isToday']) bg-orange-50 ring-2 ring-inset ring-soboa-orange @else bg-white @endif">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold w-7 h-7 flex items-center justify-center rounded-full @if(!$day['isCurrentMonth']) text-gray-400 @elseif($day['isToday']) bg-soboa-orange text-white @else text-gray-700 @endif">{{ $day['date'] }}</span>
                        @if($dayMatches->count() > 0)
                            <span class="text-[10px] bg-soboa-blue text-white px-1.5 py-0.5 rounded-full font-bold">{{ $dayMatches->count() }}</span>
                        @endif
                    </div>
                    @if($dayMatches->count() > 0)
                        <div class="space-y-1 overflow-y-auto" style="max-height: 80px;">
                            @foreach($dayMatches as $match)
                                <a href="{{ route('matches') }}#match-{{ $match->id }}" class="block rounded p-1.5 text-[11px] hover:opacity-80 transition-opacity @if($match->status === 'finished') bg-green-100 border-l-2 border-green-500 @else bg-blue-50 border-l-2 border-blue-400 @endif">
                                    <div class="text-[9px] text-gray-500 mb-0.5">{{ Carbon::parse($match->match_date)->format('H:i') }}</div>
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="flex items-center gap-1 min-w-0 flex-1">
                                            @if($match->homeTeam && $match->homeTeam->iso_code)
                                                <img src="https://flagcdn.com/w20/{{ $match->homeTeam->iso_code }}.png" class="w-4 h-3 object-cover rounded-sm flex-shrink-0">
                                            @endif
                                            <span class="font-medium truncate">{{ Str::limit($match->homeTeam->name ?? '?', 3, '') }}</span>
                                        </div>
                                        @if($match->status === 'finished')
                                            <span class="font-black text-green-700 flex-shrink-0">{{ $match->score_a }}-{{ $match->score_b }}</span>
                                        @else
                                            <span class="text-blue-500 flex-shrink-0 text-[10px]">vs</span>
                                        @endif
                                        <div class="flex items-center gap-1 min-w-0 flex-1 justify-end">
                                            <span class="font-medium truncate">{{ Str::limit($match->awayTeam->name ?? '?', 3, '') }}</span>
                                            @if($match->awayTeam && $match->awayTeam->iso_code)
                                                <img src="https://flagcdn.com/w20/{{ $match->awayTeam->iso_code }}.png" class="w-4 h-3 object-cover rounded-sm flex-shrink-0">
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
    </div>
</div>

{{-- Légende --}}
<div class="max-w-7xl mx-auto mt-4">
    <div class="flex flex-wrap justify-center gap-4 text-xs md:text-sm">
        <div class="flex items-center gap-2 text-white">
            <div class="w-4 h-4 bg-green-100 border-l-2 border-green-500 rounded"></div>
            <span>Terminé</span>
        </div>
        <div class="flex items-center gap-2 text-white">
            <div class="w-4 h-4 bg-blue-50 border-l-2 border-blue-400 rounded"></div>
            <span>À venir</span>
        </div>
        <div class="flex items-center gap-2 text-white">
            <div class="w-4 h-4 bg-soboa-orange rounded-full"></div>
            <span>Aujourd'hui</span>
        </div>
    </div>
</div>

</div>
</x-layouts.app>
