<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class UserGetterException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = trans('sso::errors.user_getter');
        }
        parent::__construct($message, $code, $previous);
    }
}
