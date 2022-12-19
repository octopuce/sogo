<?php

// This authenticate the user, and if it's someone known, it generates (or returns) a token to use the Nextcloud for that user.

require_once("../auth-bootstrap.php");

// apikey = $conf['nextcloud_octomail_token'] + user + name 
// no need to call 'impersonate' 

if (isset($session["auth"]) && $session["auth"]) {
    mylog2("nctoken 1");
    $token=false;
/* // we don't want to use a recently obtained token since those are short-lived. better generate one each time...
    $stmt = $db->prepare("SELECT * FROM nctoken WHERE username=?;");
    $stmt->execute([$session["me"]]);
    $token=$stmt->fetch(PDO::FETCH_ASSOC);
*/
    if ($token) {
        header("Content-Type: application/json");
        echo json_encode(["username" => $session["me"], "token" => $token["token"], "url" => "https://".$conf["domain"] ]);
        exit();
    } else {
        // get a token for this user, once.
        $cookie = tempnam("/tmp","octomail-cookiejar-");
        $ch=curl_init("https://".$conf["domain"]."/apps/octomail/token"); //?apikey=".$conf['nextcloud_octomail_token']."&user=".$session["me"]."&name=sogo_file_browser");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS	=> [ "apikey" => $conf['nextcloud_octomail_token'], "user" => $session["me"], "name" => "sogo_file_browser" ],
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_HEADER => true,
        ]);
        $res = curl_exec($ch);
        $info = curl_getinfo($ch);
        mylog2("Return from impersonate = Code(".$info["http_code"].") Location(".$info["redirect_url"].") DATA: $res");
        $res=json_decode($res,true);
        $stmt = $db->prepare("REPLACE INTO nctoken SET username=?, token=?;");
        $stmt->execute( [ $session["me"], $res["token"] ] );
        header("Content-Type: application/json");
        echo json_encode(["username" => $session["me"], "token" => $res["token"], "url" => "https://".$conf["domain"] ]);
        @unlink($cookie);
        exit();
                          
    } 
}

mylog2("nctoken 0");
header("HTTP/1.0 401 Nope");
exit();

