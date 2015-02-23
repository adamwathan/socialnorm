<?php namespace SocialNorm;

use SocialNorm\Exceptions\ApplicationRejectedException;

final class Request
{
    private $queryParams;

    public function __construct($queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /**
     * Optional helper constructor to reduce setup
     * for people who don't really care.
     */
    public static function createFromGlobals()
    {
        return new self($_REQUEST);
    }

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
