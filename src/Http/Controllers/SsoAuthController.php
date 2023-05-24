<?php

namespace Shenasa\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redirect;
use Sela\Attributes\SelaProcess;
use Shenasa\Contracts\SsoLogoutContract;
use Shenasa\Contracts\SsoActiveUserProviderContract;
use Shenasa\Contracts\SsoLoginContract;
use Shenasa\Contracts\SsoUserFinderContract;
use Shenasa\Exceptions\LoginException;
use Shenasa\Exceptions\UnhandledException;
use Shenasa\Facades\Sso;

class SsoAuthController extends BaseController
{
    public SsoUserFinderContract         $userFinderAction;
    public SsoActiveUserProviderContract $ssoActiveUserProviderAction;
    public SsoLogoutContract             $logoutAction;
    public SsoLoginContract              $ssoLoginAction;

    /**
     * @param \Shenasa\Contracts\SsoUserFinderContract         $userFinderAction
     * @param \Shenasa\Contracts\SsoActiveUserProviderContract $ssoActiveUserProviderAction
     * @param \Shenasa\Contracts\SsoLogoutContract             $logoutAction
     * @param \Shenasa\Contracts\SsoLoginContract              $ssoLoginAction
     */
    public function __construct(
        SsoUserFinderContract         $userFinderAction,
        SsoActiveUserProviderContract $ssoActiveUserProviderAction,
        SsoLogoutContract             $logoutAction,
        SsoLoginContract              $ssoLoginAction,
    )
    {
        $this->userFinderAction            = $userFinderAction;
        $this->ssoActiveUserProviderAction = $ssoActiveUserProviderAction;
        $this->logoutAction                = $logoutAction;
        $this->ssoLoginAction              = $ssoLoginAction;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    #[SelaProcess([
        'name' => 'sso_login_redirect',
        'info' => 'Generate new url for sso login and redirect user to it'
    ])]
    public function loginRedirect()
    {
        return Redirect::to(
            Sso::generateLoginUrl()
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    #[SelaProcess([
        'name' => 'sso_get_login',
        'info' => 'Generate and return new url for sso login'
    ])]
    public function getLogin()
    {
        return response()->json([
            'login_url' => Sso::generateLoginUrl()
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Shenasa\Exceptions\LoginException
     * @throws \Shenasa\Exceptions\UnhandledException
     */
    #[SelaProcess([
        'name' => 'sso_verify_login',
        'info' => 'Verify user sso login by callback api',
        'data_tags' => [
            ['name' => 'state', 'info' => 'State value of sso login'],
            ['name' => 'code', 'info' => 'Code value of sso login']
        ]
    ])]
    public function verifyLogin(Request $request)
    {
        validator()
            ->make($request->all(), [
                'state' => 'required|string',
                'code'  => 'required|string'
            ])
            ->validate();

        $state = $request->input('state');
        $code  = $request->input('code');

        $userInfo = Sso::validateLoginCode($state, $code, $this->userFinderAction);

        if (!!$userInfo) {
            try {
                [$user, $username, $identifyCode] = $userInfo;
                return call_user_func($this->ssoLoginAction, $request, $user, $username, $identifyCode);
            } catch (Exception) {
                throw new LoginException;
            }
        }

        throw new UnhandledException;
    }

    /**
     * @return void
     */
    #[SelaProcess([
        'name' => 'sso_logout',
        'info' => 'Logout from sso server'
    ])]
    public function logout()
    {
        [$user, $username, $identifyCode] = call_user_func($this->ssoActiveUserProviderAction);
        $loggedOut = Sso::logout($username, $identifyCode);

        if ($loggedOut) {
            return call_user_func($this->logoutAction, $user, $username, $identifyCode);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @throws \Shenasa\Exceptions\LoginException
     * @throws \Shenasa\Exceptions\UnhandledException
     */
    #[SelaProcess([
        'name' => 'sso_callback',
        'info' => 'Verify user sso login by callback page'
    ])]
    public function callback(Request $request)
    {
        $state = $request->get('state');
        $code  = $request->get('code');

        $userInfo = Sso::validateLoginCode($state, $code, $this->userFinderAction);

        if (!!$userInfo) {
            try {
                [$user, $username, $identifyCode] = $userInfo;
                return call_user_func($this->ssoLoginAction, $request, $user, $username, $identifyCode);
            } catch (Exception) {
                throw new LoginException;
            }
        }

        throw new UnhandledException;
    }
}
