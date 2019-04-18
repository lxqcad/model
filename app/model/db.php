<?php

class Database {
 
    private $host = "localhost";
    private $db_name = "db_onsite";
    private $username = "root";
    private $password = "";
    // database connection and table name
    private $conn;
 
    // constructor with database connection
    public function __construct() {
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
    }

    public function execute($query) {
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

}