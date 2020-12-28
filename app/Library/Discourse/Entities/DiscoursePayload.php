<?php

namespace App\Library\Discourse\Entities;

final class DiscoursePayload
{
    /**
     * @var string
     */
    private $nonce;

    /**
     * @var int
     */
    private $pcbId;

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
    private $username;

    /**
     * @var string
     */
    private $avatarUrl;

    /**
     * @var bool
     */
    private $requiresActivation;

    /**
     * @var string
     */
    private $groups;

    public function __construct(?string $nonce = null)
    {
        $this->nonce = $nonce;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function setUsername(?string $username)
    {
        $this->username = $username;

        return $this;
    }

    public function setAvatarUrl(string $url)
    {
        $this->avatarUrl = $url;

        return $this;
    }

    public function setPcbId(int $id)
    {
        $this->pcbId = $id;

        return $this;
    }

    public function requiresActivation(bool $value)
    {
        $this->requiresActivation = $value;

        return $this;
    }

    /**
     * Comma separated list of group slugs.
     *
     *
     * @return $this
     */
    public function setGroups(string $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    public function build(): array
    {
        $payload = [
            'email' => $this->email,
            'external_id' => $this->pcbId,
        ];

        if (! empty($this->nonce)) {
            $payload['nonce'] = $this->nonce;
        }
        if ($this->requiresActivation !== null) {
            $payload['require_activation'] = $this->requiresActivation ? 'true' : 'false';
        }
        if ($this->username !== null) {
            $payload['username'] = $this->username;
        }
        if ($this->avatarUrl !== null) {
            $payload['avatar_url'] = $this->avatarUrl;
        }
        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }
        if ($this->groups !== null) {
            $payload['groups'] = $this->groups;
        } else {
            throw new \Exception('Groups must be provided in the payload');
        }

        return $payload;
    }
}
