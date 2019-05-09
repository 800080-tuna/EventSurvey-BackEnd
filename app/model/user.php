<?
//
//  databaseController.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/29/19.
//

abstract class UserType
{
    const Admin = 0;
    const Basic = 1;
}

class User {
    public $emailAddress;
    public $password;
    public $userType;   //  UserType

    function __construct($emailAddress, $password) {
        $this->emailAddress = $emailAddress;
        $this->password = $password;
    }
}
?>
