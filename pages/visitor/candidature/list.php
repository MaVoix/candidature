<?php

$oListeCandidature=new CandidatureListe();
$oListeCandidature->applyRules4ListVisitor();
$aCandidatures=$oListeCandidature->getPage();
$aDataScript["candidatures"]=$aCandidatures;
