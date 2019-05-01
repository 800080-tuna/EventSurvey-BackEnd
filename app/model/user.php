<?
//
//  databaseController.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/29/19.
//

class User {
    public $emailAddress;
    public $password;

    function __construct($emailAddress, $password) {
        $this->emailAddress = $emailAddress;
        $this->password = $password;
    }
}
?>
