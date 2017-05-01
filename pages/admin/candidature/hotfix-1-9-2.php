<?php
$aDataScript["out"] = "";
global $nCountFiles;
$nCountFiles=0;
$nCountCandidats=0;
if(!isset($_GET["action"])){
    $_GET["action"]='';
}
function testPath($sPath,$sField,$nId){
    global $nCountFiles;
    $sOut="";
    if(!file_exists($sPath) && $sPath!=""){
        $sOut.="<div> IMPOSSIBLE DE TROUVER : ".$sPath."</div>";
        $nCountFiles++;
        if( $_GET["action"]=="tentative-recup" ||  $_GET["action"]=="recup" ){
            $sSql="SELECT $sField FROM `candidature-backup` WHERE id='".$nId."'";
            $stmt=DbLink::getInstance()->prepare($sSql);
            $stmt->execute(array());
            $data=$stmt->fetchAll();
            if(count($data)){
                $sFile=$data[0][$sField];
                if(file_exists($sFile)){
                    $sOut.="<div>--- Fichier récupérable : ".$sFile."</div>";
                    if($_GET["action"]=="recup"){
                        $candidature=new Candidature(array("id"=>$nId));
                        $candidature->hydrateFromBDD(array("*"));
                        switch($sField){
                            case "path_pic": $candidature->setPath_pic($sFile); break;
                            case "path_certificate": $candidature->setPath_certificate($sFile); break;
                            case "path_idcard": $candidature->setPath_idcard($sFile); break;
                            case "path_idcard_verso": $candidature->setPath_idcard_verso($sFile); break;
                            case "path_criminal_record": $candidature->setPath_criminal_record($sFile); break;
                        }
                        $candidature->save();
                        $sOut.="<div>------- Fichier récupéré : ".$sFile."</div>";

                    }
                }else{
                    $sOut.="<div><b>--- Fichier irrécupérable</b></div>";
                }
            }



        }


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
        $sOut.= testPath($aCandidature["path_pic"],"path_pic",$aCandidature["id"]);
        $sOut.= testPath($aCandidature["path_certificate"],"path_certificate",$aCandidature["id"]);
        $sOut.= testPath($aCandidature["path_idcard"],"path_idcard",$aCandidature["id"]);
        $sOut.= testPath($aCandidature["path_idcard_verso"],"path_idcard_verso",$aCandidature["id"]);
        $sOut.= testPath($aCandidature["path_criminal_record"],"path_idcard_verso",$aCandidature["id"]);
        if($sOut!=""){
            $nCountCandidats++;
            $aDataScript["out"] .="<hr />CANDIDATURE :".$aCandidature["id"].$sOut;
        }
    }
    $aDataScript["out"] .= "<hr /><hr />";
    $aDataScript["out"] .= "<div> candidatures  : $nCountCandidats</div>";
    $aDataScript["out"] .= "<div> fichiers  : $nCountFiles</div>";

}else{
    $aDataScript["out"] .= "<div>EXECUTION BLOQUE (cf. fichier de config)</div>";
}