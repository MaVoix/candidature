<?php

class User	{

    private $_sDbInstance = null;
    private $_type="visitor";
    private $_login="";
    private $nId;


    public function __construct( $aParam=array(), $sDbInstance=null )
    {

        $this->nId = ( isset($aParam['id']) ) ? $aParam['id'] : 0;
        $this->_sDbInstance = $sDbInstance;
    }

    public function getType(){
        return $this->_type;
    }

    public function setType($sType){
        $this->_type=$sType;
    }

    public function getLogin(){
        return $this->_login;
    }

    public function setLogin($sLogin){
        $this->_login=$sLogin;
    }



}