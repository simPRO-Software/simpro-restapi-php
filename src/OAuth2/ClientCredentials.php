<?php
namespace simPRO\RestClient\OAuth2;

class ClientCredentials extends Provider
{
    protected function loadType()
    {
        $this->type = 'client_credentials';
    }
}
