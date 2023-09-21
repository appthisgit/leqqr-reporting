<?php

namespace App\Exceptions;

use Throwable;

class TemplateException extends \Exception
{
    public function __construct(string $command, string $message, ?\Throwable $cause = null) {
        parent::__construct('<' . $command . '> ' . $message, 0, $cause);
    }
}
