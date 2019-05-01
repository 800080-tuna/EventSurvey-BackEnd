<?
//
//  databaseController.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/22/19.
//

class Event {
    public $identifier;
    public $name;

    function __construct($identifier, $name) {
        $this->identifier = $identifier;
        $this->name = $name;
    }
}
?>
