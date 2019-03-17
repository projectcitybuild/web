<?php

namespace Domains\Library\OAuth\Adapters\Google;

final class GoogleOAuthUser
{

    /**
     * @var string
     */
    private $kind;

    /**
     * @var string
     */
    private $etag;

    /**
     * @var array
     */
    private $emails = [];

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var array
     */
    private $name = [];

    /**
     * @var array
     */
    private $image = [];

    /**
     * @var bool
     */
    private $plusUser;

    /**
     * @var string
     */
    private $language;

    /**
     * @var bool
     */
    private $verified;


    public function __construct(
        string $kind,
        string $etag,
        array $emails,
        string $id,
        string $displayName,
        array $name,
        array $image,
        bool $plusUser,
        string $language,
        bool $verified
    ) {
        $this->kind = $kind;
        $this->etag = $etag;
        $this->emails = $emails;
        $this->id = $id;
        $this->displayName = $displayName;
        $this->name = $name;
        $this->image = $image;
        $this->plusUser = $plusUser;
        $this->language = $language;
        $this->verified = $verified;
    }


    public function getKind() : string
    {
        return $this->kind;
    }

    public function getEtag() : string
    {
        return $this->etag;
    }

    public function getEmails() : array
    {
        return $this->emails;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getDisplayName() : string
    {
        return $this->displayName;
    }

    public function getName() : array
    {
        return $this->name;
    }

    public function getImages() : array
    {
        return $this->image;
    }

    public function isPlusUser() : bool
    {
        return $this->isPlusUser;
    }

    public function getLanguage() : string 
    {
        return $this->language;
    }

    public function isVerified() : bool
    {
        return $this->isVerified();
    }

    public function getFullName() : string
    {
        $name = array_reverse($this->name);
        return implode(' ', $name);
    }

    public function getFirstImage() : string
    {
        return $this->image[0];
    }

    public function getFirstEmail() : string
    {
        return $this->emails[0]['value'];
    }


    public static function fromJSON(array $json) : GoogleOAuthUser
    {
        return new GoogleOAuthUser($json['kind'],
                                   $json['etag'],
                                   $json['emails'],
                                   $json['id'],
                                   $json['displayName'],
                                   $json['name'],
                                   $json['image'],
                                   $json['isPlusUser'],
                                   $json['language'],
                                   $json['verified']);
    }
}
