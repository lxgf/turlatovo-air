<?php
class FieldError {
    private $key;
    private $message;

    public function __construct($key, $message)
    {
        $this->key = $key;
        $this->message = $message;
    }

    public function to_array() {
        $response = ['key' => $this->key, 'message' => $this->message];
        return $response;
    }
}