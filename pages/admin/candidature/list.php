<?php

$oListeCandidature=new CandidatureListe();
$oListeCandidature->applyRules4ListAdmin();
$aCandidatures=$oListeCandidature->getPage();
$aDataScript["candidatures"]=$aCandidatures;
