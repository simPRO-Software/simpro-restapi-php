# simPRO PHP Rest Client

This library gives you a starting point for working with the simPRO RESTful API using PHP. It provides a means of authenticating, sending requests and receiving responses.

This readme assumes that you have read through each step of the [Getting Started](http://developer.simprogroup.com/apidoc/?page=57be307ee1bd93b729fdc4c13f15e201) section of our API documentation.
Instructions for how to use each of the grant types are detailed below.

## How to install
```
composer require simpro/restclient-php
```

## Client Credentials Grant
If your application authenticates using a client credentials grant, then see our [client credentials example](examples/ClientCredentials.php).

## Resource Owner Credentials Grant
If your application authenticates using a resource owner credentials grant, then see our [resource owner example](examples/ResourceOwner.php).

## Authorisation Code Grant
If your application authenticates using an authorisation code grant, then see our [authorisation code example](examples/AuthorisationCode.php).

## API Key
If your application authenticates using an API Key, then see our [API key example](examples/APIKey.php).

## Implicit Code Grant
If your application authenticates using an implicit code grant, then you cannot perform this using PHP.
Implicit grants are specifically designed for javascript clients. However, the workflow is similar to authorisation code grant.
Please see [our documentation](http://developer.simprogroup.com/apidoc/?page=3366d2ea7906f693b27d57ed9cca3acb#tag/Implicit-grant-workflow) for instructions.

## Workflow example
Once you have your provider loaded, find an example of creating, reading, updating and deleting data [here](examples/CustomerWorkflow.php).

## Still have questions?
Feel free to ask us a question on our [support forum](http://apiforum.simprogroup.com/) if you are unable to get your application up and running using our API.
