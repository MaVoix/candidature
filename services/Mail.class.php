<?php

class Mail
{
    /**
     * @param string|string[] $to
     * @param string $title
     * @param string $body
     * @param string|string[] $cc (Optional)
     * @param string|string[] $bcc (Optional)
     * @param string|string[] $files (Optional)
     * @return boolean
     */
	public static function sendMail($to, $title, $body, $cc=[], $bcc=[], $files=[])
	{
	    if( is_string($to) )
        {
            if( empty($to) )
            {
                throw new InvalidArgumentException("Argument 'To' expected to be non empty array or string");
            }
            else
            {
                $to = [$to];
            }
        }

	    if( !is_array($to) OR count($to)==0 )
        {
            throw new InvalidArgumentException("Argument 'To' expected to be non empty array or string");
        }

        if( !is_string($title) OR empty($title) )
        {
            throw new InvalidArgumentException("Argument 'Title' expected to be non empty string");
        }

        if( !is_string($body) OR empty($body) )
        {
            throw new InvalidArgumentException("Argument 'Body' expected to be non empty string");
        }

        if( is_string($cc) )
        {
            $cc = [$cc];
        }

        if( is_string($bcc) )
        {
            $bcc = [$bcc];
        }

        if( is_string($files) )
        {
            $files = [$files];
        }

        $aTo = $aBcc = $aCc = $aPiecesJointes = [];
        $sSujet = $title;
        $sMessageHtml = $body;

        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';

        if( ConfigService::get("mail-isSMTP") )
        {
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = ConfigService::get("mail-smtp-serveur");  // Specify main and backup server
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = ConfigService::get("mail-smtp-login");                            // SMTP username
            $mail->Password = ConfigService::get("mail-smtp-pass");                           // SMTP password
        }

        $mail->isHTML(true);
        $mail->From = ConfigService::get("mail-expediteur-mail");
        $mail->FromName = ConfigService::get("mail-expediteur-nom");
        $mail->addReplyTo( ConfigService::get("mail-reply-mail"), ConfigService::get("mail-reply-nom") );

        // Envois de mails activés
        if( ConfigService::get("mail-enabled")===true )
        {
            // Destinataire
            foreach( $to as $sDestinataire )
            {
                Vars::pushIfNotInArray($aTo, $sDestinataire);
            }

            // Copie
            foreach( $cc as $sDestinataire )
            {
                Vars::pushIfNotInArray($aCc, $sDestinataire);
            }

            // Copie cachée
            foreach( $bcc as $sDestinataire )
            {
                Vars::pushIfNotInArray($aBcc, $sDestinataire);
            }

            // Copies cachées automatiques
            $config_mail_bcc = ConfigService::get("mail-bcc");
            if( is_array($config_mail_bcc) )
            {
                foreach( $config_mail_bcc as $sEmail )
                {
                    Vars::pushIfNotInArray($aBcc, $sEmail);
                }
            }
            elseif( !is_array($config_mail_bcc) and !empty($config_mail_bcc) )
            {
                Vars::pushIfNotInArray($aBcc, $config_mail_bcc);
            }

        }
        else // Envoi de mails désactivé
        {
            foreach( $to as $sDestinataire )
            {
                $sSujet .= "[{$sDestinataire}]";
            }

            $config_mail_bcctest = ConfigService::get("mail-bcctest");
            $config_mail_test = ConfigService::get("mail-test");

            // Destinataire
            if( is_array($config_mail_test) )
            {
                foreach( $config_mail_test as $sEmail )
                {
                    Vars::pushIfNotInArray($aTo, $sEmail);
                }
            }
            else
            {
                if( !empty($config_mail_test) )
                {
                    Vars::pushIfNotInArray($aTo, $config_mail_test);
                }
            }

            // Copie cachée
            if( is_array($config_mail_bcctest) )
            {
                foreach( $config_mail_bcctest as $sEmail )
                {
                    Vars::pushIfNotInArray($aBcc, $sEmail);
                }
            }
            else
            {
                if( !empty($config_mail_bcctest) )
                {
                    Vars::pushIfNotInArray($aBcc, $config_mail_bcctest);
                }
            }
        }

        // Pièces jointes
        foreach( $files as $sFile )
        {
            Vars::pushIfNotInArray($aPiecesJointes, $sFile);
        }

        // Contenu
        $sAltBody = Html2Text\Html2Text::convert($sMessageHtml);
        $sMessageTexte = strip_tags( $sAltBody );

        // Destinataires
        foreach( $aTo as $sEmail )
        {
            $mail->addAddress($sEmail);
        }

        // Copies
        foreach( $aCc as $sEmail )
        {
            $mail->addCC($sEmail);
        }

        // Copies cachées
        foreach( $aBcc as $sEmail )
        {
            $mail->addBCC($sEmail);
        }

        // Pièces jointes
        foreach( $aPiecesJointes as $sFilePath )
        {
            $mail->addAttachment($sFilePath);
        }

        // Sujet
        $mail->Subject = $sSujet;

        // Message html
        $mail->Body = $sMessageHtml;

        // Message texte
        $mail->AltBody = $sMessageTexte;

        if( !$mail->send() )
        {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        }
        else return true;
	}
	
	public static function sendMailBug($aParam)
	{
		if(isset($aParam["titre"]) && isset($aParam["body"]) ){
			$mail = new PHPMailer;	
			$mail->CharSet = 'UTF-8';   
			//$mail->isSMTP();                                      // Set mailer to use SMTP
			//$mail->Host = 'smtp.example.com;smtp2.example.com';  // Specify main and backup server
			//$mail->SMTPAuth = false;                               // Enable SMTP authentication
			//$mail->Username = '';                            // SMTP username
			//$mail->Password = '';                           // SMTP password
			//$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
	
			$mail->From = ConfigService::get("mail-expediteur-mail");
			$mail->FromName = ConfigService::get("mail-expediteur-nom");			
			$mail->addReplyTo(ConfigService::get("mail-reply-mail"),ConfigService::get("mail-reply-nom"));	
				
			if(ConfigService::get("mail-bug-enabled")){				
				//ENVOI DES MAILS DE TEST
				if(is_array(ConfigService::get("mail-test"))){
					foreach(ConfigService::get("mail-test") as $sDestinataire){
						$mail->addAddress($sDestinataire);            
					}
				}else{
					$mail->addAddress(ConfigService::get("mail-test"));  
				}
				
				if(ConfigService::get("mail-bcctest")){
					if(is_array(ConfigService::get("mail-bcctest"))){
						foreach(ConfigService::get("mail-bcctest") as $sDestinataire){
							$mail->addBCC($sDestinataire);            
						}
					}else{
						$mail->addBCC(ConfigService::get("mail-bcctest"));  
					}				
				}			
				
				//$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
				if(isset($aParam["file"])){
					if(is_array($aParam["file"])){
						foreach($aParam["file"] as $sFile){
							$mail->addAttachment($sFile);           
						}
					}else{
						$mail->addAttachment($aParam["file"]);
					}				
				}
				$mail->isHTML(true);   
											   // Set email format to HTML	
				
				$mail->Subject = $aParam["titre"];

                $sReferer="";
                if(isset($_SERVER['HTTP_REFERER'])){
                    $sReferer=$_SERVER['HTTP_REFERER'];
                }
				//variable globale
				$aParam["body"].="<hr />";
				$aParam["body"].="<br /><h3>SESSION :</h3>";
				$aParam["body"].="<br /><strong>id : </strong>".SessionService::get("id");
				$aParam["body"].="<br /><strong>langue : </strong>".SessionService::get("langue");
				$aParam["body"].="<br /><strong>type : </strong>".SessionService::get("type");
				$aParam["body"].="<br /><h3>NAVIGATEUR :</h3>";
				$aParam["body"].="<br /><strong>USER AGENT : </strong>".isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'-';
				$aParam["body"].="<br /><strong>IP : </strong>".$_SERVER['REMOTE_ADDR'];
				$aParam["body"].="<br /><strong>QUERY STRING : </strong>".$_SERVER['QUERY_STRING'];
				$aParam["body"].="<br /><strong>REQUEST URI : </strong>".$_SERVER['REQUEST_URI'];
				$aParam["body"].="<br /><strong>HTTP REFERER : </strong>".$sReferer;
				
				$mail->Body    = $aParam["body"];
				$sAltBody= str_replace(array("<br />","<br/>","<br>","<p>","</p>","&nbsp;"),array("\n","\n","\n","\n\n","\n\n"," "),$aParam["body"]);
			
				$mail->AltBody = strip_tags($sAltBody);
				$sFichier="tmp/debug/".hash('sha512',($mail->Body.$mail->Subject)).".txt";
				
				if(file_exists($sFichier)){
					if(filemtime($sFichier)<strtotime("now -1 minutes")){
						unlink($sFichier);
					}
				}
				//calcul du nombre de fichier envoyé depuis 10 minutes
				$nNbFile=0;				
				if ($handle = opendir('tmp/debug/')) {
   					while (false !== ($entry = readdir($handle))) {
        				if ($entry != "." && $entry != "..") {
           					if(filemtime("tmp/debug/".$entry)<strtotime("now -1 minutes")){
								unlink("tmp/debug/".$entry);
							}else{
								$nNbFile++;
							}
       				 	}
   					}
   				 closedir($handle);
				}
				
				if(!file_exists($sFichier) && $nNbFile<20){					
					file_put_contents($sFichier,$mail->AltBody);
					if(!$mail->send()) {
					   echo 'Message could not be sent.';
					   echo 'Mailer Error: ' . $mail->ErrorInfo;
					   exit;
					}
				}
			}
		}
	}
}
