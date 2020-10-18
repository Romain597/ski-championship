<?php

declare(strict_types=1);

namespace App\Exception;

class CompetitorEmptyException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null) {
        if (trim($message) == '') {
            $message = 'Class Competitor parameter is empty.';
        }
        parent::__construct($message, $code, $previous);
    }
}
