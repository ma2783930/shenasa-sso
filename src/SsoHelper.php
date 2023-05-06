<?php

/** @noinspection PhpUnused */

namespace Shenasa;

use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Shenasa\Actions\SsoActiveUserProviderAction;
use Shenasa\Actions\SsoCallbackFailureAction;
use Shenasa\Actions\SsoLoginAction;
use Shenasa\Actions\SsoLogoutAction;
use Shenasa\Actions\SsoUserFinderAction;
use Shenasa\Exceptions\AttemptLoggerException;
use Shenasa\Exceptions\CallbackResponseException;
use Shenasa\Exceptions\CertificateProviderException;
use Shenasa\Exceptions\ConfigurationException;
use Shenasa\Exceptions\InvalidAttemptException;
use Shenasa\Exceptions\InvalidCallbackResponseException;
use Shenasa\Exceptions\InvalidTokenException;
use Shenasa\Exceptions\InvalidUserException;
use Shenasa\Exceptions\RemoteConfigurationException;
use Shenasa\Exceptions\TokenParserException;
use Shenasa\Exceptions\UserGetterException;
use Shenasa\Models\SsoAttempt;

class SsoHelper
{
    /**
     * SSO functionality state flag
     *
     * @var bool
     */
    private bool $enable;

    /**
     * Base URL of SSO server
     *
     * @var string|null
     */
    private string|null $baseAddress;

    /**
     * Client-ID of SSO account
     *
     * @var string|null
     */
    private string|null $clientId;

    /**
     * Client-Secret of SSO account
     *
     * @var string|null
     */
    private string|null $clientSecret;

    /**
     * Callback URL for redirect after SSO operation
     *
     * @var string
     */
    private string $redirectUri;

    /**
     * Configuration of SSO
     *
     * @var array|mixed
     */
    private array $configuration;

    /**
     * Value of ISS for token validation
     *
     * @var string
     */
    private string $iss = 'https://www.shenasa.com';

    /**
     * Type of token for token validation
     *
     * @var string
     */
    private string $tokenType = 'Bearer';

    /**
     * Uri of openid configuration service
     *
     * @var string
     */
    private string $openidConfigurationUri = '/.well-known/openid-configuration';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enable       = config('sso.enable');
        $this->clientId     = config('sso.client_id');
        $this->clientSecret = $this->createClientSecretHash(config('sso.client_secret'));
        $this->baseAddress  = config('sso.base_address');
        $this->redirectUri  = sprintf('%s/%s', config('app.url'), config('sso.callback_url'));
    }

    /**
     * User finder action
     *
     * @param string $callback
     * @return void
     */
    public function findUserUsing(string $callback): void
    {
        app()->singleton(SsoUserFinderAction::class, $callback);
    }

    /**
     * Login action
     *
     * @param string $callback
     * @return void
     */
    public function loginUsing(string $callback): void
    {
        app()->singleton(SsoLoginAction::class, $callback);
    }

    /**
     * Logout action
     *
     * @param string $callback
     * @return void
     */
    public function logoutUsing(string $callback): void
    {
        app()->singleton(SsoLogoutAction::class, $callback);
    }

    /**
     * Returns logged-in user with callback class
     *
     * @param string $callback
     * @return void
     */
    public function provideActiveUserUsing(string $callback): void
    {
        app()->singleton(SsoActiveUserProviderAction::class, $callback);
    }

    /**
     * @param string $callback
     * @return void
     */
    public function callbackFailureHandlerUsing(string $callback): void
    {
        app()->singleton(SsoCallbackFailureAction::class, $callback);
    }

    /**
     * Generates login url for redirection
     *
     * @return string
     * @throws \Shenasa\Exceptions\RemoteConfigurationException
     * @throws \Shenasa\Exceptions\AttemptLoggerException
     */
    public function generateLoginUrl(): string
    {
        $configuration = $this->getOpenIdConfiguration();

        $state         = Str::uuid();
        $response_type = "code";
        $scope         = "openid";
        $client_id     = $this->clientId;

        $login_url = sprintf(
            "%s?response_type=%s&scope=%s&client_id=%s&state=%s&redirect_uri=%s",
            $configuration['authorization_endpoint'],
            $response_type,
            $scope,
            $client_id,
            $state,
            $this->redirectUri
        );

        $this->logAttempt($state, $login_url);

        return $login_url;
    }

    /**
     *      * Validates response code.
     * Returns attempt type, user, username and identify code with successful validation and false with unsuccessful validation
     * @param                                      $state
     * @param                                      $code
     * @param \Shenasa\Actions\SsoUserFinderAction $userFinderAction
     * @return bool|array
     * @throws \Shenasa\Exceptions\CallbackResponseException
     * @throws \Shenasa\Exceptions\InvalidAttemptException
     * @throws \Shenasa\Exceptions\InvalidCallbackResponseException
     * @throws \Shenasa\Exceptions\InvalidTokenException
     * @throws \Shenasa\Exceptions\RemoteConfigurationException
     * @throws \Shenasa\Exceptions\UserGetterException
     * @throws \Shenasa\Exceptions\TokenParserException
     * @throws \Throwable
     */
    public function validateLoginCode($state, $code, SsoUserFinderAction $userFinderAction): bool|array
    {
        $ssoAttempt = SsoAttempt::where('state', (string)$state)
                                ->whereNull('is_successful')
                                ->first();

        if (empty($ssoAttempt)) {
            throw new InvalidAttemptException;
        }

        $this->configuration = $this->getOpenIdConfiguration();

        try {
            $response = Http::withoutVerifying()
                            ->asForm()
                            ->post(
                                sprintf("%s", $this->configuration['token_endpoint']),
                                [
                                    "grant_type"    => "authorization_code",
                                    "code"          => $code,
                                    "redirect_uri"  => $this->redirectUri,
                                    "client_id"     => $this->clientId,
                                    "client_secret" => $this->clientSecret
                                ]
                            )
                            ->json();
        } catch (Exception) {
            throw new CallbackResponseException;
        }

        if (isset($response['jws'])) {
            $token        = $response['jws'];
            $decodedToken = json_decode(
                base64_decode(
                    str_replace(
                        '_',
                        '/',
                        str_replace(
                            '-',
                            '+',
                            explode('.', $token)[1]
                        )
                    )
                ),
                true
            );

            $certificate = $this->getCertificate();

            try {
                JWT::$leeway    = 30000;
                JWT::$timestamp = Carbon::now()->getTimestampMs();
                $data           = (array)JWT::decode($token, $certificate);
                [$username, $identifyCode] = explode('##', $data['sub']);
            } catch (Exception) {
                throw new TokenParserException;
            }

            try {
                $user = call_user_func($userFinderAction, $username, $identifyCode);
            } catch (Exception) {
                throw new UserGetterException;
            }

            $hasValidToken = true;

            if (
                Carbon::createFromTimestampMs($decodedToken['exp'])->isPast() ||
                Carbon::createFromTimestampMs($decodedToken['iat'])->subSeconds(5)->isFuture() ||
                $decodedToken['iss'] != $this->iss ||
                $decodedToken['aud'] != $this->clientId ||
                $response['token_type'] != $this->tokenType
            ) {
                $hasValidToken = false;
            }

            $hasValidUser = !!$user;

            $ssoAttempt->update(['is_successful' => $hasValidToken && $hasValidUser]);

            throw_if(!$hasValidUser, InvalidUserException::class);
            throw_if(!$hasValidToken, InvalidTokenException::class);

            return [$user, $username, $identifyCode];
        }

        throw new InvalidCallbackResponseException;
    }

    //TODO: Logout operation will be implemented later
    public function logout($username, $identifyCode): void
    {
        Http::withoutVerifying()
                        ->post($this->configuration['logout_endpoint'], [
                            'sub'           => sprintf('%s##%s', $username, $identifyCode),
                            'client_id'     => $this->clientId,
                            'client_secret' => $this->clientSecret
                        ])
                        ->json();


            /*[
                'sub'           => sprintf('%s##%s', $username, $identifyCode),
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret
            ]*/

    }

    /**
     * Get SSO state
     *
     * @return bool
     */
    public function getIsEnable(): bool
    {
        return $this->enable;
    }

    /**
     * SSO global configuration setter
     *
     * @param array $options
     * @return void
     * @throws Exception
     */
    public function setOptions(array $options): void
    {
        if (
            !isset($options['enable']) ||
            !isset($options['baseUrl']) ||
            !isset($options['clientId']) ||
            !isset($options['clientSecret'])
        ) {
            throw new ConfigurationException;
        }

        $this->baseAddress  = $options['baseUrl'];
        $this->enable       = $options['enable'];
        $this->clientId     = $options['clientId'];
        $this->clientSecret = $this->createClientSecretHash($options['clientSecret']);
    }

    /**
     * Returns full url of login page
     *
     * @return string
     */
    public function getLoginUrl(): string
    {
        return URL::route('sso-auth.login');
    }

    /**
     * Returns full url of logout
     *
     * @return string
     */
    public function getLogoutUrl(): string
    {
        return URL::route('sso-auth.logout');
    }

    /**
     * Get Open-ID configuration
     *
     * @return array
     * @throws \Shenasa\Exceptions\RemoteConfigurationException
     */
    private function getOpenIdConfiguration(): array
    {
        try {
            return Http::withoutVerifying()
                       ->retry(1)
                       ->connectTimeout(3)
                       ->timeout(3)
                       ->get(
                           sprintf("%s%s", $this->baseAddress, $this->openidConfigurationUri)
                       )
                       ->json();
        } catch (Exception) {
            throw new RemoteConfigurationException;
        }
    }

    /**
     * @return \Firebase\JWT\Key
     * @throws \Shenasa\Exceptions\CertificateProviderException
     */
    private function getCertificate(): Key
    {
        try {
            $certificateKeys = Http::withoutVerifying()
                                   ->get($this->configuration['jwks_uri'])
                                   ->json('keys');
        } catch (Exception) {
            throw new CertificateProviderException;
        }

        return JWK::parseKey($certificateKeys[0], 'RS256');
    }

    /**
     * @param string $state
     * @param string $url
     * @return void
     * @throws \Shenasa\Exceptions\AttemptLoggerException
     */
    private function logAttempt(string $state, string $url): void
    {
        try {
            SsoAttempt::create([
                'type'       => 'login',
                'state'      => $state,
                'url'        => $url,
                'ip_address' => Request::ip(),
                'expired_at' => Carbon::now()->addMinutes(30)
            ]);
        } catch (Exception) {
            throw new AttemptLoggerException;
        }
    }

    /**
     * Returns hash value of client secret
     *
     * @param $clientSecret
     * @return string
     */
    private function createClientSecretHash($clientSecret): string
    {
        return str_replace(
            '/',
            '_',
            str_replace(
                '+',
                '-',
                base64_encode(
                    hash(
                        'sha256',
                        utf8_encode($clientSecret),
                        true
                    )
                )
            )
        );
    }
}
