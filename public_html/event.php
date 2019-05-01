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

    $databaseController = new DatabaseController();
    $res = $databaseController->fetchAllEvents();
    HTTPResponder::sendReponse($res);
} else if( $HTTP_Method == "POST" ) {

    $data = json_decode(file_get_contents("php://input"));
    $eventName = $data->eventName;
    $databaseController = new DatabaseController();
    $res = $databaseController->createNewEvent($eventName);
    HTTPResponder::sendReponse($res);
}
?>
