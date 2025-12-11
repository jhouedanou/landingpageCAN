<x-layouts.app title="Connexion">
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-soboa-orange to-orange-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-4xl">⚽</span>
                    </div>
                    <h1 class="text-3xl font-black text-soboa-blue">Bienvenue</h1>
                    <p class="text-gray-600 mt-2">Connectez-vous pour faire vos pronostics CAN 2025</p>
                </div>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <div id="alert-container"></div>

                <!-- Step 1: Phone Number with Country Selector -->
                <div id="step-phone">
                    <form id="phone-form" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Votre nom complet
                            </label>
                            <input type="text" id="name" name="name" placeholder="Jean Kouassi"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-soboa-orange focus:border-soboa-orange transition-colors font-medium"
                                required>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Numéro de téléphone
                            </label>

                            <!-- Country Selector -->
                            <div class="flex gap-2 mb-2">
                                <select id="country-code"
                                    class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-soboa-orange focus:border-soboa-orange bg-white font-semibold">
                                    <option value="+225">🇨🇮 Côte d'Ivoire (+225)</option>
                                    <option value="+221">🇸🇳 Sénégal (+221)</option>
                                </select>
                            </div>

                            <input type="tel" id="phone" name="phone" placeholder="07 XX XX XX XX"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-soboa-orange focus:border-soboa-orange transition-colors font-medium"
                                required>
                            <p class="text-xs text-gray-500 mt-2">Un code de vérification vous sera envoyé par SMS</p>
                        </div>

                        <!-- reCAPTCHA container -->
                        <div id="recaptcha-container"></div>

                        <button type="submit" id="send-otp-btn"
                            class="w-full bg-soboa-blue hover:bg-soboa-blue-dark text-white font-bold py-4 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span id="send-btn-text">Recevoir le code</span>
                        </button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step-otp" style="display: none;">
                    <div class="text-center mb-6">
                        <div
                            class="w-16 h-16 bg-soboa-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-soboa-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-gray-600">Entrez le code reçu par SMS</p>
                        <p class="text-sm text-soboa-orange font-bold mt-1" id="phone-display"></p>
                    </div>

                    <form id="otp-form" class="space-y-6">
                        <div>
                            <input type="text" id="otp-code" placeholder="000000" maxlength="6" pattern="[0-9]{6}"
                                inputmode="numeric"
                                class="w-full px-4 py-4 text-center text-3xl font-black tracking-[0.5em] border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-soboa-orange focus:border-soboa-orange"
                                required>
                        </div>

                        <button type="submit" id="verify-btn"
                            class="w-full bg-soboa-orange hover:bg-soboa-orange-dark text-white font-bold py-4 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span id="verify-btn-text">Vérifier</span>
                        </button>
                    </form>

                    <div class="mt-4 text-center">
                        <button type="button" id="back-btn"
                            class="text-soboa-blue hover:underline text-sm font-semibold">
                            ← Modifier le numéro
                        </button>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600">
                        En vous connectant, vous acceptez nos
                        <a href="#" class="text-soboa-orange hover:underline font-semibold">conditions d'utilisation</a>
                    </p>
                </div>
            </div>

            <!-- Points Info -->
            <div class="mt-6 bg-soboa-blue rounded-xl p-4 text-white text-center">
                <p class="font-bold">🎮 Gagnez des points à chaque match!</p>
                <p class="text-sm text-white/80 mt-1">+1 pronostic • +3 bon vainqueur • +3 score exact</p>
            </div>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>

    <script>
        // Firebase Configuration
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.project_id') }}.firebaseapp.com",
            projectId: "{{ config('services.firebase.project_id') }}",
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        let confirmationResult = null;
        let recaptchaVerifier = null;
        let userName = '';
        let userPhone = '';

        // Initialize reCAPTCHA
        function initRecaptcha() {
            recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible',
                'callback': (response) => {
                    // reCAPTCHA solved
                }
            });
        }

        // Show alert
        function showAlert(message, type = 'error') {
            const container = document.getElementById('alert-container');
            const bgColor = type === 'error'
                ? 'bg-red-100 border-red-400 text-red-700'
                : 'bg-green-100 border-green-400 text-green-700';
            container.innerHTML = `<div class="${bgColor} px-4 py-3 rounded-lg mb-6 border" role="alert"><span class="font-medium">${message}</span></div>`;
            setTimeout(() => container.innerHTML = '', 5000);
        }

        // Format phone number with selected country code
        function formatPhone(phone) {
            const countryCode = document.getElementById('country-code').value;
            // Remove all non-digits
            phone = phone.replace(/\D/g, '');
            // Remove leading 0 if present
            if (phone.startsWith('0')) {
                phone = phone.substring(1);
            }
            // Return with country code
            return countryCode + phone;
        }

        // Send OTP
        document.getElementById('phone-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            userName = document.getElementById('name').value.trim();
            const phoneInput = document.getElementById('phone').value.trim();

            if (!userName) {
                showAlert('Veuillez entrer votre nom');
                return;
            }

            // Remove all non-digits for validation
            const digitsOnly = phoneInput.replace(/\D/g, '');

            // Côte d'Ivoire: 10 digits, Senegal: 9 digits (without leading 0)
            const countryCode = document.getElementById('country-code').value;
            const minLength = countryCode === '+225' ? 10 : 9;

            if (digitsOnly.length < minLength) {
                showAlert(`Numéro trop court. Entrez ${minLength} chiffres minimum (ex: ${countryCode === '+225' ? '07 XX XX XX XX' : '77 XXX XX XX'})`);
                return;
            }

            userPhone = formatPhone(phoneInput);

            // Validate E.164 format
            if (userPhone.length < 12) {
                showAlert('Numéro de téléphone invalide. Vérifiez le format.');
                return;
            }

            const btn = document.getElementById('send-otp-btn');
            const btnText = document.getElementById('send-btn-text');
            btn.disabled = true;
            btnText.textContent = 'Envoi en cours...';

            try {
                if (!recaptchaVerifier) {
                    initRecaptcha();
                }

                confirmationResult = await firebase.auth().signInWithPhoneNumber(userPhone, recaptchaVerifier);

                // Switch to OTP step
                document.getElementById('step-phone').style.display = 'none';
                document.getElementById('step-otp').style.display = 'block';
                document.getElementById('phone-display').textContent = userPhone;
                document.getElementById('otp-code').focus();

            } catch (error) {
                console.error('Firebase Error:', error);
                let errorMessage = 'Erreur lors de l\'envoi du code.';

                if (error.code === 'auth/invalid-phone-number' || error.message.includes('TOO_SHORT')) {
                    errorMessage = 'Numéro de téléphone invalide. Vérifiez le format (ex: 77 123 45 67).';
                } else if (error.code === 'auth/too-many-requests') {
                    errorMessage = 'Trop de tentatives. Réessayez dans quelques minutes.';
                } else if (error.code === 'auth/quota-exceeded') {
                    errorMessage = 'Quota SMS dépassé. Contactez l\'administrateur.';
                } else if (error.message.includes('reCAPTCHA')) {
                    errorMessage = 'Erreur de vérification. Rechargez la page et réessayez.';
                }

                showAlert(errorMessage);

                // Reset reCAPTCHA
                if (recaptchaVerifier) {
                    recaptchaVerifier.clear();
                    recaptchaVerifier = null;
                }
            } finally {
                btn.disabled = false;
                btnText.textContent = 'Recevoir le code';
            }
        });

        // Verify OTP
        document.getElementById('otp-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const code = document.getElementById('otp-code').value.trim();

            if (code.length !== 6) {
                showAlert('Le code doit contenir 6 chiffres');
                return;
            }

            const btn = document.getElementById('verify-btn');
            const btnText = document.getElementById('verify-btn-text');
            btn.disabled = true;
            btnText.textContent = 'Vérification...';

            try {
                const result = await confirmationResult.confirm(code);
                const user = result.user;
                const idToken = await user.getIdToken();

                // Send to Laravel backend
                const response = await fetch('/auth/firebase-callback', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        firebase_token: idToken,
                        phone: userPhone,
                        name: userName,
                        firebase_uid: user.uid
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect || '/matches';
                } else {
                    showAlert(data.message || 'Erreur lors de la connexion');
                }

            } catch (error) {
                console.error('Verification Error:', error);
                let errorMessage = 'Code incorrect ou expiré.';

                if (error.code === 'auth/invalid-verification-code') {
                    errorMessage = 'Code de vérification incorrect.';
                } else if (error.code === 'auth/code-expired') {
                    errorMessage = 'Le code a expiré. Veuillez en demander un nouveau.';
                }

                showAlert(errorMessage);
            } finally {
                btn.disabled = false;
                btnText.textContent = 'Vérifier';
            }
        });

        // Back button
        document.getElementById('back-btn').addEventListener('click', () => {
            document.getElementById('step-otp').style.display = 'none';
            document.getElementById('step-phone').style.display = 'block';
            document.getElementById('otp-code').value = '';

            // Reset reCAPTCHA
            if (recaptchaVerifier) {
                recaptchaVerifier.clear();
                recaptchaVerifier = null;
            }
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            initRecaptcha();
        });
    </script>
</x-layouts.app>