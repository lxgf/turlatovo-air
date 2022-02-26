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

$required_fields = ['first_name', 'last_name', 'phone', 'document_number', 'password'];

foreach ($required_fields as $required_field) {
    if (!isset($_POST[$required_field])) {
        $field_error = new FieldError('general', ('All fields are required!'));
        array_push($errors, $field_error->to_array());
        http_response_code(422);
        $error_response = new ErrorResponse(http_response_code(), 'Validation Error', $errors);
        echo(json_encode($error_response->to_array(), JSON_UNESCAPED_UNICODE));
        return;
    }
}

foreach ($_POST as $field_key => $field_value) {
    if (strlen($field_value) == 0) {
        $field_error = new FieldError($field_key, ($field_key . ' is empty!'));
        array_push($errors, $field_error->to_array());
    }
    if ($field_key == 'document_number') {
        if (ctype_digit($field_value) == false) {
            $field_error = new FieldError($field_key, ($field_key . ' is not digital!'));
            array_push($errors, $field_error->to_array());
        } elseif (strlen($field_value) != 10) {
            $field_error = new FieldError($field_key, ($field_key . ' length is not 10!'));
            array_push($errors, $field_error->to_array());
        }
    }
    if ($field_key == 'phone' and !$user->check_phone_coincidence($field_value)) {
        $field_error = new FieldError($field_key, ($field_key . ' is not unique!'));
        array_push($errors, $field_error->to_array());
    }
}

if (count($errors) == 0) {
    http_response_code(204);
    $user->registration($_POST);
} else {
    http_response_code(422);
    $error_response = new ErrorResponse(http_response_code(), 'Validation Error', $errors);
    echo(json_encode($error_response->to_array(), JSON_UNESCAPED_UNICODE));
}