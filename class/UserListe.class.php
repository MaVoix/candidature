<?php

/**
 * Class UserListe
 */
class UserListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "login",
        "pass",
        "type",
        "enable"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("user");
        $this->setAliasPrincipal("User");
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


    public function applyRules4Connexion($login, $pass)
    {
        $this->setAllFields();

        $this->addCriteres([
            [
                "field" => "login",
                "compare" => "=",
                "value" => vars::secureInjection($login)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "pass",
                "compare" => "=",
                "value" => vars::secureInjection($pass)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "enable",
                "compare" => "=",
                "value" => "1"
            ]
        ]);

        return $this;
    }

}