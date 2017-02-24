<?php

session_start();

ini_set("memory_limit", "256M");


$bMaintenance=false;
if(file_exists('maintenance.php')){
    require_once 'maintenance.php';
}else{
    echo "<h1>File maintenance.php not found. (see maintenance.sample.php for further details)</h1>";
}
if($bMaintenance){
    echo "<h1>Under maintenance, please try later ...</h1>";
}else{
    //composer loader
    require_once '../vendor/autoload.php';

    //config loader
    require_once 'config.php';
    if( strstr($_SERVER['HTTP_HOST'], "mavoix.info") ) {
        require_once 'config.online.php';
    }elseif( strstr($_SERVER['HTTP_HOST'], "nomserveurdev") ) {
        require_once 'config.dev.php';
    }else{
        require_once 'config.local.php';
    }

    //class loader
    require_once '../class/Navigate.class.php';
    require_once '../class/TwigExtension.class.php';
    require_once '../class/TwigExtensionFilter.class.php';
    require_once '../class/User.class.php';

    //service loader
    require_once '../services/App.class.php';
    require_once '../services/ConfigService.class.php';
    require_once '../services/DbLink.class.php';
    require_once '../services/Mail.class.php';
    require_once '../services/Mysql.class.php';
    require_once '../services/MysqlStatement.class.php';
    require_once '../services/SessionService.class.php';
    require_once '../services/Vars.class.php';


    //init app
    App::init();
}


