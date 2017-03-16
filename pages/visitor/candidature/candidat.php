<?php

if(isset($_GET["id"])){
    $oListeCandidature=new CandidatureListe();
    if($oMe->getType()=="admin"){
        $oListeCandidature->applyRules4GetCandidatAdmin($_GET["id"]);
    }else{
        $oListeCandidature->applyRules4GetCandidatVisitor($_GET["id"]);
    }
    $aCandidatures=$oListeCandidature->getPage();
    if(count($aCandidatures)==1){
        $oCandidature=new Candidature(array("id"=>$aCandidatures[0]["id"]));
        $oCandidature->hydrate($aCandidatures[0]);
        $aDataScript["candidature"]= $oCandidature;
    }else{
        $aDataScript["candidature"]=null;
    }

}


