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
        "country",
        "email",
        "tel",
        "presentation",
        "url_video",
        "path_pic",
        "path_certificate",
        "path_idcard",
        "path_idcard_verso",
        "path_criminal_record",
        "is_certificate",
        "is_idcard",
        "is_criminal_record",
        "comment",
        "key_edit",
        "lat",
        "lng",
        "tire_au_sort"
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

        /*$this->addCriteres([
            [
                "field" => "state",
                "compare" => "=",
                "value" => "offline"
            ]
        ]);*/

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

        $this->setTri("RAND()");

    }

    public function applyRules4ListTireAuSort()
    {
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "tire_au_sort",
                "compare" => "=",
                "value" => "1"
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

    public function applyRules4GetEditLink($email,$cp)
    {
        $this->setAllFields();

        $this->addCriteres([
            [
                "field" => "email",
                "compare" => "=",
                "value" => vars::secureInjection($email)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "zipcode",
                "compare" => "=",
                "value" => vars::secureInjection($cp)
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

    public function applyRules4GetCandidatSaved($id)
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
            case "is_idcard" :
                $this->addCriteres([
                    [
                        "field" => "is_idcard",
                        "compare" => "=",
                        "value" => "1"
                    ]
                ]);
                break;
            case "is_criminal_record" :
                $this->addCriteres([
                    [
                        "field" => "is_criminal_record",
                        "compare" => "=",
                        "value" => "1"
                    ]
                ]);
                break;
            case "not_certificate" :
                $this->addCriteres([
                    [
                        "field" => "is_certificate",
                        "compare" => "=",
                        "value" => "0"
                    ]
                ]);
                break;
            case "not_idcard" :
                $this->addCriteres([
                    [
                        "field" => "is_idcard",
                        "compare" => "=",
                        "value" => "0"
                    ]
                ]);
                break;
            case "not_criminal_record" :
                $this->addCriteres([
                    [
                        "field" => "is_criminal_record",
                        "compare" => "=",
                        "value" => "0"
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
