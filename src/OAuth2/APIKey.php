<?php
namespace simPRO\RestClient\OAuth2;

class APIKey extends Provider
{
    public function refreshAccessToken()
    {
        //Not part of OAuth2, but we can emulate it.
        $accessToken = new \League\OAuth2\Client\Token\AccessToken([
            'token_type' => 'Bearer',
            'scope' => null,
            'access_token' => $this->token,
            'refresh_token' => null,
            'expires' => 127174453200
        ]);

        return $this->withAccessTokenObject($accessToken);
    }

    protected function loadType()
    {
        //Not applicable
    }
}
