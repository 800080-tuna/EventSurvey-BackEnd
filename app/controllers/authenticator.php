<?
//
//  apiResponseCodes.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 2/17/19.
//

include_once(dirname(__FILE__) . '/keyGenerator.php');
include_once(dirname(__FILE__) . '/apiResponseCodes.php');
include_once(dirname(__FILE__) . '/../libs/php-jwt-master/src/BeforeValidException.php');
include_once(dirname(__FILE__) . '/../libs/php-jwt-master/src/ExpiredException.php');
include_once(dirname(__FILE__) . '/../libs/php-jwt-master/src/SignatureInvalidException.php');
include_once(dirname(__FILE__) . '/../libs/php-jwt-master/src/JWT.php');
use \Firebase\JWT\JWT;

class AuthenticationResponse {
    public $success;
    public $errorMessage;

    public function __construct($success, $errorMessage) {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
    }
}

class Authenticator {

    static public function newToken($user_id, $firstname, $lastname, $email) {

        $claims = Authenticator::jwtClaims();
        $jwtData = array(
            "id" => $user_id,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "email" => $email
        );
        $token = array_merge($claims, $jwtData);
        $key = KeyGenerator::currentKey();
        return JWT::encode($token, $key);
    }

    static public function authenticateRequest($authHeader) {

        list($type, $token) = explode(" ", $authHeader, 2);

        if (strcasecmp($type, "Bearer") == 0) {
            $authResponse = Authenticator::validateToken($token);
            if( $authResponse->success === false ) {
                Authenticator::authDidFail(APIErrorCode::AuthTokenInvalid);
            }
        } else {
            Authenticator::authDidFail(APIErrorCode::AuthTokenMissing);
        }
    }

    #pragma Mark - private methods

    static private function authDidFail($errorCode) {
        http_response_code(401);
        die(json_encode(array(
            "success" => false,
            "api_response_code" => $errorCode
        )));
    }

    static private function jwtClaims() {
        /*
         *  jwt claims to use (rfc7519)
         *      4.1.1.  "iss" (Issuer) Claim
         *      4.1.2.  "sub" (Subject) Claim       //  auth|some-hash-here
         *      4.1.3.  "aud" (Audience) Claim      //  unique-client-id-hash
         *      4.1.6.  "iat" (Issued At) Claim
         *      4.1.4.  "exp" (Expiration Time) Claim
         */
        return array(
            "iss" => "http://localhost/event_survey.com",   //  TODO: update with production host name
            "iat" => time(),
            "exp" => KeyGenerator::currentJWTKeyExpirationDate()
        );
    }

    /// validates the provided jwt argument
    static private function validateToken($jwt) {
        try {
            $key = KeyGenerator::currentKey();
            JWT::decode($jwt, $key, array('HS256'));
            return new AuthenticationResponse(true, "Auth Successful");
        } catch (Exception $e) {
            //  if unable to decode, jwt is invalid - fail
            return new AuthenticationResponse(false, $e->getMessage());
        }
    }
}
?>
