<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Identifiants incorrects !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;

$oMe->setLogin("");
$oMe->setType("visitor");

SessionService::set("user-login","");
SessionService::set("user-type","visitor");

//mandatory fields
if (!isset($_POST["login"]) || $_POST["login"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "nom"));
}
if (!isset($_POST["pass"]) || $_POST["pass"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "pass"));
}
if($nError==0) {

    //encode PASS
    $sPassword=User::encodePassword($_POST["pass"]);

    //CONNEXION
    $oListeUser = new UserListe();
    $oListeUser-> applyRules4Connexion($_POST["login"],  $sPassword);
    $aUsers = $oListeUser->getPage();
   if(count($aUsers)!=1){
       $nError++;
   }else{
       $aUser=$aUsers[0];
       if($aUser["login"]==$_POST["login"]  &&  $aUser["pass"]==$sPassword ){
           $oMe->setLogin($_POST["login"]);
           $oMe->setType("admin");

           SessionService::set("user-id",$aUser["id"]);

           $aResponse["redirect"] = "/candidature/list.html";
           $aResponse["durationMessage"] = "2000";
           $aResponse["durationRedirect"] = "2000";
           $aResponse["durationFade"] = "10000";
           $aResponse["message"]["title"] = "";
           $aResponse["message"]["type"] = "success";
           $aResponse["message"]["text"] = "Connexion réussie !";

       }else{
           $nError++;
       }

   }

}




//return
$aDataScript['data'] = json_encode($aResponse);