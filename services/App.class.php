<?php

class App
{
    const TWIG_TEMPLATE_DIR     = '../templates';       //without "/" at the end
    const PAGE_DIR              = '../pages';           //without "/" at the end
    const TWIG_TEMPLATE_CACHE   = '../cache/twig';      //without "/" at the end

    public static function init()
    {
        $oMe  =  self::init_user();
        $oNavigate=new Navigate($oMe);

        $oNavigate->loadPage(self::PAGE_DIR , self::TWIG_TEMPLATE_DIR);
        $twig = self::getTwig($oNavigate);
        $twig->addGlobal("oMe", $oMe );

        if(file_exists( self::TWIG_TEMPLATE_DIR."/".$oNavigate->getTemplate()))
        {
            echo $twig->render($oNavigate->getTemplate() , $oNavigate->getDataTemplate());
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
        }
    }

    public static function getTwig($oNavigate=null)
    {

        $loader = new Twig_Loader_Filesystem(self::TWIG_TEMPLATE_DIR);
        $twig = new Twig_Environment($loader, array(
            'cache' => self::TWIG_TEMPLATE_CACHE,
            'auto_reload' => ConfigService::get("twig_auto_reload")
        ));
        $twig->addGlobal('TwigExtension', new TwigExtension() );
        $twig->addGlobal("ConfigService", new ConfigService());


        $twig->addGlobal("get", $_GET);
        $twig->addGlobal("post", $_POST);

        if( !is_null($oNavigate) )
        {
            $twig->addGlobal('navigate', $oNavigate );
        }

        $twig->addExtension(new Twig_Extension_Filter());
        $twig->addExtension(new Twig_Extension_Debug());


        return $twig;
    }

    private static function init_user(){

        if(SessionService::get("user-id")){
            $oUser=new User(array("id"=>SessionService::get("user-id")));
            $oUser->hydrateFromBDD(array('*'));
        }else{
            $oUser=new User();
            $oUser->setType("visitor");
        }
        return $oUser;

    }



}