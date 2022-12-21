<?php

// This authenticate the user, and if it's someone known, it does an upload to nextcloud from sogo

require_once("../auth-bootstrap.php");

// apikey = $conf['nextcloud_octomail_token'] + user + name 
// no need to call 'impersonate' 

if (isset($session["auth"]) && $session["auth"]) {
    mylog2("ncdownload 1");
    // first check that we have a nc token for this user:
    $stmt = $db->prepare("SELECT * FROM nctoken WHERE username=?;");
    $stmt->execute([$session["me"]]);
    $token=$stmt->fetch(PDO::FETCH_ASSOC);

    if (!$token) {
        header("Content-Type: application/json");
        echo json_encode(["error" => "Nextcloud token invalid. Please retry"]);
        exit();
    }

    // now we download each Sogo attachment and we upload it to Nextcloud:
    $errors=[]; $ok=[]; $ids=[];
    $ch1=curl_init(); // for the GET to nextcloud
    $ch2=curl_init(); // for the POST to sogo
    foreach($_POST["urls"] as $path) {
        $tmp=tempnam("/tmp","ncdownload-");
        curl_setopt_array($ch1,[
            CURLOPT_URL	 => "https://".$conf["domain"].$path,
            CURLOPT_COOKIE => $conf["publiccookie"]."=".$_COOKIE[$conf["publiccookie"]],
            CURLOPT_RETURNTRANSFER => true,
        ] );
        file_put_contents($tmp,curl_exec($ch1));
        $info=curl_getinfo($ch1);
        if ($info["http_code"]!=200) {
            mylog2("can't get file $path, for user using cookie ".$conf["publiccookie"]."=".$_COOKIE[$conf["publiccookie"]]."");
            $errors[]="can't get file $path, please retry";
            @unlink($tmp);
        } else {
            // now send it to Nextcloud
            mylog2("got file $path, for user using cookie ".$conf["publiccookie"]."=".$_COOKIE[$conf["publiccookie"]]." sending to Nextcloud (stored in $tmp)");
            clearstatcache(); // for filesize below
            $tmph = fopen($tmp, "rb");
            $_POST["folder"]=trim($_POST["folder"],"/");
            if ($_POST["folder"]) $_POST["folder"].="/";
            curl_setopt_array($ch2,[
                CURLOPT_URL	 => "https://".$conf["domain"]."/remote.php/dav/files/".$session["me"]."/".$_POST["folder"].basename($path),
                CURLOPT_USERNAME	 => $session["me"],
                CURLOPT_PASSWORD	 => $token["token"],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_BINARYTRANSFER => true,
                CURLOPT_HTTPHEADER	 =>  [ "OCS-APIRequest: true", "Content-Length: ".filesize($tmp) ],
                CURLOPT_PUT => true,
                CURLOPT_INFILE => $tmph,
                CURLOPT_INFILESIZE, filesize($tmp),
                CURLOPT_VERBOSE => true,
            ] );
            $result = curl_exec($ch2);
            fclose($tmph);
            $info=curl_getinfo($ch2);
            @unlink($tmp);
            if ($info["http_code"]!=201) {
                mylog2("invalid result from nextcloud, error");
                $errors[]="can't send file $path to nextcloud, please retry, info was ".print_r($info,true);
            } else {
                $ids[]=["filename" => $path ];
                $ok[]="uploaded file $path";
                mylog2("got file $path result: ".print_r($result,true));
            }
        }
    } // for each file
    header("Content-Type: application/json");
    echo json_encode(["ok" => $ok, "errors" => $errors, "ids" => $ids]);

    exit();
}

mylog2("ncdownload 0");
header("HTTP/1.0 401 Nope");
exit();

