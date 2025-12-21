<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bar;
use App\Models\MatchGame;
use App\Models\Team;
use App\Models\Animation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AnimationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Données exactes du CSV fourni par l'utilisateur
        $animationsData = [
            ['venue_name' => 'CHEZ JEAN', 'zone' => 'THIAROYE', 'date' => '23/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'BOTSWANA', 'latitude' => '14.7517342', 'longitude' => '-17.381228'],
            ['venue_name' => 'BAR BONGRE', 'zone' => 'TIVAOUNE PEUL', 'date' => '26/12/2025', 'time' => '15 H', 'team_1' => 'AFRIQUE DU SUD', 'team_2' => 'EGYPTE', 'latitude' => '14.7880784', 'longitude' => '-17.2884962'],
            ['venue_name' => 'BAR CHEZ HENRI', 'zone' => 'SEBIKOTANE', 'date' => '27/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'RD CONGO', 'latitude' => '14.75083', 'longitude' => '-17.4558011'],
            ['venue_name' => 'BAR CHEZ PREIRA', 'zone' => 'KEUR MBAYE FALL', 'date' => '28/12/2025', 'time' => '20 H', 'team_1' => 'COTE D\'IVOIRE', 'team_2' => 'CAMEROUN', 'latitude' => '14.7498845', 'longitude' => '-17.3440214'],
            ['venue_name' => 'BAR KAMIEUM', 'zone' => 'THIAROYE', 'date' => '30/12/2025', 'time' => '19 H', 'team_1' => 'SENEGAL', 'team_2' => 'BENIN', 'latitude' => '14.7642474', 'longitude' => '-17.3732367'],
            ['venue_name' => 'BAR ALLIANCE', 'zone' => 'KEUR MBAYE FALL', 'date' => '03/01/2026', 'time' => '16 H', 'team_1' => 'HUITIEME DE FINALE', 'team_2' => '', 'latitude' => '14.7407892', 'longitude' => '-17.3234235'],
            ['venue_name' => 'BAR CHEZ TANTI', 'zone' => 'THIAROYE', 'date' => '09/01/2026', 'time' => '16 H', 'team_1' => 'QUART DE FINALE', 'team_2' => '', 'latitude' => '14.7669105', 'longitude' => '-17.3801388'],
            ['venue_name' => 'BAR BLEUKEUSSS', 'zone' => 'DIAMEGEUNE', 'date' => '14/01/2026', 'time' => '16 H', 'team_1' => 'DEMI FINALE', 'team_2' => '', 'latitude' => '14.7652458', 'longitude' => '-17.4457674'],
            ['venue_name' => 'CHEZ JEAN', 'zone' => 'THIAROYE', 'date' => '17/01/2026', 'time' => '16 H', 'team_1' => 'TROISIEME PLACE', 'team_2' => '', 'latitude' => '14.7517342', 'longitude' => '-17.381228'],
            ['venue_name' => 'BAR CHEZ PREIRA', 'zone' => 'KEUR MBAYE FALL', 'date' => '18/01/2026', 'time' => '16 H', 'team_1' => 'FINALE', 'team_2' => '', 'latitude' => '14.7498845', 'longitude' => '-17.3440214'],
            ['venue_name' => 'BAR FOUGON 2', 'zone' => 'MALIKA', 'date' => '23/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'BOTSWANA', 'latitude' => '14.7922816', 'longitude' => '-17.3289989'],
            ['venue_name' => 'BAR JOE BASS', 'zone' => 'KEUR MASSAR', 'date' => '26/12/2025', 'time' => '15 H', 'team_1' => 'AFRIQUE DU SUD', 'team_2' => 'EGYPTE', 'latitude' => '14.7778322', 'longitude' => '-17.33062'],
            ['venue_name' => 'BAR CHEZ MILI', 'zone' => 'MALIKA', 'date' => '27/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'RD CONGO', 'latitude' => '14.7508245', 'longitude' => '-17.4557677'],
            ['venue_name' => 'BAR TERANGA', 'zone' => 'KEUR MASSAR', 'date' => '28/12/2025', 'time' => '20 H', 'team_1' => 'COTE D\'IVOIRE', 'team_2' => 'CAMEROUN', 'latitude' => '14.7508636', 'longitude' => '-17.3102724'],
            ['venue_name' => 'BAR BAKASAO', 'zone' => 'MALIKA', 'date' => '30/12/2025', 'time' => '19 H', 'team_1' => 'SENEGAL', 'team_2' => 'BENIN', 'latitude' => '14.7508286', 'longitude' => '-17.4557744'],
            ['venue_name' => 'BAR KAWARAFAN', 'zone' => 'KEUR MASSAR', 'date' => '03/01/2026', 'time' => '16 H', 'team_1' => 'HUITIEME DE FINALE', 'team_2' => '', 'latitude' => '14.7644153', 'longitude' => '-17.3052866'],
            ['venue_name' => 'BAR CHEZ ALICE', 'zone' => 'KEUR MASSAR', 'date' => '09/01/2026', 'time' => '16 H', 'team_1' => 'QUART DE FINALE', 'team_2' => '', 'latitude' => '14.7612882', 'longitude' => '-17.2841361'],
            ['venue_name' => 'BAR TITANIUM', 'zone' => 'KOUNOUNE', 'date' => '14/01/2026', 'time' => '16 H', 'team_1' => 'DEMI FINALE', 'team_2' => '', 'latitude' => '14.7562447', 'longitude' => '-17.2612446'],
            ['venue_name' => 'BAR CONCENSUS', 'zone' => 'KEUR MASSAR', 'date' => '17/01/2026', 'time' => '16 H', 'team_1' => 'TROISIEME PLACE', 'team_2' => '', 'latitude' => '14.7738608', 'longitude' => '-17.3208291'],
            ['venue_name' => 'BAR FOUGON 2', 'zone' => 'MALIKA', 'date' => '18/01/2026', 'time' => '16 H', 'team_1' => 'FINALE', 'team_2' => '', 'latitude' => '14.7922816', 'longitude' => '-17.3289989'],
            ['venue_name' => 'BAR POPEGUINE', 'zone' => 'KEUR MASSAR', 'date' => '23/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'BOTSWANA', 'latitude' => '14.7722491', 'longitude' => '-17.3154377'],
            ['venue_name' => 'BAR YAKAR', 'zone' => 'KEURMASSAR', 'date' => '26/12/2025', 'time' => '15 H', 'team_1' => 'AFRIQUE DU SUD', 'team_2' => 'EGYPTE', 'latitude' => '14.771011', 'longitude' => '-17.3150093'],
            ['venue_name' => 'BAR BAZILE', 'zone' => 'GUEDIAWAYE', 'date' => '27/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'RD CONGO', 'latitude' => '14.7813471', 'longitude' => '-17.3755211'],
            ['venue_name' => 'BAR POPEGUINE', 'zone' => 'KEURMASSAR', 'date' => '28/12/2025', 'time' => '20 H', 'team_1' => 'COTE D\'IVOIRE', 'team_2' => 'CAMEROUN', 'latitude' => '14.772204', 'longitude' => '-17.315406'],
            ['venue_name' => 'BAR CHEZ PASCAL', 'zone' => 'GUEDIAWAYE', 'date' => '30/12/2025', 'time' => '19 H', 'team_1' => 'SENEGAL', 'team_2' => 'BENIN', 'latitude' => '14.785374', 'longitude' => '-17.378309'],
            ['venue_name' => 'BAR KAPOL', 'zone' => 'GUEDIAWAYE', 'date' => '03/01/2026', 'time' => '16 H', 'team_1' => 'HUITIEME DE FINALE', 'team_2' => '', 'latitude' => '14.776948', 'longitude' => '-17.377118'],
            ['venue_name' => 'CHEZ MARCEL', 'zone' => 'GUEDIAWAYE', 'date' => '09/01/2026', 'time' => '16 H', 'team_1' => 'QUART DE FINALE', 'team_2' => '', 'latitude' => '14.76825', 'longitude' => '-17.3895'],
            ['venue_name' => 'BAR ELTON', 'zone' => 'GUEDIAWAYE', 'date' => '14/01/2026', 'time' => '16 H', 'team_1' => 'DEMI FINALE', 'team_2' => '', 'latitude' => '14.785426', 'longitude' => '-17.3783207'],
            ['venue_name' => 'BAR BOUELO', 'zone' => 'GUEDIAWAYE', 'date' => '17/01/2026', 'time' => '16 H', 'team_1' => 'TROISIEME PLACE', 'team_2' => '', 'latitude' => '14.6761585', 'longitude' => '-17.4477634'],
            ['venue_name' => 'BAR POPEGUINE', 'zone' => 'KEUR MASSAR', 'date' => '18/01/2026', 'time' => '16 H', 'team_1' => 'FINALE', 'team_2' => '', 'latitude' => '14.6815672', 'longitude' => '-17.4544187'],
            ['venue_name' => 'BAR OUTHEKOR', 'zone' => 'GRAND-YOFF', 'date' => '23/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'BOTSWANA', 'latitude' => '14.736913', 'longitude' => '-17.4467729'],
            ['venue_name' => 'CHEZ HENRIETTE', 'zone' => 'GRAND-YOFF', 'date' => '26/12/2025', 'time' => '15 H', 'team_1' => 'AFRIQUE DU SUD', 'team_2' => 'EGYPTE', 'latitude' => '14.7382659', 'longitude' => '-17.4518328'],
            ['venue_name' => 'CASA BAR', 'zone' => 'GRAND-YOFF', 'date' => '27/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'RD CONGO', 'latitude' => '14.7375747', 'longitude' => '-17.444779'],
            ['venue_name' => 'BAR KAMEME', 'zone' => 'GRAND-YOFF', 'date' => '28/12/2025', 'time' => '20 H', 'team_1' => 'COTE D\'IVOIRE', 'team_2' => 'CAMEROUN', 'latitude' => '14.7343559', 'longitude' => '-17.4462383'],
            ['venue_name' => 'CHEZ MANOU', 'zone' => 'GRAND-YOFF', 'date' => '30/12/2025', 'time' => '19 H', 'team_1' => 'SENEGAL', 'team_2' => 'BENIN', 'latitude' => '14.7344494', 'longitude' => '-17.4539584'],
            ['venue_name' => 'COUCOU LE JOIE', 'zone' => 'GRAND-YOFF', 'date' => '03/01/2026', 'time' => '16 H', 'team_1' => 'HUITIEME DE FINALE', 'team_2' => '', 'latitude' => '14.7328', 'longitude' => '-17.4562'],
            ['venue_name' => 'BAR EDIOUNGOU', 'zone' => 'GRAND-YOFF', 'date' => '09/01/2026', 'time' => '16 H', 'team_1' => 'QUART DE FINALE', 'team_2' => '', 'latitude' => '14.7375483', 'longitude' => '-17.4481482'],
            ['venue_name' => 'BAR AWARA', 'zone' => 'GRAND-YOFF', 'date' => '14/01/2026', 'time' => '16 H', 'team_1' => 'DEMI FINALE', 'team_2' => '', 'latitude' => '14.7416678', 'longitude' => '-17.4444997'],
            ['venue_name' => 'BAR ROYAUME DU PORC', 'zone' => 'GRAND-YOFF', 'date' => '17/01/2026', 'time' => '16 H', 'team_1' => 'TROISIEME PLACE', 'team_2' => '', 'latitude' => '14.7378194', 'longitude' => '-17.4435484'],
            ['venue_name' => 'BAR SANTHIABA', 'zone' => 'GRAND-YOFF', 'date' => '18/01/2026', 'time' => '16 H', 'team_1' => 'FINALE', 'team_2' => '', 'latitude' => '14.7372804', 'longitude' => '-17.4447347'],
            ['venue_name' => 'BAR ETALON', 'zone' => 'GRAND-DAKAR', 'date' => '23/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'BOTSWANA', 'latitude' => '14.6917911', 'longitude' => '-17.4337784'],
            ['venue_name' => 'BAR CHEZ JEAN', 'zone' => 'GRAND-DAKAR', 'date' => '26/12/2025', 'time' => '15 H', 'team_1' => 'AFRIQUE DU SUD', 'team_2' => 'EGYPTE', 'latitude' => '14.7382449', 'longitude' => '-17.4518402'],
            ['venue_name' => 'BAR BANDIAL', 'zone' => 'REUBEUSS', 'date' => '27/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'RD CONGO', 'latitude' => '14.6704586', 'longitude' => '-17.4414847'],
            ['venue_name' => 'BAR BISTRO', 'zone' => 'SICAP LIBERTE 5', 'date' => '28/12/2025', 'time' => '20 H', 'team_1' => 'COTE D\'IVOIRE', 'team_2' => 'CAMEROUN', 'latitude' => '14.7090235', 'longitude' => '-17.4582593'],
            ['venue_name' => 'BAR CHEZ CATHO', 'zone' => 'LIBERTE 5', 'date' => '30/12/2025', 'time' => '19 H', 'team_1' => 'SENEGAL', 'team_2' => 'BENIN', 'latitude' => '14.7215509', 'longitude' => '-17.4628454'],
            ['venue_name' => 'BAR CHEZ GUILLAINE', 'zone' => 'HLM', 'date' => '03/01/2026', 'time' => '16 H', 'team_1' => 'HUITIEME DE FINALE', 'team_2' => '', 'latitude' => '14.70865', 'longitude' => '-17.446952'],
            ['venue_name' => 'BAR ETALON', 'zone' => 'GRAND-DAKAR', 'date' => '09/01/2026', 'time' => '16 H', 'team_1' => 'QUART DE FINALE', 'team_2' => '', 'latitude' => '14.700343', 'longitude' => '-17.4554238'],
            ['venue_name' => 'BAR SAMARITIN', 'zone' => 'LIBERT 3', 'date' => '14/01/2026', 'time' => '16 H', 'team_1' => 'DEMI FINALE', 'team_2' => '', 'latitude' => '14.80691', 'longitude' => '-17.33091'],
            ['venue_name' => 'BAR CHEZ JEAN', 'zone' => 'GRAND-DAKAR', 'date' => '17/01/2026', 'time' => '16 H', 'team_1' => 'TROISIEME PLACE', 'team_2' => '', 'latitude' => '14.7517342', 'longitude' => '-17.381228'],
            ['venue_name' => 'BAR ETALON', 'zone' => 'GRAND-DAKAR', 'date' => '18/01/2026', 'time' => '16 H', 'team_1' => 'FINALE', 'team_2' => '', 'latitude' => '14.6917911', 'longitude' => '-17.4337784'],
            ['venue_name' => 'BAR UMIRAN', 'zone' => 'PARCELLES ASSAINIES U 17', 'date' => '23/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'BOTSWANA', 'latitude' => '14.7567058', 'longitude' => '-17.4406723'],
            ['venue_name' => 'BAR LA GOREENNE', 'zone' => 'PATTE D\'OIE', 'date' => '26/12/2025', 'time' => '15 H', 'team_1' => 'AFRIQUE DU SUD', 'team_2' => 'EGYPTE', 'latitude' => '14.7476696', 'longitude' => '-17.4432123'],
            ['venue_name' => 'BAR DAKHARGUI', 'zone' => 'PARCELLES ASSAINIES U 17', 'date' => '27/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'RD CONGO', 'latitude' => '14.757696', 'longitude' => '-17.439845'],
            ['venue_name' => 'BAR ETHIOUNG', 'zone' => 'PARCELLES ASSAINIES U 7', 'date' => '28/12/2025', 'time' => '20 H', 'team_1' => 'COTE D\'IVOIRE', 'team_2' => 'CAMEROUN', 'latitude' => '14.72545', 'longitude' => '-17.442953'],
            ['venue_name' => 'BAR MONTAGNE', 'zone' => 'PARCELLES ASSAINIES U 26', 'date' => '30/12/2025', 'time' => '19 H', 'team_1' => 'SENEGAL', 'team_2' => 'BENIN', 'latitude' => '14.756638', 'longitude' => '-17.441177'],
            ['venue_name' => 'BAR KANDJIDIASSA', 'zone' => 'PARCELLES ASSAINIES U 19', 'date' => '03/01/2026', 'time' => '16 H', 'team_1' => 'HUITIEME DE FINALE', 'team_2' => '', 'latitude' => '14.75513', 'longitude' => '-17.451919'],
            ['venue_name' => 'BAR DAKHARGUI', 'zone' => 'PARCELLES ASSAINIES U 17', 'date' => '09/01/2026', 'time' => '16 H', 'team_1' => 'QUART DE FINALE', 'team_2' => '', 'latitude' => '14.7575839', 'longitude' => '-17.4399306'],
            ['venue_name' => 'BAR KADETH', 'zone' => 'PARCELLES ASSAINIES U 12', 'date' => '14/01/2026', 'time' => '16 H', 'team_1' => 'DEMI FINALE', 'team_2' => '', 'latitude' => '14.7573599', 'longitude' => '-17.4417203'],
            ['venue_name' => 'BAR CHEZ VINCENT', 'zone' => 'PARCELLES ASSAINIES U 24', 'date' => '17/01/2026', 'time' => '16 H', 'team_1' => 'TROISIEME PLACE', 'team_2' => '', 'latitude' => '14.7536497', 'longitude' => '-17.4467705'],
            ['venue_name' => 'BAR UMIRAN', 'zone' => 'PARCELLES ASSAINIES U 17', 'date' => '18/01/2026', 'time' => '16 H', 'team_1' => 'FINALE', 'team_2' => '', 'latitude' => '14.7567058', 'longitude' => '-17.4406723'],
            ['venue_name' => 'BAR SET SET', 'zone' => 'PARCELLES ASSAINIES U 21', 'date' => '23/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'BOTSWANA', 'latitude' => '14.7557735', 'longitude' => '-17.4448494'],
            ['venue_name' => 'BAR CASA ESTANCIA', 'zone' => 'PARCELLES ASSAINIES U 10', 'date' => '26/12/2025', 'time' => '15 H', 'team_1' => 'AFRIQUE DU SUD', 'team_2' => 'EGYPTE', 'latitude' => '14.76136', 'longitude' => '-17.4337118'],
            ['venue_name' => 'BAR CHEZ FRANCOIS', 'zone' => 'CITE FADIA', 'date' => '27/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'RD CONGO', 'latitude' => '14.7095886', 'longitude' => '-17.4523725'],
            ['venue_name' => 'BAR CASA ESTANCIA', 'zone' => 'PARCELLES ASSAINIES U 10', 'date' => '28/12/2025', 'time' => '20 H', 'team_1' => 'COTE D\'IVOIRE', 'team_2' => 'CAMEROUN', 'latitude' => '14.76136', 'longitude' => '-17.4337118'],
            ['venue_name' => 'BAR SET SET', 'zone' => 'PARCELLES ASSAINIES U 21', 'date' => '30/12/2025', 'time' => '19 H', 'team_1' => 'SENEGAL', 'team_2' => 'BENIN', 'latitude' => '14.7557735', 'longitude' => '-17.4448494'],
            ['venue_name' => 'BAR CHEZ VALERIE', 'zone' => 'ROND POINT CASE', 'date' => '03/01/2026', 'time' => '16 H', 'team_1' => 'HUITIEME DE FINALE', 'team_2' => '', 'latitude' => '14.7577258', 'longitude' => '-17.4285123'],
            ['venue_name' => 'BAR CHEZ FRANCOIS', 'zone' => 'CITE FADIA', 'date' => '09/01/2026', 'time' => '16 H', 'team_1' => 'QUART DE FINALE', 'team_2' => '', 'latitude' => '14.7095886', 'longitude' => '-17.4523725'],
            ['venue_name' => 'BAR MAISON BLANCHE', 'zone' => 'PARCELLES U 10', 'date' => '14/01/2026', 'time' => '16 H', 'team_1' => 'DEMI FINALE', 'team_2' => '', 'latitude' => '14.7617111', 'longitude' => '-17.4365972'],
            ['venue_name' => 'BAR CHEZ VALERIE', 'zone' => 'ROND POINT CASE', 'date' => '17/01/2026', 'time' => '16 H', 'team_1' => 'TROISIEME PLACE', 'team_2' => '', 'latitude' => '14.7577274', 'longitude' => '-17.4285475'],
            ['venue_name' => 'BAR SET SET', 'zone' => 'PARECELLES U 21', 'date' => '18/01/2026', 'time' => '16 H', 'team_1' => 'FINALE', 'team_2' => '', 'latitude' => '14.7557735', 'longitude' => '-17.4448494'],
            ['venue_name' => 'BAR JOYCE', 'zone' => 'OUAKAM', 'date' => '23/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'BOTSWANA', 'latitude' => '14.6928039', 'longitude' => '-17.4603993'],
            ['venue_name' => 'BAR JEROME', 'zone' => 'OUAKAM', 'date' => '26/12/2025', 'time' => '15 H', 'team_1' => 'AFRIQUE DU SUD', 'team_2' => 'EGYPTE', 'latitude' => '14.7269138', 'longitude' => '-17.4828138'],
            ['venue_name' => 'BAR JEROME', 'zone' => 'OUAKAM', 'date' => '27/12/2025', 'time' => '15 H', 'team_1' => 'SENEGAL', 'team_2' => 'RD CONGO', 'latitude' => '14.7269138', 'longitude' => '-17.4828138'],
            ['venue_name' => 'BAR LE BOURBEOIS', 'zone' => 'OUAKAM', 'date' => '28/12/2025', 'time' => '20 H', 'team_1' => 'COTE D\'IVOIRE', 'team_2' => 'CAMEROUN', 'latitude' => '14.7274408', 'longitude' => '-17.4838794'],
            ['venue_name' => 'BAR JOYCE', 'zone' => 'OUAKAM', 'date' => '30/12/2025', 'time' => '19 H', 'team_1' => 'SENEGAL', 'team_2' => 'BENIN', 'latitude' => '14.6928039', 'longitude' => '-17.4603993'],
            ['venue_name' => 'BAR JEROME', 'zone' => 'OUAKAM', 'date' => '03/01/2026', 'time' => '16 H', 'team_1' => 'HUITIEME DE FINALE', 'team_2' => '', 'latitude' => '14.7269138', 'longitude' => '-17.4828138'],
            ['venue_name' => 'BAR CHEZ LOPY', 'zone' => 'OUAKAM', 'date' => '09/01/2026', 'time' => '16 H', 'team_1' => 'QUART DE FINALE', 'team_2' => '', 'latitude' => '14.72', 'longitude' => '-17.48'],
            ['venue_name' => 'BAR JOYCE', 'zone' => 'OUAKAM', 'date' => '14/01/2026', 'time' => '16 H', 'team_1' => 'DEMI FINALE', 'team_2' => '', 'latitude' => '14.6928039', 'longitude' => '-17.4603993'],
            ['venue_name' => 'BAR AWALE', 'zone' => 'OUAKAM', 'date' => '17/01/2026', 'time' => '16 H', 'team_1' => 'TROISIEME PLACE', 'team_2' => '', 'latitude' => '14.725', 'longitude' => '-17.481'],
            ['venue_name' => 'BAR JEROME', 'zone' => 'OUAKAM', 'date' => '18/01/2026', 'time' => '16 H', 'team_1' => 'FINALE', 'team_2' => '', 'latitude' => '14.7269138', 'longitude' => '-17.4828138'],
        ];

        $createdAnimations = 0;
        $skippedAnimations = 0;
        $errors = [];

        foreach ($animationsData as $data) {
            try {
                // 1. Trouver le bar par nom et zone
                $bar = Bar::where('name', $data['venue_name'])
                    ->where('address', $data['zone'])
                    ->first();

                if (!$bar) {
                    // Créer le bar s'il n'existe pas
                    $bar = Bar::create([
                        'name' => $data['venue_name'],
                        'address' => $data['zone'],
                        'zone' => $data['zone'],
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'is_active' => true,
                    ]);
                }

                // 2. Trouver le match
                $match = $this->findMatchByTeams($data['team_1'], $data['team_2'], $data['date'], $data['time']);

                if (!$match) {
                    $errors[] = "Match introuvable: {$data['team_1']} vs {$data['team_2']} le {$data['date']} à {$data['time']} pour {$data['venue_name']}";
                    $skippedAnimations++;
                    continue;
                }

                // 3. Créer l'animation
                $animation = Animation::updateOrCreate(
                    [
                        'bar_id' => $bar->id,
                        'match_id' => $match->id,
                    ],
                    [
                        'animation_date' => $this->parseDate($data['date']),
                        'animation_time' => $data['time'],
                        'is_active' => true,
                    ]
                );

                if ($animation->wasRecentlyCreated) {
                    $createdAnimations++;
                }

            } catch (\Exception $e) {
                $errors[] = "Erreur pour {$data['venue_name']}: " . $e->getMessage();
                $skippedAnimations++;
            }
        }

        // Affichage du résumé
        $this->command->info("===== Animation Seeding Summary =====");
        $this->command->info("Animations créées: {$createdAnimations}");
        $this->command->info("Animations ignorées: {$skippedAnimations}");
        $this->command->info("Total animations dans CSV: " . count($animationsData));

        if (count($errors) > 0) {
            $this->command->warn("\nErreurs rencontrées:");
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->command->error($error);
            }
            if (count($errors) > 10) {
                $this->command->warn("... et " . (count($errors) - 10) . " autres erreurs");
            }
        }
    }

    /**
     * Trouver un match par équipes et date
     */
    private function findMatchByTeams(string $team1, string $team2, string $date, string $time): ?MatchGame
    {
        // Normaliser les noms d'équipes
        $team1Normalized = $this->normalizeTeamName($team1);
        $team2Normalized = $this->normalizeTeamName($team2);

        // Parser la date
        $matchDateTime = $this->parseDateTime($date, $time);

        // Si team2 est vide, c'est un match de phase finale
        if (empty($team2)) {
            $phase = $this->getPhaseFromName($team1);
            return MatchGame::where('phase', $phase)
                ->whereDate('match_date', $matchDateTime->format('Y-m-d'))
                ->first();
        }

        // Sinon, chercher par équipes
        $homeTeam = Team::where('name', $team1Normalized)->first();
        $awayTeam = Team::where('name', $team2Normalized)->first();

        if (!$homeTeam || !$awayTeam) {
            return null;
        }

        return MatchGame::where(function ($query) use ($homeTeam, $awayTeam) {
            $query->where('home_team_id', $homeTeam->id)
                  ->where('away_team_id', $awayTeam->id);
        })
        ->orWhere(function ($query) use ($homeTeam, $awayTeam) {
            $query->where('home_team_id', $awayTeam->id)
                  ->where('away_team_id', $homeTeam->id);
        })
        ->first();
    }

    /**
     * Normaliser le nom d'une équipe
     */
    private function normalizeTeamName(string $teamName): string
    {
        $nameMap = [
            'SENEGAL' => 'Sénégal',
            'SÉNÉGAL' => 'Sénégal',
            'AFRIQUE DU SUD' => 'Afrique du Sud',
            'EGYPTE' => 'Égypte',
            'EGYPE' => 'Égypte',
            'ÉGYPTE' => 'Égypte',
            'RD CONGO' => 'RD Congo',
            'RDC' => 'RD Congo',
            'COTE D\'IVOIRE' => 'Côte d\'Ivoire',
            'CÔTE D\'IVOIRE' => 'Côte d\'Ivoire',
            'CAMEROUN' => 'Cameroun',
            'BENIN' => 'Bénin',
            'BÉNIN' => 'Bénin',
            'BOTSWANA' => 'Botswana',
        ];

        $normalized = strtoupper(trim($teamName));
        return $nameMap[$normalized] ?? $teamName;
    }

    /**
     * Obtenir la phase depuis le nom
     */
    private function getPhaseFromName(string $phaseName): string
    {
        $phaseMap = [
            'HUITIEME DE FINALE' => 'round_of_16',
            'QUART DE FINALE' => 'quarter_final',
            'DEMI FINALE' => 'semi_final',
            'TROISIEME PLACE' => 'third_place',
            'FINALE' => 'final',
        ];

        $normalized = strtoupper(trim($phaseName));
        return $phaseMap[$normalized] ?? 'group_stage';
    }

    /**
     * Parser une date du format DD/MM/YYYY
     */
    private function parseDate(string $date): Carbon
    {
        [$day, $month, $year] = explode('/', $date);
        return Carbon::create($year, $month, $day, 0, 0, 0);
    }

    /**
     * Parser date et heure
     */
    private function parseDateTime(string $date, string $time): Carbon
    {
        [$day, $month, $year] = explode('/', $date);
        $hour = intval(trim(str_replace('H', '', $time)));
        return Carbon::create($year, $month, $day, $hour, 0, 0);
    }
}
