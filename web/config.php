<?php

$_CONFIG=array();
$_CONFIG["idSite"]                          = "appelcandidature"; //for unique session var prefix
$_CONFIG["environment"]                     = null;
$_CONFIG["twig_auto_reload"]                 = false; //set true to disable cache twig
$_CONFIG["types"]			                ="visitor|admin>visitor";
$_CONFIG["area-default"]                    = array("visitor"=>"candidature","admin"=>"home");
$_CONFIG["page-default"]                    = array("visitor"=>"list","admin"=>"home");
$_CONFIG["format-default"]                  = "html";
$_CONFIG["urlSite"]                         = "http://candidature.mavoix.info";
$_CONFIG["version-css"]                     = "1";
$_CONFIG["version-js"]                      = "1";
$_CONFIG["admin-account"]                   = array("admin"=>"admin");