<?
include_once(dirname(__FILE__) . '/databaseController.php');
include_once(dirname(__FILE__) . '/../auth/authenticator.php');
include_once(dirname(__FILE__) . '/../../model/user.php');

class UsersDatabaseController extends DatabaseController {

    public function __construct() {
        parent::__construct();
    }
    // create new user record
    function createUser($firstName, $lastName, $emailAddress, $password) {

        $emailExists = $this->emailExists($emailAddress);
        if(is_array($emailExists)) {
            //  if $res is array, its a response that should be forwarded to client
            return $res;
        }

        if($emailExists == true) {
            //  if $res is true, email exists - notify client
            return array("success" => false, "message" => APIErrorCode::EmailExists);
        }

        $sql = "INSERT INTO User
                SET
                    identifier = :identifier,
                    firstName = :firstName,
                    lastName = :lastName,
                    emailAddress = :emailAddress,
                    password = :password,
                    type = :type";

        // prepare the query
        $pdoStatement = $this->db->prepare($sql);

        // sanitize
        $firstName = htmlspecialchars(strip_tags($firstName));
        $lastName = htmlspecialchars(strip_tags($lastName));
        $emailAddress = htmlspecialchars(strip_tags($emailAddress));
        $password = htmlspecialchars(strip_tags($password));

        // bind the values
        $pdoStatement->bindParam(':identifier',     uniqid());
        $pdoStatement->bindParam(':firstName',      $firstName);
        $pdoStatement->bindParam(':lastName',       $lastName);
        $pdoStatement->bindParam(':emailAddress',   $emailAddress);
        $pdoStatement->bindParam(':password',       password_hash($password, PASSWORD_BCRYPT));
        $pdoStatement->bindParam(':type',           intval(1)); //  user type defaults to 1 (basic)

        // execute the query, also check if query was successful
        if( $pdoStatement->execute() ) {
            return array("success" => true);
        }
        print("createUser failed - ");
        $pdoStatementError = $pdoStatement->errorInfo();
        print_r($pdoStatementError);
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }

    function emailExists($emailAddress) {
        print("email: {$emailAddress} :email - ");

        $emailAddress=htmlspecialchars(strip_tags($emailAddress));
        $parameters['emailAddress'] = $emailAddress;
        $sql = "SELECT count(identifier) AS count FROM User WHERE (emailAddress = :emailAddress)";

        $pdoStatement = $this->db->prepare($sql);
        if($pdoStatement->execute($parameters)) {
            $res = $pdoStatement->fetch();
            $count = $res["count"];

            if($count == 0) {
                return false;
            }
            return true;
        }
        // $pdoStatementError = $pdoStatement->errorInfo();
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
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

            if(!password_verify($user->password, $storePassword)) {
                return array("success" => false, "apiErrorCode" => APIErrorCode::AuthFailedPassword);
            }

            $jwt = Authenticator::newToken($user->id, $user->firstname, $user->lastname, $user->email);
            return array("success" => true, "res" => $jwt, "type" => $userType);
        }
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }
}
?>
