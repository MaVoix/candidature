<?php

require_once "list.php";

$aDataScript=array();

$sCSV="civilite;nom;prenom;email;tel;ville;cp;en ligne;attestation valide;Carte ID valide;extrait judiciaire valide;lien edition;".";\n";
foreach($aObj as $oCandidat){
    $sCSV.=$oCandidat->getCivility();
    $sCSV.=";";
    $sCSV.=str_replace(";",",", mb_convert_case($oCandidat->getName(), MB_CASE_UPPER,"UTF-8"));
    $sCSV.=";";
    $sCSV.=str_replace(";",",",mb_convert_case($oCandidat->getFirstname(), MB_CASE_TITLE, "UTF-8"));
    $sCSV.=";";
    $sCSV.=str_replace(";",",",mb_convert_case($oCandidat->getEmail(), MB_CASE_LOWER,"UTF-8"));
    $sCSV.=";";
    $sCSV.=str_replace(";",",",$oCandidat->getTel());
    $sCSV.=";";
    $sCSV.=str_replace(";",",",mb_convert_case($oCandidat->getCity(), MB_CASE_TITLE,"UTF-8"));
    $sCSV.=";";
    $sCSV.=str_replace(";",",",$oCandidat->getZipcode());
    $sCSV.=";";
    $sCSV.=($oCandidat->getState()=="online")?'OUI':'NON';
    $sCSV.=";";
    $sCSV.=($oCandidat->getIs_certificate()==1)?'OUI':'NON';
    $sCSV.=";";
    $sCSV.=($oCandidat->getIs_idcard()==1)?'OUI':'NON';
    $sCSV.=";";
    $sCSV.=($oCandidat->getIs_criminal_record()==1)?'OUI':'NON';
    $sCSV.=";";
    $sCSV.=ConfigService::get("urlSite")."/candidature/form.html?id=".$oCandidat->getId()."&key=".$oCandidat->getKey_edit();
    $sCSV.=";\n";


}

echo utf8_decode($sCSV);