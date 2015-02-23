<?php namespace SocialNorm;

class Request
{
    private $queryParams;

    public function __construct($queryParams)
    {
        $this->queryParams = $queryParams;
    }

    public function verifyState($state)
    {
        if (! isset($this->queryParams['state'])) {
            return false;
        }
        return $this->queryParams['state'] === $state;
    }
}
