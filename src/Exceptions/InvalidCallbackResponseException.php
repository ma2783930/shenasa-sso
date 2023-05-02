<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class InvalidCallbackResponseException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = trans('sso::errors.invalid_callback_response');
        }
        parent::__construct($message, $code, $previous);
    }
}
