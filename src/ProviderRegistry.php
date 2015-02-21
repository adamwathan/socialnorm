<?php namespace SocialNorm;

use SocialNorm\Exceptions\ProviderNotRegisteredException;

class ProviderRegistrar
{
    private $providers = [];

    public function registerProvider($alias, Provider $provider)
    {
        $this->providers[$alias] = $provider;
    }

    public function getProvider($providerAlias)
    {
        if (! $this->hasProvider($providerAlias)) {
            throw new ProviderNotRegisteredException("No provider has been registered under the alias '{$providerAlias}'");
        }
        return $this->providers[$providerAlias];
    }

    protected function hasProvider($alias)
    {
        return isset($this->providers[$alias]);
    }
}
