<?php

$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Un problème s'est produit lors de l'enregistrement.";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();




if(isset($_POST["ref"])){
    $oListeCms=new CmsListe();
    $oListeCms->applyRules4GetBlock($_POST["ref"]);
    $aCms= $oListeCms->getPage();
    if(count($aCms)==1){
        $oCms= new Cms(array("id"=>$aCms[0]["id"]));
        $aResponse["type"] = "no-reponse";
        $oCms->setContent($_POST["content"]);
        $oCms->setDate_amended(date("Y-m-d H:i:s"));
        $oCms->save();
    }else{
        $oCms= new Cms();
        $oCms->setRef($_POST["ref"]);
        $oCms->setContent($_POST["content"]);
        $oCms->setDate_amended(date("Y-m-d H:i:s"));
        $oCms->setDate_created(date("Y-m-d H:i:s"));
        $oCms->save();
    }
}

//return
$aDataScript['data'] = json_encode($aResponse);