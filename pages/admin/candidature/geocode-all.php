<?php

$oListeCandidature=new CandidatureListe();
$oListeCandidature->applyRules4ListAdmin();

$aCandidatures=$oListeCandidature->getPage();
$aObj=array();

ini_set('max_execution_time', count($aCandidatures)*3);

foreach($aCandidatures as $aCandidature){
    $oCandidature=new Candidature(array("id"=>$aCandidature["id"]));
    $oCandidature->hydrateFromBDD(array('*'));

    $coordinate=   $oCandidature->geocode();
   if(!is_null($coordinate["lat"]) && !is_null($coordinate["lng"]) ) {
        $oCandidature->setLat($coordinate["lat"]);
        $oCandidature->setLng($coordinate["lng"]);
        $oCandidature->save();
    }
    sleep(1);
    array_push($aObj, $oCandidature);
}

$aDataScript["candidatures"]=$aObj;
