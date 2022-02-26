<?php
include_once '../config/database.php';
include_once '../objects/Airport.php';
include_once '../objects/ErrorResponse.php';
include_once '../objects/FieldError.php';

header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

$ap = new Airport($db);

$errors = [];

$airports_array = $ap->find($_GET);

http_response_code(200);

$data = ['items' => $airports_array];
echo(json_encode(['data' => $data]));