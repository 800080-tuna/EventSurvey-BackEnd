<?
/// Manages mysql database connection
class Database {
    public $conn;

    //  TODO: don't store database info / credentials in plain text
    private $host = "localhost";
    private $db_name = "event_survey";
    private $username = "root";
    private $password = "!AVFX0770!";   //  TODO: DB credentials should not be in a public file

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
