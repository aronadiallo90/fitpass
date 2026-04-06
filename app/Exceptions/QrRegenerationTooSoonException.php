<?php

namespace App\Exceptions;

use Carbon\Carbon;
use Exception;

class QrRegenerationTooSoonException extends Exception
{
    public function __construct(
        private readonly Carbon $nextAllowedAt
    ) {
        parent::__construct('QR code régénéré trop récemment. Réessayez dans 24h.');
    }

    public function getNextAllowedAt(): Carbon
    {
        return $this->nextAllowedAt;
    }
}
