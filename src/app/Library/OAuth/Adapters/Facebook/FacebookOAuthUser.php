<?php

namespace Domains\Library\OAuth\Adapters\Facebook;

final class FacebookOAuthUser
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $id;

    public function __construct(string $name,
                                string $email,
                                string $id)
    {
        $this->name = $name;
        $this->email = $email;
        $this->id = $id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public static function fromJSON(array $json) : FacebookOAuthUser
    {
        return new FacebookOAuthUser($json['name'],
                                     $json['email'],
                                     $json['id']);
    }
}
