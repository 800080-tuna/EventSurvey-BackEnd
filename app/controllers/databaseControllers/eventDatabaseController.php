<?
include_once(dirname(__FILE__) . '/databaseController.php');
include_once(dirname(__FILE__) . '/../../model/event.php');


class EventDatabaseController extends DatabaseController {

    public function __construct() {
        parent::__construct();
    }

    function createNewEvent($eventName) {

        $existingRecord = $this->fetchEventNamed($eventName);
        if($existingRecord != null) {
            return array("success" => false, "apiErrorCode" => APIErrorCode::EventExists);
        }

        $tableName = "Event";
        $event = new Event(uniqid(), $eventName);

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
}
?>
