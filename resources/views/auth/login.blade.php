<x-layouts.app title="Connexion">
    <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">

            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-white rounded-full mx-auto mb-4 shadow-lg flex items-center justify-center">
                    <img src="/images/logoGazelle.jpeg" alt="SOBOA" class="w-16 h-16 object-contain">
                </div>
                <h1 class="text-3xl font-black text-soboa-blue">Connexion</h1>
                <p class="text-gray-600 mt-2">Entrez votre numéro pour jouer</p>
            </div>

            <!-- Explication du système de mot de passe -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-bold mb-1">Comment ça marche ?</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li><strong>Première connexion :</strong> Vous recevrez un code à 6 chiffres par SMS</li>
                            <li><strong>Ce code devient votre mot de passe</strong> pour toutes vos connexions futures</li>
                            <li><strong>Prochaines connexions :</strong> Entrez simplement ce même code (pas de nouveau SMS)</li>
                            <li><strong>Code oublié ?</strong> Vous pourrez en demander un nouveau</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8" x-data="loginForm()">

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

                <!-- Étape 1: Nom et téléphone -->
                <div x-show="step === 1">
                    <form @submit.prevent="sendOtp">
                        <!-- Nom complet -->
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Votre nom complet</label>
                            <input type="text" x-model="name" placeholder="Jean Dupont"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-soboa-orange focus:ring-0 text-lg"
                                required>
                        </div>

                        <!-- Téléphone -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Numéro de téléphone</label>
                            <div class="flex gap-2">
                                <!-- Sélecteur de pays -->
                                <select x-model="countryCode" 
                                    class="px-3 py-3 border-2 border-gray-200 bg-white rounded-xl text-sm font-bold text-gray-700 focus:border-soboa-orange focus:ring-0">
                                    <option value="+221">🇸🇳 +221</option>
                                    <option value="+225">🇨🇮 +225</option>
                                </select>
                                <input type="tel" x-model="phone" 
                                    :placeholder="getPlaceholder()"
                                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-soboa-orange focus:ring-0 text-lg"
                                    required>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4 text-soboa-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span>Un <strong>mot de passe à 6 chiffres</strong> vous sera envoyé par SMS</span>
                            </p>
                        </div>

                        <!-- CAPTCHA Mathématique -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-xl border-2 border-blue-200">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Sécurité: Résolvez cette
                                opération</label>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="text-lg font-bold text-gray-800">
                                    <span x-text="captchaNum1"></span>
                                    <span x-text="captchaOp" class="mx-2"></span>
                                    <span x-text="captchaNum2"></span>
                                    <span class="mx-2">=</span>
                                    <span class="text-blue-600">?</span>
                                </div>
                            </div>
                            <input type="number" x-model="captchaAnswer" placeholder="Votre réponse"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-soboa-orange focus:ring-0 text-lg"
                                required>
                        </div>

                        <!-- Erreur CAPTCHA -->
                        <div x-show="captchaError" x-cloak
                            class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Réponse CAPTCHA incorrecte</span>
                            </div>
                        </div>

                        <!-- Bouton -->
                        <button type="submit" :disabled="loading"
                            class="w-full bg-soboa-orange hover:bg-soboa-orange-dark disabled:bg-gray-400 text-black font-bold py-4 px-6 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                            <span x-show="!loading">Recevoir mon mot de passe</span>
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
                        
                        <!-- Lien code oublié -->
                        <div class="text-center mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500 mb-2">Déjà inscrit mais code oublié ?</p>
                            <button type="button" @click="forgotPassword()"
                                class="text-soboa-orange hover:underline font-bold text-sm">
                                🔄 Demander un nouveau mot de passe
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Étape 2: Vérification du code -->
                <div x-show="step === 2" x-cloak>
                    <form @submit.prevent="verifyOtp">
                        <div class="text-center mb-6">
                            <div
                                class="w-16 h-16 bg-soboa-blue rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                            
                            <!-- Message différent selon nouveau ou ancien utilisateur -->
                            <template x-if="hasExistingPassword">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-800">Bon retour ! 👋</h2>
                                    <p class="text-gray-600 mt-1">
                                        Entrez votre <strong>mot de passe</strong> pour <span class="font-bold text-soboa-blue" x-text="fullPhone"></span>
                                    </p>
                                    <p class="text-sm text-blue-600 mt-2 flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>C'est le code à 6 chiffres reçu lors de votre inscription</span>
                                    </p>
                                </div>
                            </template>
                            
                            <template x-if="!hasExistingPassword">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-800">Mot de passe envoyé ! 📱</h2>
                                    <p class="text-gray-600 mt-1">
                                        Vérifiez vos SMS au <span class="font-bold text-soboa-blue" x-text="fullPhone"></span>
                                    </p>
                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-sm text-yellow-800 flex items-start gap-2">
                                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            <span><strong>⚠️ Notez ce code !</strong> Il sera votre mot de passe permanent pour toutes vos connexions futures. Pas de nouveau SMS à chaque connexion !</span>
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Champ code -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2" x-text="hasExistingPassword ? 'Votre mot de passe (6 chiffres)' : 'Code reçu par SMS'"></label>
                            <input type="text" x-model="code" placeholder="000000" maxlength="6"
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-soboa-blue focus:ring-0 text-2xl text-center tracking-[0.5em] font-bold"
                                required>
                        </div>

                        <!-- Boutons -->
                        <button type="submit" :disabled="loading || code.length !== 6"
                            class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 mb-4">
                            <span x-show="!loading">Se connecter</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Connexion...
                            </span>
                        </button>

                        <!-- Renvoyer le code (seulement pour les nouveaux) -->
                        <div x-show="!hasExistingPassword" class="text-center">
                            <p class="text-sm text-gray-500 mb-2">Vous n'avez pas reçu le code ?</p>
                            <button type="button" @click="resendOtp" :disabled="resendCooldown > 0"
                                class="text-soboa-orange hover:underline font-bold disabled:text-gray-400">
                                <span x-show="resendCooldown === 0">Renvoyer le code</span>
                                <span x-show="resendCooldown > 0">Réessayer dans <span
                                        x-text="resendCooldown"></span>s</span>
                            </button>
                        </div>
                        
                        <!-- Code oublié (pour les anciens utilisateurs) -->
                        <div x-show="hasExistingPassword" class="text-center">
                            <p class="text-sm text-gray-500 mb-2">Vous avez oublié votre mot de passe ?</p>
                            <button type="button" @click="requestNewCode"
                                class="text-soboa-orange hover:underline font-bold">
                                🔄 Recevoir un nouveau mot de passe par SMS
                            </button>
                        </div>

                        <!-- Changer de numéro -->
                        <div class="text-center mt-4 pt-4 border-t">
                            <button type="button" @click="step = 1; code = ''; error = ''; hasExistingPassword = false"
                                class="text-sm text-gray-500 hover:text-gray-700">
                                ← Modifier le numéro
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Conditions -->
                <p class="text-xs text-gray-500 text-center mt-6">
                    En vous connectant, vous acceptez nos
                    <a href="/conditions" class="text-soboa-blue hover:underline">conditions d'utilisation</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                step: 1,
                name: '',
                phone: '',
                countryCode: '+221', // Sénégal uniquement
                code: '',
                loading: false,
                error: '',
                captchaError: false,
                captchaNum1: 0,
                captchaNum2: 0,
                captchaOp: '+',
                captchaAnswer: '',
                captchaCorrectAnswer: 0,
                resendCooldown: 0,
                hasExistingPassword: false, // Indique si l'utilisateur a déjà un code permanent

                init() {
                    // Restaurer les données sauvegardées
                    const savedName = localStorage.getItem('user_name');
                    const savedPhone = localStorage.getItem('user_phone');
                    
                    if (savedName) {
                        this.name = savedName;
                    }
                    if (savedPhone) {
                        this.phone = savedPhone;
                    }
                    
                    this.generateCaptcha();
                },

                // Sauvegarder les données utilisateur
                saveUserData() {
                    localStorage.setItem('user_name', this.name);
                    localStorage.setItem('user_phone', this.phone);
                },

                generateCaptcha() {
                    this.captchaNum1 = Math.floor(Math.random() * 10) + 1;
                    this.captchaNum2 = Math.floor(Math.random() * 10) + 1;
                    this.captchaOp = '+';
                    this.captchaAnswer = '';
                    this.captchaCorrectAnswer = this.captchaNum1 + this.captchaNum2;
                },

                get fullPhone() {
                    return this.countryCode + this.formatPhoneNumber(this.phone);
                },

                getPlaceholder() {
                    // Côte d'Ivoire: 10 chiffres, Sénégal: 9 chiffres
                    return this.countryCode === '+225' ? '05 45 02 97 21' : '77 123 45 67';
                },

                formatPhoneNumber(phone) {
                    // Supprimer tout sauf les chiffres - ne rien retirer d'autre
                    // SMS: on garde le numéro tel quel (avec le 0 initial si présent)
                    return phone.replace(/\D/g, '');
                },

                // Valider le format du numéro selon le pays
                isValidPhone() {
                    const phoneDigits = this.formatPhoneNumber(this.phone);
                    
                    if (this.countryCode === '+221') {
                        // Sénégal: 9 chiffres commençant par 7
                        return phoneDigits.length === 9 && phoneDigits.startsWith('7');
                    } else if (this.countryCode === '+225') {
                        // Côte d'Ivoire: 10 chiffres commençant par 0
                        return phoneDigits.length === 10 && phoneDigits.startsWith('0');
                    }
                    return false;
                },

                getPhoneFormatError() {
                    if (this.countryCode === '+221') {
                        return 'Le numéro sénégalais doit contenir 9 chiffres commençant par 7 (ex: 77 123 45 67).';
                    } else if (this.countryCode === '+225') {
                        return 'Le numéro ivoirien doit contenir 10 chiffres commençant par 0 (ex: 07 XX XX XX XX).';
                    }
                    return 'Format de numéro invalide.';
                },

                async sendOtp() {
                    if (!this.name.trim() || !this.phone.trim()) {
                        this.error = 'Veuillez remplir tous les champs.';
                        return;
                    }

                    // Valider le format du numéro AVANT d'envoyer
                    if (!this.isValidPhone()) {
                        this.error = this.getPhoneFormatError();
                        return;
                    }

                    // Vérifier le CAPTCHA mathématique
                    if (!this.captchaAnswer || parseInt(this.captchaAnswer) !== this.captchaCorrectAnswer) {
                        this.captchaError = true;
                        this.error = '';
                        this.generateCaptcha();
                        return;
                    }
                    this.captchaError = false;

                    // Sauvegarder les données utilisateur pour le prochain login
                    this.saveUserData();

                    this.loading = true;
                    this.error = '';

                    console.log('=== ENVOI OTP ===');
                    console.log('Numéro original:', this.phone);
                    console.log('Code pays:', this.countryCode);
                    console.log('Numéro formaté:', this.fullPhone);

                    try {
                        const response = await fetch('/auth/send-otp', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name: this.name,
                                phone: this.fullPhone
                            })
                        });

                        const data = await response.json();

                        console.log('=== RÉPONSE SERVEUR ===');
                        console.log('Status HTTP:', response.status);
                        console.log('Data:', data);

                        if (data.success) {
                            this.step = 2;
                            // Vérifier si l'utilisateur a déjà un mot de passe permanent
                            this.hasExistingPassword = data.has_password === true;
                            
                            if (!this.hasExistingPassword) {
                                // Nouveau compte : démarrer le cooldown de renvoi
                                this.startResendCooldown();
                            }
                            this.generateCaptcha();
                            console.log('Passage à l\'étape 2, hasExistingPassword:', this.hasExistingPassword);
                        } else {
                            this.error = data.message || 'Erreur lors de l\'envoi du code.';
                            this.generateCaptcha();
                            if (data.error) {
                                console.error('Erreur SMS:', data.error);
                            }
                        }
                    } catch (err) {
                        console.error('Erreur réseau:', err);
                        this.error = 'Erreur de connexion. Veuillez réessayer.';
                        this.generateCaptcha();
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
                        const response = await fetch('/auth/verify-otp', {
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
                            // Sauvegarder le nom pour la prochaine fois
                            localStorage.setItem('user_name', this.name);

                            // Redirection après succès
                            window.location.href = data.redirect || '/matches';
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
                        const response = await fetch('/auth/send-otp', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name: this.name,
                                phone: this.fullPhone
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.startResendCooldown();
                            this.generateCaptcha();
                        } else {
                            this.error = data.message || 'Erreur lors du renvoi.';
                            this.generateCaptcha();
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
                },

                // Demander un nouveau code (pour les utilisateurs qui ont oublié leur code)
                async requestNewCode() {
                    if (!confirm('Vous allez recevoir un nouveau mot de passe par SMS. Ce nouveau mot de passe remplacera l\'ancien. Continuer ?')) {
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('/auth/request-new-code', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name: this.name,
                                phone: this.fullPhone
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.hasExistingPassword = false; // Maintenant c'est un nouveau code
                            this.startResendCooldown();
                            this.error = '';
                            alert('✅ Un nouveau mot de passe vous a été envoyé par SMS. Notez-le précieusement, il sera votre nouveau mot de passe !');
                        } else {
                            this.error = data.message || 'Erreur lors de l\'envoi du nouveau mot de passe.';
                        }
                    } catch (err) {
                        this.error = 'Erreur de connexion.';
                    } finally {
                        this.loading = false;
                    }
                },

                // Fonction pour demander un nouveau mot de passe depuis l'étape 1
                async forgotPassword() {
                    if (!this.name.trim() || !this.phone.trim()) {
                        this.error = 'Veuillez d\'abord remplir votre nom et numéro de téléphone.';
                        return;
                    }

                    // Valider le format du numéro
                    if (!this.isValidPhone()) {
                        this.error = this.getPhoneFormatError();
                        return;
                    }

                    if (!confirm('Vous avez oublié votre mot de passe ?\n\nUn nouveau mot de passe sera envoyé par SMS au ' + this.fullPhone + '.\n\nContinuer ?')) {
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('/auth/request-new-code', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name: this.name,
                                phone: this.fullPhone
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.step = 2;
                            this.hasExistingPassword = false;
                            this.startResendCooldown();
                            this.saveUserData();
                            alert('✅ Un nouveau mot de passe vous a été envoyé par SMS. Entrez-le ci-dessous.');
                        } else {
                            this.error = data.message || 'Erreur lors de l\'envoi du nouveau mot de passe.';
                        }
                    } catch (err) {
                        this.error = 'Erreur de connexion.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
</x-layouts.app>