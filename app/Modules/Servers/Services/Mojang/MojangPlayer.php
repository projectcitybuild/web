<?php
namespace App\Modules\Servers\Services\Mojang;

class MojangPlayer {

    private $alias;
    private $uuid;
    private $isDemoAccount;

    public function __construct(string $alias = null, string $uuid = null, bool $isDemoAccount = false) {
        $this->alias = $alias;
        $this->uuid = $uuid;
    }

    public function getAlias() : ?string {
        return $this->alias;
    }

    public function getUuid() : ?string {
        return $this->uuid;
    }

    public function isDemoAccount() : bool {
        return $this->isDemoAccount;
    }

    public function isPlayer() : bool {
        return isset($this->alias) && isset($this->uuid);
    }

}