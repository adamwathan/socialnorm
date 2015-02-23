<?php namespace SocialNorm;

use SocialNorm\Exceptions\ApplicationRejectedException;

class Request
{
    private $queryParams;

    public function __construct($queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /** @todo */
    public static function createFromGlobals() {}

    public function verifyState($state)
    {
        if (! isset($this->queryParams['state'])) {
            return false;
        }
        return $this->queryParams['state'] === $state;
    }

    public function authorizationCode()
    {
        if (! isset($this->queryParams['code'])) {
            throw new ApplicationRejectedException;
        }
        return $this->queryParams['code'];
    }
}
