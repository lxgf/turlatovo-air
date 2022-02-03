<?php
include_once '../config/database.php';
include_once '../objects/User.php';
include_once '../objects/ErrorResponse.php';
include_once '../objects/FieldError.php';

header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$errors = [];

foreach ($_POST as $field_key => $field_value) {
    if (strlen($field_value) == 0) {
        $field_error = new FieldError($field_key, ($field_key . ' is empty!'));
        array_push($errors, $field_error->to_array());
    }
}

if (count($errors) == 0) {
    if ($user->authorization($_POST) == true) {
        http_response_code(200);
    } else {
        $field_error = new FieldError('phone', ('Phone or password is incorrect'));;
        array_push($errors, $field_error->to_array());
        $field_error = new FieldError('password', ('Phone or password is incorrect'));
        array_push($errors, $field_error->to_array());
        http_response_code(401);
        $error_response = new ErrorResponse(http_response_code(), 'Unauthorized', $errors);
        echo(json_encode($error_response->to_array(), JSON_UNESCAPED_UNICODE));
    }
} else {
    http_response_code(422);
    $error_response = new ErrorResponse(http_response_code(), 'Validation Error', $errors);
    echo(json_encode($error_response->to_array(), JSON_UNESCAPED_UNICODE));
}