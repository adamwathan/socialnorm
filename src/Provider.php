<?php namespace SocialNorm;

interface Provider
{
    public function authorizeUrl($state);
    public function getUser();
}
