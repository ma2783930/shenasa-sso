<?php

namespace Shenasa\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Shenasa\Contracts\SsoLoginActionContract;

class SsoLoginAction implements SsoLoginActionContract
{
    /**
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string                                     $username
     * @param string                                     $identifyCode
     */
    public function __invoke(Request $request, Authenticatable $user, string $username, string $identifyCode)
    {
        //
    }
}
