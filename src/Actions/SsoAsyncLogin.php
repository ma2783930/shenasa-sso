<?php

namespace Shenasa\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Shenasa\Contracts\SsoAsyncLoginContract;

class SsoAsyncLogin implements SsoAsyncLoginContract
{
    /**
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string                                     $username
     * @param string                                     $identifyCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Authenticatable $user, string $username, string $identifyCode)
    {
        return response()->json([

        ]);
    }
}
