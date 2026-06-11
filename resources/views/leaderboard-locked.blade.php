<x-layouts.app title="Classement">
    <div class="space-y-6">
        <!-- Header -->
        <div class="relative py-8 px-6 rounded-2xl overflow-hidden mb-6 shadow-2xl text-center">
            <div class="absolute inset-0 z-0">
                <img src="/images/sen.webp" class="w-full h-full object-cover" alt="Background">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"></div>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl md:text-4xl font-black text-white drop-shadow-2xl">Classement</h1>
                <p class="text-white/90 font-bold mt-2 uppercase tracking-widest text-xs">
                    Bientôt disponible
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-10 text-center">
            <div class="text-6xl mb-4">🔒</div>
            <h2 class="text-2xl font-black text-soboa-blue mb-3">Le classement sera dévoilé après le premier match</h2>
            <p class="text-gray-600 max-w-xl mx-auto leading-relaxed">
                Vos points s'accumulent déjà ! Pronostiquez dès maintenant sur les matchs à venir :
                le classement général s'affichera dès la fin du premier match de la compétition.
            </p>
            <a href="{{ route('matches') }}"
               class="inline-flex items-center gap-2 bg-soboa-orange hover:bg-soboa-orange/90 text-white font-bold py-3 px-8 rounded-xl transition mt-6">
                🎯 Faire mes pronostics
            </a>
        </div>
    </div>
</x-layouts.app>
