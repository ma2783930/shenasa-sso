<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class LoginException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = trans('sso::errors.login_exception');
        }
        parent::__construct($message, $code, $previous);
    }
}
