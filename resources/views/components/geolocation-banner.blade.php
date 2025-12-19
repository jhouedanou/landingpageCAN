<!-- Geolocation Banner Component -->
<div
    data-venues='@json($venues ?? [])'
    x-data="{
        show: false,
        nearbyVenue: null,
        distance: null,
        userLocation: null,
        checking: false,
        hasChecked: sessionStorage.getItem('geo_checked') === 'true',

        async checkGeolocation() {
            if (this.hasChecked || this.checking) return;

            this.checking = true;

            try {
                const position = await this.getCurrentPosition();
                this.userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                await this.findNearbyVenue();
                sessionStorage.setItem('geo_checked', 'true');
                this.hasChecked = true;
            } catch (error) {
                console.log('Geolocation not available or denied');
                sessionStorage.setItem('geo_checked', 'true');
            } finally {
                this.checking = false;
            }
        },

        getCurrentPosition() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('Geolocation not supported'));
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    resolve,
                    reject,
                    {
                        enableHighAccuracy: false,
                        timeout: 5000,
                        maximumAge: 300000
                    }
                );
            });
        },

        async findNearbyVenue() {
            try {
                const venuesData = this.$el.getAttribute('data-venues');
                const venues = JSON.parse(venuesData || '[]');
                
                let closestVenue = null;
                let minDistance = Infinity;
                
                venues.forEach(venue => {
                    const distance = this.calculateDistance(
                        this.userLocation.lat,
                        this.userLocation.lng,
                        parseFloat(venue.latitude),
                        parseFloat(venue.longitude)
                    );
                    
                    if (distance < minDistance) {
                        minDistance = distance;
                        closestVenue = venue;
                    }
                });
                
                // Show banner if venue is within 5km
                if (closestVenue && minDistance <= 5) {
                    this.nearbyVenue = closestVenue;
                    this.distance = minDistance;
                    this.show = true;
                    
                    // Auto-hide after 15 seconds
                    setTimeout(() => {
                        this.show = false;
                    }, 15000);
                }
            } catch (error) {
                console.error('Error finding nearby venue:', error);
            }
        },
        
        calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        },
        
        closeBanner() {
            this.show = false;
        },
        
        goToVenue() {
            window.location.href = '/map';
        }
    }"
    x-init="setTimeout(() => checkGeolocation(), 2000)"
    x-cloak
>
    <!-- Bannière en bas de page -->
    <div 
        x-show="show"
        x-transition:enter="transition ease-out duration-500 transform"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-300 transform"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-0 left-0 right-0 z-50 safe-bottom"
    >
        <div class="max-w-7xl mx-auto px-4 pb-4">
            <div class="bg-gradient-to-r from-soboa-blue to-blue-600 rounded-t-2xl shadow-2xl border-t-4 border-soboa-orange overflow-hidden">
                <div class="p-4 flex items-center justify-between gap-4">
                    <!-- Icône & Message -->
                    <div class="flex items-center gap-4 flex-1">
                        <div class="bg-white/10 rounded-full p-3 flex-shrink-0">
                            <svg class="w-6 h-6 text-soboa-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        
                        <div class="flex-1">
                            <p class="text-white font-bold text-sm md:text-base leading-tight">
                                📍 <span x-text="nearbyVenue?.name"></span> à 
                                <span x-text="distance?.toFixed(1)"></span> km
                            </p>
                            <p class="text-white/80 text-xs md:text-sm mt-1">
                                🎉 Gagnez +4 points bonus en pronostiquant depuis ce PDV partenaire !
                            </p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button 
                            @click="goToVenue()"
                            class="bg-soboa-orange hover:bg-soboa-orange-dark text-black font-bold px-4 py-2 rounded-lg text-xs md:text-sm transition-all transform hover:scale-105 shadow-lg whitespace-nowrap"
                        >
                            Voir sur la carte
                        </button>
                        
                        <button 
                            @click="closeBanner()"
                            class="text-white/60 hover:text-white transition-colors p-2"
                            aria-label="Fermer"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Barre de progression (auto-fermeture) -->
                <div class="h-1 bg-white/20">
                    <div 
                        class="h-full bg-soboa-orange transition-all duration-[15000ms] ease-linear"
                        x-show="show"
                        :style="show ? 'width: 0%' : 'width: 100%'"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
    
    /* Safe area for mobile devices */
    .safe-bottom {
        padding-bottom: env(safe-area-inset-bottom);
    }
</style>
