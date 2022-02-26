<?php

class Flight {
    private $conn;

    //метод-конструктор нужен для получения подключения к БД
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function get_flights ($from_iata, $to_iata, $date_from, $date_to , $passangers) {
        $query = '
            SELECT
                f.id AS flight_id,
                f.flight_code,
                a_from.city AS from_city,
                a_from.name AS from_name,
                a_from.iata AS from_iata,
                f.time_from AS from_time,
                a_to.city AS to_city,
                a_to.name AS to_name,
                a_to.iata AS to_iata,
                f.time_to AS to_time,
                f.cost AS price
            FROM `flights` f 
            INNER JOIN `airports` a_from 
            ON f.from_id = a_from.id
            INNER JOIN `airports` a_to
            ON f.to_id = a_to.id
            WHERE a_from.iata = :from_iata AND a_to.iata = :to_iata
        ';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':from_iata', htmlspecialchars($from_iata), PDO::PARAM_STR);
        $stmt->bindParam(':to_iata', htmlspecialchars($to_iata), PDO::PARAM_STR);

        $stmt->execute();

        $flights = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            //извлекаем строку
            extract($row);

            //записываем все данные из строки в массив-запись
            $flight_item = array(
                "flight_id" => $flight_id,
                "flight_code" => $flight_code,
                "from" => [
                    "city" => $from_city,
                    "airport" => $from_name,
                    "iata" => $from_iata,
                    "date" => $date_from,
                    "time" => substr($from_time,0,-3)
                ],
                "to" => [
                    "city" => $to_city,
                    "airport" => $to_name,
                    "iata" => $to_iata,
                    "date" => $date_to,
                    "time" => substr($to_time,0,-3)
                ],
                "cost" => $price
            );

            //заносим запись в массив записей
            array_push($flights, $flight_item);
        }

        return $flights;
    }
}