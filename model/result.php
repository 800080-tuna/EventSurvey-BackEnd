<?
class Result {
    public $identifier;
    public $question;
    public $response;
    public $event_id;

    function __construct($identifier, $question, $response, $event_id) {
        $this->identifier = $identifier;
        $this->question = $question;
        $this->response = $response;
        $this->event_id = $event_id;
    }
}
?>
