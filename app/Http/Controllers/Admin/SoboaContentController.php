<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoboaContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoboaContentController extends Controller
{
    public function index()
    {
        $contents = SoboaContent::orderBy('sort_order')->orderByDesc('created_at')->paginate(20);
        return view('admin.soboa-foot.index', compact('contents'));
    }

    public function create()
    {
        $types = SoboaContent::$types;
        return view('admin.soboa-foot.form', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'nullable|string',
            'image'      => 'nullable|image|max:5120',
            'video_url'  => 'nullable|url|max:500',
            'cta_label'  => 'nullable|string|max:100',
            'cta_url'    => 'nullable|url|max:500',
            'type'       => 'required|in:annonce,evenement,activation,promo,galerie',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('soboa-foot', 'public');
        }

        $data['is_published'] = false;
        $data['sort_order']   = $data['sort_order'] ?? 0;

        SoboaContent::create($data);

        return redirect()->route('admin.soboa-foot.index')->with('success', 'Contenu créé.');
    }

    public function edit(SoboaContent $content)
    {
        $types = SoboaContent::$types;
        return view('admin.soboa-foot.form', compact('content', 'types'));
    }

    public function update(Request $request, SoboaContent $content)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'nullable|string',
            'image'      => 'nullable|image|max:5120',
            'video_url'  => 'nullable|url|max:500',
            'cta_label'  => 'nullable|string|max:100',
            'cta_url'    => 'nullable|url|max:500',
            'type'       => 'required|in:annonce,evenement,activation,promo,galerie',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($content->image_path) {
                Storage::disk('public')->delete($content->image_path);
            }
            $data['image_path'] = $request->file('image')->store('soboa-foot', 'public');
        }

        $content->update($data);

        return redirect()->route('admin.soboa-foot.index')->with('success', 'Contenu mis à jour.');
    }

    public function destroy(SoboaContent $content)
    {
        if ($content->image_path) {
            Storage::disk('public')->delete($content->image_path);
        }
        $content->delete();

        return back()->with('success', 'Contenu supprimé.');
    }

    public function toggle(SoboaContent $content)
    {
        $content->update([
            'is_published' => !$content->is_published,
            'published_at' => $content->is_published ? null : now(),
        ]);

        return back()->with('success', $content->is_published ? 'Publié.' : 'Dépublié.');
    }
}
