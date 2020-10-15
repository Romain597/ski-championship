<?php

declare(strict_types=1);

namespace App\Exception;

class BoundaryPassageException extends \Exception
{
    public function __construct(string $message, int $code = 0, Exception $previous = null) {
        if (trim($message) == '') {
            $message = 'Class Passage parameter is out of boundary.';
        }
        parent::__construct($message, $code, $previous);
    }
}
