<?php

namespace Shenasa\Contracts;

use Illuminate\Http\Request;

interface SsoCallbackFailureContract
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request);
}
