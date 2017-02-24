<?php


class Vars
{



    //secureInjection
    public static function secureInjection($sChaine){
        $sChaine=stripslashes($sChaine);
        return addslashes($sChaine);
    }




}

