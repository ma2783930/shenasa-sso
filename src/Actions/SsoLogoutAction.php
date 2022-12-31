<?php

namespace Shenasa\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Shenasa\Contracts\SsoLogoutActionContract;

class SsoLogoutAction implements SsoLogoutActionContract
{
    public function __invoke(Authenticatable $user, string $username, string $identifyCode)
    {
        //
    }
}
