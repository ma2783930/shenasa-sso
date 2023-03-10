<?php

namespace Shenasa\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redirect;
use Shenasa\Actions\SsoActiveUserProviderAction;
use Shenasa\Actions\SsoCallbackFailureAction;
use Shenasa\Actions\SsoLoginAction;
use Shenasa\Actions\SsoLogoutAction;
use Shenasa\Actions\SsoUserFinderAction;
use Shenasa\Facades\Sso;

class SsoAuthController extends BaseController
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        return Redirect::to(
            Sso::generateLoginUrl()
        );
    }

    /**
     * @return void
     */
    public function logout(SsoActiveUserProviderAction $ssoActiveUserProviderAction, SsoLogoutAction $logoutAction)
    {
        [$user, $username, $identifyCode] = call_user_func($ssoActiveUserProviderAction);
        $loggedOut = Sso::logout($username, $identifyCode);

        if ($loggedOut) {
            return call_user_func($logoutAction, $user, $username, $identifyCode);
        }
    }

    /**
     * @param \Illuminate\Http\Request                  $request
     * @param \Shenasa\Actions\SsoUserFinderAction      $userFinderAction
     * @param \Shenasa\Actions\SsoLoginAction           $ssoLoginAction
     * @param \Shenasa\Actions\SsoCallbackFailureAction $callbackFailureAction
     * @return mixed
     */
    public function callback(Request $request, SsoUserFinderAction $userFinderAction, SsoLoginAction $ssoLoginAction, SsoCallbackFailureAction $callbackFailureAction)
    {
        $state = $request->get('state');
        $code  = $request->get('code');

        $userInfo = Sso::validateLoginCode($state, $code, $userFinderAction);

        if (!!$userInfo) {
            [$user, $username, $identifyCode] = $userInfo;
            return call_user_func($ssoLoginAction, $request, $user, $username, $identifyCode);
        }

        return call_user_func($callbackFailureAction, $request);
    }
}
