<?
include_once 'apiResponseCodes.php';
include_once '../config/database.php';
include_once '../model/event.php';
include_once '../model/result.php';
include_once '../model/user.php';
include_once '../controllers/authenticator.php';

class DatabaseController {

    private $database;
    private $db;

    function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }

    //  EVENTS

    function createNewEvent($eventName) {

        $existingRecord = $this->fetchEventNamed($eventName);
        if($existingRecord != null) {
            return array("success" => true, "apiErrorCode" => APIErrorCode::EventExists);
        }

        $tableName = "Event";
        $event = new Event(uniqid(), $eventName);
        // $event = new Event("5cbfbd376eeeb", $eventName);

        $query = "INSERT INTO " . $tableName . "
                SET
                    identifier = :identifier,
                    name = :name";

        // prepare the query
        $pdoStatement = $this->db->prepare($query);
        $event->identifier=htmlspecialchars(strip_tags($event->identifier));
        $event->name=htmlspecialchars(strip_tags($event->name));
        $pdoStatement->bindParam(':identifier', $event->identifier);
        $pdoStatement->bindParam(':name',      $event->name);
        if( $pdoStatement->execute() ) {
            return array("success" => true, "res" => $event);
        }
        // $pdoErrorStatement = $pdoStatement->errorInfo();
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }

    function fetchEventNamed($eventName) {
        $parameters['name'] = $eventName;
        $sql = "SELECT * FROM Event WHERE (name = :name)";

        $pdoStatement = $this->db->prepare($sql);
        if( $pdoStatement->execute($parameters) ) {
            return $pdoStatement->fetch();
        }
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }

    function fetchAllEvents() {
        $sql = "SELECT * FROM Event";
        $pdoStatement = $this->db->prepare($sql);
        if( $pdoStatement->execute() ) {
            $res = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
            return array("success" => true, "res" => $res);
        }
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }

    //  RESULTS

    function fetchResultForEventWithIdentifier($eventIdentifier) {
        $parameters['event_id'] = $eventIdentifier;
        $sql = "SELECT question,
                    count(DISTINCT(identifier)) AS totalCount,
                    SUM(IF(moreLikely = TRUE , 1, 0)) AS moreLikelyCount,
                    SUM(IF(lessLikely = TRUE , 1, 0)) AS lessLikelyCount,
                    SUM(IF(unchanged = TRUE, 1, 0)) AS unchangedCount
                FROM Result
                WHERE (event_id = :event_id)
                GROUP BY question";

        $pdoStatement = $this->db->prepare($sql);
        if( $pdoStatement->execute($parameters) ) {
            $res = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
            return array("success" => true, "res" => $res);
        }
        print_r($pdoStatement->errorInfo());
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }

    function createNewResult($question, $moreLikely, $lessLikely, $unchanged, $eventIdentifer) {

        // print_r("more: {$moreLikely}  --  less: {$lessLikely}  --  unchanged: {$unchanged}");

        $result = new Result(uniqid(), $question, $moreLikely, $lessLikely, $unchanged, $eventIdentifer);

        $query = "INSERT INTO Result
                  SET
                    identifier = :identifier,
                    question = :question,
                    moreLikely = :moreLikely,
                    lessLikely = :lessLikely,
                    unchanged = :unchanged,
                    event_id = :event_id";

        // prepare the query
        $pdoStatement = $this->db->prepare($query);

        $result->identifier=htmlspecialchars(strip_tags($result->identifier));
        $result->question=htmlspecialchars(strip_tags($result->question));
        $result->moreLikely=htmlspecialchars(strip_tags($result->moreLikely));
        $result->lessLikely=htmlspecialchars(strip_tags($result->lessLikely));
        $result->unchanged=htmlspecialchars(strip_tags($result->unchanged));
        $result->event_id=htmlspecialchars(strip_tags($result->event_id));

        $pdoStatement->bindParam(':identifier', $result->identifier);
        $pdoStatement->bindParam(':question',   $result->question);
        $pdoStatement->bindParam(':moreLikely',   $result->moreLikely);
        $pdoStatement->bindParam(':lessLikely',   $result->lessLikely);
        $pdoStatement->bindParam(':unchanged',   $result->unchanged);
        $pdoStatement->bindParam(':event_id',   $result->event_id);

        if( $pdoStatement->execute() ) {
            return array("success" => true);
        }
        print_r($pdoStatement->errorInfo());
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }

    //  USERS

    function authenticateUserCredentials($emailAddress, $password) {

        $user = new User($emailAddress, $password);

        $parameters['emailAddress'] = $user->emailAddress;
        $sql = "SELECT password FROM User WHERE (emailAddress = :emailAddress)";
        $pdoStatement = $this->db->prepare($sql);

        if( $pdoStatement->execute($parameters) ) {

            $res = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
            $storePassword = $res["0"]["password"];

            if($storePassword == null) {
                return array("success" => true, "apiErrorCode" => APIErrorCode::AuthFailedEmail);
            }

            if($storePassword != $user->password) {
                return array("success" => true, "apiErrorCode" => APIErrorCode::AuthFailedPassword);
            }

            $jwt = Authenticator::newToken($user->id, $user->firstname, $user->lastname, $user->email);
            return array("success" => true, "res" => $jwt);
        }
        print_r($pdoStatement->errorInfo());
        return array("success" => false, "message" => "query failed");
    }
}
?>
