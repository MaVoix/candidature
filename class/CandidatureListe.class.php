<?php

/**
 * Class CandidatureListe
 */
class CandidatureListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "date_created",
        "date_amended",
        "date_deleted",
        "state",
        "civility",
        "firstname",
        "name",
        "ad1",
        "ad2",
        "ad3",
        "city",
        "zipcode",
        "email",
        "tel",
        "presentation",
        "url_video",
        "path_pic",
        "path_certificate",
        "is_certificate",
        "key_edit"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("candidature");
        $this->setAliasPrincipal("Candidature");
        $this->setTri("date_created");
        $this->setSens("DESC");
        /*
        $this->setSearchFields(array(
        array("field"=>"nom")
        ))*/
    }

    /**
     * Access champs table
     */
    public static function champs()
    {
        return self::$_champs;
    }

    /**
     * Methode pour récupérer tous les champs
     */
    public function setAllFields()
    {
        $this->setFields(self::$_champs);
    }


    public function applyRules4Key($key,$id)
    {
        $this->setAllFields();

        $this->addCriteres([
            [
                "field" => "key_edit",
                "compare" => "=",
                "value" => vars::secureInjection($key)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "id",
                "compare" => "=",
                "value" => vars::secureInjection($id)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "state",
                "compare" => "=",
                "value" => "offline"
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);




        return $this;
    }

    public function applyRules4ListVisitor()
    {
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "state",
                "compare" => "=",
                "value" => "online"
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);

    }

    public function applyRules4ListAdmin()
    {
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);

    }


    public function applyRules4GetCandidatVisitor($id)
    {
        $this->setAllFields();

        $this->addCriteres([
            [
                "field" => "id",
                "compare" => "=",
                "value" => vars::secureInjection($id)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "state",
                "compare" => "=",
                "value" => "online"
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);

        return $this;
    }


    public function applyRules4GetCandidatAdmin($id)
    {
        $this->setAllFields();

        $this->addCriteres([
            [
                "field" => "id",
                "compare" => "=",
                "value" => vars::secureInjection($id)
            ]
        ]);


        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);

        return $this;
    }

    public function applyRules4Search($sSearch){
        $this->addCriteres([
            [
                "field" => "",
                "compare" => "sql",
                "value" => " name LIKE '%".vars::secureInjection($sSearch)."%' 
                            OR firstname LIKE '%".vars::secureInjection($sSearch)."%'
                            OR zipcode LIKE '%".vars::secureInjection($sSearch)."%'
                            OR tel LIKE '%".vars::secureInjection($sSearch)."%'
                            OR city LIKE '%".vars::secureInjection($sSearch)."%'
                            OR email LIKE '%".vars::secureInjection($sSearch)."%'
                            "
            ]
        ]);
    }

    public function applyRules4FilterBy($sFilter)
    {
        switch ($sFilter){
            case "online" :
                $this->addCriteres([
                    [
                        "field" => "state",
                        "compare" => "=",
                        "value" => "online"
                    ]
                ]);
                break;
            case "offline" :
                $this->addCriteres([
                    [
                        "field" => "state",
                        "compare" => "=",
                        "value" => "offline"
                    ]
                ]);
                break;
            case "is_certificate" :
                $this->addCriteres([
                    [
                        "field" => "is_certificate",
                        "compare" => "=",
                        "value" => "1"
                    ]
                ]);
                break;
            }


    }

    public function applyRules4OrderBy($sOrder)
    {
        if(strstr($sOrder,"--")){
            $aOrder=explode("--",$sOrder);
            $this->setTri($aOrder[0]);
            $this->setSens($aOrder[1]);
        }
    }
}
