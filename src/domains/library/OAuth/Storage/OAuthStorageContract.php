<?php

namespace Domains\Library\OAuth\Storage;

interface OAuthStorageContract 
{
    public function store(string $redirectUri);
    public function clear();
    public function get() : ?string;
    public function pop() : ?string;
}