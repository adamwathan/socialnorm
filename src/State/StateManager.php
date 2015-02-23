<?php namespace SocialNorm\State;

class StateManager
{
    protected $session;
    protected $request;
    protected $stateGenerator;

    public function __construct(Session $session, Request $request, StateGenerator $stateGenerator)
    {
        $this->session = $session;
        $this->request = $request;
        $this->stateGenerator = $stateGenerator;
    }

    public function generateState()
    {
        $state = $this->stateGenerator->generate();
        $this->session->put('oauth.state', $state);
        return $state;
    }

    public function verifyState()
    {
        return $this->request->verifyState($this->session->get('oauth.state'));
    }
}
