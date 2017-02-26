<?php

if(isset($_GET["id"])){
    $oListeCandidature=new CandidatureListe();
    $oListeCandidature->applyRules4GetCandidatAdmin($_GET["id"]);
    $aCandidatures=$oListeCandidature->getPage();
    if(count($aCandidatures)==1){
        $aDataScript["candidature"]=$aCandidatures[0];
    }else{
        $aDataScript["candidature"]=null;
    }

}