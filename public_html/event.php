<?
//
//  databaseController.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/22/19.
//

include_once(dirname(__FILE__) . '/../app/controllers/databaseController.php');
include_once(dirname(__FILE__) . '/../app/controllers/HTTPResponder.php');
include_once(dirname(__FILE__) . '/../app/controllers/headerAccess.php');
include_once(dirname(__FILE__) . '/../app/controllers/authenticator.php');

header("Access-Control-Allow-Origin: https://philipseventsurvey.avfx.com");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$authHeader = HeaderAccess::fetchAuthHeader();
Authenticator::authenticateRequest($authHeader);

if( $_SERVER['REQUEST_METHOD'] === 'GET' ) {

    $databaseController = new DatabaseController();
    $res = $databaseController->fetchAllEvents();
    HTTPResponder::sendReponse($res);
} else if( $_SERVER['REQUEST_METHOD'] === "POST" ) {

    $data = json_decode(file_get_contents("php://input"));
    $eventName = $data->eventName;
    $databaseController = new DatabaseController();
    $res = $databaseController->createNewEvent($eventName);
    HTTPResponder::sendReponse($res);
}
?>
