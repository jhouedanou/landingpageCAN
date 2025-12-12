<x-layouts.app title="Admin - Points de Vente">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3">
                        <span class="text-4xl">📍</span> Points de Vente
                    </h1>
                    <p class="text-gray-600 mt-2">Gérez les points de vente partenaires</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition">
                        ← Retour
                    </a>
                    <a href="{{ route('admin.create-bar') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center gap-2">
                        <span>+</span> Nouveau Point de Vente
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 shadow">
                    <p class="text-2xl font-black text-soboa-blue">{{ $bars->total() }}</p>
                    <p class="text-gray-500 text-sm">Total</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow">
                    <p class="text-2xl font-black text-green-600">{{ $bars->where('is_active', true)->count() }}</p>
                    <p class="text-gray-500 text-sm">Actifs</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow">
                    <p class="text-2xl font-black text-red-600">{{ $bars->where('is_active', false)->count() }}</p>
                    <p class="text-gray-500 text-sm">Inactifs</p>
                </div>
            </div>

            <!-- Liste -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left p-4 font-bold text-gray-700">Nom</th>
                            <th class="text-left p-4 font-bold text-gray-700">Adresse</th>
                            <th class="text-left p-4 font-bold text-gray-700">Coordonnées</th>
                            <th class="text-center p-4 font-bold text-gray-700">Statut</th>
                            <th class="text-center p-4 font-bold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bars as $bar)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-soboa-blue/10 rounded-full flex items-center justify-center">
                                        <span class="text-xl">📍</span>
                                    </div>
                                    <span class="font-bold text-gray-800">{{ $bar->name }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600 max-w-xs truncate">{{ $bar->address }}</td>
                            <td class="p-4 text-gray-500 text-sm font-mono">
                                {{ number_format($bar->latitude, 6) }}, {{ number_format($bar->longitude, 6) }}
                            </td>
                            <td class="p-4 text-center">
                                @if($bar->is_active)
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Actif</span>
                                @else
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">Inactif</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('admin.toggle-bar', $bar->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-{{ $bar->is_active ? 'red' : 'green' }}-600 hover:underline text-sm font-bold">
                                            {{ $bar->is_active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('admin.edit-bar', $bar->id) }}" class="text-soboa-orange hover:underline text-sm font-bold">
                                        Modifier
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('admin.delete-bar', $bar->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce point de vente ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm font-bold">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                Aucun point de vente trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $bars->links() }}
            </div>

        </div>
    </div>
</x-layouts.app>
