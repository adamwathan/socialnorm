<?php

use Mockery as M;

use SocialNorm\SocialNorm;
use SocialNorm\Provider;
use SocialNorm\ProviderRegistry;
use SocialNorm\Request;
use SocialNorm\Session;
use SocialNorm\StateGenerator;

class SocialNormIntegrationTest extends TestCase
{
    /** @test */
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

        $this->assertStringStartsWith($authorizeUrl, $returnedUrl);

        // Simulate second request
        $state = $this->parseStateFromUrl($returnedUrl);

        $socialNorm = new SocialNorm(
            new ProviderRegistry,
            $session,
            new Request(['state' => $state]),
            new StateGenerator
        );

        $socialNorm->registerProvider('foo', $provider);

        $returnedUser = $socialNorm->getUser('foo');

        $this->assertEquals($user, $returnedUser);
    }

    private function parseStateFromUrl($url)
    {
        $queryParams = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
        return $queryParams['state'];
    }
}
