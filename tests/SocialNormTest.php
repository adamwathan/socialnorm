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
        $providerRegistry = new ProviderRegistry;
        $stateManager = M::mock('SocialNorm\State\StateManager');
        $stateManager->shouldReceive('generateState')->andReturn('stubbed-state');

        $authorizeUrl = 'http://example.com/authorize';
        $provider = new ProviderStub($authorizeUrl, M::mock('SocialNorm\User'));

        $socialNorm = new SocialNorm($providerRegistry, $stateManager);
        $socialNorm->registerProvider('foo', $provider);

        $this->assertEquals($authorizeUrl, $socialNorm->authorize('foo'));
    }

    /** @test */
    public function it_can_retrieve_users_when_state_is_verified()
    {
        $providerRegistry = new ProviderRegistry;
        $stateManager = M::mock('SocialNorm\State\StateManager');
        $stateManager->shouldReceive('verifyState')->andReturn(true);

        $authorizeUrl = 'http://example.com/authorize';
        $user = M::mock('SocialNorm\User');
        $provider = new ProviderStub($authorizeUrl, $user);

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
        $providerRegistry = new ProviderRegistry;
        $stateManager = M::mock('SocialNorm\State\StateManager');
        $stateManager->shouldReceive('verifyState')->andReturn(false);

        $authorizeUrl = 'http://example.com/authorize';
        $user = M::mock('SocialNorm\User');
        $provider = new ProviderStub($authorizeUrl, $user);

        $socialNorm = new SocialNorm($providerRegistry, $stateManager);
        $socialNorm->registerProvider('foo', $provider);
        $socialNorm->getUser('foo');
    }

    // Just for reference.. Still thinking about this design as well as adding
    // an integration test like this.
    public function it_works()
    {
        $authorizeUrl = 'http://example.com/authorize';
        $user = M::mock('SocialNorm\User');
        $provider = new ProviderStub($authorizeUrl, $user);

        $session = new InMemorySession;

        // Simulate first request
        $socialNorm = new SocialNorm(
            new ProviderRegistry,
            $session,
            new Request([]),
            new StateGenerator
        );

        $socialNorm->registerProvider('foo', $provider);
        $returnedUrl = $socialNorm->authorize('foo');

        $this->assertEquals($authorizeUrl, $returnedUrl);

        // Simulate second request

        // Maybe parse state out of the authorizeUrl here instead to be more
        // "black box", nice tip by @davedevelopment
        $state = $session->get('oauth.state');

        $socialNorm = new SocialNorm(
            new ProviderRegistry,
            $session,
            new Request(['state' => $state]),
            new StateGenerator
        );

        $returnedUser = $socialNorm->getUser('foo');

        $this->assertEquals($user, $returnedUser);
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
