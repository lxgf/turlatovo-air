<?php
class ErrorResponse {
    private $code;
    private $message;
    private $errors;

    public function __construct($code, $message, $errors)
    {
        $this->code = $code;
        $this->message = $message;
        $this->errors = $errors;
    }

    public function to_array() {
        $response = ['error' => ['code' => $this->code, 'message' => $this->message, 'errors' => $this->errors]];
        return $response;
    }
}