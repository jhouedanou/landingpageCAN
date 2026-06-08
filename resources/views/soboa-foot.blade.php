<x-layouts.app title="Animation SOBOA FOOT">
    <div class="space-y-8">

        {{-- Hero --}}
        <div class="relative py-14 px-8 rounded-2xl overflow-hidden shadow-2xl">
            <div class="absolute inset-0 z-0">
                <img src="/images/sen.webp" class="w-full h-full object-cover scale-105" alt="">
                <div class="absolute inset-0 bg-gradient-to-br from-soboa-blue-dark/90 via-soboa-blue/70 to-soboa-orange/30"></div>
            </div>
            <div class="relative z-10 text-center md:text-left max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-soboa-orange/20 border border-soboa-orange/40 text-soboa-orange text-xs font-black uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
                    <span class="w-2 h-2 bg-soboa-orange rounded-full animate-pulse"></span>
                    Coupe du Monde 2026
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white leading-tight drop-shadow-2xl">
                    Animation<br><span class="text-soboa-orange">SOBOA FOOT</span>
                </h1>
                <p class="text-white/70 mt-3 text-base max-w-lg">
                    Découvrez les activités, événements et animations SOBOA pendant la Coupe du Monde. Vivez la fête avec nous !
                </p>
            </div>
        </div>

        @if($allContents->isEmpty())
            <div class="bg-white rounded-2xl shadow p-12 text-center border border-gray-100">
                <div class="w-16 h-16 bg-soboa-orange/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-soboa-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.867V15.133a1 1 0 01-1.447.902L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-700 mb-2">Prochainement</h2>
                <p class="text-gray-500">Les animations SOBOA FOOT arrivent bientôt. Revenez nous voir !</p>
            </div>
        @else

            {{-- Filtres par type --}}
            @php
                $typeLabels = \App\Models\SoboaContent::$types;
                $activeTypes = $allContents->pluck('type')->unique()->values();
            @endphp

            <div x-data="{ activeType: 'all' }" class="space-y-6">

                {{-- Tabs --}}
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="activeType = 'all'"
                        :class="activeType === 'all' ? 'bg-soboa-blue text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-soboa-blue hover:text-soboa-blue'"
                        class="px-4 py-2 rounded-full text-sm font-bold transition-all"
                    >Tout voir ({{ $allContents->count() }})</button>

                    @foreach($activeTypes as $type)
                        <button
                            @click="activeType = '{{ $type }}'"
                            :class="activeType === '{{ $type }}' ? 'bg-soboa-orange text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-soboa-orange hover:text-soboa-orange'"
                            class="px-4 py-2 rounded-full text-sm font-bold transition-all"
                        >{{ $typeLabels[$type] ?? $type }}</button>
                    @endforeach
                </div>

                {{-- Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($allContents as $content)
                        <div
                            x-show="activeType === 'all' || activeType === '{{ $content->type }}'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all group flex flex-col"
                        >
                            {{-- Image / Vidéo --}}
                            @if($content->video_url)
                                <div class="aspect-video w-full bg-gray-900">
                                    @if(str_contains($content->video_url, 'youtube') || str_contains($content->video_url, 'youtu.be'))
                                        @php
                                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&?]+)/', $content->video_url, $yt);
                                            $ytId = $yt[1] ?? null;
                                        @endphp
                                        @if($ytId)
                                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allowfullscreen></iframe>
                                        @endif
                                    @else
                                        <video class="w-full h-full object-cover" controls>
                                            <source src="{{ $content->video_url }}">
                                        </video>
                                    @endif
                                </div>
                            @elseif($content->image_url)
                                <div class="aspect-video w-full overflow-hidden bg-gray-100">
                                    <img src="{{ $content->image_url }}" alt="{{ $content->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                            @else
                                <div class="aspect-video w-full bg-gradient-to-br from-soboa-blue to-soboa-blue-light flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3l14 9-14 9V3z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Content --}}
                            <div class="p-5 flex flex-col flex-1">
                                {{-- Type badge --}}
                                <div class="mb-3">
                                    @php
                                        $typeBadgeColors = [
                                            'annonce'    => 'bg-soboa-blue/10 text-soboa-blue',
                                            'evenement'  => 'bg-purple-100 text-purple-700',
                                            'activation' => 'bg-green-100 text-green-700',
                                            'promo'      => 'bg-soboa-orange/10 text-soboa-orange',
                                            'galerie'    => 'bg-pink-100 text-pink-700',
                                        ];
                                        $badgeColor = $typeBadgeColors[$content->type] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="inline-block text-xs font-black uppercase tracking-widest px-3 py-1 rounded-full {{ $badgeColor }}">
                                        {{ $typeLabels[$content->type] ?? $content->type }}
                                    </span>
                                </div>

                                <h3 class="font-black text-soboa-text-dark text-lg leading-tight mb-2">{{ $content->title }}</h3>

                                @if($content->body)
                                    <p class="text-gray-500 text-sm leading-relaxed flex-1">{{ Str::limit($content->body, 150) }}</p>
                                @endif

                                @if($content->published_at)
                                    <p class="text-xs text-gray-400 mt-3">{{ $content->published_at->diffForHumans() }}</p>
                                @endif

                                @if($content->cta_label && $content->cta_url)
                                    <a
                                        href="{{ $content->cta_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="mt-4 inline-flex items-center gap-2 bg-soboa-orange hover:bg-soboa-orange-secondary text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors self-start"
                                    >
                                        {{ $content->cta_label }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Mention légale --}}
        <div class="bg-red-50 border border-red-200 rounded-xl p-3">
            <div class="flex items-center justify-center gap-3">
                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-black text-xs">18+</span>
                </div>
                <p class="text-red-700 text-sm font-medium">
                    Ce jeu est réservé aux personnes majeures.
                    <a href="{{ route('terms') }}" class="underline hover:text-red-900">Conditions de participation</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
