<x-layouts.app title="Mot de passe oublié">
    <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">

            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-white rounded-full mx-auto mb-4 shadow-lg flex items-center justify-center">
                    <img src="/images/logoGazelle.jpeg" alt="SOBOA" class="w-16 h-16 object-contain">
                </div>
                <h1 class="text-3xl font-black text-soboa-blue">Mot de passe oublié</h1>
                <p class="text-gray-600 mt-2">Récupérez votre accès</p>
            </div>

            <!-- Formulaire -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8" x-data="forgotPasswordForm()">

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

                <!-- Formulaire de demande (étape 1) -->
                <div x-show="!newPassword">
                    <form @submit.prevent="resetPassword">
                        <!-- Numéro de téléphone -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Numéro de téléphone</label>
                            <div class="flex gap-2">
                                <div class="px-4 py-3 border-2 border-gray-200 bg-gray-50 rounded-xl text-sm font-bold text-gray-700 flex items-center">
                                    🇸🇳 +221
                                </div>
                                <input type="tel" x-model="phone"
                                    placeholder="77 123 45 67"
                                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-soboa-orange focus:ring-0 text-lg"
                                    required autofocus>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Entrez le numéro associé à votre compte
                            </p>
                        </div>

                        <!-- Bouton -->
                        <button type="submit" :disabled="loading"
                            class="w-full bg-soboa-orange hover:bg-soboa-orange-dark disabled:bg-gray-400 text-black font-bold py-4 px-6 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 mb-4">
                            <span x-show="!loading">Récupérer mon mot de passe</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Traitement...
                            </span>
                        </button>
                    </form>

                    <!-- Retour connexion -->
                    <div class="text-center pt-4 border-t border-gray-100">
                        <a href="/login" class="text-soboa-blue hover:underline font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour à la connexion
                        </a>
                    </div>
                </div>

                <!-- Résultat avec nouveau mot de passe (étape 2) -->
                <div x-show="newPassword" x-cloak>
                    <!-- Succès -->
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium">Nouveau mot de passe généré !</span>
                        </div>
                    </div>

                    <!-- Carte avec les infos -->
                    <div id="password-card" class="bg-gradient-to-br from-soboa-blue to-blue-800 rounded-2xl p-6 text-white mb-6">
                        <div class="text-center mb-4">
                            <img src="/images/logoGazelle.jpeg" alt="SOBOA" class="w-16 h-16 object-contain mx-auto rounded-full bg-white p-2 mb-3">
                            <h3 class="text-lg font-bold">SOBOA Foot Time</h3>
                            <p class="text-blue-200 text-sm">Vos identifiants</p>
                        </div>

                        <div class="space-y-4">
                            <!-- Nom -->
                            <div class="bg-white/10 rounded-xl p-3">
                                <p class="text-blue-200 text-xs mb-1">Nom</p>
                                <p class="font-bold text-lg" x-text="userName"></p>
                            </div>

                            <!-- Téléphone -->
                            <div class="bg-white/10 rounded-xl p-3">
                                <p class="text-blue-200 text-xs mb-1">Numéro de téléphone</p>
                                <p class="font-bold text-lg" x-text="userPhone"></p>
                            </div>

                            <!-- Mot de passe -->
                            <div class="bg-yellow-400 text-black rounded-xl p-4">
                                <p class="text-yellow-800 text-xs mb-1 font-medium">Nouveau mot de passe</p>
                                <p class="font-black text-2xl tracking-widest text-center" x-text="newPassword"></p>
                            </div>
                        </div>

                        <p class="text-center text-blue-200 text-xs mt-4">
                            Généré le {{ now()->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    <!-- Boutons de téléchargement -->
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <button @click="downloadAsImage()"
                            class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Image
                        </button>
                        <button @click="downloadAsPDF()"
                            class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            PDF
                        </button>
                    </div>

                    <!-- Avertissement -->
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl mb-6 text-sm">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p><strong>Important :</strong> Notez ou téléchargez ce mot de passe. Il ne sera plus affiché après fermeture de cette page.</p>
                        </div>
                    </div>

                    <!-- Bouton connexion -->
                    <a href="/login"
                        class="w-full bg-soboa-orange hover:bg-soboa-orange-dark text-black font-bold py-4 px-6 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Se connecter maintenant
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- html2canvas pour capture d'image -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- jsPDF pour génération PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        function forgotPasswordForm() {
            return {
                phone: '',
                loading: false,
                error: '',
                newPassword: '',
                userName: '',
                userPhone: '',
                countryCode: '+221',

                init() {
                    // Restaurer le numéro si sauvegardé
                    const savedPhone = localStorage.getItem('user_phone');
                    if (savedPhone) {
                        this.phone = savedPhone;
                    }
                },

                get fullPhone() {
                    return this.countryCode + this.formatPhoneNumber(this.phone);
                },

                formatPhoneNumber(phone) {
                    return phone.replace(/\D/g, '');
                },

                isValidPhone() {
                    const phoneDigits = this.formatPhoneNumber(this.phone);
                    return phoneDigits.length === 9 && phoneDigits.startsWith('7');
                },

                async resetPassword() {
                    if (!this.phone.trim()) {
                        this.error = 'Veuillez entrer votre numéro de téléphone.';
                        return;
                    }

                    if (!this.isValidPhone()) {
                        this.error = 'Le numéro doit contenir 9 chiffres commençant par 7 (ex: 77 123 45 67).';
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('/auth/reset-password', {
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

                        if (response.ok && data.success) {
                            this.newPassword = data.new_password;
                            this.userName = data.user_name;
                            this.userPhone = data.phone;
                        } else {
                            this.error = data.message || 'Une erreur est survenue.';
                        }
                    } catch (err) {
                        console.error('Erreur:', err);
                        this.error = 'Erreur de connexion. Veuillez réessayer.';
                    } finally {
                        this.loading = false;
                    }
                },

                async downloadAsImage() {
                    const card = document.getElementById('password-card');
                    try {
                        const canvas = await html2canvas(card, {
                            backgroundColor: null,
                            scale: 2
                        });
                        const link = document.createElement('a');
                        link.download = 'soboa-mot-de-passe.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                    } catch (err) {
                        console.error('Erreur capture:', err);
                        alert('Erreur lors de la génération de l\'image.');
                    }
                },

                async downloadAsPDF() {
                    const card = document.getElementById('password-card');
                    try {
                        const canvas = await html2canvas(card, {
                            backgroundColor: null,
                            scale: 2
                        });
                        
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF('p', 'mm', 'a4');
                        
                        // Dimensions
                        const imgWidth = 180;
                        const imgHeight = (canvas.height * imgWidth) / canvas.width;
                        const x = (210 - imgWidth) / 2; // Centrer horizontalement (A4 = 210mm)
                        const y = 20;
                        
                        // Titre
                        pdf.setFontSize(18);
                        pdf.setTextColor(0, 51, 153);
                        pdf.text('SOBOA Foot Time - Identifiants', 105, 15, { align: 'center' });
                        
                        // Image de la carte
                        pdf.addImage(canvas.toDataURL('image/png'), 'PNG', x, y, imgWidth, imgHeight);
                        
                        // Note en bas
                        pdf.setFontSize(10);
                        pdf.setTextColor(100, 100, 100);
                        pdf.text('Conservez ce document en lieu sûr.', 105, y + imgHeight + 15, { align: 'center' });
                        
                        pdf.save('soboa-mot-de-passe.pdf');
                    } catch (err) {
                        console.error('Erreur PDF:', err);
                        alert('Erreur lors de la génération du PDF.');
                    }
                }
            };
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</x-layouts.app>
