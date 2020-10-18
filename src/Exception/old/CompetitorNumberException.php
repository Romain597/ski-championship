<?php

declare(strict_types=1);

namespace App\Exception;

class CompetitorNumberException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null) {
        if (trim($message) == '') {
            $message = 'Class Competitor number parameter is negative or is equal to zero.';
        }
        parent::__construct($message, $code, $previous);
    }
}
