<?
include_once '../controllers/databaseController.php';
include_once '../controllers/HTTPResponder.php';

//  TODO: This CORS header needs to be set properly
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$HTTP_Method = filter_input( INPUT_SERVER, 'REQUEST_METHOD' );

if( $HTTP_Method == "GET" ) {

    $databaseController = new DatabaseController();
    $res = $databaseController->fetchAllEvents();
    HTTPResponder::sendReponse($res);
} else if( $HTTP_Method == "POST" ) {

    //  TODO: this is wrong - why am I accesing GET params in a POST - data should be added to request body as JSON
    // // get posted data
    $data = json_decode(file_get_contents("php://input"));
    $eventName = $data->eventName;
    $databaseController = new DatabaseController();
    $res = $databaseController->createNewEvent($eventName);
    HTTPResponder::sendReponse($res);
}
?>
