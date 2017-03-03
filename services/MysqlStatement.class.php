<?php


class MysqlStatement extends PDOStatement {
	
	private $sTitreMail			=   "";
	private $sExpediteur		=   "";
	private $aDestinataires		=    array();
	private $bSendMail			=    false;
	private $nMaxDisplayError	=	 20;
	private $nMaxSendMailError	=	 5;
	private $nNbError			=    0;
	private $bShowError			= true;
	private $sLastRequete		=   "";
	private $sErreur			=   "";
	 
	protected function __construct($dbh) {
        $this->dbh = $dbh;
		$this->sTitreMail		=	ConfigService::get("bdd-titremail");
		$this->sExpediteur		=	ConfigService::get("bdd-expediteur");
		$this->bSendMail		=	ConfigService::get("bdd-sendmail");
		$this->bShowError		=	ConfigService::get("bdd-showerreur");
		$this->aDestinataires	=   ConfigService::get("bdd-destinataires");
		$this->nMaxDisplayError =   ConfigService::get("bdd-max-show");
		$this->nMaxSendMailError =  ConfigService::get("bdd-max-mail");
    }
	
	 public function execute($aParam=array()){
		if(!parent::execute($aParam)){			
			$aErreur=$this->errorInfo();
            SessionService::set("erreur-sql",SessionService::get("erreur-sql")+1);
			$this->sErreur='<div style="background-color:#ffffff;color:#000000;font-size:12px;font-family:arial;padding:10px;">';			
			$this->sErreur.='<div style="border:1px solid #000000;padding:10px;color:#990000;">';
			$this->sErreur.=$this->formatQueryDebug($this->queryString);
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
			
			return false;
		}else{
			return true;
		}		
		 
	 }
	 
	 
	 public function getRes(){
	 	return parent::fetchAll(PDO::FETCH_ASSOC);
	 }
	 
	 
	 //recupere une ligne precise
	public function getRow($nRowNumber){
		$aData=$this->getRes();
		if(isset($aData[$nRowNumber])){
			return $aData[$nRowNumber];	
		}else{
			return array();
		}
	}
	
	//recupere une colonne pr�cise
	public function getCol($sFieldName){
		$aCol=array();
		$aData=$this->getRes();
		foreach($aData as $aRow){
			array_push($aCol,$aRow[$sFieldName]);
		}
		return $aCol;
	}
	
	//recupere une colonne pr�cise (fonction identique a getCol)
	public function getField($sFieldName){
		return self::getCol($sFieldName);
	}
	
	//recuper la valeur d'un champs � une ligne pr�cise
	public function getCase($sFieldName,$nRowNumber){
		$aData=$this->getRes();
		if(isset($aData[$nRowNumber][$sFieldName])){
			return $aData[$nRowNumber][$sFieldName];	
		}else{
			return 0;
		}
	}
	 
	 //affiche l'erreur
	private function displayError(){		
		if($this->sErreur && $this->bShowError && SessionService::get("erreur-sql")<=$this->nMaxDisplayError){				
			echo $this->sErreur;
		}
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
		if($info != FALSE) $sReturn.= "<b style='color: red;'>$info:</b><br>";
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
				elseif(is_string($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color\"".$avar."\"</span><br>\n";
				elseif(is_float($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>\n";
				elseif(is_bool($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>\n";
				elseif(is_null($avar)) $sReturn .=   "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>\n";
				else $sReturn .= "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $avar<br>\n";
		
				$var = $var[$keyvar];
				return $sReturn;
			}
		
	}
}
