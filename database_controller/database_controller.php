<?

include_once '../config/database.php';
include_once '../model/event.php';
include_once '../model/result.php';


class DatabaseController {

    private $database;
    private $db;

    function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }

    function createNewEvent($eventName) {

        $existingRecord = $this->fetchEventNamed($eventName);
        if($existingRecord != null) {
            return array("success" => false, "message" => "record exists");
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
            return array("success" => true);
        }
        // $pdoErrorStatement = $pdoStatement->errorInfo();
        return array("success" => false, "message" => "query failed");
    }

    function fetchEventNamed($eventName) {
        $parameters['name'] = $eventName;
        $sql = "SELECT * FROM Event WHERE (name = :name)";

        $pdoStatement = $this->db->prepare($sql);
        if( $pdoStatement->execute($parameters) ) {
            return $pdoStatement->fetch();
        }
        return array("success" => false, "message" => "query failed");
    }

    function fetchAllEvents() {
        $sql = "SELECT * FROM Event";
        $pdoStatement = $this->db->prepare($sql);
        if( $pdoStatement->execute() ) {
            $res = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
            return array("success" => true, "res" => json_encode($res));
        }
        return array("success" => false, "message" => "query failed");
    }

    function fetchResultForEventWithIdentifier($eventIdentifier) {
        $parameters['event_id'] = $eventIdentifier;
        $sql = "SELECT * FROM Result WHERE (event_id = :event_id)";

        $pdoStatement = $this->db->prepare($sql);
        if( $pdoStatement->execute($parameters) ) {
            $res = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
            return array("success" => true, "res" => json_encode($res));
        }
        return array("success" => false, "message" => "query failed");
    }

    function createNewResult($question, $response, $eventIdentifer) {

        // print_r("JJR - " . $question . " - " . $response . " - " . $eventIdentifer);

        $responseNum = intval($response);

        $tableName = "Result";
        $result = new Result(uniqid(), $question, $responseNum, $eventIdentifer);
        // $event = new Event("5cbfbd376eeeb", $question, $response, $eventIdentifer);

        // print_r("JJR - Result: " . $result->identifier . " - " . $result->question . " - " . $result->response . " - " . $result->event_id);


        $query = "INSERT INTO " . $tableName . "
                SET
                    identifier = :identifier,
                    question = :question,
                    response = :response,
                    event_id = :event_id";

        // prepare the query
        $pdoStatement = $this->db->prepare($query);

        $result->identifier=htmlspecialchars(strip_tags($result->identifier));
        $result->question=htmlspecialchars(strip_tags($result->question));
        $result->response=htmlspecialchars(strip_tags($result->response));
        $result->event_id=htmlspecialchars(strip_tags($result->event_id));

        $pdoStatement->bindParam(':identifier', $result->identifier);
        $pdoStatement->bindParam(':question',   $result->question);
        $pdoStatement->bindParam(':response',   $result->response);
        $pdoStatement->bindParam(':event_id',   $result->event_id);

        // print_r("JJR - Result: " . $result->identifier . " - " . $result->question . " - " . $result->response . " - " . $result->event_id);

        if( $pdoStatement->execute() ) {
            return array("success" => true);
        }
        $pdoErrorStatement = $pdoStatement->errorInfo();
        print_r($pdoErrorStatement);
        return array("success" => false, "message" => "query failed");
    }
}
?>
