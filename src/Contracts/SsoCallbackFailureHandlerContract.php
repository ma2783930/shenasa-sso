<?php

namespace Shenasa\Contracts;

use Illuminate\Http\Request;

interface SsoCallbackFailureHandlerContract
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request);
}
