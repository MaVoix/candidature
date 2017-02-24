<?php


class Mysql extends PDO {
	
	private $sHost				=	"";
	private $sLogin				=	"";
	private $sPwd				=	"";
	private $sDb				=	"";
	private $sErreur			=   "";
	private $aResult			=	array();	
	private $nNbResult			=   0;
	private $bConnect   		= 	false;
	private $sTitreMail				=   "";
	private $sExpediteur		=   "";
	private $aDestinataires	=   array();
	private $bSendMail			=   false;
	private $nMaxDisplayError	=	20;
	private $nMaxSendMailError	=	5;
	private $nNbError			=   0;
	private $bShowError			= true;
	private $sLastRequete		=  "";
	private $aBigInsert			=array();
	 
	public function __toString()
	{
		return "Objet ". get_class($this);	
	}
	
	//constructeur (connexion)
    public function __construct($aParam){		
		
		$this->sHost			=	$aParam["host"];
		$this->sLogin			=	$aParam["login"];
		$this->sPwd				=	$aParam["pass"];
		$this->sDb				=	$aParam["base"];			
		$this->bShowErreur		=	ConfigService::get("bdd-showerreur");	
		$this->nMaxDisplayError =   ConfigService::get("bdd-max-show");
			
		SessionService::set("erreur-sql",0);
				
		try{			
        	parent::__construct('mysql:host='.$this->sHost.';dbname='.$this->sDb, $this->sLogin, $this->sPwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8') ); 
			parent::setAttribute(PDO::ATTR_STATEMENT_CLASS, array ('MysqlStatement', array($this)));
			parent::exec("SET CHARACTER SET utf8");			
		}catch(PDOException $e){				

		}
				
	}
	
	//affiche l'erreur
	private function displayError(){		
		if($this->sErreur && $this->bShowError && SessionService::get("erreur-sql")<=$this->nMaxDisplayError){				
			echo $this->sErreur;
		}
	}
	
	//envoi l'erreur par e-mail
	private function sendErrorByMail(){	
		if($this->sErreur){				
			Mail::sendMailBug(array("titre"=>"BUG SQL ".ConfigService::get("idSite"),"body"=>str_replace('display:none;','',$this->sErreur)));			
		}			
	}
	
	//requete SELECT
	public function select($sRequete){	
	
		$this->sLastRequete=$sRequete;		
		$oPdoStatement=$this->query($sRequete);		
		if(!$oPdoStatement){						
			SessionService::set("erreur-sql",SessionService::get("erreur-sql")+1);
			$aErreur=$this->errorInfo();
			$this->sErreur='<div style="background-color:#ffffff;color:#000000;font-size:12px;font-family:arial;padding:10px;">';
			$this->sErreur.='<div style="border:1px solid #000000;padding:10px;color:#990000;">';
			$this->sErreur.=$this->formatQueryDebug($sRequete);
			$this->sErreur.='</div>';
			$this->sErreur.='<br \>CODE='.$aErreur[0];
			$this->sErreur.='<br \>ERREUR=<strong>'.$aErreur[2].'</strong>';
			$this->sErreur.='<br \>SCRIPT='.$_SERVER['PHP_SELF'];
			$this->sErreur.='<br \>AREA='.$_GET["area"];
			$this->sErreur.='<br \>PAGE='.$_GET["page"];
			$this->sErreur.='<br \>FORMAT='.$_GET["format"];
			$this->sErreur.='<hr \>'.$this->dumpStr($_SESSION);
			$this->sErreur.='</div>';
			$this->displayError();

		}else{
			$this->aResult=$oPdoStatement->fetchAll();
			return $this->aResult;	
		}
	}
	
	//requete select sous renvoyé sous forme de tableau ou INSERT,UPDATE,DELETE etc...
	function execute($sRequete){
		$bExecuted=false;
		if(is_string($sRequete)){
			$sRequete=trim($sRequete);		
			$this->sLastRequete=$sRequete;				
			switch(substr(strtolower($sRequete),0,6)){			
				case "select" : $bExecuted=true;$this->select($sRequete);return  $this->getRes(); break;			
				default:
					$nCount=$this->exec($sRequete);
					if($nCount===false){
						SessionService::set("erreur-sql",SessionService::get("erreur-sql")+1);
						$aErreur=$this->errorInfo();
						$this->sErreur='<div style="background-color:#ffffff;color:#000000;font-size:12px;font-family:arial;padding:10px;">';
						$this->sErreur.='<div style="border:1px solid #000000;padding:10px;color:#990000;">';
						$this->sErreur.=$this->formatQueryDebug($sRequete);
						$this->sErreur.='</div>';
						$this->sErreur.='<br \>CODE='.$aErreur[0];
						$this->sErreur.='<br \>ERREUR=<strong>'.$aErreur[2].'</strong>';
						$this->sErreur.='<br \>SCRIPT='.$_SERVER['PHP_SELF'];
						$this->sErreur.='<br \>AREA='.$_GET["area"];
						$this->sErreur.='<br \>PAGE='.$_GET["page"];
						$this->sErreur.='<br \>FORMAT='.$_GET["format"];
						$this->sErreur.='<hr \>'.$this->dumpStr($_SESSION);
						$this->sErreur.='</div>';
						$this->displayError();

					}
					return $nCount;			
				break;
			}
		}else{
			echo "ERREUR : ->execute() attend une chaine(string) ";
		}
		
	}
		
	//insert des données a partir d'un tableau
	function insert($sTable,$aParam,$bExecute=true){
		//$sTable => nom de la table
		//$aParam => tableau associatif nomdecolonne=>valeur
		//$bExecute => execute ou non la requete (facultatif...)
		
		if(!isset($this->aBigInsert[$sTable])){
			$this->aBigInsert[$sTable]=array();
		}
		if($aParam){	
			$sSql="DESCRIBE $sTable";
			$this->select($sSql);
			$aLignes=$this->getRes();	
			$sColonnes="(";
			$sValeurs="(";
			$n=0;
			foreach($aLignes as $aCol)
			{
				if( $n>0 )
				{
					$sColonnes .= ",";
					$sValeurs .= ",";
				}

				$field = $aCol["Field"];
				$sColonnes .= "`{$field}`";

				if( !array_key_exists($field, $aParam) )
				{
					if( $aCol["Null"]=="YES" )
					{
						$sValeurs .= "NULL";
					}
					else
                    {
                        if( $aCol["Extra"]==="auto_increment" OR strlen($aCol["Default"])>0 )
                        {
                            $sValeurs .= "DEFAULT";
                        }
                        else
                        {
                            $sValeurs .= "''";
                        }
					}
				}
				else
                {
                    $value = Vars::secureInjection($aParam[$field]);
                    $sValeurs .= "'{$value}'";
				}

				$n++;		
			}

			$sColonnes .= ")";
			$sValeurs .= ")";
			$sSqlReturn = $sValeurs;


		}
		if($bExecute){	
			array_push($this->aBigInsert[$sTable],$sSqlReturn);
			if($this->aBigInsert[$sTable]){
				$sSql="INSERT INTO $sTable $sColonnes VALUES ";	
				$n=0;			
				foreach($this->aBigInsert[$sTable] as $sInsertValeurs){
					if($n>0){
						$sSql.=",";
					}
					$sSql.="$sInsertValeurs";					
					$n++;
				}
				$this->aBigInsert[$sTable]=array();				
				$this->execute($sSql);
			
				return $this->lastInsertId();
			}
			$this->execute("INSERT INTO $sTable $sColonnes VALUES ".$sSqlReturn);
			//echo "INSERT INTO $sTable $sColonnes VALUES ".$sSqlReturn;			
			return intval( $this->lastInsertId() );
		}else{
			array_push($this->aBigInsert[$sTable],$sSqlReturn);
		}
	}

    //insert avec on duplicate key
    function insertDuplicateKey($sTable,$aParam){
        //$sTable => nom de la table
        //$aParam => tableau associatif nomdecolonne=>valeur

        if($aParam){
            $sSql="DESCRIBE $sTable";
            $this->select($sSql);
            $aLignes=$this->getRes();
            $sColonnes="(";
            $sValeurs="(";
            $sUpdate="";
            $n=0;
            foreach($aLignes as $aCol){
                if($n>0){
                    $sColonnes.=",";
                    $sValeurs.=",";
                    if($sUpdate){$sUpdate.=",";} 
                }
                $sColonnes.="`".$aCol["Field"]."`";
                if(!isset($aParam[$aCol["Field"]])){
                    if($aCol["Null"]=="YES"){
                        $sValeurs.="NULL";
                        if($aCol["Field"]!='id'){$sUpdate.="`".$aCol["Field"]."`=NULL";}
                    }else{
                        $sValeurs.="''";
                        if($aCol["Field"]!='id'){$sUpdate.="`".$aCol["Field"]."`=''";}
                    }
                }else{
                    $sValeurs.="'".Vars::secureInjection($aParam[$aCol["Field"]])."'";
                    if($aCol["Field"]!='id'){$sUpdate.="`".$aCol["Field"]."`='".Vars::secureInjection($aParam[$aCol["Field"]])."'";}
                }
                $n++;
            }
            $sColonnes.=")";
            $sValeurs.=")";

            $sSql="INSERT INTO $sTable $sColonnes VALUES ".$sValeurs." ON DUPLICATE KEY UPDATE ".$sUpdate;

            $this->execute($sSql);
        }

        return $this->lastInsertId();

    }
	
	//update des données a partir d'un tableau
	function update($sTable,$aParam,$sSqlCondition){	
		$sSql="DESCRIBE $sTable";
		$this->select($sSql);
		$aLignes=$this->getRes();			
		$sValeurs="";
		$n=0;
		$nbCol=0;
		$sColonnes="";

		foreach($aLignes as $aCol)
		{
			
			$field = $aCol["Field"];
				
            if( array_key_exists($field,$aParam) )
            {
                $value = $aParam[$field];

                if($nbCol>0 && substr($sValeurs,-1)!=",")
                {
                    $sColonnes .= ",";
                    $sValeurs .= ",";
                }

                $nbCol++;

                if( $aCol["Null"]=="YES" AND is_null($value) )
                {
                    $sValue = "NULL";
                }
                else
                {
                    if( is_null($value) )
                    {
                        $sValue="''";
                    }
                    else
                    {
                        if( is_bool($value) )
                        {
                            $value = intval($value);
                        }
                        $value = Vars::secureInjection($value);
                        $sValue = "'{$value}'";
                    }
                }

                $sValeurs .= "`{$field}`={$sValue}";
            }

            $n++;
			
		}			
		if( $sSqlCondition )
		{
			$sSqlCondition = "AND {$sSqlCondition}";
		}

		$sSqlReturn = "UPDATE {$sTable} SET {$sValeurs} WHERE 1=1 {$sSqlCondition}";

		if($nbCol){		
			return $this->execute($sSqlReturn);			
		}else{
			return 0;
		}
	}
	
	//duplique une ligne sur des conditions
	function duplicate($sTable,$sSqlCondition){		
		$sSql="DESCRIBE $sTable";		
		if($sSqlCondition){
			$sSqlCondition="AND ".$sSqlCondition;
		}		
		$this->select($sSql);
		$aLignes=$this->getRes();	
		$sCol="";
		$n=0;	
		foreach($aLignes as $aCol){	  			
			if($aCol['Field'] && $aCol['Field']!='id'){
				if($n>0){
					$sCol.=",";
				}
				$sCol.="`".$aCol['Field']."`";
				$n++;
			}			
		}		
		$this->execute("INSERT INTO $sTable ($sCol) SELECT $sCol FROM $sTable WHERE 1=1 $sSqlCondition ");	
		return $this->lastInsertId();		
	}
	
	//recupere un tableau d'une table en vu d'une hydratation simple d'un objet / nId peut etre de la forme id1='value' AND id2='value2' 
	public function selectForHydrate($nId,$sTable,$aFields)
	{
		$sSql="DESCRIBE ". $sTable;	
		$this->select($sSql);
		$aColonnes=$this->getCol("Field");

		$sPrimaryKey = "";

		$aRows = $this->getRes();
		foreach( $aRows as $aLine )
		{


			if( $aLine['Key']=="PRI" )
			{
				$sPrimaryKey = $aLine['Field'];	
			}
		}
		
		$sSqlSelect="";	
		if($aFields[0]!="*")
		{	
			foreach($aFields as $sField)
			{
				if(in_array($sField,$aColonnes) && $sField!=$sPrimaryKey){
					if($sSqlSelect){$sSqlSelect.=", ";}
					$sSqlSelect.="`$sField`";				
				}
			}
		}
		else
		{
			$sSqlSelect="*";
		}
		
		if(strstr($nId, "=")!==false)
		{
			$aReturns=$this->execute("SELECT ".$sSqlSelect." FROM ".$sTable." WHERE ".$nId." ");
		}
		else
		{
			$aReturns=$this->execute("SELECT ".$sSqlSelect." FROM ".$sTable." WHERE `".$sPrimaryKey."`=".intval($nId)." ");
			//print_r( "SELECT ".$sSqlSelect." FROM ".$sTable." WHERE `".$sPrimaryKey."`=".intval($nId)." " );
		}
		if(!isset($aReturns[0])){
			$aReturns[0]=array();
		}
		$aTab=debug_backtrace();
		
		SessionService::set("hydrate-debug",SessionService::get("hydrate-debug")+1);
	
		return $aReturns[0];
	}
	
	//recupere le tableau de donné
	public function getRes(){
		return $this->aResult;	
	}
	
	//recupere une ligne precise
	public function getRow($nRowNumber){
		return $this->aResult[$nRowNumber];	
	}
	
	//recupere une colonne précise
	public function getCol($sFieldName){
		$aCol=array();
		foreach($this->aResult as $aRow){
			array_push($aCol,$aRow[$sFieldName]);
		}
		return $aCol;
	}
	
	//recupere une colonne précise (fonction identique a getCol)
	public function getField($sFieldName){
		return getCol($sFieldName);
	}
	
	//recuper la valeur d'un champs à une ligne précise
	public function getCase($sFieldName,$nRowNumber){
		if(count($this->aResult)>$nRowNumber){
			return $this->aResult[$nRowNumber][$sFieldName];
		}else{
			return "";
		}
	}
	
	//recupere la derniere requete saisie
	public function getLastQuery(){
		return $this->sLastRequete;
	}	
	
	//affiche la derniere requete
	public function showLastQuery(){
		echo $this->formatQueryDebug($this->getLastQuery());
	}
	
	//met en forme une requete pour un affichage debug
	public function formatQueryDebug($sRequest){
		if( $sRequest!=NULL && trim($sRequest)!="" ) 
		{
			$result = "";
	
			$result = preg_replace("#(,)\s+#is",",<br />",$sRequest);
			$result = preg_replace("#(\=)#","<span style='color: green; font-weight: bold;'>$1</span>",$result);
			
			$result = preg_replace("#(SELECT)\s+#is","<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(FROM)\s+#is","<br /><span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(INNER\s{1,1}JOIN|LEFT\s{1,1}JOIN|RIGHT\s{1,1}JOIN|JOIN)\s+#is","<br /><span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(ON)\s+#is","<br /><span style='color: blue; font-weight: bold; margin-left: 15px;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(WHERE)\s+#is","<br /><span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(AS)\s+#is","&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(ORDER\s{1,1}BY)\s+#is","<br /><span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(ASC)#is","&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(DESC)#is","&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(GROUP\s+BY)\s+#is","<br />&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(AND)\s+#is","<br /><span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(OR)\s+#is","&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(IN)\s+#is","&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(NOT)\s+#is","&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(EXISTS)\s+#is","&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			$result = preg_replace("#\s+(BETWEEN)\s+#is","&nbsp;<span style='color: blue; font-weight: bold;'>$1</span>&nbsp;",$result);
			
			$result = preg_replace("#\s+(LIKE)\s+#is","&nbsp;<span style='color: #C9A87C; font-weight: bold;'>$1</span>&nbsp;",$result);
			
			$result = preg_replace("#(COUNT\()#is","<span style='color: #F268E2; font-weight: bold;'>COUNT</span>(",$result);
			$result = preg_replace("#(SUM\()#is","<span style='color: #F268E2; font-weight: bold;'>SUM</span>(",$result);
			$result = preg_replace("#(DISTINCT)#is","<span style='color: #F268E2; font-weight: bold;'>$1</span>",$result);
			
			return "<div contenteditable='true'>".$result."</div>";
		}
	}
	
	private function dumpStr(&$var, $info = FALSE)
	{
		$sReturn =  "";
		$scope = false;
		$prefix = 'unique';
		$suffix = 'value';
	 
		if($scope) $vals = $scope;
		else $vals = $GLOBALS;
	
		$old = $var;
		$var = $new = $prefix.rand().$suffix; $vname = FALSE;
		foreach($vals as $key => $val) { 			
				if($val === $new){
					 $vname = $key;
				}			
		}
		$var = $old;			
		$sReturn .=  "<div style='position:absolute;margin: 0px 0px 10px 0px; display: block; background: white; color: black; font-family: Verdana; border: 1px solid #cccccc; padding: 5px; font-size: 10px; line-height: 13px; display:none;' id=\"ErreurSESSION\">\n";
		if($info != FALSE) echo "<b style='color: red;'>$info:</b><br>";
		$sReturn .= $this->do_dumpStr($var, '$'.$vname);
		$sReturn .= "</div>";
		return $sReturn;
	}

	private function do_dumpStr(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
	{
		$sReturn = "";
		$do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp;\n ";
		$reference = $reference.$var_name;
		$keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';
		
			
		if (is_array($var) && isset($var[$keyvar]))
		{
			$real_var = &$var[$keyvar];
			$real_name = &$var[$keyname];
			$type = ucfirst(gettype($real_var));
			$sReturn .=  "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>\n";
		}
		else
		{
			$var = array($keyvar => $var, $keyname => $reference);
			$avar = &$var[$keyvar];
	   
			$type = ucfirst(gettype($avar));
			if($type == "String") $type_color = "<span style='color:green'>";
			elseif($type == "Integer") $type_color = "<span style='color:red'>";
			elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
			elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
			elseif($type == "NULL") $type_color = "<span style='color:black'>";
	 
	
			if(is_array($avar))
			{
				$count = count($avar);
				$sReturn .= "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#a2a2a2'>$type ($count)</span><br>$indent(<br>\n";
				$keys = array_keys($avar);
				foreach($keys as $name)
				{
					if(strstr($name,ConfigService::get("idSite")) ){
						$value = &$avar[$name];
						$sReturn .= $this->do_dumpStr($value, "['$name']", $indent.$do_dump_indent, $reference);
					}
				}
				$sReturn .= "$indent)<br>";
			}
			elseif(is_object($avar))
			{
				$sReturn .=  "$indent$var_name <span style='color:#a2a2a2'>$type</span><br>$indent(<br>\n";
				foreach($avar as $name=>$value) $sReturn .= $this->do_dumpStr($value, "$name", $indent.$do_dump_indent, $reference);
				$sReturn .=  "$indent)<br>";
			}
			elseif(is_int($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>\n";
			elseif(is_string($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color\"$avar\"</span><br>\n";
			elseif(is_float($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>\n";
			elseif(is_bool($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>\n";
			elseif(is_null($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>\n";
			else $sReturn .= "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $avar<br>\n";
	
			$var = $var[$keyvar];
			return $sReturn;
		}
		
	}
	
	
}
