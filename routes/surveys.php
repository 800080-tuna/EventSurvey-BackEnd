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
include_once '../config/database.php';
include_once '../model/survey.php';

// $authHeader = apache_request_headers()["Authorization"];
// Authenticator::authenticateRequest($authHeader);

// // get database connection
// $database = new Database();
// $db = $database->getConnection();

//  grab a reference to the HTTP Method
$HTTP_Method = filter_input( INPUT_SERVER, 'REQUEST_METHOD' );

if( $HTTP_Method == "GET" ) {

    $array = array("message" => "Hi, my name is Joe.");
    http_response_code(200);
    echo json_encode($array);

} else if( $HTTP_Method == "POST" ) {

    // // get posted data
    // $data = json_decode(file_get_contents("php://input"));
    //
    // // instantiate User object and set property values
    // $event = new Event($db);
    // $event->title = $data->title;
    // $event->type = $data->type;

}
?>
