<?php

class Mail
{

	public static function sendMail($to, $title, $body, $sAltBody , $isHtml, $cc=[], $bcc=[], $files=[])
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

        $mail->isHTML($isHtml);
        $mail->From = ConfigService::get("mail-expediteur-mail");
        $mail->FromName = ConfigService::get("mail-expediteur-nom");
        $mail->addReplyTo( ConfigService::get("mail-reply-mail"), ConfigService::get("mail-reply-nom") );

        // Envois de mails activés

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



        // Pièces jointes
        foreach( $files as $sFile )
        {
            Vars::pushIfNotInArray($aPiecesJointes, $sFile);
        }


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
	


}
