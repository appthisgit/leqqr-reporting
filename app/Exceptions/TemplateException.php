<?php

namespace App\Exceptions;

use Throwable;

class TemplateException extends \Exception
{
    public function __construct(string $command, string $message, int $lineNumber, ?\Throwable $cause = null) {
        parent::__construct('<' . $command . '> ' . $message . ' at line ' . $lineNumber, 0, $cause);
    }
}
