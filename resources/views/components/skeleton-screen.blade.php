@props(['type' => 'cards', 'cards' => 6])

@php $skelId = 'page-skeleton-' . \Illuminate\Support\Str::random(6); @endphp

<div id="{{ $skelId }}" class="page-skeleton" aria-hidden="true">
    <div class="max-w-5xl mx-auto px-4 pt-24 pb-10 space-y-6">

        {{-- Bandeau / hero --}}
        <div class="skeleton h-28 w-full rounded-2xl"></div>

        @if($type === 'list')
            {{-- Lignes (classement) --}}
            <div class="space-y-3">
                @for($i = 0; $i < $cards; $i++)
                    <div class="flex items-center gap-4 bg-white rounded-xl p-4 ring-1 ring-gray-100">
                        <div class="skeleton skeleton-circle w-10 h-10 flex-shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="skeleton skeleton-text w-1/3"></div>
                            <div class="skeleton skeleton-text w-1/4"></div>
                        </div>
                        <div class="skeleton h-7 w-14 rounded-lg flex-shrink-0"></div>
                    </div>
                @endfor
            </div>
        @else
            {{-- Onglets --}}
            <div class="flex gap-2">
                @for($i = 0; $i < 4; $i++)
                    <div class="skeleton h-9 w-24 rounded-xl"></div>
                @endfor
            </div>
            {{-- Grille de cartes match --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @for($i = 0; $i < $cards; $i++)
                    <div class="bg-white rounded-2xl ring-1 ring-gray-100 overflow-hidden">
                        <div class="skeleton h-9 w-full rounded-none"></div>
                        <div class="p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col items-center gap-2 flex-1">
                                    <div class="skeleton skeleton-circle w-14 h-14"></div>
                                    <div class="skeleton skeleton-text w-16"></div>
                                </div>
                                <div class="skeleton skeleton-text w-8"></div>
                                <div class="flex flex-col items-center gap-2 flex-1">
                                    <div class="skeleton skeleton-circle w-14 h-14"></div>
                                    <div class="skeleton skeleton-text w-16"></div>
                                </div>
                            </div>
                            <div class="skeleton h-6 w-full rounded-md"></div>
                            <div class="skeleton h-11 w-full rounded-xl"></div>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        var el = document.getElementById('{{ $skelId }}');
        if (!el) return;
        var done = false;
        var hide = function () {
            if (done) return;
            done = true;
            el.classList.add('is-hidden');
            setTimeout(function () { el.remove(); }, 400);
        };
        if (document.readyState === 'complete') {
            setTimeout(hide, 150);
        } else {
            window.addEventListener('load', hide, { once: true });
        }
        // Filet de sécurité : ne jamais bloquer plus de 2,5 s
        setTimeout(hide, 2500);
    })();
</script>
