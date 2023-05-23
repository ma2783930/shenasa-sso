<?php

namespace Shenasa\Actions;

use Illuminate\Http\Request;
use Shenasa\Contracts\SsoCallbackFailureContract;

class SsoCallbackFailureAction implements SsoCallbackFailureContract
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
