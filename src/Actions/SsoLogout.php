<?php

namespace Shenasa\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Shenasa\Contracts\SsoLogoutContract;

class SsoLogout implements SsoLogoutContract
{
    public function __invoke(Authenticatable $user, string $username, string $identifyCode)
    {
        //
    }
}
