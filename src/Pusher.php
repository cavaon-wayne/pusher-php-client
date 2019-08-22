<?php

namespace Cavaon\Pusher;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class Pusher
{

    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    public $token;

    public function __construct($app = null)
    {
        if (!$app) {
            $app = app();
        }
        $this->app = $app;
    }

    /**
     * @return array
     * @throws PusherException
     */
    protected function getAppSecret()
    {
        $appId = $this->app['config']->get('broadcasting.connections.pusher.app_id');
        $appSecret = $this->app['config']->get('broadcasting.connections.pusher.secret');
        if (empty($appId) || empty($appSecret)) {
            throw new PusherException("Please make sure you have set app id and its secret for these two config keys:'broadcasting.connections.pusher.app_id' and 'broadcasting.connections.pusher.secret' ");
        }
        return [$appId, $appSecret];
    }

    public function getAppId(){
        return $this->app['config']->get('broadcasting.connections.pusher.app_id');
    }

    public function getHostURL(){
        $port=config('broadcasting.connections.pusher.options.port');
        return config('broadcasting.connections.pusher.options.host').($port?":".$port:"").'/'.config('broadcasting.connections.pusher.app_id');
    }

    /**
     * @param bool $forceNew
     * @return \Lcobucci\JWT\Token
     */
    public function getToken($forceNew = false)
    {
        if (!$this->token || $forceNew) {
            $this->token = $this->generateToken();
        }
        return $this->token;
    }

    /**
     * @return \Lcobucci\JWT\Token
     */
    public function generateToken()
    {

        list($appId, $appSecret) = $this->getAppSecret();
        $signer = new Sha256();
        $token = (new Builder())->setIssuer(url(""))// Configures the issuer (iss claim)
        ->setAudience(request()->getBaseUrl())// Configures the audience (aud claim)
        ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
        ->setExpiration(time() + 24 * 3600)// Configures the expiration time of the token (exp claim)
        ->set('app_id', $appId)// Configures a new claim, called "uid"
        ->sign($signer, $appSecret)// creates a signature using "testing" as key
        ->getToken(); // Retrieves the generated token

        return $token;
    }

}