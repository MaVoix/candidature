<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Tous les champs suivi de * sont obligatoires !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;

//mandatory fields
if (!isset($_POST["nom"]) || $_POST["nom"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "nom"));
}

if($nError==0){
    $aResponse["redirect"] = "/candidature/success.html";
    $aResponse["durationMessage"] = "2000";
    $aResponse["durationRedirect"] = "2000";
    $aResponse["durationFade"] = "10000";
    $aResponse["message"]["title"] = "";
    $aResponse["message"]["type"] = "success";
    $aResponse["message"]["text"] = "Candidature envoyée correctement !";
}


//return
$aDataScript['data'] = json_encode($aResponse);