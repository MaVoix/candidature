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
            $aResponse["type"] = "no-reponse";
            if($_POST["checked"]=='true'){
                $oCandidature->setIs_criminal_record(true);
            }else{

                $oCandidature->setIs_criminal_record(false);
            }
            $oCandidature->save();
        }
    }
}

//return
$aDataScript['data'] = json_encode($aResponse);