<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class TokenVerificationException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = __('Invalid License');
        }
        parent::__construct($message, $code, $previous);
    }
}
