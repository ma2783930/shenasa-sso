<?php

namespace Shenasa\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

interface SsoLoginActionContract
{
    /**
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string                                     $username
     * @param string                                     $identifyCode
     */
    public function __invoke(Request $request, Authenticatable $user, string $username, string $identifyCode);
}
