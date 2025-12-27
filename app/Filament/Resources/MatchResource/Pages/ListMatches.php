<?php

namespace App\Filament\Resources\MatchResource\Pages;

use App\Filament\Resources\MatchResource;
use App\Models\MatchGame;
use App\Models\Team;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class ListMatches extends ListRecords
{
    protected static string $resource = MatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importMatches')
                ->label('Importer des matchs (JSON)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    Textarea::make('json_data')
                        ->label('Données JSON des matchs')
                        ->helperText('Collez le JSON des matchs. Format attendu: {"matchs_termines": [{"date": "2025-12-21", "groupe": "A", "equipe_1": "Maroc", "score_1": 2, "equipe_2": "Comores", "score_2": 0}, ...]}')
                        ->required()
                        ->rows(15)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $this->importMatchesFromJson($data['json_data']);
                }),
        ];
    }

    protected function importMatchesFromJson(string $jsonData): void
    {
        try {
            $data = json_decode($jsonData, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Notification::make()
                    ->title('Erreur de format JSON')
                    ->body('Le JSON fourni est invalide: ' . json_last_error_msg())
                    ->danger()
                    ->send();
                return;
            }

            $matchs = $data['matchs_termines'] ?? $data['matchs'] ?? $data;
            
            if (!is_array($matchs)) {
                Notification::make()
                    ->title('Format incorrect')
                    ->body('Le JSON doit contenir un tableau de matchs.')
                    ->danger()
                    ->send();
                return;
            }

            $imported = 0;
            $errors = [];
            $skipped = 0;

            // Mapping des noms d'équipes vers les variantes possibles
            $teamNameMapping = [
                'Maroc' => ['Maroc', 'Morocco'],
                'Comores' => ['Comores', 'Comoros'],
                'Mali' => ['Mali'],
                'Zambie' => ['Zambie', 'Zambia'],
                'Égypte' => ['Égypte', 'Egypte', 'Egypt'],
                'Zimbabwe' => ['Zimbabwe'],
                'Afrique du Sud' => ['Afrique du Sud', 'South Africa'],
                'Angola' => ['Angola'],
                'Tunisie' => ['Tunisie', 'Tunisia'],
                'Ouganda' => ['Ouganda', 'Uganda'],
                'Nigeria' => ['Nigeria', 'Nigéria'],
                'Tanzanie' => ['Tanzanie', 'Tanzania'],
                'RD Congo' => ['RD Congo', 'DR Congo', 'Congo DR', 'RDC'],
                'Bénin' => ['Bénin', 'Benin'],
                'Sénégal' => ['Sénégal', 'Senegal'],
                'Botswana' => ['Botswana'],
                'Algérie' => ['Algérie', 'Algerie', 'Algeria'],
                'Soudan' => ['Soudan', 'Sudan'],
                'Burkina Faso' => ['Burkina Faso'],
                'Guinée Équatoriale' => ['Guinée Équatoriale', 'Guinée Equatoriale', 'Equatorial Guinea'],
                'Cameroun' => ['Cameroun', 'Cameroon'],
                'Gabon' => ['Gabon'],
                'Côte d\'Ivoire' => ['Côte d\'Ivoire', 'Cote d\'Ivoire', 'Ivory Coast'],
                'Mozambique' => ['Mozambique'],
            ];

            foreach ($matchs as $matchData) {
                try {
                    // Trouver les équipes
                    $homeTeam = $this->findTeam($matchData['equipe_1'], $teamNameMapping);
                    $awayTeam = $this->findTeam($matchData['equipe_2'], $teamNameMapping);

                    if (!$homeTeam || !$awayTeam) {
                        $errors[] = "Équipe non trouvée: {$matchData['equipe_1']} vs {$matchData['equipe_2']}";
                        continue;
                    }

                    // Vérifier si le match existe déjà
                    $existingMatch = MatchGame::where('home_team_id', $homeTeam->id)
                        ->where('away_team_id', $awayTeam->id)
                        ->whereDate('match_date', $matchData['date'])
                        ->first();

                    if ($existingMatch) {
                        // Mettre à jour le score si le match existe
                        $existingMatch->update([
                            'score_a' => $matchData['score_1'],
                            'score_b' => $matchData['score_2'],
                            'status' => 'finished',
                        ]);
                        $skipped++;
                        continue;
                    }

                    // Créer le match
                    MatchGame::create([
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'team_a' => $homeTeam->name,
                        'team_b' => $awayTeam->name,
                        'match_date' => $matchData['date'] . ' 17:00:00',
                        'stadium' => 'Stade CAN 2025',
                        'group_name' => $matchData['groupe'] ?? null,
                        'status' => isset($matchData['score_1']) ? 'finished' : 'scheduled',
                        'score_a' => $matchData['score_1'] ?? null,
                        'score_b' => $matchData['score_2'] ?? null,
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Erreur pour {$matchData['equipe_1']} vs {$matchData['equipe_2']}: " . $e->getMessage();
                }
            }

            $message = "Import terminé: {$imported} matchs importés";
            if ($skipped > 0) {
                $message .= ", {$skipped} matchs mis à jour";
            }
            if (!empty($errors)) {
                $message .= ". Erreurs: " . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= "... et " . (count($errors) - 3) . " autres";
                }
            }

            Notification::make()
                ->title('Import des matchs')
                ->body($message)
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur')
                ->body('Erreur lors de l\'import: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function findTeam(string $name, array $mapping): ?Team
    {
        // Chercher directement par nom
        $team = Team::where('name', $name)->first();
        if ($team) {
            return $team;
        }

        // Chercher dans le mapping
        foreach ($mapping as $standardName => $variants) {
            if (in_array($name, $variants)) {
                $team = Team::where('name', 'LIKE', "%{$standardName}%")->first();
                if ($team) {
                    return $team;
                }
                // Essayer toutes les variantes
                foreach ($variants as $variant) {
                    $team = Team::where('name', 'LIKE', "%{$variant}%")->first();
                    if ($team) {
                        return $team;
                    }
                }
            }
        }

        // Recherche approximative
        return Team::where('name', 'LIKE', "%{$name}%")->first();
    }
}
