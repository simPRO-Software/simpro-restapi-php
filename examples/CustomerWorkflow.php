<?php
function seeCustomerWorkflow($provider)
{
    //List the first 30 customers.
    listCustomers($provider);

    //Create a new customer, and store the uri which we access the customer.
    $location = createCustomer($provider);

    //Should see that the customer matches the data we sent when we created the customer.
    getCustomer($provider, $location);

    //Update the customer.
    updateCustomer($provider, $location);

    //Should see that the customer now matches the data we sent when we updated the customer.
    getCustomer($provider, $location);

    //Delete the customer
    deleteCustomer($provider, $location);

    //Should see a 404 error because we're trying to fetch data for a customer we just deleted.
    getDeletedCustomer($provider, $location);
}

function listCustomers($provider)
{
    //Company ID is 0 for single-company builds. If you are querying against a multi-company build, adjust the company id accordingly.
    $url = '/api/v1.0/companies/0/customers/';
    print '<strong>get ' . $url . '</strong><br />';

    $request = $provider->fetchRequest($url);
    $json = $provider->fetchJSON($request); //Can use fetchJSON instead of fetchResponse to just get the JSON.
    print 'Customer List:<br /><pre>' . json_encode($json, JSON_PRETTY_PRINT) . '</pre>';

    print '<br />';
}

function createCustomer($provider)
{
    //Company ID is 0 for single-company builds. If you are querying against a multi-company build, adjust the company id accordingly.
    $url = '/api/v1.0/companies/0/customers/individuals/';
    $method = 'post';
    $data = ['GivenName' => 'Fred', 'FamilyName' => 'Nerf'];

    print '<strong>' . $method . ' ' . $url . '</strong><br /><pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre>';

    $request = $provider->fetchRequest($url, $method, $data); //PSR7 Request object.
    $response = $provider->fetchResponse($request); //PSR7 Response object.
    $headers = $response->getHeaders(); //Array of headers which were sent in the response.

    $location = $headers['Location'][0]; //Eg. /api/v1.0/companies/0/customers/individuals/1999
    $resourceBaseURI = $headers['Resource-Base-URI'][0]; //Eg. /api/v1.0/companies/0/customers/individuals/
    $resourceID = $headers['Resource-ID'][0]; //Eg. 1999
    $json = json_decode((string)$response->getBody()); //Full json representation of the customer we just inserted.

    print 'New Customer:<br /><pre>' . json_encode($json, JSON_PRETTY_PRINT) . '</pre>';
    print 'Customer ID: ' . $resourceID . '<br />';
    print 'Accessed by URL: ' . $location . '<br />';
    print '<br />';

    return $location;
}

function getCustomer($provider, $location)
{
    $url = $location;
    print '<strong>get ' . $url . '</strong><br />';

    $request = $provider->fetchRequest($url);
    $json = $provider->fetchJSON($request);
    print 'Customer:<br /><pre>' . json_encode($json, JSON_PRETTY_PRINT) . '</pre>';
    print '<br />';
}

function updateCustomer($provider, $location)
{
    $url = $location;
    $method = 'patch';
    $data = ['GivenName' => 'Freddy', 'FamilyName' => 'Nerfed'];

    print '<strong>' . $method . ' ' . $url . '</strong><br /><pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre>';

    $request = $provider->fetchRequest($url, $method, $data);
    $response = $provider->fetchResponse($request);
    $statusCode = $response->getStatusCode();

    print 'Response Status Code: ' . $statusCode . '<br />';
    print '<br />';
}

function deleteCustomer($provider, $location)
{
    $url = $location;
    $method = 'delete';
    print '<strong>' . $method . ' ' . $url . '</strong><br />';

    $response = $provider->fetchResponse($provider->fetchRequest($url, $method)); //Delete the customer we just inserted.
    $statusCode = $response->getStatusCode();

    print 'Response Status Code: ' . $statusCode . '<br />';

    print '<br />';
}

function getDeletedCustomer($provider, $location)
{
    $url = $location;
    $allowError = true;
    print '<strong>get ' . $url . '</strong><br />';

    $request = $provider->fetchRequest($url);

    //We're expecting a 404 error. Set $allowError to true to not throw an exception on 4xx or 5xx errors.
    $response = $provider->fetchResponse($request, $allowError);
    $statusCode = $response->getStatusCode();

    print 'Response Status Code: ' . $statusCode . '<br />';
    print '<br />';
}
