<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class InvalidTokenException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = trans('sso::errors.invalid_token');
        }
        parent::__construct($message, $code, $previous);
    }
}
