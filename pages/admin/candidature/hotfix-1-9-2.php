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
        $sOut="";
        $sOut.= testPath($aCandidature["path_pic"]);
        $sOut.= testPath($aCandidature["path_certificate"]);
        $sOut.= testPath($aCandidature["path_idcard"]);
        $sOut.= testPath($aCandidature["path_idcard_verso"]);
        $sOut.= testPath($aCandidature["path_criminal_record"]);
        if($sOut!=""){
            $aDataScript["out"] .="<hr />CANDIDATURE :".$aCandidature["id"].$sOut;
        }

    }
}else{
    $aDataScript["out"] .= "<div>EXECUTION BLOQUE (cf. fichier de config)</div>";
}