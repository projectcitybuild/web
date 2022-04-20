<?php

namespace Library\Discourse\Entities;

final class DiscoursePayload
{
    private ?string $nonce = null;
    private ?int $pcbId = null;
    private ?string $email = null;
    private ?string $name = null;
    private ?string $username = null;
    private ?string $avatarUrl = null;
    private bool $requiresActivation = true;
    private ?string $groups = null;

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

    public function setGroups(string $groups)
    {
        $this->groups = $groups;
        return $this;
    }

    public function build(): array
    {
        if ($this->email === null) {
            throw new \Exception('Email must be provided in the payload');
        }
        if ($this->pcbId === null) {
            throw new \Exception('PCB ID must be provided in the payload');
        }
        if ($this->groups === null) {
            throw new \Exception('Groups must be provided in the payload');
        }

        $payload = [
            'email' => $this->email,
            'external_id' => $this->pcbId,
            'groups' => $this->groups,
            'require_activation' => $this->requiresActivation,
        ];

        if (! empty($this->nonce)) {
            $payload['nonce'] = $this->nonce;
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

        return $payload;
    }
}
