<?
//
//  databaseController.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/22/19.
//

include_once(dirname(__FILE__) . '/../app/controllers/databaseController.php');
include_once(dirname(__FILE__) . '/../app/controllers/HTTPResponder.php');
include_once(dirname(__FILE__) . '/../app/controllers/authenticator.php');

//  TODO: This CORS header needs to be set properly
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$authHeader = apache_request_headers()["Authorization"];
Authenticator::authenticateRequest($authHeader);

$HTTP_Method = filter_input( INPUT_SERVER, 'REQUEST_METHOD' );

if( $HTTP_Method == "GET" ) {

    if (isset($_GET['event_identifier'])) {
        $eventIdentifier = $_GET['event_identifier'];
        $databaseController = new DatabaseController();
        $res = $databaseController->fetchResultForEventWithIdentifier($eventIdentifier);
        HTTPResponder::sendReponse($res);
    } else {
        //  TODO: if event_name is not set, should return missing param error
    }
} else if( $HTTP_Method == "POST" ) {

    $databaseController = new DatabaseController();
    $data = json_decode(file_get_contents("php://input"));
    $res = $databaseController->createNewResult($data->question,
                                                $data->moreLikely,
                                                $data->lessLikely,
                                                $data->unchanged,
                                                $data->eventIdentifer);
    HTTPResponder::sendReponse($res);
}

?>
