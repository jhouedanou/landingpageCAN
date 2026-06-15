@php $letter = strtolower($s['scenario']); @endphp
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="px-6 py-5 {{ $letter === 'a' ? 'bg-gradient-to-r from-red-600 to-red-500' : 'bg-gradient-to-r from-soboa-blue to-blue-500' }} text-white">
        <h2 class="text-xl font-black">{{ $s['label'] }}</h2>
        <p class="text-white/90 text-sm mt-1">{{ $s['rule'] }}</p>
    </div>

    <div class="p-6">
        <!-- Totaux -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-soboa-blue">{{ $s['totals']['utilisateurs'] }}</p>
                <p class="text-xs text-gray-500">Utilisateurs classés</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-green-600">{{ $s['totals']['bonus_pos_retenus'] }}</p>
                <p class="text-xs text-gray-500">Bonus visite PDV retenus</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-red-500">{{ $s['totals']['bonus_pos_ecartes'] }}</p>
                <p class="text-xs text-gray-500">Bonus visite PDV écartés</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-soboa-orange">{{ $s['totals']['points_pos_total'] }}</p>
                <p class="text-xs text-gray-500">Points visite PDV distribués</p>
            </div>
        </div>

        <!-- Boutons export -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="{{ route('admin.ranking-scenarios-export', ['scenario' => $letter, 'format' => 'csv', 'include_staff' => $includeStaff ? 1 : null]) }}"
               class="bg-green-600 hover:bg-green-700 text-white font-bold px-5 py-2.5 rounded-xl shadow flex items-center gap-2">
                <span>⬇️</span> Télécharger CSV
            </a>
            <a href="{{ route('admin.ranking-scenarios-export', ['scenario' => $letter, 'format' => 'html', 'include_staff' => $includeStaff ? 1 : null]) }}"
               target="_blank" rel="noopener"
               class="bg-soboa-blue hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow flex items-center gap-2">
                <span>🖨️</span> Ouvrir / Imprimer en PDF
            </a>
        </div>

        <!-- Aperçu top 20 -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="border px-3 py-2 text-left">Rang</th>
                        <th class="border px-3 py-2 text-left">Nom</th>
                        <th class="border px-3 py-2 text-right">Points pronostic</th>
                        <th class="border px-3 py-2 text-right">Points connexion</th>
                        <th class="border px-3 py-2 text-right">Points visite PDV</th>
                        <th class="border px-3 py-2 text-right">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(array_slice($s['rows'], 0, 20) as $row)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="border px-3 py-2">{{ $row['rang'] }}</td>
                            <td class="border px-3 py-2">{{ $row['name'] }}</td>
                            <td class="border px-3 py-2 text-right">{{ $row['points_pronostics'] }}</td>
                            <td class="border px-3 py-2 text-right">{{ $row['points_connexion'] }}</td>
                            <td class="border px-3 py-2 text-right">{{ $row['points_pos'] }}</td>
                            <td class="border px-3 py-2 text-right font-bold">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="border px-3 py-6 text-center text-gray-400">Aucun utilisateur classé.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if(count($s['rows']) > 20)
                <p class="text-xs text-gray-400 mt-2">Aperçu des 20 premiers — l'export contient les {{ count($s['rows']) }} utilisateurs.</p>
            @endif
        </div>
    </div>
</div>
