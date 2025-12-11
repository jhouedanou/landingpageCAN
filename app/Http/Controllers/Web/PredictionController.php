<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function store(Request $request)
    {
        // Vérifier que l'utilisateur est connecté
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour faire un pronostic.');
        }

        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'score_a' => 'required|integer|min:0|max:20',
            'score_b' => 'required|integer|min:0|max:20',
        ]);

        $match = MatchGame::findOrFail($request->match_id);
        $userId = session('user_id');

        // Vérifier si l'utilisateur a déjà pronostiqué sur ce match
        $existingPrediction = Prediction::where('user_id', $userId)
            ->where('match_id', $request->match_id)
            ->first();

        if ($existingPrediction) {
            return back()->with('error', 'Vous avez déjà fait un pronostic sur ce match. Un seul pronostic par match est autorisé.');
        }

        // Vérifier que le match n'a pas encore commencé
        if ($match->status === 'finished') {
            return back()->with('error', 'Ce match est déjà terminé.');
        }

        if ($match->status === 'live') {
            return back()->with('error', 'Ce match est en cours. Les pronostics sont fermés.');
        }

        // Lock predictions 1 hour before match starts
        $lockTime = $match->match_date->copy()->subHour();
        if (now()->gte($lockTime)) {
            return back()->with('error', 'Les pronostics sont fermés 1 heure avant le match.');
        }

        // Déterminer le gagnant prédit
        $predictedWinner = 'draw';
        if ($request->score_a > $request->score_b) {
            $predictedWinner = 'team_a';
        } elseif ($request->score_b > $request->score_a) {
            $predictedWinner = 'team_b';
        }

        // Créer le pronostic (pas de mise à jour possible)
        Prediction::create([
            'user_id' => $userId,
            'match_id' => $request->match_id,
            'predicted_winner' => $predictedWinner,
            'score_a' => $request->score_a,
            'score_b' => $request->score_b,
        ]);

        return back()->with('success', 'Votre pronostic a été enregistré !');
    }

    public function myPredictions()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

        $userId = session('user_id');
        $user = User::find($userId);

        $predictions = Prediction::with('match')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('predictions', compact('predictions', 'user'));
    }
}
