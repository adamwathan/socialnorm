<?php namespace SocialNorm\State;

interface Session
{
    public function put($key, $value);
    public function get($key);
}
