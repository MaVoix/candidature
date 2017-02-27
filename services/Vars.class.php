<?php


class Vars
{



    //secureInjection
    public static function secureInjection($sChaine){
        $sChaine=stripslashes($sChaine);
        return addslashes($sChaine);
    }


    public static function pushIfNotInArray(&$array, $value)
    {
        if( !in_array($value, $array) )
        {
            $array[] = $value;
        }
    }



}

