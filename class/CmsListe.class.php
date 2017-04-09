<?php

/**
 * Class CmsListe
 */
class CmsListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "date_created",
        "date_amended",
        "date_deleted",
        "ref",
        "content"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct($aParam = array())
    {
        parent::__construct();
        $this->setTablePrincipale("cms");
        $this->setAliasPrincipal("Cms");
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


    public function applyRules4GetBlock($sRef)
    {
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "ref",
                "compare" => "=",
                "value" => $sRef
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

    public function applyRules4All()
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

}