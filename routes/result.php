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

    if (isset($_GET['event_identifier'])) {
        $eventIdentifier = $_GET['event_identifier'];

        $databaseController = new DatabaseController();
        $res = $databaseController->fetchResultForEventWithIdentifier($eventIdentifier);

        if($res['success'] == false) {
            print_r("Request Failed");
            http_response_code(500);
            echo json_encode($res);
            return;
        }

        http_response_code(200);
        echo json_encode($res);
    }
    //  TODO: if event_name is not set, should return missing param error

} else if( $HTTP_Method == "POST" ) {

    $databaseController = new DatabaseController();
    $data = json_decode(file_get_contents("php://input"));

    $res = $databaseController->createNewResult($data->question,
                                                $data->moreLikely,
                                                $data->lessLikely,
                                                $data->unchanged,
                                                $data->eventIdentifer);

    if($res['success'] == false) {
        print_r("Request Failed");
        http_response_code(500);
        echo json_encode($res);
        return;
    }

    http_response_code(200);
    echo json_encode($res);
}
?>
