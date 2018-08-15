<?php
namespace simPRO\RestClient\OAuth2;

class ResourceOwner extends Provider
{
    protected function loadType()
    {
        $this->type = 'password';
    }

    protected function fetchOptions()
    {
        return [
            'username' => $this->username,
            'password' => $this->password
        ];
    }
}
