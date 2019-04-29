<?
//  TODO:   ensure that JWTs are sent of HTTPS
//          HTTPS helps prevents unauthorized users from stealing the sent JWT by making it so that the communication between the servers and the user cannot be intercepted.

// generate json web token
include_once 'keyGenerator.php';
include_once 'apiResponseCodes.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

class AuthenticationResponse {
    public $success; // boolean value indacting whether or not the authencitation requestion has succeedded
    public $errorMessage; // if auth request has failed, this property stores the error message
    // constructor
    public function __construct($success, $errorMessage) {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
    }
}

class Authenticator {

    #pragma Mark - public interface

    /// generates and returns a new JWT
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

    /// This method is invoked by all routes in order to validate JWT before processing request. If JWT validation fails, request fails with 401. If successful, method falls through.
    static public function authenticateRequest($authHeader) {
        //  seperate auth type and auth token into seperate vars
        list($type, $token) = explode(" ", $authHeader, 2);

        echo "type: $type";

        //  check auth type
        if (strcasecmp($type, "Bearer") == 0) {

            //  validate auth token
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
        if( $jwt ){
            try {
                //  at some point, the key will be recycled and will no longer be able to decode the jwt
                //  at that point, login has expired and clients will need to request new jwt
                //  app only auth will need to request new key
                $key = KeyGenerator::currentKey();


                //  if successfully decoded, return successful AuthenticationResponse
                JWT::decode($jwt, $key, array('HS256'));
                return new AuthenticationResponse(true, "Joe is Cool");
            } catch (Exception $e) {
                //  if unable to decode, jwt is invalid - fail
                return new AuthenticationResponse(false, $e->getMessage());
            }
        } else {
            //  if jwt is empty, fail
            return new AuthenticationResponse(false, "");
        }
    }
}
?>
