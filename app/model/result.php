<?
//
//  databaseController.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/22/19.
//

class Result {
    public $identifier;
    public $question;
    public $moreLikely;
    public $lessLikely;
    public $unchanged;
    public $event_id;
    public $session_id;

    function __construct($identifier, $question, $moreLikely, $lessLikely, $unchanged, $event_id, $session_id) {
        $this->identifier = $identifier;
        $this->question = $question;
        $this->moreLikely = $moreLikely == null ? 0 : $moreLikely;
        $this->lessLikely = $lessLikely == null ? 0 : $lessLikely;
        $this->unchanged = $unchanged == null ? 0 : $unchanged;
        $this->event_id = $event_id;
        $this->session_id = $session_id;
    }
}
?>
