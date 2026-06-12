<?php

namespace App\Console\Commands;

use App\Services\PasswordSmsService;
use Illuminate\Console\Command;

/**
 * Renvoie par SMS le code personnel des utilisateurs dont l'envoi a échoué
 * (sms_logs status=failed sans envoi réussi postérieur). Même logique que
 * le bouton Admin > Logs OTP ; version CLI pour usage serveur/scripté.
 */
class SendPasswordSms extends Command
{
    protected $signature = 'users:send-password-sms
                            {--dry-run : Liste les renvois prévus sans envoyer}';

    protected $description = 'Renvoie par SMS les codes personnels dont l\'envoi a échoué';

    public function handle(PasswordSmsService $sms): int
    {
        $pending = $sms->pendingFailures();

        if ($pending->isEmpty()) {
            $this->info('Aucun envoi de code en échec à relancer.');
            return self::SUCCESS;
        }

        $this->info($pending->count() . ' échec(s) en attente de renvoi.');

        $report = $sms->resendFailures((bool) $this->option('dry-run'));

        foreach ($report['resent'] as $line) {
            $this->line('  renvoyé : ' . $line);
        }
        foreach ($report['failed'] as $line) {
            $this->warn('  échec   : ' . $line);
        }
        foreach ($report['skipped'] as $line) {
            $this->warn('  ignoré  : ' . $line);
        }

        $this->newLine();
        $this->info(sprintf(
            'Renvoyés : %d — Échecs : %d — Ignorés : %d',
            count($report['resent']),
            count($report['failed']),
            count($report['skipped'])
        ));

        return self::SUCCESS;
    }
}
