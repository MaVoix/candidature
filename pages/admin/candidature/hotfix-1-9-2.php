<?php
$aDataScript["out"] = "";

function testPath($sPath){
    $sOut="";
    if(!file_exists($sPath) && $sPath!=""){
        $sOut.="<div> IMPOSSIBLE DE TROUVER : ".$sPath."</div>";
    }
    return $sOut;
}

if( ConfigService::get("enable-script-hotfix-1-9-2") ) {

    //liste les candidatures en base
    $oListeCandidature = new CandidatureListe();
    $oListeCandidature->applyRules4ListAdmin();
    $aCandidatures = $oListeCandidature->getPage();
    // parcrous les candidatures
    foreach ($aCandidatures as $aCandidature) {

        $aDataScript["out"] .="<hr />CANDIDATURE :".$aCandidature["id"];
        $aDataScript["out"] .= testPath($aCandidature["path_pic"]);
        $aDataScript["out"] .= testPath($aCandidature["path_certificate"]);
        $aDataScript["out"] .= testPath($aCandidature["path_idcard"]);
        $aDataScript["out"] .= testPath($aCandidature["path_idcard_verso"]);
        $aDataScript["out"] .= testPath($aCandidature["path_criminal_record"]);

    }
}else{
    $aDataScript["out"] .= "<div>EXECUTION BLOQUE (cf. fichier de config)</div>";
}