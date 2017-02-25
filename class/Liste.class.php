<?php
class Liste {
    protected $sTablePrincipale		= '';
    protected $sAliasPrincipal		= '';

    protected $aListSelection		= array();
    protected $aFieldsSelected		= array();
    protected $aListeTables			= array();
    protected $aCriteres			= array();
    protected $aCritereSpecifiques	= array();
    protected $aSecureFields		= array();
    protected $aSearchFields		= array();

    protected $sGroupBy				= '';
    protected $sTri					= '';
    protected $sSens				= '';
    protected $sLastListe			= '';
    protected $sHaving 				= '';

    protected $aHaving              = [];

    protected $nNbParPage			= 0;
    protected $nCurrentPage			= 1;
    protected $nCountItem			= -1;

    public function __construct($aParam=array()){
        $this->aCriteres = array();
        $this->aListAll = array();
        $this->aListSelection = array();
    }

    //Accessseurs $aCriteres
    //ajout de critere
    public function addCritere($aParam=array()){//array("table"=>"","tb_alias"=>"","table_principale"=>"","jointure"=>"","field"=>"","value"=>"","compare"=>"")
        if(!isset($aParam[0])){$aParam=array($aParam);}
        if(isset($aParam['table_principale']) && strpos( $aParam['table_principale'], '`')===false ){$aParam['table_principale'] = self::getTable($aParam['table_principale']);}
        foreach($aParam as $aOneCritere){
            if(!in_array($aOneCritere, $this->aCriteres)){
                array_push($this->aCriteres, $aOneCritere);
                $this->aListSelection = array();
            }
        }
    }

    protected function addCritereItem(){
        //fonction à étendre dans chaque class spécifique
    }

    protected function addCritereSearch(){
        //$aCritere=array("field"=>"nom","value"=>"%".secureInjection($_POST["search"])."%","compare"=>"LIKE", "table"=>getTable("client"));
    }

    //retourne ts criteres sous forme SQL
    protected function translateCritere()
    {
        $sReturnStr = '';
        $aBetweenValues = array();

        foreach($this->aCriteres as $aOneCritere)
        {
            $aOneCritere=$this->protectParamTable($aOneCritere);

            if( !isset($aOneCritere['compare']) )
            {
                $aOneCritere['compare']='';
            }

            $sFieldWithAlias = "`". $aOneCritere['tb_alias'] ."`.`". $aOneCritere['field'] ."`";

            if( strstr($sFieldWithAlias, '(') )
            {
                $sFieldWithAlias=$aOneCritere['field'];
            }

            switch( strtoupper($aOneCritere['compare']) )
            {

                case '=':
                case '<>':	case '!=':
                case '<':	case '<=':
                case '>':	case '>=':
                case 'LIKE':
                    $sReturnStr.= "AND ".$sFieldWithAlias." ".$aOneCritere['compare']." '".Vars::secureInjection($aOneCritere['value'])."' ";
                    break;
                case 'NOTLIKE':
                case "NOT LIKE":
                    $sReturnStr.= "AND ".$sFieldWithAlias." NOT LIKE '".$aOneCritere['value']." ";
                    break;
                case 'IN':
                    $sReturnStr.= "AND ".$sFieldWithAlias." ".$aOneCritere['compare']." (".$aOneCritere['value'].") ";
                    break;
                case 'NOTIN':
                case "NOT IN":
                    $sReturnStr.= "AND ".$sFieldWithAlias." NOT IN (".$aOneCritere['value'].") ";
                    break;
                case 'BETWEEN':
                    $aBetweenValues = explode(',', $aOneCritere['value']);
                    $sReturnStr.= "AND ".$sFieldWithAlias." ".$aOneCritere['compare']." '".Vars::secureInjection($aBetweenValues[0])."' AND '".Vars::secureInjection($aBetweenValues[1])."' ";
                    break;
                case 'SQL':
                    $sReturnStr.= "AND (".$aOneCritere['value'].") ";
                    break;
                case '':
                    break;
                case 'IS NULL': case 'IS NOT NULL':
                $sReturnStr.= "AND ".$sFieldWithAlias." ".$aOneCritere['compare']." ";
                break;
                default:
                    echo "<br />Liste->translateCritere() compare : &laquo;".$aOneCritere['compare']."&raquo; non gere ";
                    break;
            }
            $this->setListeTable($aOneCritere);

        }
        return $sReturnStr;
    }

    public function getPage( $nPage=1 )
    {
        $this->setCurrentPage( $nPage );
        $this->addCritereItem();

        $sSqlQuery = '';
        $sSqlQuery.=$this->translateCritere();

        //Verification des fields necessaires
        if(!$sSqlQuery){
            $this->aListSelection = array();
            return $this->aListSelection;
        }else{
            foreach($this->aSecureFields as $aOneSecure){
                $bNotFound = true;
                foreach($aOneSecure as $sSecure){
                    if(!strstr($sSecure, $sSqlQuery) && $bNotFound){
                        $bNotFound = false;
                    }
                }
                if($bNotFound){
                    $this->aListSelection = array();
                    return $this->aListSelection;
                }
            }
        }

        $aDataTables = $this->getListeTable();
        $aDataFields = $this->getField();
        $sTri 		 = $this->getTri();
        $sSens		 = $this->getSens();
        $sGoupBy	 = $this->getGroupBy();

        $this->generateHaving();

        //Montage de la requete avec $aDataTables['table']/['jointure'] + $sSqlQuery + getTri/getSens
        $sSqlSelect = "SELECT ".$aDataFields['fields']." FROM ".$aDataTables['table']." WHERE 1=1 ".$aDataTables['jointure'].$sSqlQuery;

        if($sGoupBy){$sSqlSelect.= $sGoupBy;}
        if($this->sHaving){ $sSqlSelect .= $this->sHaving; }
        if($sTri){$sSqlSelect.= "ORDER BY ". $sTri ." ". $sSens ." ";}
        if($this->getNbParPage()>0){$sSqlSelect.= "LIMIT ".(($this->getCurrentPage()-1)*$this->getNbParPage()).", ".$this->getNbParPage()." ";}

        $this->setLastListe($sSqlSelect);
        //echo $sSqlSelect;
        $oReq=DbLink::getInstance()->prepare($sSqlSelect);
        $oReq->execute(array());
        $this->aListSelection = $oReq->getRes();
        return  $this->aListSelection;
    }

    public function countItem($debug=false){

        $this->addCritereItem();

        $sSqlQuery = '';
        $sSqlQuery.=$this->translateCritere();

        //Verification des fields necessaires
        if(!$sSqlQuery){
            $this->aListSelection = array();
            return $this->aListSelection;
        }else{
            foreach($this->aSecureFields as $aOneSecure){
                $bNotFound = true;
                foreach($aOneSecure as $sSecure){
                    if(!strstr($sSecure, $sSqlQuery) && $bNotFound){
                        $bNotFound = false;
                    }
                }
                if($bNotFound){
                    $this->aListSelection = array();
                    return $this->aListSelection;
                }
            }
        }

        $aDataTables = $this->getListeTable();
        $aDataFields = $this->getField();
        $sGoupBy	 = $this->getGroupBy();
//        $sHaving = $this->getHaving();


        $this->generateHaving();

//		if( $debug ) print_r( $aDataFields );

        /*$aDataFieldsCount = array();
        foreach( $aDataFields as $aDataField )
        {
            if( array_key_exists("required4count", $aDataField) and $aDataField["required4count"] )
            {
                $aDataFieldsCount[] = $aDataField;
            }
        }*/

        //Montage de la requete avec $aDataTables['table']/['jointure'] + $sSqlQuery + getTri/getSens
        $sSqlSelect = "SELECT COUNT(DISTINCT ".$this->getAliasPrincipal().".id) AS nb FROM ".$aDataTables['table']." WHERE 1=1 ".$aDataTables['jointure'].$sSqlQuery;

        if($sGoupBy){
            if(strstr($sGoupBy, $this->getAliasPrincipal().".id")===false){
                $sSqlSelect.= $sGoupBy;
            }
        }

//		if($sHaving){ $sSqlSelect .= $sHaving; }

        /*if( $debug===true )
        {
            echo $sSqlSelect;
        }*/

        $oReq=DbLink::getInstance()->prepare($sSqlSelect);
        //echo "<hr />".$sSqlSelect."<hr />";
        $oReq->execute(array());
        $this->setCountItem($oReq->getCase("nb", 0));
        return  $this->getCountItem();
    }

    public function limitSearch($aParam){

        $sSearchParam = '';
        if(isset($aParam['search'])){$sSearchParam = $aParam['search'];}

        $aSearchOnly = array();
        if(isset($aParam['only'])){$aSearchOnly = $aParam['only'];}

        if($sSearchParam){
            //array("field","table","jointure", "type"=>phone/date/str/int)
            $aFieldSearch=$this->getSearchFields();
            $aFieldSearchClean = array();
            foreach($aFieldSearch as $aField){
                $aField=$this->protectParamTable($aField);
                $this->setListeTable($aField);
                array_push($aFieldSearchClean, $aField);
            }

            $sSqlCondition = "";
            $aSearch=explode(" ",$sSearchParam);
            foreach($aSearch as $sSearch){
                $sSubCond = "";
                foreach($aFieldSearchClean as $aField){
                    if(count($aSearchOnly)==0 || in_array($aField['field'], $aSearchOnly) || in_array($aField['tb_alias'].'.'.$aField['field'], $aSearchOnly)){
                        $sField = $aField['field'];
                        if(strpos($aField['field'], '.')===false){$sField = $aField['tb_alias'].'.'.$sField;}
                        if($sSubCond){$sSubCond.= " OR ";}
                        if(!isset($aField['type'])){$aField['type']='';}
                        switch($aField['type']){
                            //phone
                            case 'int':
                                $sSubCond.= " $sField = '".Vars::secureInjection($sSearch)."' ";
                                break;
                            case 'date':
                                $sSubCond.= " $sField LIKE '%".Vars::secureInjection(substr(display::dateToSql($sSearch),0,10))."%' ";
                                break;
                            case '':
                            case 'str':
                            default:
                                $sSubCond.= " $sField LIKE '%".Vars::secureInjection($sSearch)."%' ";
                                break;
                        }
                    }
                }
                if($sSubCond){$sSqlCondition.= "AND (".$sSubCond.")";}
            }

            if($sSqlCondition){
                //addcritere ...
                $sSqlCondition = "1=1 ".$sSqlCondition;
                $this->addCritere(array("field"=>"","value"=>$sSqlCondition, "compare"=>"SQL"));
            }else{
                $this->addCritere(array("field"=>"","value"=>"1=0", "compare"=>"SQL"));
            }
        }
    }


    // Accessseurs $aListeTables========================================================================
    protected function setListeTable($aRefTable){//
        /*
        array("table"=>"","tb_alias"=>"","jointure"=>"")
        */
        $aRefTable=$this->protectParamTable($aRefTable);

        $sTable = str_replace(' ', '', $aRefTable['table']);
        $sAlias = str_replace(' ', '', $aRefTable['tb_alias']);
        $sJointure = '';
        if(isset($aRefTable['jointure'])){$sJointure = $aRefTable['jointure'];}else{$aRefTable['jointure']='';}

        if(!isset($this->aListeTables[$sTable.$sAlias])){
            $this->aListeTables[$sTable.$sAlias] = $aRefTable;
        }else{
            if($this->aListeTables[$sTable.$sAlias]['jointure']!=$sJointure){
                //$this->aListeTables[$sTable.$sAlias]['jointure'].=$sJointure." error_table_double_declare ";
            }
        }
    }

    protected function getListeTable(){
        $sTablePrincipal = $this->getTablePrincipale();
        $sListe = '';
        $sListeLeft = '';
        $sListePrincipale = '';
        $sJointures = '';

        $bFoundPrincipale = false;
        foreach($this->aListeTables as $sKey=>$aTableDeclare){
            //si sTablePrincipal non trouve : Error
            if($aTableDeclare['table']==$this->getTablePrincipale() && $aTableDeclare['tb_alias']==$this->getAliasPrincipal()){
                $bFoundPrincipale = true;
                $sListePrincipale = $aTableDeclare['table']." AS `".$aTableDeclare['tb_alias']."` ";
            }
        }
        if(!$bFoundPrincipale){
            $sListe.= 'error_table_declare_no_principale ';
        }

        foreach($this->aListeTables as $sKey=>$aTableDeclare){
            //les left join colle a sTablePrincipal
            if($aTableDeclare['table']!=$this->getTablePrincipale())
            {
                /*echo $aTableDeclare['table'] ."<br />";
                var_dump($aTableDeclare['table_principale']==$this->getTablePrincipale());
                var_dump(isset($aTableDeclare['table_principale']));
                var_dump( strpos($aTableDeclare['jointure'], 'LEFT')!==false);
                echo "<hr />";*/
                if(
                    strpos($aTableDeclare['jointure'], 'LEFT')!==false &&
                    isset($aTableDeclare['table_principale']) &&
                    $aTableDeclare['table_principale']==$this->getTablePrincipale()
                )
                {
                    if( substr($sListeLeft,-1)!=" " )
                    {
                        $sListeLeft .= " ";
                    }
                    $sListeLeft .= $aTableDeclare['jointure']." ";
                }
                else
                {
                    $sListe .= "INNER JOIN ".$aTableDeclare['table']." AS ".$aTableDeclare['tb_alias']." ON 1=1 ".$aTableDeclare['jointure']." ";
                }
            }
        }

        $aData = array();
        $aData['table'] = $sListePrincipale.$sListe.$sListeLeft;
        $aData['jointure'] = $sJointures;
        $aData['test'] = $this->aListeTables;
        return $aData;
    }

    public function setFields($aFields=array())
    {
        foreach( $aFields as $sFieldName )
        {
            $this->setField(array("field"=>$sFieldName));
        }
    }

    public function addCriteres($aCriteres=array())
    {
        foreach( $aCriteres as $aCritere )
        {
            $this->addCritere( $aCritere );
        }
    }

    private static function getTable($sTablename)
    {
        global $_TABLE;
        if(!isset($_TABLE[$sTablename])){$_TABLE[$sTablename]=$sTablename;}
        return " `".$_TABLE[$sTablename]."` ";
    }

    // Accessseurs $aFieldsSelected========================================================================
    public function setField( $aField=array() )
    {
        //("field"=>"", "fd_alias"=>"", "table"=>, "tb_alias"=>"", "jointure"=>"")
        $aField = $this->protectParamTable($aField);

//        if(isset($aField['table_principale']) && strpos( $aField['table_principale'], '`')===false ){$aField['table_principale'] = self::getTable($aField['table_principale']);}
        if( !isset( $aField['fd_alias'] ) and $aField['field']!="*" )
        {
            $aField['fd_alias']=$aField['field'];
        }

        $sField = str_replace(' ', '', $aField['field']);

        if( isset( $aField['fd_alias'] ) )
        {
            $sFdAlias = str_replace(' ', '', $aField['fd_alias']);
        }
        else
        {
            $sFdAlias="";
        }

        if( !isset( $this->aFieldsSelected[ $sField . $sFdAlias ] ) )
        {
            $this->aFieldsSelected[ $sField . $sFdAlias ] = $aField;
        }
        else
        {
            if( $this->aFieldsSelected[ $sField . $sFdAlias ]!=$aField )
            {
                $this->aFieldsSelected[ $sField . $sFdAlias ]['fd_alias'] .= " error_field_double_declare ";
            }
        }

        $this->setListeTable($aField);
    }

    protected function getField(){
        $sField = '';
        //si no field => table pricinpal id

        if(count($this->aFieldsSelected)==0)
        {
            $sField.= "`". $this->getAliasPrincipal()."`.`id` ";
        }
        else
        {
            if(!$this->isInListField('id')){
                //$this->setField(array("field"=>"id", "table"=>$this->getTablePrincipale(), "tb_alias"=>$this->getAliasPrincipal()));
            }
            $this->isIdsInListField();
            foreach($this->aFieldsSelected as $sKey=>$aField)
            {
                if($sField)
                {
                    $sField .= ", ";
                }

                if( isset($aField["sql"]) and !empty($aField["sql"]) )
                {
                    $sField .= $aField["sql"];
                }
                else
                {
                    if(strpos($aField['field'], '.')===false)
                    {
                        $sField .= "`". $aField['tb_alias'] .'`.';
                    }

                    $sField .= "". $aField['field'] ."";

                    if(isset($aField['fd_alias']))
                    {
                        if($aField['fd_alias'])
                        {
                            $sField .= " AS `". $aField['fd_alias'] ."`";
                        }
                    }
                }
            }
        }

        $aData = array();
        $aData['fields'] = $sField;
        return $aData;
    }

    protected function isInListField($sField){
        $bFound = false;
        foreach($this->aFieldsSelected as $sKey=>$aField){
            if($aField['field'] == $sField && $aField["table"]==$this->getTablePrincipale()){
                $bFound = true;
            }
        }
        return $bFound;
    }

    protected function isIdsInListField(){
        $aTables = array();
        foreach($this->aListeTables as $aOneTable){;
            $bFound = false;
            foreach($this->aFieldsSelected as $sKey=>$aField){
                /*if($aField['field'] == 'id' && $aField["table"]==$aOneTable['table']){
                    $bFound = true;
                }*/
                if( $aField["table"]==$aOneTable['table']){
                    $bFound = true;
                }

            }
            if(!$bFound){
                // -- $this->setField(array("field"=>"*", "fd_alias"=>"id_table_".str_replace(' ', '', str_replace('`', '', $aOneTable['table'])), "table"=>$aOneTable['table'], "tb_alias"=>$aOneTable['tb_alias'], "jointure"=>$aOneTable['jointure']));
                //$this->setField(array("field"=>"id", "table"=>$aOneTable['table'], "tb_alias"=>$aOneTable['tb_alias'], "jointure"=>$aOneTable['jointure']));
            }
        }
    }

    protected function protectParamTable($aParam){
        if(!isset($aParam['table']) && !isset($aParam['tb_alias'])){
            $aParam['table']=$this->getTablePrincipale();
            $aParam['tb_alias']=$this->getAliasPrincipal();
        }
        if(!isset($aParam['tb_alias']) || $aParam['tb_alias']==''){$aParam['tb_alias']=ucfirst(str_replace(' ', '', str_replace('`', '', $aParam['table'])));}
        if(!isset($aParam['jointure'])){$aParam['jointure']='';}

        if($aParam['jointure']){
            if(
                (strpos(str_replace(' ', '', $aParam['jointure']), "AND")>0 || strpos(str_replace(' ', '', $aParam['jointure']), "AND")===false) &&
                strpos(str_replace(' ', '', $aParam['jointure']), "LEFT")===false
            ){$aParam['jointure'] = "AND ".$aParam['jointure'];}
        }
        return $aParam;
    }

    // Accessseurs $sGroupBy========================================================================
    public function setGroupBy($sGroupBy){
        if(!strstr($this->sGroupBy,'.') && $this->sGroupBy!="" && strpos(str_replace(' ', '', $sGroupBy), "GROUP")===false){
            $this->sGroupBy=$this->getTablePrincipale().".".$this->sGroupBy;
        }
        if(is_string($sGroupBy) && $sGroupBy!=''){
            if(strpos(str_replace(' ', '', $sGroupBy), "GROUP")===false){$sGroupBy = "GROUP BY ".$sGroupBy;}
            $this->sGroupBy=$sGroupBy." " ;
            $this->aListSelection = array();
        }
    }

    public function setHaving($sHaving)
    {
        $this->aHaving = [$sHaving];
    }

    private function generateHaving()
    {
        if( count($this->aHaving)>0 )
        {
            $this->sHaving = " HAVING ";
            $this->sHaving .= implode(" AND ", $this->aHaving);
            $this->sHaving .= " ";
        }
    }

    public function addHaving($sHavingClause)
    {
        $this->aHaving[] = $sHavingClause;
    }

    public function getGroupBy(){
        return $this->sGroupBy;
    }

    public function getNbPageTotal(){
        $nNbItem=$this->getCountItem();

        if($nNbItem<0){
            $nNbItem=$this->countItem(); //lance la requete
        }
        $nNbPageTotal = 1;
        if($this->getNbParPage()){$nNbPageTotal = ceil($nNbItem/$this->getNbParPage());}
        return $nNbPageTotal;
    }

    public function getPageNext(){
        $nNbPage=$this->getNbPageTotal();
        $nNext=$this->getCurrentPage()+1;
        if($nNext>$nNbPage){
            $nNext=1;
        }
        return $nNext;
    }


    public function getPagePrev(){
        $nNbPage=$this->getNbPageTotal();
        $nPrev=$this->getCurrentPage()-1;
        if($nPrev<1){
            $nPrev=$nNbPage;
        }
        return $nPrev;
    }

    public function save($aParam){
        if( isset($aParam["new"]) && is_array($aParam["new"]) ){
            $aDataNew=$aParam["new"];
            $nOldNbParPage=$this->getNbParPage();
            $nOldCurrentPage=$this->getCurrentPage();
            $this->setNbParPage(0);
            $this->setCurrentPage(0);
            $aListes=$this->getPage();


            //on enleve la colonne id
            $aTabs=array();
            foreach($aListes as $aListe){
                unset($aListe["id"]);
                array_push($aTabs,$aListe);
            }

            //nettoi les anciens
            foreach($aTabs as $aTab){
                if(in_array($aTab,$aDataNew)){
                    //NOTHING
                }

                if(!in_array($aTab,$aDataNew)){
                    //DELETE
                    $sSqlDelete="DELETE FROM ".$this->getTablePrincipale()." WHERE 1=1 ";
                    foreach($aTab as $sKey=>$sValue){
                        $sSqlDelete.=" AND `".$sKey."`='".$sValue."' ";
                    }
                    if(strstr($sSqlDelete," AND `")){
                        $oReq=DbLink::getInstance()->prepare($sSqlDelete);
                        $oReq->execute(array());
                    }

                }
            }


            $aNewToInserts = array();

            foreach($aDataNew as $aData)
            {

                if(in_array($aData,$aTabs))
                {
                    //NOTHING
                }
                if(!in_array($aData,$aTabs))
                {
                    $aNewToInserts[] = $aData;
                }
            }

            foreach( $aNewToInserts as $index=>$data_to_insert )
            {
                //INSERT
                $bExecute = ($index==count($aNewToInserts)-1) ? true : false;
                $oReq=DbLink::getInstance()->insert($this->sTablePrincipale,$data_to_insert,$bExecute);
            }

            //ajoute les nouveaux
            $this->setNbParPage($nOldNbParPage);
            $this->setCurrentPage($nOldCurrentPage);
        }
    }


    // Accessseurs $nNbparpage========================================================================
    public function setNbParPage($nNbParPage){ if(is_numeric($nNbParPage)){$this->nNbParPage=$nNbParPage; $this->aListSelection = array();}}
    public function getNbParPage(){
        //if($this->nNbParPage==0){$this->setNbParPage(10);};
        return $this->nNbParPage;}
    // Accessseurs $nPage========================================================================
    public function setCurrentPage($nCurrentPage){ if(is_numeric($nCurrentPage)){$this->nCurrentPage=$nCurrentPage; $this->aListSelection = array();}}
    public function getCurrentPage(){ return $this->nCurrentPage;}
    // Accessseurs $sTri========================================================================
    public function setTri($sTri){ if(is_string($sTri)){$this->sTri=$sTri; $this->aListSelection = array();}}
    public function getTri(){ return $this->sTri;}
    // Accessseurs $sSens========================================================================
    public function setSens($sSens){ if(is_string($sSens)){$this->sSens=$sSens; $this->aListSelection = array();}}
    public function getSens(){ return $this->sSens;}
    // Accessseurs $sTablePrinciaple========================================================================
    public function setTablePrincipale($sTable){ if(is_string($sTable)){$this->sTablePrincipale=$sTable;}}
    public function getTablePrincipale(){ return $this->sTablePrincipale;}
    // Accessseurs $sAliasPrincipal========================================================================
    public function setAliasPrincipal($sTable){ if(is_string($sTable)){$this->sAliasPrincipal=$sTable;}}
    public function getAliasPrincipal(){ return $this->sAliasPrincipal;}
    // Accessseurs $sSecureField========================================================================
    public function setSecureFields($aTab){ if(is_array($aTab)){$this->aSecureFields=$aTab;}}
    public function getSecureFields(){ return $this->aSecureFields;}
    // Accessseurs $sSearchField========================================================================
    public function setSearchFields($aTab){ if(is_array($aTab)){$this->aSearchFields=$aTab;}}
    public function getSearchFields(){ return $this->aSearchFields;}
    // Accessseurs $sCritereSpecifiques========================================================================
    protected function setCritereSpecifiques($aTab){ if(is_array($aTab)){$this->aCritereSpecifiques=$aTab;}}
    protected function addCritereSpecifiques($aTab){ if(is_array($aTab)){array_push($this->aCritereSpecifiques,$aTab);}}
    protected function getCritereSpecifiques(){ return $this->aCritereSpecifiques;}
    // Accessseurs $nCountItem========================================================================
    protected function setCountItem($nCountItem){if(is_numeric($nCountItem)){$this->nCountItem=$nCountItem;}}
    protected function getCountItem(){return $this->nCountItem;}
    // Accessseurs $sLastListe========================================================================
    protected function setLastListe($sLastListe){if(is_string($sLastListe)){$this->sLastListe=$sLastListe;}}
    public function getLastListe(){return $this->sLastListe;}
    public function getLastQuery(){return $this->getLastListe();}







}

