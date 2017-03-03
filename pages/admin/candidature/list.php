<?php

$oListeCandidature=new CandidatureListe();
$oListeCandidature->applyRules4ListAdmin();

//option search, orderby and filter
$sSearch="";
if(trim(SessionService::get("search-admin"))){
    $sSearch=trim(SessionService::get("search-admin"));
}
if(isset($_POST["search"])){
    $sSearch=$_POST["search"];
    SessionService::set("search-admin",$sSearch);
}
if(trim($sSearch)){
    $oListeCandidature->applyRules4Search($sSearch);
}

$sOrder="";
if(trim(SessionService::get("order-admin"))){
    $sOrder=trim(SessionService::get("order-admin"));
}
if(isset($_POST["order"])){
    $sOrder=$_POST["order"];
    SessionService::set("order-admin",$sOrder);
}
if(trim($sOrder)){
  $oListeCandidature->applyRules4OrderBy($sOrder);
}


$sFilter="";
if(trim(SessionService::get("filter-admin"))){
    $sFilter=trim(SessionService::get("filter-admin"));
}
if(isset($_POST["filter"])){
    $sFilter=$_POST["filter"];
    SessionService::set("filter-admin",$sFilter);
}
if(trim($sFilter)){
    $oListeCandidature->applyRules4FilterBy($sFilter);
}


$aCandidatures=$oListeCandidature->getPage();
$aObj=array();
foreach($aCandidatures as $aCandidature){
    $oCandidature=new Candidature(array("id"=>$aCandidature["id"]));
    $oCandidature->hydrateFromBDD(array('*'));
    array_push($aObj, $oCandidature);
}

$aDataScript["search"]=$sSearch;
$aDataScript["order"]=$sOrder;
$aDataScript["filter"]=$sFilter;
$aDataScript["candidatures"]=$aObj;
