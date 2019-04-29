<?
class User {
    public $emailAddress;
    public $password;

    function __construct($emailAddress, $password) {
        $this->emailAddress = $emailAddress;
        $this->password = $password;
    }
}
?>
