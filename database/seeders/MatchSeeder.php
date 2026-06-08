<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MatchSeeder extends Seeder
{
    /**
     * Matchs de la Coupe du Monde 2026 (Football Fest 2026).
     * 104 matchs : 72 en phase de groupes (équipes connues) + 32 en phase finale
     * (équipes à déterminer, marquées par des placeholders : 1A, 2B, W74, L101...).
     * Heures stockées en UTC.
     * Idempotent : phase de groupes par (home, away, date) ; phase finale par numéro de match.
     */
    public function run(): void
    {
        $matches = [
            // ===== Phase de groupes (group_stage) =====
            ['home' => 'Mexico', 'away' => 'South Africa', 'label_a' => 'Mexico', 'label_b' => 'South Africa', 'date' => '2026-06-11 19:00:00', 'phase' => 'group_stage', 'grp' => 'A', 'stadium' => 'Estadio Azteca', 'num' => null],
            ['home' => 'South Korea', 'away' => 'Czech Republic', 'label_a' => 'South Korea', 'label_b' => 'Czech Republic', 'date' => '2026-06-12 02:00:00', 'phase' => 'group_stage', 'grp' => 'A', 'stadium' => 'Estadio Akron', 'num' => null],
            ['home' => 'Czech Republic', 'away' => 'South Africa', 'label_a' => 'Czech Republic', 'label_b' => 'South Africa', 'date' => '2026-06-18 16:00:00', 'phase' => 'group_stage', 'grp' => 'A', 'stadium' => 'Mercedes-Benz Stadium', 'num' => null],
            ['home' => 'Mexico', 'away' => 'South Korea', 'label_a' => 'Mexico', 'label_b' => 'South Korea', 'date' => '2026-06-19 01:00:00', 'phase' => 'group_stage', 'grp' => 'A', 'stadium' => 'Estadio Akron', 'num' => null],
            ['home' => 'Czech Republic', 'away' => 'Mexico', 'label_a' => 'Czech Republic', 'label_b' => 'Mexico', 'date' => '2026-06-25 01:00:00', 'phase' => 'group_stage', 'grp' => 'A', 'stadium' => 'Estadio Azteca', 'num' => null],
            ['home' => 'South Africa', 'away' => 'South Korea', 'label_a' => 'South Africa', 'label_b' => 'South Korea', 'date' => '2026-06-25 01:00:00', 'phase' => 'group_stage', 'grp' => 'A', 'stadium' => 'Estadio BBVA', 'num' => null],
            ['home' => 'Canada', 'away' => 'Bosnia & Herzegovina', 'label_a' => 'Canada', 'label_b' => 'Bosnia & Herzegovina', 'date' => '2026-06-12 19:00:00', 'phase' => 'group_stage', 'grp' => 'B', 'stadium' => 'BMO Field', 'num' => null],
            ['home' => 'Qatar', 'away' => 'Switzerland', 'label_a' => 'Qatar', 'label_b' => 'Switzerland', 'date' => '2026-06-13 19:00:00', 'phase' => 'group_stage', 'grp' => 'B', 'stadium' => 'Levi\'s Stadium', 'num' => null],
            ['home' => 'Switzerland', 'away' => 'Bosnia & Herzegovina', 'label_a' => 'Switzerland', 'label_b' => 'Bosnia & Herzegovina', 'date' => '2026-06-18 19:00:00', 'phase' => 'group_stage', 'grp' => 'B', 'stadium' => 'SoFi Stadium', 'num' => null],
            ['home' => 'Canada', 'away' => 'Qatar', 'label_a' => 'Canada', 'label_b' => 'Qatar', 'date' => '2026-06-18 22:00:00', 'phase' => 'group_stage', 'grp' => 'B', 'stadium' => 'BC Place', 'num' => null],
            ['home' => 'Switzerland', 'away' => 'Canada', 'label_a' => 'Switzerland', 'label_b' => 'Canada', 'date' => '2026-06-24 19:00:00', 'phase' => 'group_stage', 'grp' => 'B', 'stadium' => 'BC Place', 'num' => null],
            ['home' => 'Bosnia & Herzegovina', 'away' => 'Qatar', 'label_a' => 'Bosnia & Herzegovina', 'label_b' => 'Qatar', 'date' => '2026-06-24 19:00:00', 'phase' => 'group_stage', 'grp' => 'B', 'stadium' => 'Lumen Field', 'num' => null],
            ['home' => 'Brazil', 'away' => 'Morocco', 'label_a' => 'Brazil', 'label_b' => 'Morocco', 'date' => '2026-06-13 22:00:00', 'phase' => 'group_stage', 'grp' => 'C', 'stadium' => 'MetLife Stadium', 'num' => null],
            ['home' => 'Haiti', 'away' => 'Scotland', 'label_a' => 'Haiti', 'label_b' => 'Scotland', 'date' => '2026-06-14 01:00:00', 'phase' => 'group_stage', 'grp' => 'C', 'stadium' => 'Gillette Stadium', 'num' => null],
            ['home' => 'Scotland', 'away' => 'Morocco', 'label_a' => 'Scotland', 'label_b' => 'Morocco', 'date' => '2026-06-19 22:00:00', 'phase' => 'group_stage', 'grp' => 'C', 'stadium' => 'Gillette Stadium', 'num' => null],
            ['home' => 'Brazil', 'away' => 'Haiti', 'label_a' => 'Brazil', 'label_b' => 'Haiti', 'date' => '2026-06-20 00:30:00', 'phase' => 'group_stage', 'grp' => 'C', 'stadium' => 'Lincoln Financial Field', 'num' => null],
            ['home' => 'Scotland', 'away' => 'Brazil', 'label_a' => 'Scotland', 'label_b' => 'Brazil', 'date' => '2026-06-24 22:00:00', 'phase' => 'group_stage', 'grp' => 'C', 'stadium' => 'Hard Rock Stadium', 'num' => null],
            ['home' => 'Morocco', 'away' => 'Haiti', 'label_a' => 'Morocco', 'label_b' => 'Haiti', 'date' => '2026-06-24 22:00:00', 'phase' => 'group_stage', 'grp' => 'C', 'stadium' => 'Mercedes-Benz Stadium', 'num' => null],
            ['home' => 'USA', 'away' => 'Paraguay', 'label_a' => 'USA', 'label_b' => 'Paraguay', 'date' => '2026-06-13 01:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'SoFi Stadium', 'num' => null],
            ['home' => 'Australia', 'away' => 'Turkey', 'label_a' => 'Australia', 'label_b' => 'Turkey', 'date' => '2026-06-14 04:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'BC Place', 'num' => null],
            ['home' => 'USA', 'away' => 'Australia', 'label_a' => 'USA', 'label_b' => 'Australia', 'date' => '2026-06-19 19:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'Lumen Field', 'num' => null],
            ['home' => 'Turkey', 'away' => 'Paraguay', 'label_a' => 'Turkey', 'label_b' => 'Paraguay', 'date' => '2026-06-20 03:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'Levi\'s Stadium', 'num' => null],
            ['home' => 'Turkey', 'away' => 'USA', 'label_a' => 'Turkey', 'label_b' => 'USA', 'date' => '2026-06-26 02:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'SoFi Stadium', 'num' => null],
            ['home' => 'Paraguay', 'away' => 'Australia', 'label_a' => 'Paraguay', 'label_b' => 'Australia', 'date' => '2026-06-26 02:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'Levi\'s Stadium', 'num' => null],
            ['home' => 'Germany', 'away' => 'Curaçao', 'label_a' => 'Germany', 'label_b' => 'Curaçao', 'date' => '2026-06-14 17:00:00', 'phase' => 'group_stage', 'grp' => 'E', 'stadium' => 'NRG Stadium', 'num' => null],
            ['home' => 'Ivory Coast', 'away' => 'Ecuador', 'label_a' => 'Ivory Coast', 'label_b' => 'Ecuador', 'date' => '2026-06-14 23:00:00', 'phase' => 'group_stage', 'grp' => 'E', 'stadium' => 'Lincoln Financial Field', 'num' => null],
            ['home' => 'Germany', 'away' => 'Ivory Coast', 'label_a' => 'Germany', 'label_b' => 'Ivory Coast', 'date' => '2026-06-20 20:00:00', 'phase' => 'group_stage', 'grp' => 'E', 'stadium' => 'BMO Field', 'num' => null],
            ['home' => 'Ecuador', 'away' => 'Curaçao', 'label_a' => 'Ecuador', 'label_b' => 'Curaçao', 'date' => '2026-06-21 00:00:00', 'phase' => 'group_stage', 'grp' => 'E', 'stadium' => 'Arrowhead Stadium', 'num' => null],
            ['home' => 'Curaçao', 'away' => 'Ivory Coast', 'label_a' => 'Curaçao', 'label_b' => 'Ivory Coast', 'date' => '2026-06-25 20:00:00', 'phase' => 'group_stage', 'grp' => 'E', 'stadium' => 'Lincoln Financial Field', 'num' => null],
            ['home' => 'Ecuador', 'away' => 'Germany', 'label_a' => 'Ecuador', 'label_b' => 'Germany', 'date' => '2026-06-25 20:00:00', 'phase' => 'group_stage', 'grp' => 'E', 'stadium' => 'MetLife Stadium', 'num' => null],
            ['home' => 'Netherlands', 'away' => 'Japan', 'label_a' => 'Netherlands', 'label_b' => 'Japan', 'date' => '2026-06-14 20:00:00', 'phase' => 'group_stage', 'grp' => 'F', 'stadium' => 'AT&T Stadium', 'num' => null],
            ['home' => 'Sweden', 'away' => 'Tunisia', 'label_a' => 'Sweden', 'label_b' => 'Tunisia', 'date' => '2026-06-15 02:00:00', 'phase' => 'group_stage', 'grp' => 'F', 'stadium' => 'Estadio BBVA', 'num' => null],
            ['home' => 'Netherlands', 'away' => 'Sweden', 'label_a' => 'Netherlands', 'label_b' => 'Sweden', 'date' => '2026-06-20 17:00:00', 'phase' => 'group_stage', 'grp' => 'F', 'stadium' => 'NRG Stadium', 'num' => null],
            ['home' => 'Tunisia', 'away' => 'Japan', 'label_a' => 'Tunisia', 'label_b' => 'Japan', 'date' => '2026-06-21 04:00:00', 'phase' => 'group_stage', 'grp' => 'F', 'stadium' => 'Estadio BBVA', 'num' => null],
            ['home' => 'Japan', 'away' => 'Sweden', 'label_a' => 'Japan', 'label_b' => 'Sweden', 'date' => '2026-06-25 23:00:00', 'phase' => 'group_stage', 'grp' => 'F', 'stadium' => 'AT&T Stadium', 'num' => null],
            ['home' => 'Tunisia', 'away' => 'Netherlands', 'label_a' => 'Tunisia', 'label_b' => 'Netherlands', 'date' => '2026-06-25 23:00:00', 'phase' => 'group_stage', 'grp' => 'F', 'stadium' => 'Arrowhead Stadium', 'num' => null],
            ['home' => 'Belgium', 'away' => 'Egypt', 'label_a' => 'Belgium', 'label_b' => 'Egypt', 'date' => '2026-06-15 19:00:00', 'phase' => 'group_stage', 'grp' => 'G', 'stadium' => 'Lumen Field', 'num' => null],
            ['home' => 'Iran', 'away' => 'New Zealand', 'label_a' => 'Iran', 'label_b' => 'New Zealand', 'date' => '2026-06-16 01:00:00', 'phase' => 'group_stage', 'grp' => 'G', 'stadium' => 'SoFi Stadium', 'num' => null],
            ['home' => 'Belgium', 'away' => 'Iran', 'label_a' => 'Belgium', 'label_b' => 'Iran', 'date' => '2026-06-21 19:00:00', 'phase' => 'group_stage', 'grp' => 'G', 'stadium' => 'SoFi Stadium', 'num' => null],
            ['home' => 'New Zealand', 'away' => 'Egypt', 'label_a' => 'New Zealand', 'label_b' => 'Egypt', 'date' => '2026-06-22 01:00:00', 'phase' => 'group_stage', 'grp' => 'G', 'stadium' => 'BC Place', 'num' => null],
            ['home' => 'Egypt', 'away' => 'Iran', 'label_a' => 'Egypt', 'label_b' => 'Iran', 'date' => '2026-06-27 03:00:00', 'phase' => 'group_stage', 'grp' => 'G', 'stadium' => 'Lumen Field', 'num' => null],
            ['home' => 'New Zealand', 'away' => 'Belgium', 'label_a' => 'New Zealand', 'label_b' => 'Belgium', 'date' => '2026-06-27 03:00:00', 'phase' => 'group_stage', 'grp' => 'G', 'stadium' => 'BC Place', 'num' => null],
            ['home' => 'Spain', 'away' => 'Cape Verde', 'label_a' => 'Spain', 'label_b' => 'Cape Verde', 'date' => '2026-06-15 16:00:00', 'phase' => 'group_stage', 'grp' => 'H', 'stadium' => 'Mercedes-Benz Stadium', 'num' => null],
            ['home' => 'Saudi Arabia', 'away' => 'Uruguay', 'label_a' => 'Saudi Arabia', 'label_b' => 'Uruguay', 'date' => '2026-06-15 22:00:00', 'phase' => 'group_stage', 'grp' => 'H', 'stadium' => 'Hard Rock Stadium', 'num' => null],
            ['home' => 'Spain', 'away' => 'Saudi Arabia', 'label_a' => 'Spain', 'label_b' => 'Saudi Arabia', 'date' => '2026-06-21 16:00:00', 'phase' => 'group_stage', 'grp' => 'H', 'stadium' => 'Mercedes-Benz Stadium', 'num' => null],
            ['home' => 'Uruguay', 'away' => 'Cape Verde', 'label_a' => 'Uruguay', 'label_b' => 'Cape Verde', 'date' => '2026-06-21 22:00:00', 'phase' => 'group_stage', 'grp' => 'H', 'stadium' => 'Hard Rock Stadium', 'num' => null],
            ['home' => 'Cape Verde', 'away' => 'Saudi Arabia', 'label_a' => 'Cape Verde', 'label_b' => 'Saudi Arabia', 'date' => '2026-06-27 00:00:00', 'phase' => 'group_stage', 'grp' => 'H', 'stadium' => 'NRG Stadium', 'num' => null],
            ['home' => 'Uruguay', 'away' => 'Spain', 'label_a' => 'Uruguay', 'label_b' => 'Spain', 'date' => '2026-06-27 00:00:00', 'phase' => 'group_stage', 'grp' => 'H', 'stadium' => 'Estadio Akron', 'num' => null],
            ['home' => 'France', 'away' => 'Senegal', 'label_a' => 'France', 'label_b' => 'Senegal', 'date' => '2026-06-16 19:00:00', 'phase' => 'group_stage', 'grp' => 'I', 'stadium' => 'MetLife Stadium', 'num' => null],
            ['home' => 'Iraq', 'away' => 'Norway', 'label_a' => 'Iraq', 'label_b' => 'Norway', 'date' => '2026-06-16 22:00:00', 'phase' => 'group_stage', 'grp' => 'I', 'stadium' => 'Gillette Stadium', 'num' => null],
            ['home' => 'France', 'away' => 'Iraq', 'label_a' => 'France', 'label_b' => 'Iraq', 'date' => '2026-06-22 21:00:00', 'phase' => 'group_stage', 'grp' => 'I', 'stadium' => 'Lincoln Financial Field', 'num' => null],
            ['home' => 'Norway', 'away' => 'Senegal', 'label_a' => 'Norway', 'label_b' => 'Senegal', 'date' => '2026-06-23 00:00:00', 'phase' => 'group_stage', 'grp' => 'I', 'stadium' => 'MetLife Stadium', 'num' => null],
            ['home' => 'Norway', 'away' => 'France', 'label_a' => 'Norway', 'label_b' => 'France', 'date' => '2026-06-26 19:00:00', 'phase' => 'group_stage', 'grp' => 'I', 'stadium' => 'Gillette Stadium', 'num' => null],
            ['home' => 'Senegal', 'away' => 'Iraq', 'label_a' => 'Senegal', 'label_b' => 'Iraq', 'date' => '2026-06-26 19:00:00', 'phase' => 'group_stage', 'grp' => 'I', 'stadium' => 'BMO Field', 'num' => null],
            ['home' => 'Argentina', 'away' => 'Algeria', 'label_a' => 'Argentina', 'label_b' => 'Algeria', 'date' => '2026-06-17 01:00:00', 'phase' => 'group_stage', 'grp' => 'J', 'stadium' => 'Arrowhead Stadium', 'num' => null],
            ['home' => 'Austria', 'away' => 'Jordan', 'label_a' => 'Austria', 'label_b' => 'Jordan', 'date' => '2026-06-17 04:00:00', 'phase' => 'group_stage', 'grp' => 'J', 'stadium' => 'Levi\'s Stadium', 'num' => null],
            ['home' => 'Argentina', 'away' => 'Austria', 'label_a' => 'Argentina', 'label_b' => 'Austria', 'date' => '2026-06-22 17:00:00', 'phase' => 'group_stage', 'grp' => 'J', 'stadium' => 'AT&T Stadium', 'num' => null],
            ['home' => 'Jordan', 'away' => 'Algeria', 'label_a' => 'Jordan', 'label_b' => 'Algeria', 'date' => '2026-06-23 03:00:00', 'phase' => 'group_stage', 'grp' => 'J', 'stadium' => 'Levi\'s Stadium', 'num' => null],
            ['home' => 'Algeria', 'away' => 'Austria', 'label_a' => 'Algeria', 'label_b' => 'Austria', 'date' => '2026-06-28 02:00:00', 'phase' => 'group_stage', 'grp' => 'J', 'stadium' => 'Arrowhead Stadium', 'num' => null],
            ['home' => 'Jordan', 'away' => 'Argentina', 'label_a' => 'Jordan', 'label_b' => 'Argentina', 'date' => '2026-06-28 02:00:00', 'phase' => 'group_stage', 'grp' => 'J', 'stadium' => 'AT&T Stadium', 'num' => null],
            ['home' => 'Portugal', 'away' => 'DR Congo', 'label_a' => 'Portugal', 'label_b' => 'DR Congo', 'date' => '2026-06-17 17:00:00', 'phase' => 'group_stage', 'grp' => 'K', 'stadium' => 'NRG Stadium', 'num' => null],
            ['home' => 'Uzbekistan', 'away' => 'Colombia', 'label_a' => 'Uzbekistan', 'label_b' => 'Colombia', 'date' => '2026-06-18 02:00:00', 'phase' => 'group_stage', 'grp' => 'K', 'stadium' => 'Estadio Azteca', 'num' => null],
            ['home' => 'Portugal', 'away' => 'Uzbekistan', 'label_a' => 'Portugal', 'label_b' => 'Uzbekistan', 'date' => '2026-06-23 17:00:00', 'phase' => 'group_stage', 'grp' => 'K', 'stadium' => 'NRG Stadium', 'num' => null],
            ['home' => 'Colombia', 'away' => 'DR Congo', 'label_a' => 'Colombia', 'label_b' => 'DR Congo', 'date' => '2026-06-24 02:00:00', 'phase' => 'group_stage', 'grp' => 'K', 'stadium' => 'Estadio Akron', 'num' => null],
            ['home' => 'Colombia', 'away' => 'Portugal', 'label_a' => 'Colombia', 'label_b' => 'Portugal', 'date' => '2026-06-27 23:30:00', 'phase' => 'group_stage', 'grp' => 'K', 'stadium' => 'Hard Rock Stadium', 'num' => null],
            ['home' => 'DR Congo', 'away' => 'Uzbekistan', 'label_a' => 'DR Congo', 'label_b' => 'Uzbekistan', 'date' => '2026-06-27 23:30:00', 'phase' => 'group_stage', 'grp' => 'K', 'stadium' => 'Mercedes-Benz Stadium', 'num' => null],
            ['home' => 'England', 'away' => 'Croatia', 'label_a' => 'England', 'label_b' => 'Croatia', 'date' => '2026-06-17 20:00:00', 'phase' => 'group_stage', 'grp' => 'L', 'stadium' => 'AT&T Stadium', 'num' => null],
            ['home' => 'Ghana', 'away' => 'Panama', 'label_a' => 'Ghana', 'label_b' => 'Panama', 'date' => '2026-06-17 23:00:00', 'phase' => 'group_stage', 'grp' => 'L', 'stadium' => 'BMO Field', 'num' => null],
            ['home' => 'England', 'away' => 'Ghana', 'label_a' => 'England', 'label_b' => 'Ghana', 'date' => '2026-06-23 20:00:00', 'phase' => 'group_stage', 'grp' => 'L', 'stadium' => 'Gillette Stadium', 'num' => null],
            ['home' => 'Panama', 'away' => 'Croatia', 'label_a' => 'Panama', 'label_b' => 'Croatia', 'date' => '2026-06-23 23:00:00', 'phase' => 'group_stage', 'grp' => 'L', 'stadium' => 'BMO Field', 'num' => null],
            ['home' => 'Panama', 'away' => 'England', 'label_a' => 'Panama', 'label_b' => 'England', 'date' => '2026-06-27 21:00:00', 'phase' => 'group_stage', 'grp' => 'L', 'stadium' => 'MetLife Stadium', 'num' => null],
            ['home' => 'Croatia', 'away' => 'Ghana', 'label_a' => 'Croatia', 'label_b' => 'Ghana', 'date' => '2026-06-27 21:00:00', 'phase' => 'group_stage', 'grp' => 'L', 'stadium' => 'Lincoln Financial Field', 'num' => null],

            // ===== 32es de finale (round_of_32) =====
            ['home' => null, 'away' => null, 'label_a' => '2A', 'label_b' => '2B', 'date' => '2026-06-28 19:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'SoFi Stadium', 'num' => 73],
            ['home' => null, 'away' => null, 'label_a' => '1E', 'label_b' => '3A/B/C/D/F', 'date' => '2026-06-29 20:30:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'Gillette Stadium', 'num' => 74],
            ['home' => null, 'away' => null, 'label_a' => '1F', 'label_b' => '2C', 'date' => '2026-06-30 01:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'Estadio BBVA', 'num' => 75],
            ['home' => null, 'away' => null, 'label_a' => '1C', 'label_b' => '2F', 'date' => '2026-06-29 17:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'NRG Stadium', 'num' => 76],
            ['home' => null, 'away' => null, 'label_a' => '1I', 'label_b' => '3C/D/F/G/H', 'date' => '2026-06-30 21:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'MetLife Stadium', 'num' => 77],
            ['home' => null, 'away' => null, 'label_a' => '2E', 'label_b' => '2I', 'date' => '2026-06-30 17:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'AT&T Stadium', 'num' => 78],
            ['home' => null, 'away' => null, 'label_a' => '1A', 'label_b' => '3C/E/F/H/I', 'date' => '2026-07-01 01:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'Estadio Azteca', 'num' => 79],
            ['home' => null, 'away' => null, 'label_a' => '1L', 'label_b' => '3E/H/I/J/K', 'date' => '2026-07-01 16:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'Mercedes-Benz Stadium', 'num' => 80],
            ['home' => null, 'away' => null, 'label_a' => '1D', 'label_b' => '3B/E/F/I/J', 'date' => '2026-07-02 00:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'Levi\'s Stadium', 'num' => 81],
            ['home' => null, 'away' => null, 'label_a' => '1G', 'label_b' => '3A/E/H/I/J', 'date' => '2026-07-01 20:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'Lumen Field', 'num' => 82],
            ['home' => null, 'away' => null, 'label_a' => '2K', 'label_b' => '2L', 'date' => '2026-07-02 23:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'BMO Field', 'num' => 83],
            ['home' => null, 'away' => null, 'label_a' => '1H', 'label_b' => '2J', 'date' => '2026-07-02 19:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'SoFi Stadium', 'num' => 84],
            ['home' => null, 'away' => null, 'label_a' => '1B', 'label_b' => '3E/F/G/I/J', 'date' => '2026-07-03 03:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'BC Place', 'num' => 85],
            ['home' => null, 'away' => null, 'label_a' => '1J', 'label_b' => '2H', 'date' => '2026-07-03 22:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'Hard Rock Stadium', 'num' => 86],
            ['home' => null, 'away' => null, 'label_a' => '1K', 'label_b' => '3D/E/I/J/L', 'date' => '2026-07-04 01:30:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'Arrowhead Stadium', 'num' => 87],
            ['home' => null, 'away' => null, 'label_a' => '2D', 'label_b' => '2G', 'date' => '2026-07-03 18:00:00', 'phase' => 'round_of_32', 'grp' => null, 'stadium' => 'AT&T Stadium', 'num' => 88],

            // ===== 16es de finale (round_of_16) =====
            ['home' => null, 'away' => null, 'label_a' => 'W74', 'label_b' => 'W77', 'date' => '2026-07-04 21:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'Lincoln Financial Field', 'num' => 89],
            ['home' => null, 'away' => null, 'label_a' => 'W73', 'label_b' => 'W75', 'date' => '2026-07-04 17:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'NRG Stadium', 'num' => 90],
            ['home' => null, 'away' => null, 'label_a' => 'W76', 'label_b' => 'W78', 'date' => '2026-07-05 20:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'MetLife Stadium', 'num' => 91],
            ['home' => null, 'away' => null, 'label_a' => 'W79', 'label_b' => 'W80', 'date' => '2026-07-06 00:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'Estadio Azteca', 'num' => 92],
            ['home' => null, 'away' => null, 'label_a' => 'W83', 'label_b' => 'W84', 'date' => '2026-07-06 19:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'AT&T Stadium', 'num' => 93],
            ['home' => null, 'away' => null, 'label_a' => 'W81', 'label_b' => 'W82', 'date' => '2026-07-07 00:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'Lumen Field', 'num' => 94],
            ['home' => null, 'away' => null, 'label_a' => 'W86', 'label_b' => 'W88', 'date' => '2026-07-07 16:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'Mercedes-Benz Stadium', 'num' => 95],
            ['home' => null, 'away' => null, 'label_a' => 'W85', 'label_b' => 'W87', 'date' => '2026-07-07 20:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'BC Place', 'num' => 96],

            // ===== Quarts de finale (quarter_final) =====
            ['home' => null, 'away' => null, 'label_a' => 'W89', 'label_b' => 'W90', 'date' => '2026-07-09 20:00:00', 'phase' => 'quarter_final', 'grp' => null, 'stadium' => 'Gillette Stadium', 'num' => 97],
            ['home' => null, 'away' => null, 'label_a' => 'W93', 'label_b' => 'W94', 'date' => '2026-07-10 19:00:00', 'phase' => 'quarter_final', 'grp' => null, 'stadium' => 'SoFi Stadium', 'num' => 98],
            ['home' => null, 'away' => null, 'label_a' => 'W91', 'label_b' => 'W92', 'date' => '2026-07-11 21:00:00', 'phase' => 'quarter_final', 'grp' => null, 'stadium' => 'Hard Rock Stadium', 'num' => 99],
            ['home' => null, 'away' => null, 'label_a' => 'W95', 'label_b' => 'W96', 'date' => '2026-07-12 01:00:00', 'phase' => 'quarter_final', 'grp' => null, 'stadium' => 'Arrowhead Stadium', 'num' => 100],

            // ===== Demi-finales (semi_final) =====
            ['home' => null, 'away' => null, 'label_a' => 'W97', 'label_b' => 'W98', 'date' => '2026-07-14 19:00:00', 'phase' => 'semi_final', 'grp' => null, 'stadium' => 'AT&T Stadium', 'num' => 101],
            ['home' => null, 'away' => null, 'label_a' => 'W99', 'label_b' => 'W100', 'date' => '2026-07-15 19:00:00', 'phase' => 'semi_final', 'grp' => null, 'stadium' => 'Mercedes-Benz Stadium', 'num' => 102],

            // ===== Match pour la 3e place (third_place) =====
            ['home' => null, 'away' => null, 'label_a' => 'L101', 'label_b' => 'L102', 'date' => '2026-07-18 21:00:00', 'phase' => 'third_place', 'grp' => null, 'stadium' => 'Hard Rock Stadium', 'num' => null],

            // ===== Finale (final) =====
            ['home' => null, 'away' => null, 'label_a' => 'W101', 'label_b' => 'W102', 'date' => '2026-07-19 19:00:00', 'phase' => 'final', 'grp' => null, 'stadium' => 'MetLife Stadium', 'num' => null],
        ];

        $created = 0;
        $updated = 0;

        foreach ($matches as $matchData) {
            $homeTeamId = null;
            $awayTeamId = null;
            // Par défaut, on utilise les libellés fournis (placeholders pour la phase finale)
            $teamA = $matchData['label_a'] ?? 'À déterminer';
            $teamB = $matchData['label_b'] ?? 'À déterminer';

            // Trouver les équipes si spécifiées (null pour les matchs de phase finale à déterminer)
            if (!empty($matchData['home'])) {
                $homeTeam = Team::where('name', $matchData['home'])->first();
                if ($homeTeam) {
                    $homeTeamId = $homeTeam->id;
                    $teamA = $homeTeam->name;
                } else {
                    $this->command->warn("⚠️ Équipe domicile non trouvée: {$matchData['home']}");
                    continue;
                }
            }

            if (!empty($matchData['away'])) {
                $awayTeam = Team::where('name', $matchData['away'])->first();
                if ($awayTeam) {
                    $awayTeamId = $awayTeam->id;
                    $teamB = $awayTeam->name;
                } else {
                    $this->command->warn("⚠️ Équipe extérieur non trouvée: {$matchData['away']}");
                    continue;
                }
            }

            $matchDate = Carbon::parse($matchData['date']);
            $matchNumber = $matchData['num'] ?? null;

            // Clé unique pour éviter les doublons :
            // - Phase de groupes : home + away + date
            // - Phase finale numérotée : numéro de match (unique)
            // - 3e place / finale (sans numéro) : phase (unique)
            if ($homeTeamId && $awayTeamId) {
                $uniqueKey = [
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'match_date' => $matchDate,
                ];
            } elseif ($matchNumber !== null) {
                $uniqueKey = [
                    'match_number' => $matchNumber,
                ];
            } else {
                $uniqueKey = [
                    'phase' => $matchData['phase'] ?? 'group_stage',
                ];
            }

            $matchModel = MatchGame::updateOrCreate(
                $uniqueKey,
                [
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'team_a' => $teamA,
                    'team_b' => $teamB,
                    'match_date' => $matchDate,
                    'phase' => $matchData['phase'] ?? 'group_stage',
                    'group_name' => $matchData['grp'],
                    'stadium' => $matchData['stadium'],
                    'match_number' => $matchNumber,
                    'status' => 'scheduled',
                    // Preserve existing scores if match already exists
                ]
            );

            if ($matchModel->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Matches: {$created} created, {$updated} updated (Total: " . ($created + $updated) . ")");
    }
}
