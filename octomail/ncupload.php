<?php

// This authenticate the user, and if it's someone known, it does an upload to a draft email into sogo.

require_once("../auth-bootstrap.php");

// apikey = $conf['nextcloud_octomail_token'] + user + name 
// no need to call 'impersonate' 

if (isset($session["auth"]) && $session["auth"]) {
    mylog2("ncupload 1");
    // first check that we have a nc token for this user:
    $stmt = $db->prepare("SELECT * FROM nctoken WHERE username=?;");
    $stmt->execute([$session["me"]]);
    $token=$stmt->fetch(PDO::FETCH_ASSOC);

    if (!$token) {
        header("Content-Type: application/json");
        echo json_encode(["error" => "Nextcloud token invalid. Please retry"]);
        exit();
    }

    // now we download each Nextcloud attachment and we upload it to Sogo:
    $errors=[]; $ids=[];
    $ch1=curl_init(); // for the GET to nextcloud
    $ch2=curl_init(); // for the POST to sogo
    foreach($_POST["files"] as $path) {
        $tmp=tempnam("/tmp","ncupload-");
        curl_setopt_array($ch1,[
            CURLOPT_URL	 => "https://".$conf["domain"]."/remote.php/webdav".$path,
            CURLOPT_USERNAME	 => $session["me"],
            CURLOPT_PASSWORD	 => $token["token"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER	 =>  [ "OCS-APIRequest: true" ]
        ] );
        file_put_contents($tmp,curl_exec($ch1));
        $info=curl_getinfo($ch1);
        if ($info["http_code"]!=200) {
            mylog2("can't get file $path, for user ".$session["me"]." using token ".$token["token"]."");
            $errors[]="can't get file $path, please retry";
            @unlink($tmp);
        } else {
            // now send it to SOGO
            mylog2("got file $path, for user ".$session["me"]." using token ".$token["token"]." sending to SOGO (stored in $tmp)");
            $cFile = new CURLFile($tmp, null, basename($path));
            curl_setopt_array($ch2,[
                CURLOPT_URL	 => "https://".$conf["domain"]."".$_POST["url"],
                // set the cookie to be the same as the one sent by the client:
                CURLOPT_COOKIE => $conf["publiccookie"]."=".$_COOKIE[$conf["publiccookie"]],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => ["attachments" => $cFile],
                CURLOPT_HTTPHEADER => [ "Content-Type: multipart/form-data" ],
//                CURLOPT_VERBOSE => true,
            ] );
            $result = curl_exec($ch2);
            $info=curl_getinfo($ch2);
            mylog2("got $result from sogo");
            $result=json_decode($result,true); 
            @unlink($tmp);
            if (!$result) {
                mylog2("invalid json from sogo, error");
                $errors[]="can't send file $path to sogo, please retry, info was ".print_r($info,true);
            } else {
                $ids[]=["filename" => $path, "uid" => $result["uid"], "url" => $result["lastAttachmentAttrs"][0]["url"]];
                mylog2("got file $path result: ".print_r($result,true));
            }
        }
    } // for each file
    header("Content-Type: application/json");
    echo json_encode(["errors" => $errors, "ids" => $ids]);

    exit();
}

mylog2("ncupload 0");
header("HTTP/1.0 401 Nope");
exit();

