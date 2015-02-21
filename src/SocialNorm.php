<?php namespace SocialNorm;

use SocialNorm\Exceptions\ProviderNotRegisteredException;
use SocialNorm\Exceptions\InvalidAuthorizationCodeException;

class SocialNorm
{
    protected $stateManager;
    protected $providers;

    public function __construct(StateManager $stateManager, ProviderRegistry $providers)
    {
        $this->stateManager = $stateManager;
        $this->providers = $providers;
    }

    public function registerProvider($alias, Provider $provider)
    {
        $this->providers->registerProvider($alias, $provider);
    }

    public function authorize($providerAlias)
    {
        $state = $this->stateManager->generateState();
        return $this->getProvider($providerAlias)->authorizeUrl($state);
    }

    public function getUser($providerAlias)
    {
        if (! $this->stateManager->verifyState()) {
            throw new InvalidAuthorizationCodeException;
        }
        return $this->getProvider($providerAlias)->getUserDetails();
    }

    protected function getProvider($providerAlias)
    {
        return $this->providers->getProvider($providerAlias);
    }
}
