<?php


if(isset($_GET["key"]) && isset($_GET["id"])){

    $oListeCandidature=new CandidatureListe();
    $oListeCandidature->applyRules4Key($_GET["key"],$_GET["id"]);
    $aCandidatures=$oListeCandidature->getPage();
    if(count($aCandidatures)==1){
        $Candidature=new Candidature(array("id"=>$aCandidatures[0]["id"]));
        $Candidature->hydrateFromBDD(array('*'));
        $aDataScript["candidature"]=$Candidature;
        $aDataScript["nameimage"]=basename($Candidature->getPath_pic());
        $aDataScript["checkedengagement"]="checked";
        $type = pathinfo($Candidature->getPath_pic(), PATHINFO_EXTENSION);
        $data = file_get_contents($Candidature->getPath_pic());
        $aDataScript["base64image"]= 'data:image/' . $type . ';base64,' . base64_encode($data);
        $aDataScript["key"]=$_GET["key"];
    }else{
        header("Location: /candidature/candidatures.html");
    }

}else{
    $aDataScript["candidature"]=array("country"=>"France");
}




