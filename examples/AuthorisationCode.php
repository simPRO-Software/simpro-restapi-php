<?php
require('/vendor/autoload.php');

$buildURL = 'https://client.simprosuite.com'; //Actual url for your customer's build
$clientID = 'xxx'; //From the key file you received
$clientSecret = 'xxx'; //From the key file you received

//Create an access token for this user. You only need to call this once per user.
createAccessToken($buildURL, $clientID, $clientSecret);

//On subsequent loads, you don't need the user to login again. Just use the saved access token.
//You will still need the build url, client id, and client secret.
$provider = loadAccessToken($buildURL, $clientID, $clientSecret);

//The system will automatically handle refreshing your token. But if, for whatever reason, you need to refresh it manually:
//$provider->refreshAccessToken();

//Now, fetch and select a company ID. For most builds, there is only one company id.
$companyID = getCompanyID($provider);

//Then, list all employees and select one to view full details.
$employeeID = selectEmployeeFromList($provider, $companyID);

//Now let's get the full information for this employee.
viewEmployeeDetails($provider, $companyID, $employeeID);

function createAccessToken($buildURL, $clientID, $clientSecret)
{
    //Load the authorisation code provider using your details.
    $provider = (new \simPRO\RestClient\OAuth2\AuthorisationCode())
        ->withBuildURL($buildURL)
        ->withClientDetails($clientID, $clientSecret)
    ;

    //Now, the user needs to log into simPRO and authorise your application.
    //The first step is to redirect them to simPRO.
    //After a time, simPRO will redirect them back to your application with either an authorisation code or an error.
    //If there's an error, you will need to handle it. If it's successful, we can proceed.

    //=========================================================================================================
    // If it doesn't redirect properly, then your application's Redirect URI may not be correctly configured.
    // See http://developer.simprogroup.com/apidoc/?page=5b265889ce433f9710dcef721505c158
    //=========================================================================================================

    if (isset($_GET['error'])) {
        //The authorisation server returned an error.
        throw new \Exception($_GET['error_description']);
    } elseif (isset($_GET['code'])) {
        //The authorisation server successfully returned a code. Use it.
        $provider->withCode($_GET['code']);
    } else {
        //We don't have an authorisation code yet.
        //Redirect the user to simPRO so they can log in and authorise your application.
        header('Location: ' . $provider->getBaseAuthorizationUrl());
        exit();
    }

    //Now that we are fully authenticated, fetch the access token.
    $accessTokenData = $provider->getAccessTokenObject()->jsonSerialize();

    print "Access token:" . PHP_EOL;
    print json_encode($accessTokenData, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

    //And store it in your database.
    $yourDatabase->saveAccessToken($accessTokenData);
}

function loadAccessToken($buildURL, $clientID, $clientSecret)
{
    //Load it from your database.
    $accessTokenData = $yourDatabase->loadAccessToken();

    //Use the access token provider instead.
    $provider = (new \simPRO\RestClient\OAuth2\AccessToken())
        ->withBuildURL($buildURL)
        ->withClientDetails($clientID, $clientSecret)
        ->withAccessTokenObject($accessTokenData)
    ;

    return $provider;
}

function getCompanyID($provider)
{
    $companyURL = '/api/v1.0/companies/';

    print "Now calling URL {$companyURL}" . PHP_EOL;
    $request = $provider->fetchRequest($companyURL); //PSR7 Request Object
    $response = $provider->fetchResponse($request); //PSR7 Response Object
    $companyArray = json_decode((string)$response->getBody());
    print "Response is:" . PHP_EOL;
    print json_encode($companyArray, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

    foreach ($companyArray as $companyObject) {
        print "Company with ID " . $companyObject->ID . " is named " . $companyObject->Name . PHP_EOL;
    }

    //Select which company ID you wish to use.
    $companyID = $companyArray[count($companyArray)-1]->ID;
    print "Using company id {$companyID}" . PHP_EOL . PHP_EOL;
    return $companyID;
}

function selectEmployeeFromList($provider, $companyID)
{
    //Fetch a list of employees
    $employeeListURL = "/api/v1.0/companies/{$companyID}/employees/";

    print "Now calling URL {$employeeListURL}" . PHP_EOL;
    $request = $provider->fetchRequest($employeeListURL);
    $employeeArray = $provider->fetchJSON($request); //Can use fetchJSON instead of fetchResponse to just get the JSON.
    print 'Employee Count: ' . count($employeeArray) . PHP_EOL;

    //Select an employee.
    $employeeObject = $employeeArray[mt_rand(0, count($employeeArray)-1)];
    print 'Selected employee: ' . PHP_EOL;
    print json_encode($employeeObject, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

    $employeeID = $employeeObject->ID;
    return $employeeID;
}

function viewEmployeeDetails($provider, $companyID, $employeeID)
{
    $employeeGetURL = "/api/v1.0/companies/{$companyID}/employees/{$employeeID}";
    print "Now calling URL {$employeeGetURL}" . PHP_EOL;

    $employeeInfo = $provider->fetchJSON($provider->fetchRequest($employeeGetURL)); //Can combine into one line.
    print json_encode($employeeInfo, JSON_PRETTY_PRINT) . PHP_EOL;
}
