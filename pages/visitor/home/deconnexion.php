<?php


$aResponse = array();
$aResponse["type"] = "message";


SessionService::set("user-login","");
SessionService::set("user-type","visitor");

$aResponse["redirect"] = "/candidature/form.html";
$aResponse["durationMessage"] = "2000";
$aResponse["durationRedirect"] = "2000";
$aResponse["durationFade"] = "10000";
$aResponse["message"]["title"] = "";
$aResponse["message"]["type"] = "success";
$aResponse["message"]["text"] = "Déconnexion ...";


//return
$aDataScript['data'] = json_encode($aResponse);