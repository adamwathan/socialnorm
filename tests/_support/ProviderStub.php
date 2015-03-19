<?php

use SocialNorm\Provider;

class ProviderStub implements Provider
{
    private $authorizeUrl;
    private $user;

    public function __construct($authorizeUrl, $user)
    {
        $this->authorizeUrl = $authorizeUrl;
        $this->user = $user;
    }

    public function authorizeUrl($state)
    {
        return $this->authorizeUrl . "?state={$state}";
    }

    public function getUser()
    {
        return $this->user;
    }
}
