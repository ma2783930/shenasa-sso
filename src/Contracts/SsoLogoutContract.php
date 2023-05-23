<?php

namespace Shenasa\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface SsoLogoutContract
{
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string                                     $username
     * @param string                                     $identifyCode
     */
    public function __invoke(Authenticatable $user, string $username, string $identifyCode);
}
