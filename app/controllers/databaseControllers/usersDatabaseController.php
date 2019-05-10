<?
include_once(dirname(__FILE__) . '/databaseController.php');
include_once(dirname(__FILE__) . '/../auth/authenticator.php');
include_once(dirname(__FILE__) . '/../../model/user.php');

class UsersDatabaseController extends DatabaseController {

    public function __construct() {
        parent::__construct();
    }

    function authenticateUserCredentials($emailAddress, $password) {

        $user = new User($emailAddress, $password);

        $parameters['emailAddress'] = $user->emailAddress;
        $sql = "SELECT password, type FROM User WHERE (emailAddress = :emailAddress)";
        $pdoStatement = $this->db->prepare($sql);

        if( $pdoStatement->execute($parameters) ) {

            $res = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
            $storePassword = $res["0"]["password"];
            $userType = $res["0"]["type"];

            if($storePassword == null) {
                return array("success" => false, "apiErrorCode" => APIErrorCode::AuthFailedEmail);
            }

            if($storePassword != $user->password) {
                return array("success" => false, "apiErrorCode" => APIErrorCode::AuthFailedPassword);
            }

            $jwt = Authenticator::newToken($user->id, $user->firstname, $user->lastname, $user->email);
            return array("success" => true, "res" => $jwt, "type" => $userType);
        }
        return array("success" => false, "message" => "query failed");
    }
}
?>
