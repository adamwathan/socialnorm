<?php namespace SocialNorm;

use SocialNorm\Exceptions\InvalidAuthorizationCodeException;
use SocialNorm\State\StateManager;

class SocialNorm
{
    protected $providers;
    protected $stateManager;

    public function __construct(ProviderRegistry $providers, StateManager $stateManager)
    {
        $this->providers = $providers;
        $this->stateManager = $stateManager;
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
        return $this->getProvider($providerAlias)->getUser();
    }

    protected function getProvider($providerAlias)
    {
        return $this->providers->getProvider($providerAlias);
    }
}
