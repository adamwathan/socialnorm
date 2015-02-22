<?php

use Mockery as M;

use SocialNorm\SocialNorm;
use SocialNorm\Provider;
use SocialNorm\ProviderRegistry;

class SocialNormTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_authorize_urls_for_providers()
    {
        $authorizeUrl = 'http://example.com/authorize';
        $provider = new ProviderStub($authorizeUrl, M::mock('SocialNorm\User'));

        $providerRegistry = new ProviderRegistry;
        $stateManager = M::mock('SocialNorm\State\StateManager');
        $stateManager->shouldReceive('generateState')->andReturn('stubbed-state');

        $socialNorm = new SocialNorm($providerRegistry, $stateManager);
        $socialNorm->registerProvider('foo', $provider);

        $this->assertEquals($authorizeUrl, $socialNorm->authorize('foo'));
    }

    /** @test */
    public function it_can_retrieve_users_when_state_is_verified()
    {
        $authorizeUrl = 'http://example.com/authorize';
        $user = M::mock('SocialNorm\User');
        $provider = new ProviderStub($authorizeUrl, $user);

        $providerRegistry = new ProviderRegistry;
        $stateManager = M::mock('SocialNorm\State\StateManager');
        $stateManager->shouldReceive('verifyState')->andReturn(true);

        $socialNorm = new SocialNorm($providerRegistry, $stateManager);
        $socialNorm->registerProvider('foo', $provider);

        $this->assertEquals($user, $socialNorm->getUser('foo'));
    }

    /**
     * @test
     * @expectedException SocialNorm\Exceptions\InvalidAuthorizationCodeException
     */
    public function it_throws_if_the_state_cant_be_verified()
    {
        $authorizeUrl = 'http://example.com/authorize';
        $user = M::mock('SocialNorm\User');
        $provider = new ProviderStub($authorizeUrl, $user);

        $providerRegistry = new ProviderRegistry;
        $stateManager = M::mock('SocialNorm\State\StateManager');
        $stateManager->shouldReceive('verifyState')->andReturn(false);

        $socialNorm = new SocialNorm($providerRegistry, $stateManager);
        $socialNorm->registerProvider('foo', $provider);
        $socialNorm->getUser('foo');
    }
}

class ProviderStub implements Provider
{
    private $authorizeUrl;
    private $user;

    public function __construct($authorizeUrl, $user)
    {
        $this->authorizeUrl = $authorizeUrl;
        $this->user = $user;
    }

    public function authorizeUrl($state)
    {
        return $this->authorizeUrl;
    }

    public function getUser()
    {
        return $this->user;
    }
}
