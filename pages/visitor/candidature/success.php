<?php


$nIdSaved=SessionService::get("last-save-id");
if(isset($nIdSaved)){
    $oListeCandidature=new CandidatureListe();
    $oListeCandidature->applyRules4GetCandidatSaved($nIdSaved);
    $aCandidatures=$oListeCandidature->getPage();
    if(count($aCandidatures)==1){
        $oCandidature=new Candidature(array("id"=>$aCandidatures[0]["id"]));
        $oCandidature->hydrate($aCandidatures[0]);
        $aDataScript["candidature"]= $oCandidature;
    }else{
        $aDataScript["candidature"]=null;
    }

}