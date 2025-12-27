<x-layouts.app title="Inscription">
    <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">

            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-white rounded-full mx-auto mb-4 shadow-lg flex items-center justify-center">
                    <img src="/images/logoGazelle.jpeg" alt="SOBOA" class="w-16 h-16 object-contain">
                </div>
                <h1 class="text-3xl font-black text-soboa-blue">Inscription</h1>
                <p class="text-gray-600 mt-2">Créez votre compte</p>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8" x-data="registerForm()">

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

                <!-- Messages de succès -->
                <div x-show="success" x-cloak
                    class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span x-text="success" class="font-medium"></span>
                    </div>
                </div>

                <form @submit.prevent="register">
                    <!-- Nom complet -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Votre nom complet</label>
                        <input type="text" x-model="name" placeholder="Jean Dupont"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-soboa-orange focus:ring-0 text-lg"
                            required autofocus>
                    </div>

                    <!-- Numéro de téléphone -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Numéro de téléphone</label>
                        <div class="flex gap-2">
                            <!-- Indicatif Sénégal uniquement -->
                            <div class="px-4 py-3 border-2 border-gray-200 bg-gray-50 rounded-xl text-sm font-bold text-gray-700 flex items-center">
                                🇸🇳 +221
                            </div>
                            <input type="tel" x-model="phone"
                                placeholder="77 123 45 67"
                                class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-soboa-orange focus:ring-0 text-lg"
                                required>
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" x-model="password"
                                placeholder="Minimum 6 caractères"
                                @input="checkPasswordStrength()"
                                class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-soboa-orange focus:ring-0 text-lg"
                                required>
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <!-- Force du mot de passe -->
                        <div x-show="password.length > 0" class="mt-2">
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div :class="{
                                    'bg-red-500': passwordStrength === 'weak',
                                    'bg-yellow-500': passwordStrength === 'medium',
                                    'bg-green-500': passwordStrength === 'strong'
                                }"
                                :style="'width: ' + passwordStrengthPercent + '%'"
                                class="h-full transition-all"></div>
                            </div>
                            <p class="text-xs mt-1" :class="{
                                'text-red-600': passwordStrength === 'weak',
                                'text-yellow-600': passwordStrength === 'medium',
                                'text-green-600': passwordStrength === 'strong'
                            }" x-text="passwordStrengthText"></p>
                        </div>
                    </div>

                    <!-- Confirmation mot de passe -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Confirmer le mot de passe</label>
                        <input :type="showPasswordConfirm ? 'text' : 'password'" x-model="passwordConfirm"
                            placeholder="Retapez votre mot de passe"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-soboa-orange focus:ring-0 text-lg"
                            required>
                        <p x-show="passwordConfirm && password !== passwordConfirm" class="text-xs text-red-600 mt-1">
                            Les mots de passe ne correspondent pas
                        </p>
                    </div>

                    <!-- Bouton d'inscription -->
                    <button type="submit" :disabled="loading || (password && password !== passwordConfirm)"
                        class="w-full bg-soboa-orange hover:bg-soboa-orange-dark disabled:bg-gray-400 text-black font-bold py-4 px-6 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 mb-4">
                        <span x-show="!loading">Créer mon compte</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Création en cours...
                        </span>
                    </button>

                    <!-- Lien connexion -->
                    <div class="text-center pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-600 mb-2">Vous avez déjà un compte ?</p>
                        <a href="/login" class="text-soboa-orange hover:underline font-bold">
                            Se connecter
                        </a>
                    </div>
                </form>

                <!-- Conditions -->
                <p class="text-xs text-gray-500 text-center mt-6">
                    En vous inscrivant, vous acceptez nos
                    <a href="/conditions" class="text-soboa-blue hover:underline">conditions d'utilisation</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function registerForm() {
            return {
                name: '',
                phone: '',
                password: '',
                passwordConfirm: '',
                showPassword: false,
                showPasswordConfirm: false,
                loading: false,
                error: '',
                success: '',
                countryCode: '+221', // Sénégal uniquement
                passwordStrength: '',
                passwordStrengthPercent: 0,
                passwordStrengthText: '',

                get fullPhone() {
                    return this.countryCode + this.formatPhoneNumber(this.phone);
                },

                formatPhoneNumber(phone) {
                    // Supprimer tout sauf les chiffres
                    return phone.replace(/\D/g, '');
                },

                // Valider le format du numéro (Sénégal uniquement)
                isValidPhone() {
                    const phoneDigits = this.formatPhoneNumber(this.phone);
                    // Sénégal: 9 chiffres commençant par 7
                    return phoneDigits.length === 9 && phoneDigits.startsWith('7');
                },

                checkPasswordStrength() {
                    const pwd = this.password;
                    let strength = 0;

                    if (pwd.length >= 6) strength += 1;
                    if (pwd.length >= 8) strength += 1;
                    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength += 1;
                    if (/[0-9]/.test(pwd)) strength += 1;
                    if (/[^a-zA-Z0-9]/.test(pwd)) strength += 1;

                    if (strength <= 2) {
                        this.passwordStrength = 'weak';
                        this.passwordStrengthPercent = 33;
                        this.passwordStrengthText = 'Faible';
                    } else if (strength <= 3) {
                        this.passwordStrength = 'medium';
                        this.passwordStrengthPercent = 66;
                        this.passwordStrengthText = 'Moyen';
                    } else {
                        this.passwordStrength = 'strong';
                        this.passwordStrengthPercent = 100;
                        this.passwordStrengthText = 'Fort';
                    }
                },

                async register() {
                    if (!this.name.trim() || !this.phone.trim() || !this.password.trim()) {
                        this.error = 'Veuillez remplir tous les champs.';
                        return;
                    }

                    // Valider le format du numéro
                    if (!this.isValidPhone()) {
                        this.error = 'Le numéro doit contenir 9 chiffres commençant par 7 (ex: 77 123 45 67).';
                        return;
                    }

                    // Vérifier la longueur du mot de passe
                    if (this.password.length < 6) {
                        this.error = 'Le mot de passe doit contenir au moins 6 caractères.';
                        return;
                    }

                    // Vérifier la confirmation
                    if (this.password !== this.passwordConfirm) {
                        this.error = 'Les mots de passe ne correspondent pas.';
                        return;
                    }

                    this.loading = true;
                    this.error = '';
                    this.success = '';

                    try {
                        const response = await fetch('/auth/register', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name: this.name,
                                phone: this.fullPhone,
                                password: this.password,
                                password_confirmation: this.passwordConfirm
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.success = 'Compte créé avec succès !';

                            // Redirection après succès
                            setTimeout(() => {
                                window.location.href = data.redirect || '/matches';
                            }, 1000);
                        } else {
                            this.error = data.message || 'Erreur lors de l\'inscription.';
                        }
                    } catch (err) {
                        console.error('Erreur:', err);
                        this.error = 'Erreur de connexion. Veuillez réessayer.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</x-layouts.app>
