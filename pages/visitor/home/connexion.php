<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Identifiants incorrects !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;

$oMe->setLogin("");
$oMe->setType("visitor");

SessionService::set("user-login","");
SessionService::set("user-type","visitor");

//mandatory fields
if (!isset($_POST["login"]) || $_POST["login"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "nom"));
}
if (!isset($_POST["pass"]) || $_POST["pass"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "pass"));
}
if($nError==0) {
    $_POST["login"]=trim($_POST["login"]);
    $_POST["pass"]=trim($_POST["pass"]);

    $aLogins = array_keys(ConfigService::get("admin-account"));
    if (in_array($_POST["login"],$aLogins)){
        if(ConfigService::get("admin-account")[$_POST["login"]]==$_POST["pass"]){
            $oMe->setLogin($_POST["login"]);
            $oMe->setType("admin");

            SessionService::set("user-login",$_POST["login"]);
            SessionService::set("user-type","admin");

            $aResponse["redirect"] = "/candidature/list.html";
            $aResponse["durationMessage"] = "2000";
            $aResponse["durationRedirect"] = "2000";
            $aResponse["durationFade"] = "10000";
            $aResponse["message"]["title"] = "";
            $aResponse["message"]["type"] = "success";
            $aResponse["message"]["text"] = "Connexion réussie !";
        }
    }
}




//return
$aDataScript['data'] = json_encode($aResponse);