<?php

$_CONFIG=array();

// dev/prod features
$_CONFIG["twig_auto_reload"]                = false; //set true to disable cache twig
$_CONFIG["version-css"]                     = "1"; //var to force browser cache reload CSS, use : strtotime("now") for always reload
$_CONFIG["version-js"]                      = "1"; //var to force browser cache reload JS, use : strtotime("now") for always reload
$_CONFIG["urlSite"]                         = "http://candidature.mavoix.info"; //url site without "/" at the end

// MAIN
$_CONFIG["idSite"]                          = "appelcandidature"; //for unique session var prefix
$_CONFIG["types"]			                = "visitor|admin>visitor"; //set hierarchy of user type ex: "type1|type2>type1|type3>type1|admin|type3>type2>type1"
$_CONFIG["area-default"]                    = array("visitor"=>"candidature","admin"=>"home"); //dir default for each type user
$_CONFIG["page-default"]                    = array("visitor"=>"list","admin"=>"home"); //page default for each type user
$_CONFIG["format-default"]                  = "html"; //format default (html/json/xml)
$_CONFIG["admin-account"]                   = array("admin"=>"admin"); //array of login/pass for admin

