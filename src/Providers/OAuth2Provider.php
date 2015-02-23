<?php namespace SocialNorm\Providers;

use SocialNorm\Exceptions\ApplicationRejectedException;
use SocialNorm\Exceptions\InvalidAuthorizationCodeException;
use SocialNorm\Provider;
use SocialNorm\Request;
use SocialNorm\User;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException;

abstract class OAuth2Provider implements Provider
{
    protected $httpClient;
    protected $request;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $scopes = [];

    protected $headers = [
        'authorize' => [],
        'access_token' => [],
        'user_details' => [],
    ];

    protected $accessToken;
    protected $providerUserData;

    public function __construct($config, HttpClient $httpClient, Request $request)
    {
        $this->httpClient = $httpClient;
        $this->request = $request;
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->redirectUri = $config['redirect_uri'];
        if (isset($config['scope'])) {
            $this->scopes = array_merge($this->scopes, $config['scopes']);
        }
    }

    protected function redirectUri()
    {
        return $this->redirectUri;
    }

    public function authorizeUrl($state)
    {
        $url = $this->getAuthorizeUrl();
        $url .= '?' . $this->buildAuthorizeQueryString($state);
        return $url;
    }

    protected function buildAuthorizeQueryString($state)
    {
        $queryString = "client_id=".$this->clientId;
        $queryString .= "&scope=".urlencode($this->compileScopes());
        $queryString .= "&redirect_uri=".$this->redirectUri();
        $queryString .= "&response_type=code";
        $queryString .= "&state=".$state;
        return $queryString;
    }

    protected function compileScopes()
    {
        return implode(',', $this->scopes);
    }

    public function getUser()
    {
        $this->accessToken = $this->requestAccessToken();
        $this->providerUserData = $this->requestUserData();
        return new User([
            'access_token' => $this->accessToken,
            'id' => $this->userId(),
            'nickname' => $this->nickname(),
            'full_name' => $this->fullName(),
            'email' => $this->email(),
            'avatar' => $this->avatar(),
        ], $this->providerUserData);
    }

    protected function getProviderUserData($key)
    {
        if (! isset($this->providerUserData[$key])) {
            return null;
        }
        return $this->providerUserData[$key];
    }

    protected function requestAccessToken()
    {
        $url = $this->getAccessTokenBaseUrl();
        try {
            $response = $this->httpClient->post($url, [
                'headers' => $this->headers['access_token'],
                'body' => $this->buildAccessTokenPostBody(),
            ]);
        } catch (BadResponseException $e) {
            throw new InvalidAuthorizationCodeException((string) $e->getResponse());
        }
        return $this->parseTokenResponse((string) $response->getBody());
    }

    protected function requestUserData()
    {
        $url = $this->buildUserDataUrl();
        $response = $this->httpClient->get($url, ['headers' => $this->headers['user_details']]);
        return $this->parseUserDataResponse((string) $response->getBody());
    }

    protected function buildAccessTokenPostBody()
    {
        $body = "code=".$this->request->authorizationCode();
        $body .= "&client_id=".$this->clientId;
        $body .= "&client_secret=".$this->clientSecret;
        $body .= "&redirect_uri=".$this->redirectUri();
        $body .= "&grant_type=authorization_code";
        return $body;
    }

    protected function buildUserDataUrl()
    {
        $url = $this->getUserDataUrl();
        $url .= "?access_token=".$this->accessToken;
        return $url;
    }

    protected function parseJsonTokenResponse($response)
    {
        $response = json_decode($response);
        if (! isset($response->access_token)) {
            throw new InvalidAuthorizationCodeException;
        }
        return $response->access_token;
    }

    abstract protected function getAuthorizeUrl();
    abstract protected function getAccessTokenBaseUrl();
    abstract protected function getUserDataUrl();

    abstract protected function parseTokenResponse($response);
    abstract protected function parseUserDataResponse($response);

    abstract protected function userId();
    abstract protected function nickname();
    abstract protected function fullName();
    abstract protected function email();
    abstract protected function avatar();
}
