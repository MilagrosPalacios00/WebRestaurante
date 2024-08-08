<?php

class DB {
    public function conectDB() {
        $host = "localhost";
        $dbname = "resto2";
        $username = "root";
        $password = "";
    
        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->connection;
        } catch (PDOException $e) {
            die("Error en la conexión a la base de datos: " . $e->getMessage()); 
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        $this->connection = null;
    }   
}
