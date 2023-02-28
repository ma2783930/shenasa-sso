<?php

namespace Shenasa\Actions;

use Illuminate\Http\Request;
use Shenasa\Contracts\SsoCallbackFailureHandlerContract;

class SsoCallbackFailureAction implements SsoCallbackFailureHandlerContract
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request)
    {
        abort(
            500,
            "SSO Unhandled Exception..."
        );
    }
}
