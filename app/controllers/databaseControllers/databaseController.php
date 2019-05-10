<?
//
//  databaseController.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/22/19.
//

include_once(dirname(__FILE__) . './../apiErrorCodes.php');
include_once(dirname(__FILE__) . './../../config/database.php');

class DatabaseController {

    protected $database;
    protected $db;

    protected function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }
}
// $pdoStatementError = $pdoStatement->errorInfo();
// print_r($pdoStatementError);
?>
