<?php

use Mockery as M;
use SocialNorm\ProviderRegistry;

class ProviderRegistryTest extends TestCase
{
    /** @test */
    public function it_can_register_providers()
    {
        $provider = M::mock('SocialNorm\Provider');

        $providerRegistry = new ProviderRegistry;

        $providerRegistry->registerProvider('foo', $provider);

        $this->assertEquals($provider, $providerRegistry->getProvider('foo'));
    }

    /**
     * @test
     * @expectedException SocialNorm\Exceptions\ProviderNotRegisteredException
     */
    public function it_throws_when_retrieving_an_unregistered_provider()
    {
        $providerRegistry = new ProviderRegistry;
        $providerRegistry->getProvider('not-registered');
    }

    /** @test */
    public function providers_can_be_replaced()
    {
        $toReplace = M::mock('SocialNorm\Provider');
        $replacement = M::mock('SocialNorm\Provider');

        $providerRegistry = new ProviderRegistry;

        $providerRegistry->registerProvider('foo', $toReplace);
        $providerRegistry->registerProvider('foo', $replacement);

        $this->assertEquals($replacement, $providerRegistry->getProvider('foo'));
    }
}
