<?
//
//  database.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 2/17/19.
//

/// Manages mysql database connection
class Database {
    public $conn;

    private $host;
    private $db_name;
    private $username;
    private $password;

    function __construct() {
        $config = parse_ini_file(dirname(__FILE__) . '/../config.ini');
        $this->host = $config["host"];
        $this->db_name = $config["db_name"];
        $this->username = $config["username"];
        $this->password = $config["password"];
    }

    /// Creates and returns database connection. Fails if unable to connect to database.
    public function getConnection() {
        $this->conn = null;
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            //  TODO: implement graceful error handling, here
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
