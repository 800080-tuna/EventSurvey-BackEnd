<?
//
//  login.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 4/29/19.
//

include_once(dirname(__FILE__) . '/../app/controllers/databaseControllers/usersDatabaseController.php');
include_once(dirname(__FILE__) . '/../app/controllers/HTTPResponder.php');

header("Access-Control-Allow-Origin: https://philipseventsurvey.avfx.com");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

    $data = json_decode(file_get_contents("php://input"));
    $userData = $data["0"];
    $databaseController = new UsersDatabaseController();
    $res = $databaseController->authenticateUserCredentials($userData->emailAddress, $userData->password);
    HTTPResponder::sendReponse($res);
}
?>
