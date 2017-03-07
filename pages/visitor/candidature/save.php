<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Tous les champs suivi de * sont obligatoires !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;

//check edit KEY
$bEdit=false;
$OldCandidature=new Candidature();
if(isset($_POST["id"]) && isset($_POST["key"])){
    $oListeCandidature=new CandidatureListe();
    $oListeCandidature->applyRules4Key($_POST["key"],$_POST["id"]);
    $aCandidatures=$oListeCandidature->getPage();
    if(count($aCandidatures)==1){
        $bEdit=true;
        $OldCandidature=new Candidature(array("id"=>$aCandidatures[0]["id"]));
        $OldCandidature->hydrateFromBDD(array('*'));
    }else{

        $nError++;
        $aResponse["message"]["text"] = "Impossible de modifier cette candidature.";
    }
}


//mandatory fields
$aMandoryFields=array("civilite","nom","prenom","email","tel","ad1","ville","cp","pays","engagement-a1","engagement-a2","engagement-a3","engagement-a4","engagement-a5","engagement-a6","engagement-a7","engagement-a8","engagement-a9","engagement-a10","engagement-a11","imageFilename");

foreach($aMandoryFields as $sField){
    if (!isset($_POST[$sField]) || $_POST[$sField] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => $sField));
        $_POST[$sField]="";
    }
}


if(ConfigService::get("enable-captcha")){
    if (!isset($_POST["captcha"]) || $_POST["captcha"] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => "captcha"));
        $_POST["captcha"]="";
    }else{
       if(SessionService::get("captcha-value") !=  $_POST["captcha"]){
           $nError++;
           $aResponse["message"]["text"] = "Le code de sécurité est incorrect.";
           $_POST["captcha"]="";
       }
    }
}


//mandatory files
//pas de vérification de fichier obligatoire sur mode edition (car le fichier précédent est conservé)
if(!$bEdit){
    if (array_key_exists("idcard", $_FILES)) {
        if ($_FILES["idcard"]["tmp_name"] == "") {
            $nError++;
            array_push($aResponse["required"], array("field" => "idcard"));
        }
    }else{
        array_push($aResponse["required"], array("field" => "idcard"));
        $nError++;
    }
}

/*
if (array_key_exists("criminal_record", $_FILES)) {
    if ($_FILES["criminal_record"]["tmp_name"] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => "criminal_record"));
    }
}else{
    array_push($aResponse["required"], array("field" => "criminal_record"));
    $nError++;
}*/

if (!isset($_POST["ad2"])) {
    $_POST["ad3"]="";
}


if (!isset($_POST["ad3"])) {
    $_POST["ad3"]="";
}

$bPresentation=false;
$bVideoPresentation=false;

if (isset($_POST["presentation"]) && $_POST["presentation"] != "") {
    $bPresentation=true;
}else{
    $_POST["presentation"]="";
}
if (isset($_POST["video"]) && $_POST["video"] != "") {
    $bVideoPresentation=true;
}else{
    $_POST["video"]="";
}

if(!$bPresentation && !$bVideoPresentation){
    $nError++;
    array_push($aResponse["required"], array("field" => "presentation"));
    array_push($aResponse["required"], array("field" => "video"));
}
if( $nError==0 ) {
    if (!$bVideoPresentation && strlen($_POST["presentation"])<140 ){
        $nError++;
        $aResponse["message"]["text"] = "Le texte de présentation doit faire au moins 140 caractères";
        array_push($aResponse["required"], array("field" => "presentation"));
    }
}

//Values
if( $nError==0 ) {
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $aResponse["message"]["text"] = "L'adresse e-mail est incorrecte.";
        array_push($aResponse["required"],array("field"=>"email"));
        $nError++;
    }
}

//comment this line for international values
/*if( $nError==0 ) {
    if( !is_numeric($_POST["tel"]) || substr($_POST["tel"],1)==0 || strlen($_POST["tel"])!=9){
        $aResponse["message"]["text"] = "Le numéro de téléphone est incorrect.(9 chiffres)";
        array_push($aResponse["required"],array("field"=>"tel"));
        $nError++;
    }
}

if( $nError==0 ) {
    if( ( !is_numeric($_POST["cp"]) && substr(strtoupper($_POST["cp"]),0,2)!="2A" && substr(strtoupper($_POST["cp"]),0,2)!="2B" ) || strlen($_POST["cp"])!=5){
        $aResponse["message"]["text"] = "Le code postal est incorrect.";
        array_push($aResponse["required"],array("field"=>"cp"));
        $nError++;
    }
}*/

//link
if ( $nError==0 ){
    if($_POST["video"]){
        $handle = curl_init($_POST["video"]);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode > 404 || $httpCode==0 ) {
            $aResponse["message"]["text"] = "Le lien vidéo ne fonctionne pas.";
            array_push($aResponse["required"],array("field"=>"video"));
            $nError++;
        }
        curl_close($handle);

        if(substr($_POST["video"],0,4)!="http"){
            $aResponse["message"]["text"] = "Le lien vidéo doit commencer par http:// ou https://.";
            array_push($aResponse["required"],array("field"=>"video"));
            $nError++;
        }
    }

}




//check upload picture
$aLimitMime = ConfigService::get("mime-type-limit");
$aMime = array_keys(ConfigService::get("mime-type-limit"));

 if ($nError == 0) {
    if (!isset($_POST["imageFilename"]) || $_POST["imageFilename"] == "") {
        $nError++;
        $aResponse["message"]["text"] = "N'oubliez pas d'envoyer votre photo !";
    }
    if (!isset($_POST["imageData"]) || $_POST["imageData"] == "") {
        $nError++;
        $aResponse["message"]["text"] = "N'oubliez pas d'envoyer votre photo !";
    }
}

$sExtension="jpg";
if ($nError == 0 ) {
    //Add base 64 encode data in FILE "image"
    if(!isset($_FILES)){
        $_FILES=array("image"=>array());
    }
    $sExtension=strtolower(substr($_POST["imageFilename"],-3));
    if($sExtension=="peg"){
        $sExtension="jpg";
    }
    $_FILES["image"]["tmp_name"]= '../tmp/'.md5(rand(1000,99999).time().ConfigService::get("key")).'.'.$sExtension;
    $_FILES["image"]["name"]=$_POST["imageFilename"];
    $encodedData = explode(',', $_POST["imageData"]);
    $decodedData = base64_decode($encodedData[1]);
    file_put_contents($_FILES["image"]["tmp_name"], $decodedData ) ;
}

if ($nError == 0 ) {
    if (!in_array(mime_content_type($_FILES['image']['tmp_name']), $aMime)) {
        $nError++;
        $aResponse["message"]["text"] = "Format de fichier de votre photo non reconnu.";
    }
}
if ($nError == 0) {
    if (filesize($_FILES['image']['tmp_name']) > ConfigService::get("max-filesize") * 1024 * 1024) {
        $nError++;
        $aResponse["message"]["text"] = "Votre photo dépasse le poids maximum autorisé. (" . ConfigService::get("max-filesize") . " Mb )";
    }
}


if ($nError == 0) {
    //format de l'image
    $img = new claviska\ SimpleImage($_FILES['image']['tmp_name']);
    if (
        $img->getWidth() < ConfigService::get("min-width") || $img->getWidth() > ConfigService::get("max-width") ||
        $img->getHeight() < ConfigService::get("min-height") || $img->getHeight() > ConfigService::get("max-height")
    ) {
        $nError++;
        $aResponse["message"]["text"] = "Les dimensions de votre photo ne sont pas valides ( entre ".ConfigService::get("min-width")."px et ".ConfigService::get("max-height")."px )";
    }

}

//check PDF ...



if($nError==0){
    if($bEdit){
        $Candidature=new Candidature(array("id"=>$OldCandidature->getId()));
        $Candidature->setDate_amended(date("Y-m-d H:i:s"));
        $OldCandidature->hydrateFromBDD(array('*'));
    }else{
        $Candidature=new Candidature();
        $Candidature->setDate_created(date("Y-m-d H:i:s"));
        //generate key for link
        $sKey=sha1($_SERVER["REMOTE_ADDR"].ConfigService::get("key").rand(1000,9999).time());
        $Candidature->setKey_edit($sKey);
        $Candidature->setState("offline");
    }
    $Candidature->setName($_POST["nom"]);
    $Candidature->setFirstname($_POST["prenom"]);
    $Candidature->setCivility($_POST["civilite"]);
    $Candidature->setEmail($_POST["email"]);
    $Candidature->setTel($_POST["tel"]);
    $Candidature->setAd1($_POST["ad1"]);
    $Candidature->setAd2($_POST["ad2"]);
    $Candidature->setAd3($_POST["ad3"]);
    $Candidature->setCity($_POST["ville"]);
    $Candidature->setZipcode($_POST["cp"]);
    $Candidature->setCountry($_POST["pays"]);
    $Candidature->setUrl_video($_POST["video"]);
    $Candidature->setPresentation(vars::cleanInput($_POST["presentation"]));



    if($oMe->getType()=="admin") {
        if (isset($_POST["autovalid"]) && $_POST["autovalid"] == "1") {
            $Candidature->setState("online");
        }
    }

    //save Files
    $outputDir = "data/" . date("Y") . "/" . date("m") . "/" . date("d") . "/". time() . session_id() . "/";
    mkdir($outputDir, 0777, true);
    $outputFilePhoto= $outputDir."original.".$sExtension;

    $outputFilePhotoFit= $outputDir."photo-fit.jpg";
    $outputFileCerificat=$outputDir."certificate.pdf";
    $outputFileIdcard=$outputDir."idcard.pdf";
    $outputFileCriminalRecord=$outputDir."extrait-judiciaire.pdf";


    //PIC
    if (@copy($_FILES['image']['tmp_name'], $outputFilePhoto)) {
        $img = new claviska\ SimpleImage($outputFilePhoto);
        /*$exif = $img->getExif();
        if (array_key_exists("Orientation", $exif)) {
            $img->autoOrient();
        }*/
        $img->bestFit(800, 800);
        $img->toFile($outputFilePhotoFit, "image/jpeg", 100);
        $Candidature->setPath_pic($outputFilePhoto);
    }else{
        $aResponse["message"]["text"] = "Erreur lors de l'enregistrement de votre photo.";
        $nError++;
    }
    @unlink($_FILES['image']['tmp_name']);


    //PDF
    if (array_key_exists("idcard", $_FILES)) {
        if(file_exists($_FILES['idcard']['tmp_name'])){
            $extension=pathinfo($_FILES['idcard']['name'], PATHINFO_EXTENSION);
            $outputFileIdcard=$outputDir."idcard.".$extension;
            if (@move_uploaded_file($_FILES['idcard']['tmp_name'], $outputFileIdcard)) {
                if (!in_array(mime_content_type($outputFileIdcard), array_merge(array("application/pdf"),$aMime) )) {
                    $nError++;
                    $aResponse["message"]["text"] = "Carte d'identité : Format de fichier non reconnu.";
                    array_push($aResponse["required"],array("field"=>"idcard"));
                }else{
                    $Candidature->setPath_idcard($outputFileIdcard);
                }
            } else {
                $aResponse["message"]["text"] = "Carte d'identité : Erreur lors de l'enregistrement de votre fichier.";
                array_push($aResponse["required"],array("field"=>"idcard"));
                $nError++;
            }
        }
    }

    if (array_key_exists("criminal_record", $_FILES)) {
        if(file_exists($_FILES['criminal_record']['tmp_name'])){
            $extension=pathinfo($_FILES['criminal_record']['name'], PATHINFO_EXTENSION);
            $outputFileCriminalRecord=$outputDir."extrait-judiciaire.".$extension;
            if (@move_uploaded_file($_FILES['criminal_record']['tmp_name'],  $outputFileCriminalRecord)) {
                if (!in_array(mime_content_type( $outputFileCriminalRecord), array_merge(array("application/pdf"),$aMime) )) {
                    $nError++;
                    $aResponse["message"]["text"] = "Extrait judiciaire : Format de fichier non reconnu.";
                    array_push($aResponse["required"],array("field"=>"criminal_record"));
                }else{
                    $Candidature->setPath_criminal_record($outputFileCriminalRecord);
                }
            } else {
                $aResponse["message"]["text"] = "Extrait judiciaire : Erreur lors de l'enregistrement de votre fichier.";
                array_push($aResponse["required"],array("field"=>"criminal_record"));
                $nError++;
            }
        }
    }

   /* if (array_key_exists("attestation", $_FILES)) {
        if(file_exists($_FILES['attestation']['tmp_name'])){
            if (@move_uploaded_file($_FILES['attestation']['tmp_name'], $outputFileCerificat)) {
                if (!in_array(mime_content_type($outputFileCerificat), array("application/pdf"))) {
                    $nError++;
                    $aResponse["message"]["text"] = "Format de fichier PDF non reconnu.";
                }else{
                    $Candidature->setPath_certificate($outputFileCerificat);
                }
            } else {
                $aResponse["message"]["text"] = "Erreur lors de l'enregistrement de votre fichier PDF.";
                $nError++;
            }
        }

    }*/

    if( $nError==0){

        $Candidature->saveWithPDOSecure();

        $TwigEngine = App::getTwig();
        $sBodyMailHTML = $TwigEngine->render("visitor/mail/body.html.twig", [
            "candidature" => $Candidature
        ]);
        $sBodyMailTXT = $TwigEngine->render("visitor/mail/body.txt.twig", [
            "candidature" => $Candidature
        ]);
        if(!$bEdit) {
            Mail::sendMail($Candidature->getEmail(), "Confirmation de candidature", $sBodyMailHTML, $sBodyMailTXT, true);
        }

        if($oMe->getType()=="admin"){
            $aResponse["redirect"] = "/candidature/list.html";
        }else{
            $aResponse["redirect"] = "/candidature/success.html";
        }

        $aResponse["durationMessage"] = "2000";
        $aResponse["durationRedirect"] = "2000";
        $aResponse["durationFade"] = "10000";
        $aResponse["message"]["title"] = "";
        $aResponse["message"]["type"] = "success";
        //if edit clean old file
        if($bEdit){
            @unlink($OldCandidature->getPath_pic());
            $aResponse["message"]["text"] = "Modification enregistrée !";
        }else{
            $aResponse["message"]["text"] = "Candidature envoyée correctement !";
        }

    }

}


//return
$aDataScript['data'] = json_encode($aResponse);
