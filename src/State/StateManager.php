<?php namespace SocialNorm\State;

class StateManager
{
    protected $session;
    protected $request;
    protected $generateRandomString;

    public function __construct(Session $session, $request, Closure $generateRandomString = null)
    {
        $this->session = $session;
        $this->request = $request;
        $this->generateRandomString = $generateRandomString ?: function () {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            return str_shuffle(str_repeat($pool, 16));
        };
    }

    public function generateState()
    {
        $this->store($state = $this->generateRandomString->__invoke());
        return $state;
    }

    protected function store($state)
    {
        $this->session->put('oauth.state', $state);
    }

    public function verifyState()
    {
        if (! isset($this->request['state'])) {
            return false;
        }
        return $this->request['state'] === $this->retrieveState();
    }

    protected function retrieveState()
    {
        return $this->session->get('oauth.state');
    }
}
