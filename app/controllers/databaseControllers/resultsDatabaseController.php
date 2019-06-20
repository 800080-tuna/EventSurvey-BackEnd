<?
include_once(dirname(__FILE__) . '/databaseController.php');
include_once(dirname(__FILE__) . '/../../model/result.php');


class ResultsDatabaseController extends DatabaseController {

    public function __construct() {
        parent::__construct();
    }

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
        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }

    function createNewResult($resultArray) {

        foreach ($resultArray as $resultData) {

            $result = new Result(uniqid(),
                                $resultData->question,
                                $resultData->moreLikely,
                                $resultData->lessLikely,
                                $resultData->unchanged,
                                $resultData->eventIdentifier,
                                $resultData->sessionIdentifier);

            $query = "INSERT INTO Result
                      SET
                        identifier = :identifier,
                        question = :question,
                        moreLikely = :moreLikely,
                        lessLikely = :lessLikely,
                        unchanged = :unchanged,
                        event_id = :event_id,
                        session_id = :session_id";

            // prepare the query
            $pdoStatement = $this->db->prepare($query);

            $result->identifier=htmlspecialchars(strip_tags($result->identifier));
            $result->question=htmlspecialchars(strip_tags($result->question));
            $result->moreLikely=htmlspecialchars(strip_tags($result->moreLikely));
            $result->lessLikely=htmlspecialchars(strip_tags($result->lessLikely));
            $result->unchanged=htmlspecialchars(strip_tags($result->unchanged));
            $result->event_id=htmlspecialchars(strip_tags($result->event_id));
            $result->session_id=htmlspecialchars(strip_tags($result->session_id));

            $pdoStatement->bindParam(':identifier',     $result->identifier);
            $pdoStatement->bindParam(':question',       $result->question);
            $pdoStatement->bindParam(':moreLikely',     $result->moreLikely);
            $pdoStatement->bindParam(':lessLikely',     $result->lessLikely);
            $pdoStatement->bindParam(':unchanged',      $result->unchanged);
            $pdoStatement->bindParam(':event_id',       $result->event_id);
            $pdoStatement->bindParam(':session_id',     $result->session_id);

            if( $pdoStatement->execute() == false ) {
                return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
            }
        }

        return array("success" => true);
    }

    function reconcileResponseIdentifiers($data) {

        $payload = $data[0];
        $eventIdentifier = $payload->eventIdentifier;
        $sessionIdentifiers = $payload->sessionIdentifiers;

        $sessionIdentifier = function($res) {
            return $res["session_id"];
        };

        //  fetch all results matching $surveyIdentifier
        $parameters['event_id'] = $eventIdentifier;
        $sql = "SELECT *
                FROM Result
                WHERE (event_id = :event_id)";

        $pdoStatement = $this->db->prepare($sql);
        if( $pdoStatement->execute($parameters) ) {
            $results = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

            //  colapse into array of result.sessionIdentifiers
            $knownSessionIdentifiers = array_map($sessionIdentifier, $results);

            // //  iterate over $sessionIdentifiers, and if sessionId does not exist, remove it from the list
            $res = array_intersect($sessionIdentifiers, $knownSessionIdentifiers);
            $res = array_values($res);
            return array("success" => true, "res" => $res);
        }

        return array("success" => false, "apiErrorCode" => APIErrorCode::QueryFailed);
    }
}
?>
