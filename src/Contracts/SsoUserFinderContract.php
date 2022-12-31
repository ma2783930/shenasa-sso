<?php

namespace Shenasa\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface SsoUserFinderContract
{
    /**
     * @param string $username
     * @param string $identifyCode
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function __invoke(string $username, string $identifyCode): Authenticatable|null;
}
