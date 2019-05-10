<?
//
//  apiResponseCodes.php
//  EventSurveyDatabase
//
//  Created by Joe Rouleau on 2/17/19.
//

class KeyGenerator {

    //  TODO: don't write raw data to disk - use DB instead?

    //  storage location for current key data
    static private function filepath() {
        $current_directory = dirname(__FILE__);
        $keyDataDirectory = dirname(dirname($current_directory));
        return $keyDataDirectory . "/private/keydata.txt";
    }

    ///  returns the expiration date for the current key data
    static public function currentJWTKeyExpirationDate() {
        $chachedJson = file(KeyGenerator::filepath());
        $chachedKeyData = json_decode($chachedJson[0], true);
        return $chachedKeyData["expiration"];
    }

    /**
     *  returns a string to be used as a key for a jwt
     *  the key string is recycled and replaced every 24 hours
     */
    static public function currentKey() {

        $filepath = KeyGenerator::filepath();

        //  read data at paths
        $chachedJson = file($filepath);
        $chachedKeyData = json_decode($chachedJson[0], true);

        //  if expired, generate new data
        if( is_null($chachedKeyData) || time() > $chachedKeyData["expiration"] ) {
            //  initial key data has expired

            //  new initial key data
            $newKeyData = array(
                "keyData" => uniqid(),
                "expiration" => time() + (1 * 24 * 60 * 60)  //  expires in 24 hours
            );

            //  write to files
            file_put_contents($filepath, json_encode($newKeyData));

            $chachedKeyData = $newKeyData;
        }

        return $chachedKeyData["keyData"];
    }
}
?>
