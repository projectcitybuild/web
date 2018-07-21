<?php
namespace Infrastructure\Library\Discord\OAuth;

class DiscordOAuthUser
{

    /**
     * @var string
     */
    private $username;

    /**
     * @var bool
     */
    private $verified;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var bool
     */
    private $mfaEnabled;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $avatar;

    /**
     * @var string
     */
    private $discriminator;

    /**
     * @var string
     */
    private $email;


    public function __construct(
        string $username,
                                bool $verified,
                                string $locale,
                                bool $mfaEnabled,
                                string $id,
                                string $avatar,
                                string $discriminator,
                                string $email
    ) {
        $this->username     = $username;
        $this->verified     = $verified;
        $this->locale       = $locale;
        $this->mfaEnabled   = $mfaEnabled;
        $this->id           = $id;
        $this->avatar       = $avatar;
        $this->discriminator = $discriminator;
        $this->email        = $email;
    }


    public function getUsername() : string
    {
        return $this->username;
    }

    public function isVerified() : bool
    {
        return $this->verified;
    }

    public function getLocale() : string
    {
        return $this->locale;
    }

    public function isMfaEnabled() : bool
    {
        return $this->mfaEnabled;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getAvatar() : string
    {
        return $this->avatar;
    }

    public function getDiscriminator() : string
    {
        return $this->discriminator;
    }

    public function getEmail() : string
    {
        return $this->email;
    }


    public static function fromJSON(array $json) : DiscordOAuthUser
    {
        return new DiscordOAuthUser(
            $json['username'],
                                    $json['verified'],
                                    $json['locale'],
                                    $json['mfa_enabled'],
                                    $json['id'],
                                    $json['avatar'],
                                    $json['discriminator'],
                                    $json['email']
        );
    }
}
