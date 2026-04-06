<?php

namespace App\Exceptions;

use Exception;

class QrRegenerationNoActiveSubscriptionException extends Exception
{
    public function __construct()
    {
        parent::__construct('Aucun abonnement actif. La régénération du QR code n\'est pas disponible.');
    }
}
