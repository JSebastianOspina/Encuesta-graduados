<?php
session_start();
include './vendor/autoload.php';
require 'Helpers/Sessions.php';

use Ospina\EasySQL\EasySQL;

$request = parseRequest();

$easySQL = new EasySQL('encuesta_graduados', 'local');
$easySQL->table('form_answers')->where('ID', '=', $request->id)->update(
    [
        'is_deleted' => 0
    ]
);
flashSession('Se ha reactivado el registro exitosamente');
header("Location: " . $_SERVER['HTTP_REFERER']);
die();

function parseRequest()
{
    return (object)$_REQUEST;
}

function dd($var)
{
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($var);
    die();
}

