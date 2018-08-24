<?php
require('/vendor/autoload.php');

$buildURL = 'https://client.simprosuite.com'; //Actual url for your customer's build
$token = 'xxx'; //From the key file you received

$provider = (new \simPRO\RestClient\OAuth2\APIKey())
    ->withBuildURL($buildURL)
    ->withToken($token)
;

//First, fetch and select a company ID. For most builds, there is only one company id.
$companyID = getCompanyID($provider);

//Then, list all employees and select one to view full details.
$employeeID = selectEmployeeFromList($provider, $companyID);

//Now let's get the full information for this employee.
viewEmployeeDetails($provider, $companyID, $employeeID);

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
