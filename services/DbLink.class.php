<?php




	class DbLink
	{
		private static $_instance;
		private static $_extra_instances = [];

		private function __construct() { }

		public static function getInstance($sNameInstance=null)
		{
			if( is_null($sNameInstance) )
			{
				if( is_null( self::$_instance ) )
				{
					self::$_instance = new Mysql(array(
						"host" => ConfigService::get("bdd-serveur"),
						"login" => ConfigService::get("bdd-login"),
						"pass" => ConfigService::get("bdd-pass"),
						"base" => ConfigService::get("bdd-base")
					));
				}
				return self::$_instance;
			}
			else
			{
				if( array_key_exists($sNameInstance, self::$_extra_instances) )
				{
					return self::$_extra_instances[$sNameInstance];
				}
				else return null;
			}
		}

		public static function setExtraInstance($sNameInstance, $aConnectionDatas)
		{
			self::$_extra_instances[$sNameInstance] = new Mysql($aConnectionDatas);

			return self::getInstance($sNameInstance);
		}

		public static function initRaz($aArray){
			self::$_instance = @new Mysql(array(
				"host" => ConfigService::get("bdd-serveur"),
				"login" => $aArray["bdd-login"],
				"pass" => $aArray["bdd-pass"],
				"base" => $aArray["bdd-base"]
			));
		}
	}
