<?php

class Navigate
{
    private $user=null;
    private $page=null;
    private $area=null;
    private $format=null;
    private $type=null;
    private $template=null;
    private $data_template=array();

    public function __construct(User $oUser )
    {
        $this->user = $oUser;

        if( !$oUser->getType() )
        {
            $this->type = "visitor";
        }
        else
        {
            $this->type = $oUser->getType();
        }

        if(!isset($_GET["page"]))
        {
            $_GET["page"] = $this->getDefaultPage();
        }

        if(!isset($_GET["area"]))
        {
            $_GET["area"] = $this->getDefaultArea();
        }

        if(!isset($_GET["format"]))
        {
            $_GET["format"] = $this->getDefaultPageFormat();
        }

        $this->page   = $_GET["page"];
        $this->area   = $_GET["area"];
        $this->format = $_GET["format"];
    }

    public function setUser(User $User)
    {
        $this->user = $User;
        return $this;
    }

    public function getDefaultPageFormat()
    {
        return ConfigService::get("format-default");
    }

    public function getDefaultPage()
    {
        $default_pages = ConfigService::get("page-default");
        $user_type = $this->getUser()->getType();

        if( array_key_exists($user_type, $default_pages) )
        {
            return $default_pages[ $user_type ];
        }

       else return "@no-default-page";
    }

    public function getDefaultArea()
    {
        $default_areas = ConfigService::get("area-default");
        $user_type = $this->getUser()->getType();

        if( array_key_exists($user_type, $default_areas) )
        {
            return $default_areas[ $user_type ];
        }

        else return "@no-default-area";
    }

    public function getDefaultPath()
    {
        return sprintf("/%s/%s.%s", $this->getDefaultArea(), $this->getDefaultPage(), $this->getDefaultPageFormat());
    }

    public function loadPage($sPageDir,$sTemplateDir){

        $sPathOfScript=$sPageDir."/".$this->type."/".$this->area."/".$this->page.".php";
        if(!file_exists($sPathOfScript)){
            $aTypes=explode("|",ConfigService::get("types"));
            foreach($aTypes as $sTypes){
                $aType=explode(">",$sTypes);
                if($aType[0]==$this->type){
                    foreach($aType as $sType){
                        if(!file_exists($sPathOfScript)){
                            $sPathOfScript=$sPageDir."/".$sType."/".$this->area."/".$this->page.".php";

                        }
                    }
                }
            }
        }
        $sPathOfTemplate="";
        if( $this->format == "html"){
            $sPathOfTemplate=$sTemplateDir."/".$this->type."/".$this->area."/".$this->page.".html.twig";

            if(!file_exists( $sPathOfTemplate)){
                $aTypes=explode("|",ConfigService::get("types"));
                foreach($aTypes as $sTypes){
                    $aType=explode(">",$sTypes);
                    if($aType[0]==$this->type){
                        foreach($aType as $sType){
                            if(!file_exists( $sPathOfTemplate)){
                                $sPathOfTemplate=$sTemplateDir."/".$sType."/".$this->area."/".$this->page.".html.twig";
                            }
                        }
                    }
                }
            }
        }

        if( $this->format == "xml")
        {
            header("Content-type: text/xml");
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            $sPathOfTemplate = $sTemplateDir."/xml.twig";
        }

        if( $this->format == "json")
        {
            header("Content-type: application/json");
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            $sPathOfTemplate = $sTemplateDir."/json.twig";
        }

        if( $this->format == "csv")
        {
            header("Content-type: text/csv");
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            $sPathOfTemplate = $sTemplateDir."/csv.twig";
        }

        $aDataScript = [];
        $oMe = $this->user;
        $oNavigate = $this;

        if(file_exists($sPathOfScript))
        {
            require_once $sPathOfScript;
        }

        $aDataScript["versionjs"]=time();
        $aDataScript["versioncss"]=time();

        $this->data_template=$aDataScript;

        $this->template=substr($sPathOfTemplate,strlen($sTemplateDir."/"));

    }
    public function getUrl(){
        $sParam='';
        if(isset($_GET["id"])){
            $sParam="?id=".$_GET["id"];
        }
        return ConfigService::get("urlSite")."/".$this->getArea()."/".$this->getPage().".".$this->getFormat().$sParam;
    }

    public function getUser(){
        return $this->user;
    }

    public function getPage(){
        return $this->page;
    }

    public function getArea(){
        return $this->area;
    }

    public function getType(){
        return $this->type;
    }

    public function getFormat(){
        return $this->format;
    }

    public function getTemplate(){
        return $this->template;
    }

    public function getDataTemplate(){
        return $this->data_template;
    }
}