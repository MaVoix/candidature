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
$aMandoryFields=array("email","cp");


foreach($aMandoryFields as $sField){
    if (!isset($_POST[$sField]) || $_POST[$sField] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => $sField));
        $_POST[$sField]="";
    }
}

//captcha
if(ConfigService::get("enable-captcha-editlink")){
    if (!isset($_POST["captcha"]) || $_POST["captcha"] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => "captcha"));
        $_POST["captcha"]="";
    }else{
        if(SessionService::get("captcha-value") !=  $_POST["captcha"]){
            $nError++;
            $aResponse["message"]["text"] = "Le code de sécurité est incorrect.";
            $_POST["captcha"]="";
        }
    }
}
$Candidature=new Candidature();

//search candidature
if($nError==0){
    $listeCandidatures=new CandidatureListe();
    $listeCandidatures->applyRules4GetEditLink($_POST["email"],$_POST["cp"]);
    $aCandidatures=$listeCandidatures->getPage();
    if(count($aCandidatures)==1){
        $Candidature=new Candidature(array("id"=>$aCandidatures[0]["id"]));
        $Candidature->hydrateFromBDD(array('*'));
    }elseif(count($aCandidatures)>1){
        $nError++;
        $aResponse["message"]["text"] = "Erreur : Votre adresse correspond a plusieurs candidatures, veuillez contacter l'administrateur.";
    }else{
        $nError++;
        $aResponse["message"]["text"] = "Aucune candidature ne correspond à ces informations.";
    }
}


if( $nError==0){

    $bIsCriminalRecordSent=$Candidature->getPath_criminal_record()==""?false:true;

    $TwigEngine = App::getTwig();
    $sBodyMailHTML = $TwigEngine->render("visitor/mail/body.html.twig", [
        "candidature" => $Candidature,
        "isCriminalRecordSent"=> $bIsCriminalRecordSent,
    ]);
    $sBodyMailTXT = $TwigEngine->render("visitor/mail/body.txt.twig", [
        "candidature" => $Candidature,
        "isCriminalRecordSent"=> $bIsCriminalRecordSent,
    ]);

    Mail::sendMail($Candidature->getEmail(), "Confirmation de candidature", $sBodyMailHTML, $sBodyMailTXT, true);


    if($oMe->getType()=="admin"){
        $aResponse["redirect"] = "/candidature/list.html";
    }else{
        $aResponse["redirect"] = "/candidature/candidatures.html";
    }
    SessionService::set("last-save-id",$Candidature->getId());

    $aResponse["durationMessage"] = "5000";
    $aResponse["durationRedirect"] = "5000";
    $aResponse["durationFade"] = "10000";
    $aResponse["message"]["title"] = "";
    $aResponse["message"]["type"] = "success";
    $aResponse["message"]["text"] = "Confirmation de candidature renvoyée sur <b>".$Candidature->getEmail()."</b> !";


}

//return
$aDataScript['data'] = json_encode($aResponse);