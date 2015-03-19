<?php namespace SocialNorm;

use SocialNorm\Exceptions\InvalidAuthorizationCodeException;
use SocialNorm\State\StateManager;

class SocialNorm
{
    protected $providers;
    protected $session;
    protected $request;
    protected $stateGenerator;

    public function __construct(
        ProviderRegistry $providers,
        Session $session,
        Request $request,
        StateGenerator $stateGenerator
    )
    {
        $this->providers = $providers;
        $this->session = $session;
        $this->request = $request;
        $this->stateGenerator = $stateGenerator;
    }

    public function registerProvider($alias, Provider $provider)
    {
        $this->providers->registerProvider($alias, $provider);
    }

    public function authorize($providerAlias)
    {
        $state = $this->stateGenerator->generate();
        $this->session->put('oauth.state', $state);
        return $this->getProvider($providerAlias)->authorizeUrl($state);
    }

    public function getUser($providerAlias)
    {
        $this->verifyState();
        return $this->getProvider($providerAlias)->getUser();
    }

    protected function getProvider($providerAlias)
    {
        return $this->providers->getProvider($providerAlias);
    }

    protected function verifyState()
    {
        if ($this->session->get('oauth.state') !== $this->request->state()) {
            throw new InvalidAuthorizationCodeException;
        }
    }
}
