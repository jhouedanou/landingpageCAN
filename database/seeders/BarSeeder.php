<?php

namespace Database\Seeders;

use App\Models\Bar;
use Illuminate\Database\Seeder;

class BarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Production-safe: updateOrCreate instead of truncate
        // Preserves existing bars and their relationships

        $venues = [
            ['name' => 'BAR ALLIANCE', 'address' => 'KEUR MBAYE FALL', 'latitude' => '14.74078920', 'longitude' => '-17.32342350', 'is_active' => true],
            ['name' => 'BAR AWALE', 'address' => 'OUAKAM', 'latitude' => '14.72500000', 'longitude' => '-17.48100000', 'is_active' => true],
            ['name' => 'BAR AWARA', 'address' => 'GRAND-YOFF', 'latitude' => '14.74166780', 'longitude' => '-17.44449970', 'is_active' => true],
            ['name' => 'BAR BAKASAO', 'address' => 'MALIKA', 'latitude' => '14.75082860', 'longitude' => '-17.45577440', 'is_active' => true],
            ['name' => 'BAR BANDIAL', 'address' => 'REUBEUSS', 'latitude' => '14.67045860', 'longitude' => '-17.44148470', 'is_active' => true],
            ['name' => 'BAR BAZILE', 'address' => 'GUEDIAWAYE', 'latitude' => '14.78134710', 'longitude' => '-17.37552110', 'is_active' => true],
            ['name' => 'BAR BISTRO', 'address' => 'SICAP LIBERTE 5', 'latitude' => '14.70902350', 'longitude' => '-17.45825930', 'is_active' => true],
            ['name' => 'BAR BLEUKEUSSS', 'address' => 'DIAMEGEUNE', 'latitude' => '14.76524580', 'longitude' => '-17.44576740', 'is_active' => true],
            ['name' => 'BAR BONGRE', 'address' => 'TIVAOUNE PEUL', 'latitude' => '14.78807840', 'longitude' => '-17.28849620', 'is_active' => true],
            ['name' => 'BAR BOUELO', 'address' => 'GUEDIAWAYE', 'latitude' => '14.67615850', 'longitude' => '-17.44776340', 'is_active' => true],
            ['name' => 'BAR CASA ESTANCIA', 'address' => 'PARCELLES ASSAINIES U 10', 'latitude' => '14.76136000', 'longitude' => '-17.43371180', 'is_active' => true],
            ['name' => 'BAR CHEZ ALICE', 'address' => 'KEUR MASSAR', 'latitude' => '14.76128820', 'longitude' => '-17.28413610', 'is_active' => true],
            ['name' => 'BAR CHEZ CATHO', 'address' => 'LIBERTE 5', 'latitude' => '14.72155090', 'longitude' => '-17.46284540', 'is_active' => true],
            ['name' => 'BAR CHEZ FRANCOIS', 'address' => 'CITE FADIA', 'latitude' => '14.70958860', 'longitude' => '-17.45237250', 'is_active' => true],
            ['name' => 'BAR CHEZ GUILLAINE', 'address' => 'HLM', 'latitude' => '14.70865000', 'longitude' => '-17.44695200', 'is_active' => true],
            ['name' => 'BAR CHEZ HENRI', 'address' => 'SEBIKOTANE', 'latitude' => '14.75083000', 'longitude' => '-17.45580110', 'is_active' => true],
            ['name' => 'BAR CHEZ JEAN', 'address' => 'GRAND-DAKAR', 'latitude' => '14.73824490', 'longitude' => '-17.45184020', 'is_active' => true],
            ['name' => 'BAR CHEZ LOPY', 'address' => 'OUAKAM', 'latitude' => '14.72000000', 'longitude' => '-17.48000000', 'is_active' => true],
            ['name' => 'BAR CHEZ MILI', 'address' => 'MALIKA', 'latitude' => '14.75082450', 'longitude' => '-17.45576770', 'is_active' => true],
            ['name' => 'BAR CHEZ PASCAL', 'address' => 'GUEDIAWAYE', 'latitude' => '14.78537400', 'longitude' => '-17.37830900', 'is_active' => true],
            ['name' => 'BAR CHEZ PREIRA', 'address' => 'KEUR MBAYE FALL', 'latitude' => '14.74988450', 'longitude' => '-17.34402140', 'is_active' => true],
            ['name' => 'BAR CHEZ TANTI', 'address' => 'THIAROYE', 'latitude' => '14.76691050', 'longitude' => '-17.38013880', 'is_active' => true],
            ['name' => 'BAR CHEZ VALERIE', 'address' => 'ROND POINT CASE', 'latitude' => '14.75772580', 'longitude' => '-17.42851230', 'is_active' => true],
            ['name' => 'BAR CHEZ VINCENT', 'address' => 'PARCELLES ASSAINIES U 24', 'latitude' => '14.75364970', 'longitude' => '-17.44677050', 'is_active' => true],
            ['name' => 'BAR CONCENSUS', 'address' => 'KEUR MASSAR', 'latitude' => '14.77386080', 'longitude' => '-17.32082910', 'is_active' => true],
            ['name' => 'BAR DAKHARGUI', 'address' => 'PARCELLES ASSAINIES U 17', 'latitude' => '14.75769600', 'longitude' => '-17.43984500', 'is_active' => true],
            ['name' => 'BAR EDIOUNGOU', 'address' => 'GRAND-YOFF', 'latitude' => '14.73754830', 'longitude' => '-17.44814820', 'is_active' => true],
            ['name' => 'BAR ELTON', 'address' => 'GUEDIAWAYE', 'latitude' => '14.78542600', 'longitude' => '-17.37832070', 'is_active' => true],
            ['name' => 'BAR ETALON', 'address' => 'GRAND-DAKAR', 'latitude' => '14.69179110', 'longitude' => '-17.43377840', 'is_active' => true],
            ['name' => 'BAR ETHIOUNG', 'address' => 'PARCELLES ASSAINIES U 7', 'latitude' => '14.72545000', 'longitude' => '-17.44295300', 'is_active' => true],
            ['name' => 'BAR FOUGON 2', 'address' => 'MALIKA', 'latitude' => '14.79228160', 'longitude' => '-17.32899890', 'is_active' => true],
            ['name' => 'BAR JEROME', 'address' => 'OUAKAM', 'latitude' => '14.72691380', 'longitude' => '-17.48281380', 'is_active' => true],
            ['name' => 'BAR JOE BASS', 'address' => 'KEUR MASSAR', 'latitude' => '14.77783220', 'longitude' => '-17.33062000', 'is_active' => true],
            ['name' => 'BAR JOYCE', 'address' => 'OUAKAM', 'latitude' => '14.69280390', 'longitude' => '-17.46039930', 'is_active' => true],
            ['name' => 'BAR KADETH', 'address' => 'PARCELLES ASSAINIES U 12', 'latitude' => '14.75735990', 'longitude' => '-17.44172030', 'is_active' => true],
            ['name' => 'BAR KAMEME', 'address' => 'GRAND-YOFF', 'latitude' => '14.73435590', 'longitude' => '-17.44623830', 'is_active' => true],
            ['name' => 'BAR KAMIEUM', 'address' => 'THIAROYE', 'latitude' => '14.76424740', 'longitude' => '-17.37323670', 'is_active' => true],
            ['name' => 'BAR KANDJIDIASSA', 'address' => 'PARCELLES ASSAINIES U 19', 'latitude' => '14.75513000', 'longitude' => '-17.45191900', 'is_active' => true],
            ['name' => 'BAR KAPOL', 'address' => 'GUEDIAWAYE', 'latitude' => '14.77694800', 'longitude' => '-17.37711800', 'is_active' => true],
            ['name' => 'BAR KAWARAFAN', 'address' => 'KEUR MASSAR', 'latitude' => '14.76441530', 'longitude' => '-17.30528660', 'is_active' => true],
            ['name' => 'BAR LA GOREENNE', 'address' => 'PATTE D\'OIE', 'latitude' => '14.74766960', 'longitude' => '-17.44321230', 'is_active' => true],
            ['name' => 'BAR LE BOURBEOIS', 'address' => 'OUAKAM', 'latitude' => '14.72744080', 'longitude' => '-17.48387940', 'is_active' => true],
            ['name' => 'BAR MAISON BLANCHE', 'address' => 'PARCELLES U 10', 'latitude' => '14.76171110', 'longitude' => '-17.43659720', 'is_active' => true],
            ['name' => 'BAR MONTAGNE', 'address' => 'PARCELLES ASSAINIES U 26', 'latitude' => '14.75663800', 'longitude' => '-17.44117700', 'is_active' => true],
            ['name' => 'BAR OUTHEKOR', 'address' => 'GRAND-YOFF', 'latitude' => '14.73691300', 'longitude' => '-17.44677290', 'is_active' => true],
            ['name' => 'BAR POPEGUINE', 'address' => 'KEURMASSAR', 'latitude' => '14.77220400', 'longitude' => '-17.31540600', 'is_active' => true],
            ['name' => 'BAR ROYAUME DU PORC', 'address' => 'GRAND-YOFF', 'latitude' => '14.73781940', 'longitude' => '-17.44354840', 'is_active' => true],
            ['name' => 'BAR SAMARITIN', 'address' => 'LIBERT 3', 'latitude' => '14.80691000', 'longitude' => '-17.33091000', 'is_active' => true],
            ['name' => 'BAR SANTHIABA', 'address' => 'GRAND-YOFF', 'latitude' => '14.73728040', 'longitude' => '-17.44473470', 'is_active' => true],
            ['name' => 'BAR SET SET', 'address' => 'PARCELLES ASSAINIES U 21', 'latitude' => '14.75577350', 'longitude' => '-17.44484940', 'is_active' => true],
            ['name' => 'BAR TERANGA', 'address' => 'KEUR MASSAR', 'latitude' => '14.75086360', 'longitude' => '-17.31027240', 'is_active' => true],
            ['name' => 'BAR TITANIUM', 'address' => 'KOUNOUNE', 'latitude' => '14.75624470', 'longitude' => '-17.26124460', 'is_active' => true],
            ['name' => 'BAR UMIRAN', 'address' => 'PARCELLES ASSAINIES U 17', 'latitude' => '14.75670580', 'longitude' => '-17.44067230', 'is_active' => true],
            ['name' => 'BAR YAKAR', 'address' => 'KEURMASSAR', 'latitude' => '14.77101100', 'longitude' => '-17.31500930', 'is_active' => true],
            ['name' => 'CASA BAR', 'address' => 'GRAND-YOFF', 'latitude' => '14.73757470', 'longitude' => '-17.44477900', 'is_active' => true],
            ['name' => 'CHEZ HENRIETTE', 'address' => 'GRAND-YOFF', 'latitude' => '14.73826590', 'longitude' => '-17.45183280', 'is_active' => true],
            ['name' => 'CHEZ JEAN', 'address' => 'THIAROYE', 'latitude' => '14.75173420', 'longitude' => '-17.38122800', 'is_active' => true],
            ['name' => 'CHEZ MANOU', 'address' => 'GRAND-YOFF', 'latitude' => '14.73444940', 'longitude' => '-17.45395840', 'is_active' => true],
            ['name' => 'CHEZ MARCEL', 'address' => 'GUEDIAWAYE', 'latitude' => '14.76825000', 'longitude' => '-17.38950000', 'is_active' => true],
            ['name' => 'COUCOU LE JOIE', 'address' => 'GRAND-YOFF', 'latitude' => '14.73280000', 'longitude' => '-17.45620000', 'is_active' => true],
            ['name' => 'Jean Luc Houédanou', 'address' => 'Treichville rue des carrossiers,immeuble habitat africain', 'latitude' => '5.29489554', 'longitude' => '-3.99675161', 'is_active' => true],
            ['name' => 'JL', 'address' => 'cic', 'latitude' => '5.74717408', 'longitude' => '-4.02099609', 'is_active' => true],
        ];

        $created = 0;
        $updated = 0;

        foreach ($venues as $venue) {
            $bar = Bar::updateOrCreate(
                ['name' => $venue['name'], 'address' => $venue['address']], // Unique key
                $venue // All data to update/create
            );

            if ($bar->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Bars: {$created} created, {$updated} updated (Total: " . count($venues) . ")");
        $this->command->info('📍 Zones couvertes: Grand-Yoff, Ouakam, Parcelles Assainies, Keur Massar, Guediawaye, Thiaroye, Malika, HLM, Liberté, et plus');
    }
}
