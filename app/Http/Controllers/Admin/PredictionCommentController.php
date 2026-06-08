<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PredictionComment;

class PredictionCommentController extends Controller
{
    public function index()
    {
        $comments = PredictionComment::with(['user', 'prediction.match'])
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.prediction-comments.index', compact('comments'));
    }

    public function destroy(PredictionComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Commentaire supprimé.');
    }

    public function moderate(PredictionComment $comment)
    {
        $comment->update(['is_moderated' => !$comment->is_moderated]);
        return back()->with('success', $comment->is_moderated ? 'Commentaire masqué.' : 'Commentaire réactivé.');
    }
}
