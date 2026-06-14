<x-layouts.app title="Vérification réclamation">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-3xl mx-auto px-4">

            <div class="mb-8">
                <a href="{{ route('admin.dashboard') }}" class="text-soboa-orange hover:underline font-bold mb-2 inline-block">
                    ← Retour au tableau de bord
                </a>
                <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3">
                    <span class="text-4xl">🔎</span> Vérification réclamation
                </h1>
                <p class="text-gray-600 mt-2">
                    Recherchez un utilisateur par son nom ou son numéro de téléphone pour consulter
                    la répartition détaillée de ses points et générer une fiche imprimable.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <label for="claimSearch" class="block text-sm font-bold text-gray-700 mb-2">
                    Nom ou numéro de téléphone
                </label>
                <div class="relative">
                    <input type="text" id="claimSearch" autocomplete="off"
                        placeholder="Ex : Awa Diop ou 771234567"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-soboa-orange focus:border-soboa-orange outline-none">

                    <div id="claimSuggestions"
                        class="absolute z-20 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl hidden max-h-80 overflow-y-auto">
                    </div>
                </div>
                <p id="claimHint" class="text-xs text-gray-400 mt-2">Saisissez au moins 2 caractères.</p>
            </div>

        </div>
    </div>

    <script>
        (function () {
            const input = document.getElementById('claimSearch');
            const box = document.getElementById('claimSuggestions');
            const hint = document.getElementById('claimHint');
            let timer = null;
            let controller = null;

            function hide() { box.classList.add('hidden'); box.innerHTML = ''; }

            function render(users) {
                if (!users.length) {
                    box.innerHTML = '<div class="px-4 py-3 text-sm text-gray-400">Aucun utilisateur trouvé.</div>';
                    box.classList.remove('hidden');
                    return;
                }
                box.innerHTML = users.map(u => `
                    <a href="/admin/claim-verification/${u.id}"
                       class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                        <span>
                            <span class="block text-sm font-semibold text-gray-900">${escapeHtml(u.name ?? '—')}</span>
                            <span class="block text-xs text-gray-500">${escapeHtml(u.phone ?? '')}</span>
                        </span>
                        <span class="text-xs font-bold text-soboa-orange whitespace-nowrap">${u.points_total} pts</span>
                    </a>
                `).join('');
                box.classList.remove('hidden');
            }

            function escapeHtml(s) {
                return String(s).replace(/[&<>"']/g, c => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[c]));
            }

            input.addEventListener('input', function () {
                const q = input.value.trim();
                clearTimeout(timer);
                if (q.length < 2) { hide(); hint.textContent = 'Saisissez au moins 2 caractères.'; return; }
                hint.textContent = 'Recherche…';
                timer = setTimeout(async () => {
                    if (controller) controller.abort();
                    controller = new AbortController();
                    try {
                        const res = await fetch(`/admin/claim-verification/search?q=${encodeURIComponent(q)}`, {
                            headers: { 'Accept': 'application/json' },
                            signal: controller.signal,
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const data = await res.json();
                        render(data);
                        hint.textContent = `${data.length} résultat(s).`;
                    } catch (e) {
                        if (e.name !== 'AbortError') { hide(); hint.textContent = 'Erreur de recherche.'; }
                    }
                }, 250);
            });

            document.addEventListener('click', function (e) {
                if (!box.contains(e.target) && e.target !== input) hide();
            });
        })();
    </script>
</x-layouts.app>
