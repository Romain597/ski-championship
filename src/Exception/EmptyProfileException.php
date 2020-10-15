<?php

declare(strict_types=1);

namespace App\Exception;

class EmptyProfileException extends \Exception
{
    public function __construct(string $message, int $code = 0, Exception $previous = null) {
        if (trim($message) == '') {
            $message = 'Class Profile parameter is empty.';
        }
        parent::__construct($message, $code, $previous);
    }
}
