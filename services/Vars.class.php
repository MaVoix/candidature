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

    public static function cleanInput($input) {

        $search = array(
            '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
        );

        $output = preg_replace($search, '', $input);
        return $output;
    }

    public static function removeDirectory($path) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? self::removeDirectory($file) : @unlink($file);
        }
        @rmdir($path);
        return;
    }

    public static function rcopy($src, $dest){

        // If source is not a directory stop processing
        if(!is_dir($src)) return false;

        // If the destination directory does not exist create it
        if(!is_dir($dest)) {
            if(!mkdir($dest)) {
                // If the destination directory could not be created stop processing
                return false;
            }
        }

        // Open the source directory to read in files
        $i = new DirectoryIterator($src);
        foreach($i as $f) {
            if($f->isFile()) {
                @copy($f->getRealPath(), "$dest/" . $f->getFilename());
            } else if(!$f->isDot() && $f->isDir()) {
                self::rcopy($f->getRealPath(), "$dest/$f");
            }
        }
    }

}

