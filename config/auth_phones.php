<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Numéros de téléphone autorisés pour les tests (Côte d'Ivoire)
    |--------------------------------------------------------------------------
    |
    | Ces numéros ivoiriens sont autorisés en mode test malgré la restriction
    | aux numéros sénégalais pour le grand public.
    |
    */
    'test_phones_ci' => [
        '+2250545029721',
        '+2250748348221',
    ],

    /*
    |--------------------------------------------------------------------------
    | Numéro de téléphone administrateur
    |--------------------------------------------------------------------------
    |
    | Ce numéro a accès à l'interface d'administration.
    | Doit correspondre à un utilisateur avec role='admin' dans la base.
    |
    */
    'admin_phone' => '+2250748348221',

    /*
    |--------------------------------------------------------------------------
    | Indicatifs de pays autorisés
    |--------------------------------------------------------------------------
    |
    | Par défaut, seul le Sénégal (+221) est autorisé pour les inscriptions
    | du grand public, sauf exceptions dans test_phones_ci.
    |
    */
    'allowed_country_code_public' => '+221',
    'allowed_country_code_admin' => '+225',
];
