<?php
$bMaintenance=false;

$aIp=array();
$aIp["my-ip"]          ="192.168.1.1";


if(in_array($_SERVER['REMOTE_ADDR'],$aIp)){
    $bMaintenance=false;
}