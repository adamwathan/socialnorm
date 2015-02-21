<?php

use Mockery as M;
use SocialNorm\OAuthManager;
use SocialNorm\Providers\OAuth2Provider;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\Mock as SubscriberMock;

class ProviderTest extends PHPUnit_Framework_TestCase
{
    private function getStubbedHttpClient($responses = [])
    {
        $client = new HttpClient;
        $mockSubscriber = new SubscriberMock($responses);
        $client->getEmitter()->attach($mockSubscriber);
        return $client;
    }

    /** @test */
    public function it_can_retrieve_a_normalized_user()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/fixtures/oauth2_accesstoken_response.txt',
            __DIR__ . '/fixtures/oauth2_user_response.txt',
        ]);

        $provider = new GenericProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, ['code' => 'abc123']);

        $user = $provider->getUser();

        $this->assertEquals('4323180', $user->id);
        $this->assertEquals('adamwathan', $user->nickname);
        $this->assertEquals('Adam Wathan', $user->full_name);
        $this->assertEquals('adam@example.com', $user->email);
        $this->assertEquals('https://avatars.example.com/4323180', $user->avatar);
        $this->assertEquals('abcdefgh12345678', $user->access_token);
    }
}

class GenericProvider extends OAuth2Provider
{
    protected $scope = [ 'email' ];

    protected function getAuthorizeUrl()
    {
        return 'http://example.com/authorize';
    }

    protected function getAccessTokenBaseUrl()
    {
        return 'http://example.com/access-token';
    }

    protected function getUserDataUrl()
    {
        return 'http://api.example.com/user-details';
    }

    protected function parseTokenResponse($response)
    {
        return $this->parseJsonTokenResponse($response);
    }

    protected function parseUserDataResponse($response)
    {
        return json_decode($response, true);
    }

    protected function userId()
    {
        return $this->getProviderUserData('id');
    }

    protected function nickname()
    {
        return $this->getProviderUserData('login');
    }

    protected function fullName()
    {
        return $this->getProviderUserData('name');
    }

    protected function email()
    {
        return $this->getProviderUserData('email');
    }

    protected function avatar()
    {
        return $this->getProviderUserData('avatar_url');
    }
}
