<?php
class Airport {
    private $conn;

    //метод-конструктор нужен для получения подключения к БД
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function find($user_data) {
        $find_key = '%' . $user_data['query'] . '%';

        $query = "
        SELECT
            `name`, `iata`
        FROM
            airports
        WHERE
            iata like :find_key OR name like :find_key OR city like :find_key
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':find_key', htmlspecialchars($find_key), PDO::PARAM_STR);

        $stmt->execute();

        $airports = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            //извлекаем строку
            extract($row);

            //записываем все данные из строки в массив-запись
            $ap_item = array(
                "name" => $name,
                "iata" => $iata,
            );

            //заносим запись в массив записей
            array_push($airports, $ap_item);
        }

        return $airports;
    }
}