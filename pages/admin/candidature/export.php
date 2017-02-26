<?php

require_once "list.php";

$aDataScript=array();

$sCSV="nom;prenom;email;tel;ville;cp;en ligne;attestation;".";\n";
foreach($aObj as $oCandidat){
    $sCSV.=$oCandidat->getName();
    $sCSV.=";";
    $sCSV.=$oCandidat->getFirstname();
    $sCSV.=";";
    $sCSV.=$oCandidat->getEmail();
    $sCSV.=";";
    $sCSV.=substr(chunk_split("0".$oCandidat->getTel(),2,"."),0,14);
    $sCSV.=";";
    $sCSV.=$oCandidat->getCity();
    $sCSV.=";";
    $sCSV.=$oCandidat->getZipcode();
    $sCSV.=";";
    $sCSV.=($oCandidat->getState()=="onine")?'OUI':'NON';
    $sCSV.=";";
    $sCSV.=($oCandidat->getIs_certificate()==1)?'OUI':'NON';
    $sCSV.=";\n";

}

echo utf8_decode($sCSV);