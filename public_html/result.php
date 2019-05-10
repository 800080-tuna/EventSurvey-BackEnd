<?
//
//  result.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/22/19.
//

include_once(dirname(__FILE__) . '/../app/controllers/databaseControllers/resultsDatabaseController.php');
include_once(dirname(__FILE__) . '/../app/controllers/HTTPResponder.php');
include_once(dirname(__FILE__) . '/../app/controllers/headerAccess.php');
include_once(dirname(__FILE__) . '/../app/controllers/auth/authenticator.php');

header("Access-Control-Allow-Origin: https://philipseventsurvey.avfx.com");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$authHeader = HeaderAccess::fetchAuthHeader();
Authenticator::authenticateRequest($authHeader);

if( $_SERVER['REQUEST_METHOD'] === "GET" ) {

    if (isset($_GET['event_identifier'])) {
        $eventIdentifier = $_GET['event_identifier'];
        $databaseController = new ResultsDatabaseController();
        $res = $databaseController->fetchResultForEventWithIdentifier($eventIdentifier);
        HTTPResponder::sendReponse($res);
    } else {
        //  TODO: if event_name is not set, should return missing param error
    }
} else if( $_SERVER['REQUEST_METHOD'] === "POST" ) {

    $databaseController = new ResultsDatabaseController();
    $data = json_decode(file_get_contents("php://input"));
    // print_r($data);
    $res = $databaseController->createNewResult($data);
    HTTPResponder::sendReponse($res);
}

?>
