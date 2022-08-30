<?php
session_start();
require 'autoloader.php';

use Ospina\EasySQL\EasySQL;

//Leer y parsear la petición del front (ID)
$requestData = getDataFromPostRequest();

//Crear un método para volver a sincronizar - Función (verifyIfIsGraduated) arroja 0 o 1
try {
    $isGraduated = verifyIfIsGraduated($requestData->identificationNumber);
} catch (JsonException $e) {
}

//Actualizar DataBase
$mysql = new EasySQL('encuesta_graduados', 'local');
$result = $mysql->table('form_answers')
    ->where('id', '=', $requestData->id)
    ->update([
        'is_graduated' => $isGraduated
    ]);

//Mostrar en front el usuario actualizado y removido de "No encontrado"
//Write notification

flashSession($isGraduated === 1 ? 'El usuario ha sido migrado exitosamente' : 'El usuario aún no se encuentra migrado en el SIGA');

header('Location: pending.php');
die();
//---------------- Functions-------------------

function getDataFromPostRequest(): object
{

    if (!isset($_POST['id'])) {
        echo 'Debe proporcionar un ID válido';
        die();
    }
    if (!isset($_POST['identification_number'])) {
        echo 'Debe proporcionar una cédula válida';
        die();
    }
    $id = $_POST['id'];
    $identificationNumber = $_POST['identification_number'];

    return (object)[
        'id' => $id,
        'identificationNumber' => $identificationNumber,
    ];
}

//Function for verify
function verifyIfIsGraduated(string $identification_number): int
{
    $response = random_int(0, 1);
    return $response;

    $endpoint = 'https://academia.unibague.edu.co/atlante/graduados_siga.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta' => 'Consultar',
        'documento' => $identification_number,
    ]);
    $response = $curl->makeRequest();
    return json_decode($response, true, 512, JSON_THROW_ON_ERROR)['data'];
}

function dd($var)
{
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($var);
    die();
}

