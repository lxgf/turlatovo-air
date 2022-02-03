<?php
class User {
    private $conn;

    //метод-конструктор нужен для получения подключения к БД
    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function generate_token() {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
        $result = '';
        for ($i = 0; $i < 50; $i++)
            $result .= $characters[mt_rand(0, 63)];
        return $result;
    }

    public function registration($user_data) {
        $query = "
        INSERT INTO users (`first_name`, `last_name`, `phone`, `document_number`, `password`, `api_token`, `created_at`, `updated_at`)
        VALUES (:first_name, :last_name, :phone, :document_number, :password, :api_token, NOW(), NOW())
        ";

        $stmt = $this->conn->prepare($query);

        foreach ($user_data as $field_key => $field_value) {
            $stmt->bindParam((':'.$field_key), htmlspecialchars($field_value), PDO::PARAM_STR);
        }

        $stmt->bindParam(':api_token', $this->generate_token());

        $stmt->execute();

        return $stmt;
    }

    public function check_phone_unique($phone) {
        $query = "
        SELECT `phone` FROM users WHERE `phone` = :phone
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam((':phone'),  htmlspecialchars($phone), PDO::PARAM_STR);

        $stmt->execute();

        if (count($stmt->fetchAll(PDO::FETCH_COLUMN)) == 0)
            return true;

        return false;
    }
}