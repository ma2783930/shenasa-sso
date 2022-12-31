<?php

namespace Shenasa\Contracts;

interface SsoActiveUserProviderContract
{
    /**
     * @return array
     */
    public function __invoke(): array;
}
