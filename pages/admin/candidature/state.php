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




if(isset($_POST["id"])){
    $oListeCandidature=new CandidatureListe();
    $oListeCandidature->applyRules4GetCandidatAdmin($_POST["id"]);
    $aCandidatures=$oListeCandidature->getPage();
    if(count($aCandidatures)==1){
        $oCandidature= new Candidature(array("id"=>$aCandidatures[0]["id"]));
        if(isset($_POST["checked"])){
            $aResponse["type"] = "refresh-state-list";
            $aResponse["id"]=$aCandidatures[0]["id"];
            if($_POST["checked"]=='true'){
                $aResponse["class"]="online";
                $oCandidature->setState("online");
            }else{
                $aResponse["class"]="offline";
                $oCandidature->setState("offline");
            }


            $oCandidature->save();
        }
    }
}

//return
$aDataScript['data'] = json_encode($aResponse);