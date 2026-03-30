<?php

namespace App\Exceptions;

use RuntimeException;

class CheckinException extends RuntimeException
{
    public static function invalidQrToken(): self
    {
        return new self('QR code invalide ou inconnu.');
    }

    public static function noActiveSubscription(): self
    {
        return new self('Aucun abonnement actif. Renouvelez votre abonnement FitPass.');
    }

    public static function alreadyCheckedInToday(): self
    {
        return new self('Vous avez déjà validé votre entrée dans cette salle aujourd\'hui.');
    }

    public static function noCheckinsLeft(): self
    {
        return new self('Vous n\'avez plus de séances disponibles sur votre pass.');
    }
}
