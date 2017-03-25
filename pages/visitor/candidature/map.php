<?php

$oListeCandidature=new CandidatureListe();
$oListeCandidature->applyRules4ListVisitor();
$aCandidatures=$oListeCandidature->getPage();
$aObj=array();
foreach($aCandidatures as $aCandidature){
    $oCandidature=new Candidature(array("id"=>$aCandidature["id"]));
    $oCandidature->hydrateFromBDD(array('*'));
    array_push($aObj, $oCandidature);
}


$aDataScript["candidatures"]=$aObj;