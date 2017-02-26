<?php

$oCanditure=new Candidature();
$oCanditure->setDate_created(date("Y-m-d H:i:s"));
$oCanditure->setDate_deleted(date("Y-m-d H:i:s"));
$oCanditure->setName("INSERTDEMO");
$oCanditure->save();
