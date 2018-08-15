<?php
namespace simPRO\RestClient\OAuth2;

class AuthorisationCode extends Provider
{
    protected function loadType()
    {
        $this->type = 'authorization_code';
    }

    protected function fetchOptions()
    {
        return [
            'code' => $this->code
        ];
    }
}
