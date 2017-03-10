<?php	/**
 * Class Candidature
 */
class Candidature	{

    private $aDataNonHydrate = array();
    private $aDataSet = array();
    private $callHydrateFromBDDOnGet = 0;

    private $_sDbInstance = null;

    private $nId;
    private $sDate_created;
    private $sDate_amended;
    private $sDate_deleted;
    private $sState;
    private $sCivility;
    private $sFirstname;
    private $sName;
    private $sAd1;
    private $sAd2;
    private $sAd3;
    private $sCity;
    private $sZipcode;
    private $sCountry;
    private $sEmail;
    private $sTel;
    private $sPresentation;
    private $sUrl_video;
    private $sPath_pic;
    private $sPath_certificate;
    private $sPath_idcard;
    private $sPath_idcard_verso;
    private $sPath_criminal_record;
    private $bIs_certificate;
    private $bIs_idcard;
    private $bIs_criminal_record;
    private $sComment;
    private $sKey_edit;


    /**
     * Constructeur
     * @param array $aParam tableau de parametres ( clé "id" pour instancier un candidature avec un id précis )
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
                    //echo "<br />Candidature->$sMethode() n'existe pas!";
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
            $aData=DbLink::getInstance($this->_sDbInstance)->selectForHydrate($this->getId(),"candidature",$aFields);

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
        $oReq=DbLink::getInstance($this->_sDbInstance)->prepare('DELETE FROM '."candidature".' WHERE  id=:id ');
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
        $oReq=DbLink::getInstance($sDbInstance)->prepare('SELECT id FROM '."candidature".' WHERE  id=:id ');
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
        if(isset($this->aDataSet["date_created"]))
        {
            $aData["date_created"]=$this->getDate_created();
        }

        if(isset($this->aDataSet["date_amended"]))
        {
            $aData["date_amended"]=$this->getDate_amended();
        }

        if(isset($this->aDataSet["date_deleted"]))
        {
            $aData["date_deleted"]=$this->getDate_deleted();
        }

        if(isset($this->aDataSet["state"]))
        {
            $aData["state"]=$this->getState();
        }

        if(isset($this->aDataSet["civility"]))
        {
            $aData["civility"]=$this->getCivility();
        }

        if(isset($this->aDataSet["firstname"]))
        {
            $aData["firstname"]=$this->getFirstname();
        }

        if(isset($this->aDataSet["name"]))
        {
            $aData["name"]=$this->getName();
        }

        if(isset($this->aDataSet["ad1"]))
        {
            $aData["ad1"]=$this->getAd1();
        }

        if(isset($this->aDataSet["ad2"]))
        {
            $aData["ad2"]=$this->getAd2();
        }

        if(isset($this->aDataSet["ad3"]))
        {
            $aData["ad3"]=$this->getAd3();
        }

        if(isset($this->aDataSet["city"]))
        {
            $aData["city"]=$this->getCity();
        }

        if(isset($this->aDataSet["zipcode"]))
        {
            $aData["zipcode"]=$this->getZipcode();
        }

        if(isset($this->aDataSet["country"]))
        {
            $aData["country"]=$this->getCountry();
        }

        if(isset($this->aDataSet["email"]))
        {
            $aData["email"]=$this->getEmail();
        }

        if(isset($this->aDataSet["tel"]))
        {
            $aData["tel"]=$this->getTel();
        }

        if(isset($this->aDataSet["presentation"]))
        {
            $aData["presentation"]=$this->getPresentation();
        }

        if(isset($this->aDataSet["url_video"]))
        {
            $aData["url_video"]=$this->getUrl_video();
        }

        if(isset($this->aDataSet["path_pic"]))
        {
            $aData["path_pic"]=$this->getPath_pic();
        }

        if(isset($this->aDataSet["path_certificate"]))
        {
            $aData["path_certificate"]=$this->getPath_certificate();
        }

        if(isset($this->aDataSet["path_idcard"]))
        {
            $aData["path_idcard"]=$this->getPath_idcard();
        }

        if(isset($this->aDataSet["path_idcard_verso"]))
        {
            $aData["path_idcard_verso"]=$this->getPath_idcard_verso();
        }

        if(isset($this->aDataSet["path_criminal_record"]))
        {
            $aData["path_criminal_record"]=$this->getPath_criminal_record();
        }

        if(isset($this->aDataSet["is_certificate"]))
        {
            $aData["is_certificate"]=$this->getIs_certificate();
        }

        if(isset($this->aDataSet["is_idcard"]))
        {
            $aData["is_idcard"]=$this->getIs_idcard();
        }

        if(isset($this->aDataSet["is_criminal_record"]))
        {
            $aData["is_criminal_record"]=$this->getIs_criminal_record();
        }

        if(isset($this->aDataSet["comment"]))
        {
            $aData["comment"]=$this->getComment();
        }

        if(isset($this->aDataSet["key_edit"]))
        {
            $aData["key_edit"]=$this->getKey_edit();
        }

        if($this->getId()>0)
        {
            DbLink::getInstance($this->_sDbInstance)->update("candidature",$aData,' id="'.$this->getId().'" ');
        }
        else
        {
            $this->setId(DbLink::getInstance($this->_sDbInstance)->insert("candidature",$aData));
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
        $this->setDate_created(NULL);
        $this->setDate_amended(NULL);
        $this->setDate_deleted(NULL);
        $this->setState(NULL);
        $this->setCivility(NULL);
        $this->setFirstname(NULL);
        $this->setName(NULL);
        $this->setAd1(NULL);
        $this->setAd2(NULL);
        $this->setAd3(NULL);
        $this->setCity(NULL);
        $this->setZipcode(NULL);
        $this->setCountry(NULL);
        $this->setEmail(NULL);
        $this->setTel(NULL);
        $this->setPresentation(NULL);
        $this->setUrl_video(NULL);
        $this->setPath_pic(NULL);
        $this->setPath_certificate(NULL);
        $this->setPath_idcard(NULL);
        $this->setPath_idcard_verso(NULL);
        $this->setPath_criminal_record(NULL);
        $this->setIs_certificate(0);
        $this->setIs_idcard(0);
        $this->setIs_criminal_record(0);
        $this->setComment(NULL);
        $this->setKey_edit(NULL);
    }

    /**
     * Renvoie l'objet sous forme de chaine de caractère
     */
    public function __toString()
    {
        $aObjet = [
            "id" => $this->getId(),
            "date_created" => $this->getDate_created(),
            "date_amended" => $this->getDate_amended(),
            "date_deleted" => $this->getDate_deleted(),
            "state" => $this->getState(),
            "civility" => $this->getCivility(),
            "firstname" => $this->getFirstname(),
            "name" => $this->getName(),
            "ad1" => $this->getAd1(),
            "ad2" => $this->getAd2(),
            "ad3" => $this->getAd3(),
            "city" => $this->getCity(),
            "zipcode" => $this->getZipcode(),
            "country" => $this->getCountry(),
            "email" => $this->getEmail(),
            "tel" => $this->getTel(),
            "presentation" => $this->getPresentation(),
            "url_video" => $this->getUrl_video(),
            "path_pic" => $this->getPath_pic(),
            "path_certificate" => $this->getPath_certificate(),
            "path_idcard" => $this->getPath_idcard(),
            "path_idcard_verso" => $this->getPath_idcard_verso(),
            "path_criminal_record" => $this->getPath_criminal_record(),
            "is_certificate" => $this->getIs_certificate(),
            "is_idcard" => $this->getIs_idcard(),
            "is_criminal_record" => $this->getIs_criminal_record(),
            "comment" => $this->getComment(),
            "key_edit" => $this->getKey_edit()
        ];

        return json_encode($aObjet);
    }



    /*
    ********************************************************************************************
    *                             DEBUT FONCTIONS PERSONNALISES                  	           *
    ********************************************************************************************
    */
    public function getPath_pic_fit()
    {
        return str_replace(basename($this->getPath_pic()),"photo-fit.jpg",$this->getPath_pic());
    }

    public function getPresentation_nl2br()
    {
        return nl2br($this->getPresentation());
    }

    public function getComment_nl2br()
    {
        return nl2br($this->getComment());
    }

    public function saveWithPDOSecure(){

        $aData=array();

        if(isset($this->aDataSet["date_created"]))
        {
            $aData["date_created"]=$this->getDate_created();
        }

        if(isset($this->aDataSet["date_amended"]))
        {
            $aData["date_amended"]=$this->getDate_amended();
        }

        if(isset($this->aDataSet["date_deleted"]))
        {
            $aData["date_deleted"]=$this->getDate_deleted();
        }

        if(isset($this->aDataSet["state"]))
        {
            $aData["state"]=$this->getState();
        }

        if(isset($this->aDataSet["civility"]))
        {
            $aData["civility"]=$this->getCivility();
        }

        if(isset($this->aDataSet["firstname"]))
        {
            $aData["firstname"]=$this->getFirstname();
        }

        if(isset($this->aDataSet["name"]))
        {
            $aData["name"]=$this->getName();
        }

        if(isset($this->aDataSet["ad1"]))
        {
            $aData["ad1"]=$this->getAd1();
        }

        if(isset($this->aDataSet["ad2"]))
        {
            $aData["ad2"]=$this->getAd2();
        }

        if(isset($this->aDataSet["ad3"]))
        {
            $aData["ad3"]=$this->getAd3();
        }

        if(isset($this->aDataSet["city"]))
        {
            $aData["city"]=$this->getCity();
        }

        if(isset($this->aDataSet["zipcode"]))
        {
            $aData["zipcode"]=$this->getZipcode();
        }

        if(isset($this->aDataSet["country"]))
        {
            $aData["country"]=$this->getCountry();
        }

        if(isset($this->aDataSet["email"]))
        {
            $aData["email"]=$this->getEmail();
        }

        if(isset($this->aDataSet["tel"]))
        {
            $aData["tel"]=$this->getTel();
        }

        if(isset($this->aDataSet["presentation"]))
        {
            $aData["presentation"]=$this->getPresentation();
        }

        if(isset($this->aDataSet["url_video"]))
        {
            $aData["url_video"]=$this->getUrl_video();
        }

        if(isset($this->aDataSet["path_pic"]))
        {
            $aData["path_pic"]=$this->getPath_pic();
        }

        if(isset($this->aDataSet["path_certificate"]))
        {
            $aData["path_certificate"]=$this->getPath_certificate();
        }

        if(isset($this->aDataSet["path_idcard"]))
        {
            $aData["path_idcard"]=$this->getPath_idcard();
        }

        if(isset($this->aDataSet["path_idcard_verso"]))
        {
            $aData["path_idcard"]=$this->getPath_idcard();
        }

        if(isset($this->aDataSet["path_criminal_record"]))
        {
            $aData["path_criminal_record"]=$this->getPath_criminal_record();
        }

        if(isset($this->aDataSet["is_certificate"]))
        {
            $aData["is_certificate"]=$this->getIs_certificate();
        }

        if(isset($this->aDataSet["is_idcard"]))
        {
            $aData["is_idcard"]=$this->getIs_idcard();
        }

        if(isset($this->aDataSet["is_criminal_record"]))
        {
            $aData["is_criminal_record"]=$this->getIs_criminal_record();
        }

        if(isset($this->aDataSet["comment"]))
        {
            $aData["comment"]=$this->getComment();
        }

        if(isset($this->aDataSet["key_edit"]))
        {
            $aData["key_edit"]=$this->getKey_edit();
        }

        if($this->getId()>0)
        {
            $sSql="";
            $aDataBind=array();
            foreach($aData as $key=>$value){
                if($sSql!=""){ $sSql.=",";}

                $sSql.=" $key = :$key ";
                $aDataBind[":".$key]=(string)$value;
            }
            $St=DbLink::getInstance($this->_sDbInstance)->prepare("UPDATE candidature SET ".$sSql." WHERE id= :id ");
            $aDataBind[":id"]=$this->getId();
            $St->execute($aDataBind);
        }
        else
        {
            $sSqlCol="";
            $sSqlVal="";
            $aDataBind=array();
            foreach($aData as $key=>$value){
                if($sSqlCol!=""){ $sSqlCol.=",";}
                if($sSqlVal!=""){ $sSqlVal.=",";}
                $sSqlCol.="`".$key."`";
                $sSqlVal.=" :".$key." ";
                $aDataBind[":".$key]=(string)$value;
            }
            $St=DbLink::getInstance($this->_sDbInstance)->prepare("INSERT INTO candidature (".$sSqlCol.") VALUES (".$sSqlVal.")");
            $St->execute($aDataBind);
            $this->setId(DbLink::getInstance($this->_sDbInstance)->lastInsertId());

        }
        $this->aDataSet=array();
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
     * Set le champ date_created
     * @param string $sDate_created nouvelle valeur pour le champ date_created
     */
    public function setDate_created($sDate_created)
    {
        if( is_null($sDate_created) ) $sDate_created='';
        $this->sDate_created = $sDate_created;
        $this->aDataSet["date_created"]=1;
    }



    /**
     * Get le champ date_created
     * @return string valeur du champ date_created
     */
    public function getDate_created()
    {
        if( !is_null($this->sDate_created) )
        {
            if( $this->sDate_created==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_created;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_created'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_created;
        }
    }



    /**
     * Set le champ date_amended
     * @param string $sDate_amended nouvelle valeur pour le champ date_amended
     */
    public function setDate_amended($sDate_amended)
    {
        if( is_null($sDate_amended) ) $sDate_amended='';
        $this->sDate_amended = $sDate_amended;
        $this->aDataSet["date_amended"]=1;
    }



    /**
     * Get le champ date_amended
     * @return string valeur du champ date_amended
     */
    public function getDate_amended()
    {
        if( !is_null($this->sDate_amended) )
        {
            if( $this->sDate_amended==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_amended;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_amended'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_amended;
        }
    }



    /**
     * Set le champ date_deleted
     * @param string $sDate_deleted nouvelle valeur pour le champ date_deleted
     */
    public function setDate_deleted($sDate_deleted)
    {
        if( is_null($sDate_deleted) ) $sDate_deleted='';
        $this->sDate_deleted = $sDate_deleted;
        $this->aDataSet["date_deleted"]=1;
    }



    /**
     * Get le champ date_deleted
     * @return string valeur du champ date_deleted
     */
    public function getDate_deleted()
    {
        if( !is_null($this->sDate_deleted) )
        {
            if( $this->sDate_deleted==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_deleted;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_deleted'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_deleted;
        }
    }



    /**
     * Set le champ state
     * @param string $sState nouvelle valeur pour le champ state
     */
    public function setState($sState)
    {
        if( is_null($sState) ) $sState='';
        $this->sState = $sState;
        $this->aDataSet["state"]=1;
    }



    /**
     * Get le champ state
     * @return string valeur du champ state
     */
    public function getState()
    {
        if( !is_null($this->sState) )
        {
            if( $this->sState==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sState;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('state'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sState;
        }
    }



    /**
     * Set le champ civility
     * @param string $sCivility nouvelle valeur pour le champ civility
     */
    public function setCivility($sCivility)
    {
        if( is_null($sCivility) ) $sCivility='';
        $this->sCivility = $sCivility;
        $this->aDataSet["civility"]=1;
    }



    /**
     * Get le champ civility
     * @return string valeur du champ civility
     */
    public function getCivility()
    {
        if( !is_null($this->sCivility) )
        {
            if( $this->sCivility==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sCivility;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('civility'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sCivility;
        }
    }



    /**
     * Set le champ firstname
     * @param string $sFirstname nouvelle valeur pour le champ firstname
     */
    public function setFirstname($sFirstname)
    {
        if( is_null($sFirstname) ) $sFirstname='';
        $this->sFirstname = $sFirstname;
        $this->aDataSet["firstname"]=1;
    }



    /**
     * Get le champ firstname
     * @return string valeur du champ firstname
     */
    public function getFirstname()
    {
        if( !is_null($this->sFirstname) )
        {
            if( $this->sFirstname==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sFirstname;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('firstname'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sFirstname;
        }
    }



    /**
     * Set le champ name
     * @param string $sName nouvelle valeur pour le champ name
     */
    public function setName($sName)
    {
        if( is_null($sName) ) $sName='';
        $this->sName = $sName;
        $this->aDataSet["name"]=1;
    }



    /**
     * Get le champ name
     * @return string valeur du champ name
     */
    public function getName()
    {
        if( !is_null($this->sName) )
        {
            if( $this->sName==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sName;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('name'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sName;
        }
    }



    /**
     * Set le champ ad1
     * @param string $sAd1 nouvelle valeur pour le champ ad1
     */
    public function setAd1($sAd1)
    {
        if( is_null($sAd1) ) $sAd1='';
        $this->sAd1 = $sAd1;
        $this->aDataSet["ad1"]=1;
    }



    /**
     * Get le champ ad1
     * @return string valeur du champ ad1
     */
    public function getAd1()
    {
        if( !is_null($this->sAd1) )
        {
            if( $this->sAd1==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sAd1;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('ad1'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sAd1;
        }
    }



    /**
     * Set le champ ad2
     * @param string $sAd2 nouvelle valeur pour le champ ad2
     */
    public function setAd2($sAd2)
    {
        if( is_null($sAd2) ) $sAd2='';
        $this->sAd2 = $sAd2;
        $this->aDataSet["ad2"]=1;
    }



    /**
     * Get le champ ad2
     * @return string valeur du champ ad2
     */
    public function getAd2()
    {
        if( !is_null($this->sAd2) )
        {
            if( $this->sAd2==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sAd2;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('ad2'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sAd2;
        }
    }



    /**
     * Set le champ ad3
     * @param string $sAd3 nouvelle valeur pour le champ ad3
     */
    public function setAd3($sAd3)
    {
        if( is_null($sAd3) ) $sAd3='';
        $this->sAd3 = $sAd3;
        $this->aDataSet["ad3"]=1;
    }



    /**
     * Get le champ ad3
     * @return string valeur du champ ad3
     */
    public function getAd3()
    {
        if( !is_null($this->sAd3) )
        {
            if( $this->sAd3==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sAd3;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('ad3'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sAd3;
        }
    }



    /**
     * Set le champ city
     * @param string $sCity nouvelle valeur pour le champ city
     */
    public function setCity($sCity)
    {
        if( is_null($sCity) ) $sCity='';
        $this->sCity = $sCity;
        $this->aDataSet["city"]=1;
    }



    /**
     * Get le champ city
     * @return string valeur du champ city
     */
    public function getCity()
    {
        if( !is_null($this->sCity) )
        {
            if( $this->sCity==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sCity;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('city'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sCity;
        }
    }



    /**
     * Set le champ zipcode
     * @param string $sZipcode nouvelle valeur pour le champ zipcode
     */
    public function setZipcode($sZipcode)
    {
        if( is_null($sZipcode) ) $sZipcode='';
        $this->sZipcode = $sZipcode;
        $this->aDataSet["zipcode"]=1;
    }



    /**
     * Get le champ zipcode
     * @return string valeur du champ zipcode
     */
    public function getZipcode()
    {
        if( !is_null($this->sZipcode) )
        {
            if( $this->sZipcode==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sZipcode;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('zipcode'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sZipcode;
        }
    }



    /**
     * Set le champ country
     * @param string $sCountry nouvelle valeur pour le champ country
     */
    public function setCountry($sCountry)
    {
        if( is_null($sCountry) ) $sCountry='';
        $this->sCountry = $sCountry;
        $this->aDataSet["country"]=1;
    }



    /**
     * Get le champ country
     * @return string valeur du champ country
     */
    public function getCountry()
    {
        if( !is_null($this->sCountry) )
        {
            if( $this->sCountry==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sCountry;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('country'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sCountry;
        }
    }



    /**
     * Set le champ email
     * @param string $sEmail nouvelle valeur pour le champ email
     */
    public function setEmail($sEmail)
    {
        if( is_null($sEmail) ) $sEmail='';
        $this->sEmail = $sEmail;
        $this->aDataSet["email"]=1;
    }



    /**
     * Get le champ email
     * @return string valeur du champ email
     */
    public function getEmail()
    {
        if( !is_null($this->sEmail) )
        {
            if( $this->sEmail==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sEmail;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('email'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sEmail;
        }
    }



    /**
     * Set le champ tel
     * @param string $sTel nouvelle valeur pour le champ tel
     */
    public function setTel($sTel)
    {
        if( is_null($sTel) ) $sTel='';
        $this->sTel = $sTel;
        $this->aDataSet["tel"]=1;
    }



    /**
     * Get le champ tel
     * @return string valeur du champ tel
     */
    public function getTel()
    {
        if( !is_null($this->sTel) )
        {
            if( $this->sTel==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sTel;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('tel'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sTel;
        }
    }



    /**
     * Set le champ presentation
     * @param string $sPresentation nouvelle valeur pour le champ presentation
     */
    public function setPresentation($sPresentation)
    {
        if( is_null($sPresentation) ) $sPresentation='';
        $this->sPresentation = $sPresentation;
        $this->aDataSet["presentation"]=1;
    }



    /**
     * Get le champ presentation
     * @return string valeur du champ presentation
     */
    public function getPresentation()
    {
        if( !is_null($this->sPresentation) )
        {
            if( $this->sPresentation==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPresentation;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('presentation'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPresentation;
        }
    }



    /**
     * Set le champ url_video
     * @param string $sUrl_video nouvelle valeur pour le champ url_video
     */
    public function setUrl_video($sUrl_video)
    {
        if( is_null($sUrl_video) ) $sUrl_video='';
        $this->sUrl_video = $sUrl_video;
        $this->aDataSet["url_video"]=1;
    }



    /**
     * Get le champ url_video
     * @return string valeur du champ url_video
     */
    public function getUrl_video()
    {
        if( !is_null($this->sUrl_video) )
        {
            if( $this->sUrl_video==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sUrl_video;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('url_video'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sUrl_video;
        }
    }



    /**
     * Set le champ path_pic
     * @param string $sPath_pic nouvelle valeur pour le champ path_pic
     */
    public function setPath_pic($sPath_pic)
    {
        if( is_null($sPath_pic) ) $sPath_pic='';
        $this->sPath_pic = $sPath_pic;
        $this->aDataSet["path_pic"]=1;
    }



    /**
     * Get le champ path_pic
     * @return string valeur du champ path_pic
     */
    public function getPath_pic()
    {
        if( !is_null($this->sPath_pic) )
        {
            if( $this->sPath_pic==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPath_pic;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('path_pic'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPath_pic;
        }
    }



    /**
     * Set le champ path_certificate
     * @param string $sPath_certificate nouvelle valeur pour le champ path_certificate
     */
    public function setPath_certificate($sPath_certificate)
    {
        if( is_null($sPath_certificate) ) $sPath_certificate='';
        $this->sPath_certificate = $sPath_certificate;
        $this->aDataSet["path_certificate"]=1;
    }



    /**
     * Get le champ path_certificate
     * @return string valeur du champ path_certificate
     */
    public function getPath_certificate()
    {
        if( !is_null($this->sPath_certificate) )
        {
            if( $this->sPath_certificate==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPath_certificate;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('path_certificate'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPath_certificate;
        }
    }



    /**
     * Set le champ path_idcard
     * @param string $sPath_idcard nouvelle valeur pour le champ path_idcard
     */
    public function setPath_idcard($sPath_idcard)
    {
        if( is_null($sPath_idcard) ) $sPath_idcard='';
        $this->sPath_idcard = $sPath_idcard;
        $this->aDataSet["path_idcard"]=1;
    }



    /**
     * Get le champ path_idcard
     * @return string valeur du champ path_idcard
     */
    public function getPath_idcard()
    {
        if( !is_null($this->sPath_idcard) )
        {
            if( $this->sPath_idcard==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPath_idcard;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('path_idcard'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPath_idcard;
        }
    }

    public function Pathcard()
    {
        if( $this->sPath_idcard){
            $oPathcard = new Pathcard(array("id"=>$this->sPath_idcard));
            $oPathcard->HydrateFromBDD(array("*"));
            return $oPathcard;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ path_idcard_verso
     * @param string $sPath_idcard_verso nouvelle valeur pour le champ path_idcard_verso
     */
    public function setPath_idcard_verso($sPath_idcard_verso)
    {
        if( is_null($sPath_idcard_verso) ) $sPath_idcard_verso='';
        $this->sPath_idcard_verso = $sPath_idcard_verso;
        $this->aDataSet["path_idcard_verso"]=1;
    }



    /**
     * Get le champ path_idcard_verso
     * @return string valeur du champ path_idcard_verso
     */
    public function getPath_idcard_verso()
    {
        if( !is_null($this->sPath_idcard_verso) )
        {
            if( $this->sPath_idcard_verso==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPath_idcard_verso;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('path_idcard_verso'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPath_idcard_verso;
        }
    }

    public function Pathcard_verso()
    {
        if( $this->sPath_idcard_verso){
            $oPathcard_verso = new Pathcard_verso(array("id"=>$this->sPath_idcard_verso));
            $oPathcard_verso->HydrateFromBDD(array("*"));
            return $oPathcard_verso;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ path_criminal_record
     * @param string $sPath_criminal_record nouvelle valeur pour le champ path_criminal_record
     */
    public function setPath_criminal_record($sPath_criminal_record)
    {
        if( is_null($sPath_criminal_record) ) $sPath_criminal_record='';
        $this->sPath_criminal_record = $sPath_criminal_record;
        $this->aDataSet["path_criminal_record"]=1;
    }



    /**
     * Get le champ path_criminal_record
     * @return string valeur du champ path_criminal_record
     */
    public function getPath_criminal_record()
    {
        if( !is_null($this->sPath_criminal_record) )
        {
            if( $this->sPath_criminal_record==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPath_criminal_record;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('path_criminal_record'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPath_criminal_record;
        }
    }



    /**
     * Set le champ is_certificate
     * @param bool $bIs_certificate nouvelle valeur pour le champ is_certificate
     */
    public function setIs_certificate($bIs_certificate)
    {
        if( is_null($bIs_certificate) ) $bIs_certificate='';
        if( is_bool($bIs_certificate)  ||  $bIs_certificate==1 || $bIs_certificate==0 || $bIs_certificate=='' )
        {
            $this->bIs_certificate = $bIs_certificate;
            $this->aDataSet["is_certificate"]=1;
        }
    }



    /**
     * Get le champ is_certificate
     * @return bool valeur du champ is_certificate
     */
    public function getIs_certificate()
    {
        if( !is_null($this->bIs_certificate) )
        {
            if( $this->bIs_certificate==='' )
            {
                return NULL;
            }
            else
            {
                return $this->bIs_certificate;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('is_certificate'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->bIs_certificate;
        }
    }



    /**
     * Set le champ is_idcard
     * @param bool $bIs_idcard nouvelle valeur pour le champ is_idcard
     */
    public function setIs_idcard($bIs_idcard)
    {
        if( is_null($bIs_idcard) ) $bIs_idcard='';
        if( is_bool($bIs_idcard)  ||  $bIs_idcard==1 || $bIs_idcard==0 || $bIs_idcard=='' )
        {
            $this->bIs_idcard = $bIs_idcard;
            $this->aDataSet["is_idcard"]=1;
        }
    }



    /**
     * Get le champ is_idcard
     * @return bool valeur du champ is_idcard
     */
    public function getIs_idcard()
    {
        if( !is_null($this->bIs_idcard) )
        {
            if( $this->bIs_idcard==='' )
            {
                return NULL;
            }
            else
            {
                return $this->bIs_idcard;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('is_idcard'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->bIs_idcard;
        }
    }

    public function Iscard()
    {
        if( $this->bIs_idcard){
            $oIscard = new Iscard(array("id"=>$this->bIs_idcard));
            $oIscard->HydrateFromBDD(array("*"));
            return $oIscard;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ is_criminal_record
     * @param bool $bIs_criminal_record nouvelle valeur pour le champ is_criminal_record
     */
    public function setIs_criminal_record($bIs_criminal_record)
    {
        if( is_null($bIs_criminal_record) ) $bIs_criminal_record='';
        if( is_bool($bIs_criminal_record)  ||  $bIs_criminal_record==1 || $bIs_criminal_record==0 || $bIs_criminal_record=='' )
        {
            $this->bIs_criminal_record = $bIs_criminal_record;
            $this->aDataSet["is_criminal_record"]=1;
        }
    }



    /**
     * Get le champ is_criminal_record
     * @return bool valeur du champ is_criminal_record
     */
    public function getIs_criminal_record()
    {
        if( !is_null($this->bIs_criminal_record) )
        {
            if( $this->bIs_criminal_record==='' )
            {
                return NULL;
            }
            else
            {
                return $this->bIs_criminal_record;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('is_criminal_record'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->bIs_criminal_record;
        }
    }



    /**
     * Set le champ comment
     * @param string $sComment nouvelle valeur pour le champ comment
     */
    public function setComment($sComment)
    {
        if( is_null($sComment) ) $sComment='';
        $this->sComment = $sComment;
        $this->aDataSet["comment"]=1;
    }



    /**
     * Get le champ comment
     * @return string valeur du champ comment
     */
    public function getComment()
    {
        if( !is_null($this->sComment) )
        {
            if( $this->sComment==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sComment;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('comment'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sComment;
        }
    }



    /**
     * Set le champ key_edit
     * @param string $sKey_edit nouvelle valeur pour le champ key_edit
     */
    public function setKey_edit($sKey_edit)
    {
        if( is_null($sKey_edit) ) $sKey_edit='';
        $this->sKey_edit = $sKey_edit;
        $this->aDataSet["key_edit"]=1;
    }



    /**
     * Get le champ key_edit
     * @return string valeur du champ key_edit
     */
    public function getKey_edit()
    {
        if( !is_null($this->sKey_edit) )
        {
            if( $this->sKey_edit==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sKey_edit;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('key_edit'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>ConfigService::get("maxCallHydrateFromBDDonGet"))
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sKey_edit;
        }
    }

}