<?php
namespace simPRO\RestClient\OAuth2;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;

abstract class Provider extends \League\OAuth2\Client\Provider\AbstractProvider
{
    /**
     * @var string The full url to access the simPRO build, eg. https://mycompany.simprosuite.com
     */
    protected $buildURL;
    /**
     * @var string Grant type identifier, overridden by each provider subclass.
     */
    protected $type;
    /**
     * @var string Provided to the third-party application by the end user.
     */
    protected $username;
    /**
     * @var string Provided to the third-party application by the end user.
     */
    protected $password;
    /**
     * @var string Provided to third-party developers via the key file, used in API Key only.
     */
    protected $token;
    /**
     * @var string Returned after the user authorises the application in the authorization code grant
     */
    protected $code;
    /**
     * @var AccessToken Stores information about the application's current token.
     */
    protected $accessToken;

    public function getAccessTokenObject($validate = true)
    {
        if ($validate && !$this->validAccessTokenObject()) {
            $this->refreshAccessToken();
        }

        return $this->accessToken;
    }

    public function withAccessTokenObject($accessToken)
    {
        if ($accessToken instanceof AccessToken) {
            $this->accessToken = $accessToken;
        } elseif (is_string($accessToken)) {
            throw new \Exception('Access token must be an object. If you are providing an API Key, please use withToken');
        } elseif (is_object($accessToken)) {
            $this->accessToken = new AccessToken(get_object_vars($accessToken));
        } elseif (is_array($accessToken)) {
            $this->accessToken = new AccessToken($accessToken);
        } else {
            throw new \Exception('Access token must be an object.');
        }
        return $this;
    }

    public function refreshAccessToken()
    {
        return $this->withAccessTokenObject($this->getAccessToken($this->getType(), $this->fetchOptions()));
    }

    public function fetchRequest($url, $method = 'get', $json = null)
    {
        $options = [];
        if ($json !== null) {
            if (!is_string($json)) {
                $json = json_encode($json);
            }
            $options['body'] = $json;
        }
        return $this->getAuthenticatedRequest($method, $this->getBuildURL() . $url, $this->getAccessTokenObject(), $options);
    }

    public function fetchResponse(\Psr\Http\Message\RequestInterface $request, $allowError = false)
    {
        if ($allowError) {
            try {
                return $this->getResponse($request);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                //Return the response which contains the error.
                return $e->getResponse();
            }
        } else {
            return $this->getResponse($request);
        }
    }

    public function fetchJSON(\Psr\Http\Message\RequestInterface $request, $allowError = false)
    {
        $response = $this->fetchResponse($request, $allowError);
        return json_decode((string)$response->getBody());
    }

    public function getBuildURL()
    {
        if (!isset($this->buildURL)) {
            throw new \Exception('Build url must be set using withBuildURL');
        }
        return $this->buildURL;
    }

    public function withBuildURL($buildURL)
    {
        if (substr($buildURL, -1, 1) === '/') {
            //Don't store the trailing slash. All of our documentation has urls in the format /api/etc.
            $this->buildURL = substr($buildURL, 0, strlen($buildURL) - 1);
        } else {
            $this->buildURL = $buildURL;
        }

        return $this;
    }

    public function withClientDetails($clientId, $clientSecret)
    {
        return $this
            ->withClientID($clientId)
            ->withClientSecret($clientSecret)
        ;
    }

    public function withClientID($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function withClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function withUserDetails($username, $password)
    {
        return $this
            ->withUsername($username)
            ->withPassword($password)
        ;
    }

    public function withUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function withPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function withToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function withCode($code)
    {
        $this->code = $code;
        return $this;
    }

    protected function fetchOptions()
    {
        return [];
    }

    protected function getType()
    {
        if (!isset($this->type)) {
            $this->loadType();
        }
        return $this->type;
    }

    abstract protected function loadType();

    protected function validAccessTokenObject()
    {
        return !empty($this->accessToken) && !$this->accessToken->hasExpired();
    }

    protected function getAllowedClientOptions(array $options)
    {
        return array_unique(array_merge(['verify'], parent::getAllowedClientOptions($options)));
    }

    protected function getAuthorizationHeaders($token = null)
    {
        return [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    protected function checkResponse(\Psr\Http\Message\ResponseInterface $response, $data)
    {
        if (isset($data['error_description'])) {
            throw new IdentityProviderException($data['error_description'], $response->getStatusCode(), $data);
        }
    }

    protected function createResourceOwner(array $response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        throw new \Exception('The simPRO RESTful API does not implement retrieving resource owner details.');
    }

    protected function getDefaultScopes()
    {
        return [];
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getBuildURL() . '/oauth2/token';
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->getBuildURL() . '/oauth2/login?client_id=' . $this->clientId;
    }

    public function getResourceOwnerDetailsUrl(\League\OAuth2\Client\Token\AccessToken $token)
    {
        throw new \Exception('The simPRO RESTful API does not implement retrieving resource owner details.');
    }
}
