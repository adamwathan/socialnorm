<?php

use SocialNorm\Session;

class InMemorySession implements Session
{
    private $session = [];

    public function put($key, $value)
    {
        $this->session[$key] = $value;
    }

    public function get($key)
    {
        return $this->session[$key];
    }
}
