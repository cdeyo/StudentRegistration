<?php

class DatabaseHandler {

    private $host;
    private $username;
    private $password;
    private $dbName;
    private $conn;

    public function __construct($host = "localhost", $username = "root", $password = "", $dbName = "course_registration", $table = "students") {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbName = $dbName;
	$this->table = $table;
        $this->conn = $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->dbName";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $conn = new PDO($dsn, $this->username, $this->password, $options);
            return $conn;
        } catch (PDOException $e) {
            error_log("Database Connection Failed: " . $e->getMessage());
            return false;
        }
    }

    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount() > 0 ? true : false;
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    public function executeSelectQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll();
            return $result ? $result : [];
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
}

?>
