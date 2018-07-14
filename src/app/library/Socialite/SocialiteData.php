<?php
namespace App\Library\Socialite;

class SocialiteData {

    /**
     * @var string
     */
    private $providerName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $id;


    public function __construct(string $providerName, string $email, string $name, string $id) {
        $this->providerName = $providerName;
        $this->email = $email;
        $this->name = $name;
        $this->id = $id;
    }

    public function getProviderName() : string {
        return $this->providerName;
    }

    public function getEmail() : string {
        return $this->email;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getId() : string {
        return $this->id;
    }

    public function toArray() : array {
        return [
            'email'     => $this->email,
            'name'      => $this->name,
            'id'        => $this->id,
            'provider'  => $this->providerName,
        ];
    }
}