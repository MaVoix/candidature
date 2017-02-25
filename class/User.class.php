<?php

/**
 * Class User
 */
class User	{

    private $aDataNonHydrate = array();
    private $aDataSet = array();
    private $callHydrateFromBDDOnGet = 0;

    private $_sDbInstance = null;

    private $nId;
    private $sLogin;
    private $sPass;
    private $sType;
    private $bEnable;


    /**
     * Constructeur
     * @param array $aParam tableau de parametres ( clé "id" pour instancier un user avec un id précis )
     * @param $sDbInstance (Opt) Nom de l'instance de la bdd à utiliser
     */
    public function __construct( $aParam=array(), $sDbInstance=null )
    {
        $this->hydrate($aParam);
        $this->nId = ( isset($aParam['id']) ) ? $aParam['id'] : 0;
        $this->_sDbInstance = $sDbInstance;
    }

    /**
     * Fonction permettant d'hydrater un objet
     * @param $aDonnees array tableau clé-valeur à hydrater ( par exemple "nom"=>"DUPONT" )
     */
    public function hydrate($aDonnees)
    {
        foreach ($aDonnees as $sKey => $sValue)
        {
            if(!is_int($sKey))
            {
                $sMethode = 'set'.ucfirst($sKey);
                if (method_exists($this, $sMethode))
                {
                    if( is_null($sValue) ) $sValue="";
                    $this->$sMethode($sValue);
                }
                else
                {
                    //echo "<br />User->$sMethode() n'existe pas!";
                    $this->addDataNonHydrate($sKey,$sValue);
                }
            }
        }
    }

    /**
     * Fonction permettant d'hydrater un objet à partir d'une liste de champs (s'appuie sur l'id de l'objet)
     * @param $aFields array tableau contenant la liste des champs à hydrater ( '*' pour tous)
     */
    public function hydrateFromBDD($aFields=array())
    {
        if(count($aFields))
        {
            //hydrate uniquement les champs de base (pour le reste coder directement dans les acesseurs)
            $aData=DbLink::getInstance($this->_sDbInstance)->selectForHydrate($this->getId(),"user",$aFields);

            //hydrate l'objet
            $this->hydrate($aData);
        }
    }


    /**
     * Fonction permettant d'ajouter des données non-hydratées à l'objet
     * @param string $sKey champs
     * @param mixed $sValue valeur
     */
    public function addDataNonHydrate($sKey,$sValue)
    {
        $this->aDataNonHydrate[$sKey]=$sValue;
    }

    /**
     * Fonction permettant de récuperer des données non-hydratées à l'objet
     * @param string $sKey champs à récupérer
     * @return string valeur du champ
     */
    public function getDataNonHydrate($sKey)
    {
        if(isset($this->aDataNonHydrate[$sKey]))
        {
            return $this->aDataNonHydrate[$sKey];
        }
        else
        {
            return "";
        }
    }

    /**
     * Fonction permettant de supprimer fictivement un objet (en lui passant un date supprime)
     */
    public function supprime()
    {
        $this->setDate_supprime(date("Y-m-d H:i:s"));
        $this->save();
    }

    /**
     * Fonction permettant de supprimer réellement un objet (en faisant un DELETE )
     */
    public function delete()
    {
        $oReq=DbLink::getInstance($this->_sDbInstance)->prepare('DELETE FROM '."user".' WHERE  id=:id ');
        $oReq->execute(array("id"=>$this->getId()));
        $this->vide();
    }

    /**
     * Consulte la base de données pour savoir si l'objet existe, en le recherchant par son id
     * @static
     * @param int $nId Id de l'objet à chercher
     * @param $sDbInstance (Opt) Nom de l'instance de la bdd
     * @return bool Vrai si l'objet existe, Faux sinon
     */
    public static function exists($nId=0, $sDbInstance=null)
    {
        $oReq=DbLink::getInstance($sDbInstance)->prepare('SELECT id FROM '."user".' WHERE  id=:id ');
        $oReq->execute(array("id"=>$nId));
        $aRes=$oReq->getRow(0);
        return (count($aRes)!=0);
    }

    /**
     * Sauvegarde l'objet en base
     */
    public function save()
    {
        $aData=array();
        if(isset($this->aDataSet["login"]))
        {
            $aData["login"]=$this->getLogin();
        }

        if(isset($this->aDataSet["pass"]))
        {
            $aData["pass"]=$this->getPass();
        }

        if(isset($this->aDataSet["type"]))
        {
            $aData["type"]=$this->getType();
        }

        if(isset($this->aDataSet["enable"]))
        {
            $aData["enable"]=$this->getEnable();
        }

        if($this->getId()>0)
        {
            DbLink::getInstance($this->_sDbInstance)->update("user",$aData,' id="'.$this->getId().'" ');
        }
        else
        {
            $this->setId(DbLink::getInstance($this->_sDbInstance)->insert("user",$aData));
        }
        $this->aDataSet=array();
    }

    /**
     * Deshydrate complement l'objet, et vide la liste des champs à sauvegarder
     */
    private function vide()
    {
        $this->callHydrateFromBDDOnGet=0;
        $this->aDataSet=array();
        $this->setLogin(NULL);
        $this->setPass(NULL);
        $this->setType(NULL);
        $this->setEnable(0);
    }

    /**
     * Renvoie l'objet sous forme de chaine de caractère
     */
    public function __toString()
    {
        $aObjet = [
            "id" => $this->getId(),
            "login" => $this->getLogin(),
            "pass" => $this->getPass(),
            "type" => $this->getType(),
            "enable" => $this->getEnable()
        ];

        return json_encode($aObjet);
    }



    /*
    ********************************************************************************************
    *                             DEBUT FONCTIONS PERSONNALISES                  	           *
    ********************************************************************************************
    */
    public static function encodePassword($sString){
        //encode PASS
        $oReq=DbLink::getInstance()->prepare(" SELECT PASSWORD(:pass) as pass ");
        $aData=array("pass"=>$sString);
        $oReq->execute($aData);
        $sPassword=$oReq->getCase("pass",0);
        return  $sPassword;
    }

    /*
    ********************************************************************************************
    *                             FIN FONCTIONS PERSONNALISES                     	           *
    ********************************************************************************************
    */




    /**
     * Set le champ id
     * @param number $nId nouvelle valeur pour le champ id
     */
    public function setId($nId)
    {
        if( is_null($nId) ) $nId='';
        if( is_numeric($nId)  || $nId=='' )
        {
            $this->nId = $nId;
            $this->aDataSet["id"]=1;
        }
    }



    /**
     * Get le champ id
     * @return number valeur du champ id
     */
    public function getId()
    {
        if( !is_null($this->nId) )
        {
            if( $this->nId==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nId;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nId;
        }
    }



    /**
     * Set le champ login
     * @param string $sLogin nouvelle valeur pour le champ login
     */
    public function setLogin($sLogin)
    {
        if( is_null($sLogin) ) $sLogin='';
        $this->sLogin = $sLogin;
        $this->aDataSet["login"]=1;
    }



    /**
     * Get le champ login
     * @return string valeur du champ login
     */
    public function getLogin()
    {
        if( !is_null($this->sLogin) )
        {
            if( $this->sLogin==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sLogin;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('login'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sLogin;
        }
    }



    /**
     * Set le champ pass
     * @param string $sPass nouvelle valeur pour le champ pass
     */
    public function setPass($sPass)
    {
        if( is_null($sPass) ) $sPass='';
        $this->sPass = $sPass;
        $this->aDataSet["pass"]=1;
    }



    /**
     * Get le champ pass
     * @return string valeur du champ pass
     */
    public function getPass()
    {
        if( !is_null($this->sPass) )
        {
            if( $this->sPass==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPass;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('pass'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPass;
        }
    }



    /**
     * Set le champ type
     * @param string $sType nouvelle valeur pour le champ type
     */
    public function setType($sType)
    {
        if( is_null($sType) ) $sType='';
        $this->sType = $sType;
        $this->aDataSet["type"]=1;
    }



    /**
     * Get le champ type
     * @return string valeur du champ type
     */
    public function getType()
    {
        if( !is_null($this->sType) )
        {
            if( $this->sType==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sType;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('type'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sType;
        }
    }



    /**
     * Set le champ enable
     * @param bool $bEnable nouvelle valeur pour le champ enable
     */
    public function setEnable($bEnable)
    {
        if( is_null($bEnable) ) $bEnable='';
        if( is_bool($bEnable)  ||  $bEnable==1 || $bEnable==0 || $bEnable=='' )
        {
            $this->bEnable = $bEnable;
            $this->aDataSet["enable"]=1;
        }
    }



    /**
     * Get le champ enable
     * @return bool valeur du champ enable
     */
    public function getEnable()
    {
        if( !is_null($this->bEnable) )
        {
            if( $this->bEnable==='' )
            {
                return NULL;
            }
            else
            {
                return $this->bEnable;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('enable'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->bEnable;
        }
    }

}