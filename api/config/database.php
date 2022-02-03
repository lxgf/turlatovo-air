<?php
class Database {

    // Коннект к БД
    private $host = "localhost";
    private $db_name = "apidb";
    private $username = "root";
    private $password = "root";
    public $conn;

    // Получаем соединение с БД 
    public function getConnection(){

        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}