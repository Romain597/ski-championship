<?php

declare(strict_types=1);

namespace App\Exception;

class ProfileEmptyException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null) {
        if (trim($message) == '') {
            $message = 'Class Profile parameter is empty.';
        }
        parent::__construct($message, $code, $previous);
    }
}
