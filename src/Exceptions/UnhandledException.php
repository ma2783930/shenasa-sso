<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class UnhandledException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'sso::errors.unhandled_exception';
        }
        parent::__construct($message, $code, $previous);
    }
}
