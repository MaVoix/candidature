<?php

class User	{

    private $_sDbInstance = null;
    private $nId;


    public function __construct( $aParam=array(), $sDbInstance=null )
    {

        $this->nId = ( isset($aParam['id']) ) ? $aParam['id'] : 0;
        $this->_sDbInstance = $sDbInstance;
    }

    public function getType(){
        return 'visitor';
    }


}