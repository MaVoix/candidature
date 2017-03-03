<?php



class ConfigService
{
    /**
     * @param string $sVarname
     * @return mixed
     */
    public static function get($sVarname)
    {
        global $_CONFIG;

        if( !array_key_exists($sVarname, $_CONFIG) )
        {
            return null;
        }

        return $_CONFIG[ $sVarname ];
    }


}