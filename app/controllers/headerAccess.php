<?
//
//  headerAccess.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 5/1/19.
//

abstract class HeaderAccess
{
    static public function fetchAuthHeader() {
        $config = parse_ini_file(dirname(__FILE__) . '/../config.ini');
        $buildType = $config["build_type"];
        $authHeaderKey = $config["auth_header_key"];
        $authHeader;

        if( $buildType === "dev" ) {
            $authHeader = apache_request_headers()[$authHeaderKey];
        } else if( $buildType === "prod" ) {
            $authHeader = $_SERVER[$authHeaderKey];
        }
        return $authHeader;
    }
}
?>
