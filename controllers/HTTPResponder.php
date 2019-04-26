<?
class HTTPResponder {

    public static function sendReponse($res) {
        if($res['success'] == false) {
            print_r("Request Failed");
            http_response_code(500);
            echo json_encode($res);
            return;
        }

        http_response_code(200);
        echo json_encode($res);
    }
}
?>
