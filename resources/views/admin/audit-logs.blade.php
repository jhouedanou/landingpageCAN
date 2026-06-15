<x-layouts.app title="Admin - Journal d'actions">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-soboa-blue hover:underline">&larr; Retour au dashboard</a>
                <h1 class="text-3xl font-black text-soboa-blue flex items-center gap-3 mt-2">
                    <span class="text-4xl">📒</span> Journal d'actions admin
                </h1>
                <p class="text-gray-600 mt-2">
                    Trace immuable des actions sensibles : qui a fait quoi, quand. Preuve en cas de contestation.
                </p>
            </div>

            <!-- Filtres -->
            <form method="GET" class="bg-white rounded-xl shadow p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Action</label>
                    <select name="action" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Toutes</option>
                        @foreach($actions as $a)
                            <option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Admin ID</label>
                    <input type="number" name="admin_id" value="{{ request('admin_id') }}" placeholder="ID"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Du</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Au</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div class="sm:col-span-2 lg:col-span-4 flex gap-2">
                    <button type="submit" class="bg-soboa-blue text-white text-sm font-bold px-5 py-2 rounded-lg hover:opacity-90">Filtrer</button>
                    <a href="{{ route('admin.audit-logs') }}" class="text-sm text-gray-500 px-4 py-2 hover:underline">Réinitialiser</a>
                </div>
            </form>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="border px-3 py-2 text-left">Date</th>
                                <th class="border px-3 py-2 text-left">Admin</th>
                                <th class="border px-3 py-2 text-left">Action</th>
                                <th class="border px-3 py-2 text-left">Cible</th>
                                <th class="border px-3 py-2 text-left">Description</th>
                                <th class="border px-3 py-2 text-left">Détails</th>
                                <th class="border px-3 py-2 text-left">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }} align-top">
                                    <td class="border px-3 py-2 whitespace-nowrap text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="border px-3 py-2 font-medium">
                                        {{ $log->admin_name ?? '—' }}
                                        @if($log->admin_id)
                                            <span class="text-gray-400 text-xs">#{{ $log->admin_id }}</span>
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">
                                        <span class="text-[10px] font-mono font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $log->action }}</span>
                                    </td>
                                    <td class="border px-3 py-2 text-gray-500">
                                        @if($log->target_type)
                                            {{ $log->target_type }}#{{ $log->target_id }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">{{ $log->description }}</td>
                                    <td class="border px-3 py-2 text-gray-500 text-xs">
                                        @if($log->meta)
                                            <code class="break-all">{{ json_encode($log->meta, JSON_UNESCAPED_UNICODE) }}</code>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2 font-mono text-xs text-gray-400">{{ $log->ip_address ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="border px-3 py-8 text-center text-gray-400">Aucune action enregistrée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-layouts.app>
