<?php

namespace Shenasa\Facades;

use Illuminate\Support\Facades\Facade;
use Shenasa\Actions\SsoUserFinderAction;

/**
 * @method static string generateLoginUrl()
 * @method static array getLoginToken($code, $redirect_uri)
 * @method static bool|array validateLoginCode($state, $code, SsoUserFinderAction $userFinderAction)
 * @method static mixed findUserUsing(string $callback)
 * @method static mixed loginUsing(string $callback)
 * @method static mixed logoutUsing(string $callback)
 * @method static mixed provideActiveUserUsing(string $callback)
 * @method static mixed callbackFailureHandlerUsing(string $callback)
 * @method static bool getIsEnable()
 * @method static void setOptions(array $options)
 * @method static string getLoginUrl()
 * @method static string getLogoutUrl()
 * @method static bool logout(string $username, string $identifyCode)
 */
class Sso extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sso';
    }
}
