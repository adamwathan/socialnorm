<?php

use Mockery as M;

use SocialNorm\SocialNorm;
use SocialNorm\Provider;
use SocialNorm\ProviderRegistry;
use SocialNorm\Request;
use SocialNorm\Session;
use SocialNorm\StateGenerator;

class SocialNormTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_authorize_urls_for_providers()
    {
        $providerRegistry = new ProviderRegistry;
        $session = M::mock('SocialNorm\Session')->shouldIgnoreMissing();

        $authorizeUrl = 'http://example.com/authorize';
        $provider = new ProviderStub($authorizeUrl, M::mock('SocialNorm\User'));

        $socialNorm = new SocialNorm(
            $providerRegistry,
            $session,
            Request::createFromGlobals(),
            new StateGenerator
        );
        $socialNorm->registerProvider('foo', $provider);

        $this->assertStringStartsWith($authorizeUrl, $socialNorm->authorize('foo'));
    }

    /** @test */
    public function it_can_retrieve_users_when_state_is_verified()
    {
        $state = 'valid-state';
        $session = M::mock('SocialNorm\Session');
        $session->shouldReceive('get')->with('oauth.state')->andReturn($state);

        $user = M::mock('SocialNorm\User');
        $provider = new ProviderStub('http://example.com/authorize', $user);

        $socialNorm = new SocialNorm(
            new ProviderRegistry,
            $session,
            new Request(['state' => $state]),
            new StateGenerator
        );
        $socialNorm->registerProvider('foo', $provider);

        $this->assertEquals($user, $socialNorm->getUser('foo'));
    }

    /**
     * @test
     * @expectedException SocialNorm\Exceptions\InvalidAuthorizationCodeException
     */
    public function it_throws_if_the_state_cant_be_verified()
    {
        $session = M::mock('SocialNorm\Session');
        $session->shouldReceive('get')->with('oauth.state')->andReturn('valid-state');
        $provider = new ProviderStub('http://example.com/authorize', M::mock('SocialNorm\User'));

        $socialNorm = new SocialNorm(
            new ProviderRegistry,
            $session,
            new Request(['state' => 'invalid-state']),
            new StateGenerator
        );

        $socialNorm->registerProvider('foo', $provider);

        $socialNorm->getUser('foo');
    }
}
