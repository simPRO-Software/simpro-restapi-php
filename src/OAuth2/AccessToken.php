<?php
namespace simPRO\RestClient\OAuth2;

class AccessToken extends Provider
{
    protected function loadType()
    {
        $this->type = 'refresh_token';
    }

    protected function fetchOptions()
    {
        $accessToken = $this->getAccessTokenObject(false);
        $refreshToken = !empty($accessToken) ? $accessToken->getRefreshToken() : null;
        return [
            'refresh_token' => $refreshToken
        ];
    }
}
