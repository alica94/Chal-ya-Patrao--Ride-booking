<?php
class Database {
    private $conn;
    private $config;

    public function __construct() {
        $this->config = require dirname(__FILE__) . '/config/config.php';
    }

    public function connect() {
        $this->conn = new mysqli(
            $this->config['host'],
            $this->config['username'],
            $this->config['password'],
            $this->config['db_name'],
            $this->config['port']
        );
        if (mysqli_connect_errno()) {
            die("Database Connection Failed: " . mysqli_connect_error());
        }
        return $this->conn;
    }

    public function query($sql) {
        return mysqli_query($this->conn, $sql);
    }

    public function prepare($sql) {
        return mysqli_prepare($this->conn, $sql);
    }

    public function fetchAll($result) {
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function lastInsertId() {
        return mysqli_insert_id($this->conn);
    }

    public function getError() {
        return mysqli_error($this->conn);
    }

    public function close() {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}
?>
