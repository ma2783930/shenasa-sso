<?php

namespace Shenasa\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redirect;
use Shenasa\Actions\SsoLogout;
use Shenasa\Contracts\SsoActiveUserProviderContract;
use Shenasa\Contracts\SsoAsyncLoginContract;
use Shenasa\Contracts\SsoCallbackFailureContract;
use Shenasa\Contracts\SsoLoginContract;
use Shenasa\Contracts\SsoUserFinderContract;
use Shenasa\Exceptions\LoginException;
use Shenasa\Exceptions\UnhandledException;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function state()
    {
        return response()->json([
            'enabled'   => Sso::getIsEnable(),
            'login_url' => Sso::generateLoginUrl()
        ]);
    }

    /**
     * @param \Illuminate\Http\Request                 $request
     * @param \Shenasa\Contracts\SsoUserFinderContract $userFinderAction
     * @param \Shenasa\Contracts\SsoAsyncLoginContract $asyncLoginAction
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Shenasa\Exceptions\LoginException
     * @throws \Shenasa\Exceptions\UnhandledException
     */
    public function asyncLogin(Request $request, SsoUserFinderContract $userFinderAction, SsoAsyncLoginContract $asyncLoginAction)
    {
        validator()
            ->make($request->all(), [
                'state' => 'required|string',
                'code'  => 'required|string'
            ])
            ->validate();

        $state = $request->input('state');
        $code  = $request->input('code');

        $userInfo = Sso::validateLoginCode($state, $code, $userFinderAction);

        if (!!$userInfo) {
            try {
                [$user, $username, $identifyCode] = $userInfo;
                return call_user_func($asyncLoginAction, $request, $user, $username, $identifyCode);
            } catch (Exception) {
                throw new LoginException;
            }
        }

        throw new UnhandledException;
    }

    /**
     * @return void
     */
    public function logout(SsoActiveUserProviderContract $ssoActiveUserProviderAction, SsoLogout $logoutAction)
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
     * @param \Shenasa\Actions\SsoLogin                 $ssoLoginAction
     * @param \Shenasa\Actions\SsoCallbackFailureAction $callbackFailureAction
     * @return mixed
     * @throws \Shenasa\Exceptions\UnhandledException
     * @throws \Shenasa\Exceptions\LoginException
     */
    public function callback(Request $request, SsoUserFinderContract $userFinderAction, SsoLoginContract $ssoLoginAction, SsoCallbackFailureContract $callbackFailureAction)
    {
        $state = $request->get('state');
        $code  = $request->get('code');

        $userInfo = Sso::validateLoginCode($state, $code, $userFinderAction);

        if (!!$userInfo) {
            try {
                [$user, $username, $identifyCode] = $userInfo;
                return call_user_func($ssoLoginAction, $request, $user, $username, $identifyCode);
            } catch (Exception) {
                throw new LoginException;
            }
        }

        throw new UnhandledException;
    }
}
