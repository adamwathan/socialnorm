<?php namespace SocialNorm;

/**
 * @property-read string $accessToken
 * @property-read string $id
 * @property-read string $nickname
 * @property-read string $fullName
 * @property-read string $imageUrl
 * @property-read string $email
 */
class User
{
    protected $access_token;
    protected $id;
    protected $nickname;
    protected $full_name;
    protected $avatar;
    protected $email;
    protected $raw = [];

    public function __construct($attributes, $raw = [])
    {
        $this->access_token = $this->fetch($attributes, 'access_token');
        $this->id = $this->fetch($attributes, 'id');
        $this->nickname = $this->fetch($attributes, 'nickname');
        $this->full_name = $this->fetch($attributes, 'full_name');
        $this->avatar = $this->fetch($attributes, 'avatar');
        $this->email = $this->fetch($attributes, 'email');

        $this->raw =  $raw;
    }

    private function fetch($attributes, $key, $default = null)
    {
        if (! isset($attributes[$key])) {
            return $default;
        }
        return $attributes[$key];
    }

    public function raw()
    {
        return $this->raw;
    }

    public function __get($key)
    {
        return $this->{$key};
    }
}
