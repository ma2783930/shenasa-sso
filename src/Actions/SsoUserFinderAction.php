<?php

namespace Shenasa\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Shenasa\Contracts\SsoUserFinderContract;

class SsoUserFinderAction implements SsoUserFinderContract
{
    public function __invoke(string $username, string $identifyCode): Authenticatable|null
    {
        return null;
    }
}
