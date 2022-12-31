<?php

namespace Shenasa\Actions;

use Shenasa\Contracts\SsoActiveUserProviderContract;

class SsoActiveUserProviderAction implements SsoActiveUserProviderContract
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [];
    }
}
