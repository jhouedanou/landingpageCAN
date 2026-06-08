<x-layouts.app title="Admin - {{ isset($content) ? 'Modifier' : 'Créer' }} un contenu SOBOA FOOT">
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-3xl mx-auto px-4">

            <div class="flex items-center gap-3 mb-8">
                <a href="{{ route('admin.soboa-foot.index') }}" class="text-soboa-blue hover:underline font-bold text-sm">← Retour</a>
                <h1 class="text-2xl font-black text-soboa-blue">
                    {{ isset($content) ? 'Modifier' : 'Nouveau' }} contenu Animation SOBOA FOOT
                </h1>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ isset($content) ? route('admin.soboa-foot.update', $content) : route('admin.soboa-foot.store') }}"
                enctype="multipart/form-data"
                class="bg-white rounded-2xl shadow p-6 space-y-6"
            >
                @csrf
                @isset($content) @method('PUT') @endisset

                {{-- Titre + Type --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $content->title ?? '') }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-soboa-orange/40 focus:border-soboa-orange transition outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-soboa-orange/40 focus:border-soboa-orange transition outline-none bg-white">
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $content->type ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Corps --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Description / Corps</label>
                    <textarea name="body" rows="5" placeholder="Texte, description, annonce…"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-soboa-orange/40 focus:border-soboa-orange transition outline-none resize-none">{{ old('body', $content->body ?? '') }}</textarea>
                </div>

                {{-- Image --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Image</label>
                    @isset($content)
                        @if($content->image_url)
                            <img src="{{ $content->image_url }}" class="w-40 h-24 object-cover rounded-xl mb-2 border" alt="">
                        @endif
                    @endisset
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-soboa-blue/10 file:text-soboa-blue file:font-bold hover:file:bg-soboa-blue/20 transition">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — max 5 Mo</p>
                </div>

                {{-- Vidéo URL --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">URL Vidéo (YouTube, lien direct…)</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $content->video_url ?? '') }}"
                        placeholder="https://youtube.com/watch?v=..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-soboa-orange/40 focus:border-soboa-orange transition outline-none">
                </div>

                {{-- CTA --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Libellé bouton CTA</label>
                        <input type="text" name="cta_label" value="{{ old('cta_label', $content->cta_label ?? '') }}"
                            placeholder="En savoir plus"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-soboa-orange/40 focus:border-soboa-orange transition outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">URL CTA</label>
                        <input type="url" name="cta_url" value="{{ old('cta_url', $content->cta_url ?? '') }}"
                            placeholder="https://…"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-soboa-orange/40 focus:border-soboa-orange transition outline-none">
                    </div>
                </div>

                {{-- Ordre --}}
                <div class="w-40">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $content->sort_order ?? 0) }}" min="0"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-soboa-orange/40 focus:border-soboa-orange transition outline-none">
                </div>

                {{-- Boutons --}}
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-soboa-orange hover:bg-soboa-orange-secondary text-white font-black px-8 py-3 rounded-xl transition shadow">
                        {{ isset($content) ? 'Enregistrer' : 'Créer' }}
                    </button>
                    <a href="{{ route('admin.soboa-foot.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl transition">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
