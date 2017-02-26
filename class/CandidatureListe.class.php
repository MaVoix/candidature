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
        /*$this->setTri("");
        $this->setSens("DESC");
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

}
