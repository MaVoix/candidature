<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Un problème s'est produit lors de la suppression.";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();




if(isset($_POST["id"])){
    $oListeCandidature=new CandidatureListe();
    $oListeCandidature->applyRules4GetCandidatAdmin($_POST["id"]);
    $aCandidatures=$oListeCandidature->getPage();
    if(count($aCandidatures)==1){
        $aResponse["type"]="empty";
        if($_POST["from"]=="list"){
            $aResponse["type"] = "refresh-delete-list";
            $aResponse["id"]=$aCandidatures[0]["id"];
        }else{
            $aResponse["redirect"]="/candidature/list.html";
        }

        $oCandidature= new Candidature(array("id"=>$aCandidatures[0]["id"]));
        $oCandidature->setDate_deleted(date("Y-m-d H:i:s"));
        $oCandidature->save();
    }
}

//return
$aDataScript['data'] = json_encode($aResponse);