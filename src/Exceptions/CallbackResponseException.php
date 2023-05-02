<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class CallbackResponseException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = trans('sso::errors.callback_response');
        }
        parent::__construct($message, $code, $previous);
    }
}
