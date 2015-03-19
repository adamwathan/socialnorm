<?php namespace SocialNorm;

interface Session
{
    public function put($key, $value);
    public function get($key);
}
