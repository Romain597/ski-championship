<?php

declare(strict_types=1);

namespace App\Exception;

class CompetitorDateException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null) {
        if (trim($message) == '') {
            $message = 'Class Competitor date parameter is out of boundary.';
        }
        parent::__construct($message, $code, $previous);
    }
}
