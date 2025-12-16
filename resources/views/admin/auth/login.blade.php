<x-layouts.app title="Connexion Administrateur">
    <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-4 py-8 bg-gradient-to-br from-gray-900 to-gray-800">
        <div class="w-full max-w-md">

            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-red-600 rounded-full mx-auto mb-4 shadow-lg flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-3xl font-black text-white">Administration</h1>
                <p class="text-gray-300 mt-2">Accès réservé aux administrateurs</p>
            </div>

            <!-- Formulaire -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8" x-data="adminLoginForm()">

                <!-- Messages d'erreur -->
                <div x-show="error" x-cloak
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-text="error" class="font-medium"></span>
                    </div>
                </div>

                <!-- Étape 1: Téléphone -->
                <div x-show="step === 1">
                    <form @submit.prevent="sendOtp">
                        <!-- Téléphone -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Numéro de téléphone administrateur</label>
                            <div class="flex gap-2">
                                <!-- Indicatif verrouillé Côte d'Ivoire -->
                                <div class="px-4 py-3 border-2 border-gray-300 bg-gray-100 rounded-xl text-sm font-bold text-gray-600 flex items-center">
                                    🇨🇮 +225
                                </div>
                                <input type="tel" x-model="phone" placeholder="0X XX XX XX XX"
                                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-600 focus:ring-0 text-lg"
                                    required>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                <span>Code d'accès via <strong>WhatsApp</strong></span>
                            </p>
                            <p class="text-xs text-red-600 mt-2 font-bold">
                                ⚠️ Accès réservé au numéro administrateur autorisé uniquement
                            </p>
                        </div>

                        <!-- Bouton -->
                        <button type="submit" :disabled="loading"
                            class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                            <span x-show="!loading">Recevoir le code admin</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Envoi en cours...
                            </span>
                        </button>
                    </form>
                </div>

                <!-- Étape 2: Vérification du code -->
                <div x-show="step === 2" x-cloak>
                    <form @submit.prevent="verifyOtp">
                        <div class="text-center mb-6">
                            <div
                                class="w-16 h-16 bg-red-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Code admin envoyé !</h2>
                            <p class="text-gray-600 mt-1">
                                Vérifiez vos messages WhatsApp au <span class="font-bold text-red-600"
                                    x-text="fullPhone"></span>
                            </p>
                        </div>

                        <!-- Champ code -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Code de vérification</label>
                            <input type="text" x-model="code" placeholder="000000" maxlength="6"
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-red-600 focus:ring-0 text-2xl text-center tracking-[0.5em] font-bold"
                                required>
                        </div>

                        <!-- Boutons -->
                        <button type="submit" :disabled="loading || code.length !== 6"
                            class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 mb-4">
                            <span x-show="!loading">Vérifier et accéder</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Vérification...
                            </span>
                        </button>

                        <!-- Renvoyer le code -->
                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-2">Vous n'avez pas reçu le code ?</p>
                            <button type="button" @click="resendOtp" :disabled="resendCooldown > 0"
                                class="text-red-600 hover:underline font-bold disabled:text-gray-400">
                                <span x-show="resendCooldown === 0">Renvoyer le code</span>
                                <span x-show="resendCooldown > 0">Réessayer dans <span
                                        x-text="resendCooldown"></span>s</span>
                            </button>
                        </div>

                        <!-- Changer de numéro -->
                        <div class="text-center mt-4 pt-4 border-t">
                            <button type="button" @click="step = 1; code = ''; error = ''; resendCooldown = 0"
                                class="text-sm text-gray-500 hover:text-gray-700">
                                ← Modifier le numéro
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Retour -->
                <div class="text-center mt-6">
                    <a href="/" class="text-sm text-gray-500 hover:text-gray-700">
                        ← Retour au site public
                    </a>
                </div>
            </div>

            <!-- Avertissement -->
            <div class="mt-6 text-center bg-red-900 bg-opacity-50 p-4 rounded-xl">
                <p class="text-sm text-white font-bold">
                    🔐 Zone d'administration sécurisée
                </p>
                <p class="text-xs text-gray-300 mt-2">
                    Toutes les actions sont enregistrées et surveillées
                </p>
            </div>
        </div>
    </div>

    <script>
        function adminLoginForm() {
            return {
                step: 1,
                phone: '',
                countryCode: '+225', // Verrouillé sur Côte d'Ivoire
                code: '',
                loading: false,
                error: '',
                resendCooldown: 0,

                get fullPhone() {
                    return this.countryCode + this.formatPhoneNumber(this.phone);
                },

                formatPhoneNumber(phone) {
                    // Supprimer tout sauf les chiffres
                    let digits = phone.replace(/\D/g, '');

                    // Côte d'Ivoire: 10 chiffres avec le 0 initial
                    if (!digits.startsWith('0') && digits.length === 9) {
                        digits = '0' + digits;
                    }

                    return digits;
                },

                async sendOtp() {
                    if (!this.phone.trim()) {
                        this.error = 'Veuillez entrer votre numéro de téléphone.';
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    console.log('=== ENVOI OTP ADMIN ===');
                    console.log('Numéro formaté:', this.fullPhone);

                    try {
                        const response = await fetch('/admin/auth/send-otp', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                phone: this.fullPhone
                            })
                        });

                        const data = await response.json();

                        console.log('=== RÉPONSE SERVEUR ADMIN ===');
                        console.log('Status HTTP:', response.status);
                        console.log('Data:', data);

                        if (data.success) {
                            this.step = 2;
                            this.startResendCooldown();
                            console.log('Code admin envoyé avec succès !');
                        } else {
                            this.error = data.message || 'Erreur lors de l\'envoi du code.';
                            if (data.error) {
                                console.error('Erreur:', data.error);
                            }
                        }
                    } catch (err) {
                        console.error('Erreur réseau:', err);
                        this.error = 'Erreur de connexion. Veuillez réessayer.';
                    } finally {
                        this.loading = false;
                    }
                },

                async verifyOtp() {
                    if (this.code.length !== 6) {
                        this.error = 'Le code doit contenir 6 chiffres.';
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('/admin/auth/verify-otp', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                phone: this.fullPhone,
                                code: this.code
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Redirection vers l'admin
                            window.location.href = data.redirect || '/admin';
                        } else {
                            this.error = data.message || 'Code incorrect.';
                        }
                    } catch (err) {
                        console.error('Erreur:', err);
                        this.error = 'Erreur de connexion. Veuillez réessayer.';
                    } finally {
                        this.loading = false;
                    }
                },

                async resendOtp() {
                    if (this.resendCooldown > 0) return;

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('/admin/auth/send-otp', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                phone: this.fullPhone
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.startResendCooldown();
                        } else {
                            this.error = data.message || 'Erreur lors du renvoi.';
                        }
                    } catch (err) {
                        this.error = 'Erreur de connexion.';
                    } finally {
                        this.loading = false;
                    }
                },

                startResendCooldown() {
                    this.resendCooldown = 60;
                    const interval = setInterval(() => {
                        this.resendCooldown--;
                        if (this.resendCooldown <= 0) {
                            clearInterval(interval);
                        }
                    }, 1000);
                }
            };
        }
    </script>
</x-layouts.app>
