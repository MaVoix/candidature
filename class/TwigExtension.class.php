<?php

class TwigExtension
{
    public function getPath($filepath){
        $sDest="assets/".basename($filepath);
        if(!file_exists($sDest)) {
            copy("../" . $filepath, $sDest);

            //hack for font css
            if (strstr($filepath, "font")) {
                $sFontPath = "../" . substr(str_replace("/css/", "/fonts/", $filepath), 0, -(strlen(basename($filepath))) - 1);
                self::recurse_copy($sFontPath, "fonts");
            }
        }
        return "/".$sDest;
    }

    private static function recurse_copy( $src, $dst ) {

        $dir = opendir( $src );
        @mkdir( dirname( $dst ) );

        while( false !== ( $file = readdir( $dir ) ) ) {
            if( $file != '.' && $file != '..' ) {
                if( is_dir( $src . "/" . $file ) ) {
                    self::recurse_copy( $src . "/" . $file, $dst . "/" . $file );
                } else {
                    copy( $src . "/" . $file, $dst . "/" . $file );
                }
            }
        }
        closedir( $dir );
    }
}