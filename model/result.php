<?
class Result {
    public $identifier;
    public $question;
    public $moreLikely;
    public $lessLikely;
    public $unchanged;
    public $event_id;

    function __construct($identifier, $question, $moreLikely, $lessLikely, $unchanged, $event_id) {
        $this->identifier = $identifier;
        $this->question = $question;
        $this->moreLikely = $moreLikely == null ? 0 : $moreLikely;
        $this->lessLikely = $lessLikely == null ? 0 : $lessLikely;
        $this->unchanged = $unchanged == null ? 0 : $unchanged;
        $this->event_id = $event_id;
    }
}
class ResponseCollection {
    public $response;
    public $count;

    function __construct($response, $count) {
        $this->response = $response;
        $this->count = $count;
    }

    function responseStringForIndex($responseIndex) {
        switch ($responseIndex) {
            case "0": return "More likely";
            case "1": return "Less likely";
            case "2": return "Unchanged";
        }
    }
}
class ResultResponse {
    public $question;
    public $totalResponses;
    public $responseCollections;

    function __construct($question, $totalResponses, $responseCollections) {
        $this->question = $question;
        $this->totalResponses = $totalResponses;
        $this->responseCollections = $responseCollections;
    }
}
?>
