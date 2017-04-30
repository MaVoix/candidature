<?php
$aDataScript["out"] = "";

if( ConfigService::get("enable-script-hotfix-1-9-1") ) {

    //liste les candidatures en base
    $oListeCandidature = new CandidatureListe();
    $oListeCandidature->applyRules4ListAdmin();
    $aCandidatures = $oListeCandidature->getPage();
    // parcrous les candidatures
    foreach ($aCandidatures as $aCandidature) {
        $aDataScript["out"] .= "<hr />";
        //instancie chaque candidature
        $oCandidature = new Candidature(array("id" => $aCandidature["id"]));
        $oCandidature->hydrateFromBDD(array('*'));
        $aDataScript["out"] .= "<div>CANDIDATURE : " . $oCandidature->getId() . "</div>";
        //recupere la clé (pour creer le chemin sécurisé)
        $sKeyEdit = $oCandidature->getKey_edit();

        //creer les deux nouveaux dossiers
        $outputDir = "data/" . date("Y") . "/" . date("m") . "/" . date("d") . "/" . time() . $oCandidature->getId() . "/";
        $outputDirSecure = $outputDir . substr($sKeyEdit, 0, 10) . "/";
        mkdir($outputDir, 0777, true);
        mkdir($outputDirSecure, 0777, true);
        $aDataScript["out"] .= "<div>chemin : " . $outputDir . "</div>";
        $aDataScript["out"] .= "<div>chemin secure : " . $outputDirSecure . "</div>";

        //spécifie les noms et dossiers de destination des fichiers
        $fileCertificate = $outputDirSecure . basename($oCandidature->getPath_certificate());
        $fileCriminalRecord = $outputDirSecure . basename($oCandidature->getPath_criminal_record());
        $filedIdCard = $outputDirSecure . basename($oCandidature->getPath_idcard());
        $fileIdCardVerso = $outputDirSecure . basename($oCandidature->getPath_idcard_verso());
        $filePhoto = $outputDir . basename($oCandidature->getPath_pic());
        $filePhotoFit = $outputDir . basename($oCandidature->getPath_pic_fit());

        //recupère les chemin des vieux dossiers (pour suppression en fin de boucle)
        $sOldPathPic = dirname($oCandidature->getPath_pic());
        $sOldPathCriminalRecord = dirname($oCandidature->getPath_criminal_record());
        $sOldPathIdCard = dirname($oCandidature->getPath_idcard());
        $sOldPathIdCardVerso = dirname($oCandidature->getPath_idcard_verso());

        //copie les fichiers dans les nouveaux dossiers
        @copy($oCandidature->getPath_certificate(), $fileCertificate);
        @copy($oCandidature->getPath_criminal_record(), $fileCriminalRecord);
        @copy($oCandidature->getPath_idcard(), $filedIdCard);
        @copy($oCandidature->getPath_idcard_verso(), $fileIdCardVerso);
        @copy($oCandidature->getPath_pic(), $filePhoto);
        @copy($oCandidature->getPath_pic_fit(), $filePhotoFit);

        //Met à jour les liens en base
        if (file_exists($filePhoto) && $oCandidature->getPath_pic_fit()!="") {
            $oCandidature->setPath_pic($filePhoto);
            $aDataScript["out"] .= "<div>mise à jour " . $filePhoto . "</div>";
        }

        if (file_exists($fileCertificate) && $oCandidature->getPath_certificate()!="") {
            $oCandidature->setPath_certificate($fileCertificate);
            $aDataScript["out"] .= "<div>mise à jour " . $fileCertificate . "</div>";
        }
        if (file_exists($fileCriminalRecord) && $oCandidature->getPath_criminal_record()!="") {
            $oCandidature->setPath_criminal_record($fileCriminalRecord);
            $aDataScript["out"] .= "<div>mise à jour " . $fileCriminalRecord . "</div>";
        }
        if (file_exists($filedIdCard) && $oCandidature->getPath_idcard()!="") {
            $oCandidature->setPath_idcard($filedIdCard);
            $aDataScript["out"] .= "<div>mise à jour " . $filedIdCard . "</div>";
        }
        if (file_exists($fileIdCardVerso) && $oCandidature->getPath_idcard_verso()!="") {
            $oCandidature->setPath_idcard_verso($fileIdCardVerso);
            $aDataScript["out"] .= "<div>mise à jour " . $fileIdCardVerso . "</div>";
        }

        //sauvegarde les modification
        $oCandidature->save();
        $aDataScript["out"] .= "<div>save</div>";

        //destruction des anciesn dossiers
        if (is_dir($sOldPathPic)) vars::removeDirectory($sOldPathPic);
        if (is_dir($sOldPathCriminalRecord)) vars::removeDirectory($sOldPathCriminalRecord);
        if (is_dir($sOldPathIdCard)) vars::removeDirectory($sOldPathIdCard);
        if (is_dir($sOldPathIdCardVerso)) vars::removeDirectory($sOldPathIdCardVerso);
    }
}else{
    $aDataScript["out"] .= "<div>EXECUTION BLOQUE (cf. fichier de config)</div>";
}