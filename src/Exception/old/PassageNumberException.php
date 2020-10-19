<?php

declare(strict_types=1);

namespace App\Exception;

class PassageNumberException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null) {
        if (trim($message) == '') {
            $message = 'Class Passage parameter is negative.';
        }
        parent::__construct($message, $code, $previous);
    }
}
