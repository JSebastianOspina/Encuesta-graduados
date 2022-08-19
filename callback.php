<?php
require 'vendor/autoload.php';
require 'Helpers/OspinaMysqlHelper.php';
//parse the request
try {
    $request = parseRequest();
} catch (JsonException $e) {
    echo 'There was a problem parsing the request';
}
//Get identification number an email
$identification_number = getIdentificationNumberFromRequest($request);
$email = getEmailFromRequest($request);
$name = getNameFromRequest($request);
$lastName = getLastNameFromRequest($request);
$mobilePhone = getMobilePhoneFromRequest($request);
$alternativePhone = getAlternativePhoneFromRequest($request);
$address = getAddressFromRequest($request);
$country = getCountryFromRequest($request);
$city = getCityFromRequest($request);

try {
    $answer = getAnswersFromRequestAsJsonText($request);
} catch (JsonException $e) {
    echo 'Error parseando JSON';
}

//Check if is graduated
try {
    $isGraduated = verifyIfIsGraduated($identification_number);
} catch (JsonException $e) {
    $isGraduated = false;
}

//Generate query
$smartQueryBuilder = \Ospina\SmartQueryBuilder\SmartQueryBuilder::table('form_answers');
$smartQueryBuilder->insert([
    'email' => $email,
    'identification_number' => $identification_number,
    'name' => $name,
    'last_name' => $lastName,
    'mobile_phone' => $mobilePhone,
    'alternative_mobile_phone' => $alternativePhone,
    'address' => $address,
    'country' => $country,
    'city' => $city,
    'is_graduated' => $isGraduated,
    'answers' => $answer,
    'created_at' => date('Y-m-d H:i:s'),
]);

$query = $smartQueryBuilder->getQuery();
$connection = OspinaMysqlHelper::newMysqlObject('encuesta_graduados', 'local');
$mysqlResponse = $connection->makeQuery($query);
dd($mysqlResponse);


function getNameFromRequest($request): string
{
    $questionName = 'Nombres';
    return $request->answers->$questionName ?? 'Campo no diligenciado';
}

function getLastNameFromRequest($request): string
{
    $questionName = 'Apellidos';
    return $request->answers->$questionName ?? 'Campo no diligenciado';
}

function getMobilePhoneFromRequest($request): string
{
    $questionName = 'Teléfono de contacto';
    return $request->answers->$questionName ?? 'Campo no diligenciado';
}

function getAlternativePhoneFromRequest($request): string
{
    $questionName = 'Teléfono alterno de contacto';
    return $request->answers->$questionName ?? 'Campo no diligenciado';
}

function getAddressFromRequest($request): string
{
    $questionName = 'Dirección de correspondencia';
    return $request->answers->$questionName ?? 'Campo no diligenciado';
}

function getCountryFromRequest($request): string
{
    $questionName = 'País';
    return $request->answers->$questionName ?? 'Campo no diligenciado';
}

function getCityFromRequest($request): string
{
    $questionName = 'Ciudad';
    return $request->answers->$questionName ?? 'Campo no diligenciado';
}

function getEmailFromRequest($request)
{
    return $request->code_user;
}

/**
 * @throws JsonException
 */
function getAnswersFromRequestAsJsonText($request)
{
    return json_encode($request->answers, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
}


/**
 * @throws JsonException
 */
function verifyIfIsGraduated(string $identification_number): bool
{

    $endpoint = 'https://academia.unibague.edu.co/atlante/graduados_siga.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta' => 'Consultar',
        'documento' => $identification_number,
    ]);
    $response = $curl->makeRequest();
    return json_decode($response, true, 512, JSON_THROW_ON_ERROR)['data'];
}

function getIdentificationNumberFromRequest($request)
{
    $identificationNumberQuestionName = 'Número de identificación';
    return $request->answers->$identificationNumberQuestionName;
}

/**
 * @throws JsonException
 */
function parseRequest()
{

    $data = file_get_contents('./data.json');
    //$data = file_get_contents('php//input');
    return json_decode($data, false, 512, JSON_THROW_ON_ERROR);

}

function dd($var)
{
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($var);
    die();
}