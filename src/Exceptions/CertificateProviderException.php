<?php

namespace Shenasa\Exceptions;

use Exception;
use Throwable;

class CertificateProviderException extends  Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = trans('sso::errors.certificate_provider');
        }
        parent::__construct($message, $code, $previous);
    }
}
