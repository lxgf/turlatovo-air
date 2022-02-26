<?php
include_once '../config/database.php';
include_once '../objects/Flight.php';
include_once '../objects/ErrorResponse.php';
include_once '../objects/FieldError.php';

header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

$flight = new Flight($db);

$errors = [];

$required_fields = ['from', 'to', 'date1', 'passengers'];

foreach ($required_fields as $required_field) {
    if (!isset($_GET[$required_field])) {
        $field_error = new FieldError($required_field, ($required_field . ' field is required!'));
        array_push($errors, $field_error->to_array());
    }
}

if ($_GET['passengers'] < 1 or $_GET['passengers'] > 8) {
    $field_error = new FieldError($required_field, ('incorrect amount of passengers!'));
    array_push($errors, $field_error->to_array());
}

if (strlen($_GET['from']) != 3 or strlen($_GET['to']) != 3) {
    $field_error = new FieldError($required_field, ('incorrect amount of passengers!'));
    array_push($errors, $field_error->to_array());
}

function check_date($date) {
    $month = substr($date,5,-3);;
    $day = substr($date,8,2);
    $year = substr($date,0,-6);

    return checkdate($month, $day, $year);
}

if (strlen($_GET['date1']) != 10 or strlen($_GET['date2']) != 10) {
    $field_error = new FieldError('dates', ('incorrect date format!'));
    array_push($errors, $field_error->to_array());
}

if (!check_date($_GET['date1']) or !check_date($_GET['date2'])) {
    $field_error = new FieldError('dates', ('incorrect date format!'));
    array_push($errors, $field_error->to_array());
}

$flights = array();
$flights["flights_to"] = array();
$flights["flights_back"] = array();

if (count($errors) == 0) {
    http_response_code(200);
    $flights["flights_to"] = $flight->get_flights($_GET['to'], $_GET['from'], $_GET['date1'], $_GET['date2'], $_GET['passengers']);
    if (isset($_GET['date2'])) {
        $flights["flights_back"] = $flight->get_flights($_GET['from'], $_GET['to'], $_GET['date1'], $_GET['date2'], $_GET['passengers']);
    }
    $data = ['data' => $flights];
    echo(json_encode($data, JSON_UNESCAPED_UNICODE));
} else {
    http_response_code(422);
    $error_response = new ErrorResponse(http_response_code(), 'Validation Error', $errors);
    echo(json_encode($error_response->to_array(), JSON_UNESCAPED_UNICODE));
}