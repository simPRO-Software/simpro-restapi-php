# simPRO Rest Client

This library gives you a starting point for working with the simPRO RESTful API. It provides a means of authenticating, sending requests and receiving responses.

This readme assumes that you have read through each step of the [Getting Started](http://developer.simprogroup.com/apidoc/?page=57be307ee1bd93b729fdc4c13f15e201) section of our API documentation.
Instructions for how to use each of the grant types are detailed below.

## Client Credentials Grant
If your application authenticates using a client credentials grant, then use the following:
```php
$buildURL = 'https://client.simprosuite.com'; //Actual url for your customer's build
$clientID = 'xxx'; //From the key file you received
$clientSecret = 'xxx'; //From the key file you received

$provider = (new \simPRO\RestClient\OAuth2\ClientCredentials())
    ->withBuildURL($buildURL)
    ->withClientDetails($clientID, $clientSecret);
```

## Resource Owner Credentials Grant
If your application authenticates using a resource owner credentials grant, then use the following:
```php
$buildURL = 'https://client.simprosuite.com'; //Actual url for your customer's build
$clientID = 'xxx'; //From the key file you received
$clientSecret = 'xxx'; //From the key file you received
$username = 'xxx'; //Entered by the end user
$password = 'xxx'; //Entered by the end user

$provider = (new \simPRO\RestClient\OAuth2\ResourceOwner())
    ->withBuildURL($buildURL)
    ->withClientDetails($clientID, $clientSecret)
    ->withUserDetails($username, $password)
;
```
You should only need to do this once per user. Please see below for instructions on how to store and refresh your access token.

## Authorisation Code Grant
If your application authenticates using an authorisation code grant, then you will need to redirect them to their build to log in and authorise your application. You can do this with the following:
```php
$buildURL = 'https://client.simprosuite.com'; //Actual url for your customer's build
$clientID = 'xxx'; //From the key file you received
$clientSecret = 'xxx'; //From the key file you received

$provider = (new \simPRO\RestClient\OAuth2\AuthorisationCode())
    ->withBuildURL($buildURL)
    ->withClientDetails($clientID, $clientSecret)
;

if (isset($_GET['error'])) {
    throw new \Exception($_GET['error_description']);
} elseif (isset($_GET['code'])) {
    $provider->withCode($_GET['code']);
} else {
    header('Location: ' . $provider->getBaseAuthorizationUrl());
    exit();
}
```
You should only need to do this once per user. Please see below for instructions on how to store and refresh your access token.

## API Key
If your application authenticates using an API Key, then use the following:
```php
$buildURL = 'https://client.simprosuite.com'; //Actual url for your customer's build
$token = 'xxx'; //From the key file you received

$provider = (new \simPRO\RestClient\OAuth2\APIKey())
    ->withBuildURL($buildURL)
    ->withToken($token)
;
```

## Implicit Code Grant
If your application authenticates using an implicit code grant, then you cannot perform this using PHP.
Implicit grants are specifically designed for javascript clients. However, the workflow is similar to authorisation code grant.
Please see [our documentation](http://developer.simprogroup.com/apidoc/?page=3366d2ea7906f693b27d57ed9cca3acb#tag/Implicit-grant-workflow) for instructions.

## Store and refresh your tokens
Store your access token by calling `$accessTokenData = $provider->getAccessTokenObject()->jsonSerialize()` and saving the data in your database. Then load your subsequent providers using the following:
```php
$provider = (new \simPRO\RestClient\OAuth2\AccessToken())
    ->withBuildURL($buildURL)
    ->withClientDetails($clientID, $clientSecret)
    ->withAccessTokenObject($accessToken)
;
```
The system will automatically handle refreshing your access token when it expires. However, if you need to refresh the token manually, you may do so by calling `$provider->refreshAccessToken()`

## Example code
Copy (or require) the code from [CustomerWorkflow.php](examples/CustomerWorkflow.php) and call `seeCustomerWorkflow($provider)` to see the full workflow for listing, fetching, creating, updating, and deleting a customer.

## Still have questions?
Feel free to ask us a question on our [support forum](http://apiforum.simprogroup.com/) if you are unable to get your application up and running using our API.
