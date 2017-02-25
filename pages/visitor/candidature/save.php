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
$aMandoryFields=array("civilite","nom","prenom","email","tel","ad1","ville","cp","engagement-1","engagement-2","engagement-3");

foreach($aMandoryFields as $sField){
    if (!isset($_POST[$sField]) || $_POST[$sField] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => $sField));
        $_POST[$sField]="";
    }
}

if (!isset($_POST["ad2"])) {
    $_POST["ad3"]="";
}


if (!isset($_POST["ad3"])) {
    $_POST["ad3"]="";
}

$bPresentation=false;
$bVideoPresentation=false;

if (isset($_POST["presentation"]) && $_POST["presentation"] != "") {
    $bPresentation=true;
}else{
    $_POST["presentation"]="";
}
if (isset($_POST["video"]) && $_POST["video"] != "") {
    $bVideoPresentation=true;
}else{
    $_POST["video"]="";
}

if(!$bPresentation && !$bVideoPresentation){
    $nError++;
    array_push($aResponse["required"], array("field" => "presentation"));
    array_push($aResponse["required"], array("field" => "video"));
}


//Values
if( $nError==0 ) {
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $aResponse["message"]["text"] = "L'adresse e-mail est incorrecte.";
        array_push($aResponse["required"],array("field"=>"email"));
        $nError++;
    }
}

if( $nError==0 ) {
    if( !is_numeric($_POST["tel"]) || substr($_POST["tel"],1)==0 || strlen($_POST["tel"])!=9){
        $aResponse["message"]["text"] = "Le numéro de téléphone est incorrect.(9 chiffres)";
        array_push($aResponse["required"],array("field"=>"tel"));
        $nError++;
    }
}
if( $nError==0 ) {
    if( ( !is_numeric($_POST["cp"]) && substr(strtoupper($_POST["cp"]),0,2)!="2A" && substr(strtoupper($_POST["cp"]),0,2)!="2B" ) || strlen($_POST["cp"])!=5){
        $aResponse["message"]["text"] = "Le code postal est incorrect.";
        array_push($aResponse["required"],array("field"=>"cp"));
        $nError++;
    }
}


if($nError==0){
    $Candidature=new Candidature();
    $Candidature->setDate_created(date("Y-m-d H:i:s"));
    $Candidature->setName($_POST["nom"]);
    $Candidature->setFirstname($_POST["prenom"]);
    $Candidature->setCivility($_POST["civilite"]);
    $Candidature->setEmail($_POST["email"]);
    $Candidature->setTel($_POST["tel"]);
    $Candidature->setAd1($_POST["ad1"]);
    $Candidature->setAd2($_POST["ad2"]);
    $Candidature->setAd3($_POST["ad3"]);
    $Candidature->setCity($_POST["ville"]);
    $Candidature->setUrl_video($_POST["video"]);
    $Candidature->setPresentation($_POST["presentation"]);
    $Candidature->setZipcode($_POST["cp"]);

    //generate key for link
    $sKey=md5($_SERVER["REMOTE_ADDR"].ConfigService::get("key").rand(1000,9999).time());

    $Candidature->setKey_edit($sKey);

    $Candidature->save();



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