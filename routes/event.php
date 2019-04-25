<?
//  We need to set headers on this new file so that it will only accept JSON data.
//  required headers

//  TODO: This CORS header needs to be set properly
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files needed to connect to database
include_once '../database_controller/database_controller.php';

//  grab a reference to the HTTP Method
$HTTP_Method = filter_input( INPUT_SERVER, 'REQUEST_METHOD' );

if( $HTTP_Method == "GET" ) {

    $databaseController = new DatabaseController();
    $res = $databaseController->fetchAllEvents();

    if($res['success'] == false) {
        print_r("Request Failed");
        http_response_code(500);
        echo json_encode($res);
        return;
    }
    http_response_code(200);
    echo json_encode($res);

} else if( $HTTP_Method == "POST" ) {

    //  TODO: this is wrong - why am I accesing GET params in a POST - data should be added to request body as JSON
    // // get posted data
    $data = json_decode(file_get_contents("php://input"));

    //  TODO: if event_name is not set, should return missing param error
    $eventName = $data->eventName;

    $databaseController = new DatabaseController();
    $res = $databaseController->createNewEvent($eventName);

    if($res['success'] == false) {
        print_r("Request Failed");
        http_response_code(500);
        echo json_encode($res);
        return;
    }
    // print_r("Request Succeeded");

    http_response_code(200);
    echo json_encode($res);

    // // get posted data
    // $data = json_decode(file_get_contents("php://input"));
    //
    // // instantiate User object and set property values
    // $event = new Event($db);
    // $event->title = $data->title;
    // $event->type = $data->type;

}
?>
