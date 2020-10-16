<?php

declare(strict_types=1);

namespace App\Exception;

class CompetitorDataFormatException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null) {
        if (trim($message) == '') {
            $message = 'Class Competitor has parameter not conform to data format.';
        }
        parent::__construct($message, $code, $previous);
    }
}
