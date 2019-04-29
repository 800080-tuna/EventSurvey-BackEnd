<?
abstract class APIErrorCode
{
    const AuthTokenMissing      = 1000;     //  Auth token was not passed with request
    const AuthTokenInvalid      = 1001;     //  Auth token is not valid
    const AuthFailedEmail       = 1002;     //  email does not exist
    const AuthFailedPassword    = 1003;     //  passwords do not match
    const QueryFailed           = 1004;     //  Datebase Query Failed
    const EventExists           = 1005;      //  an event record already exists with the provided name
}
?>
