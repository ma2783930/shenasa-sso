<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class ConfigurationException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = trans('sso::errors.configuration');
        }
        parent::__construct($message, $code, $previous);
    }
}
