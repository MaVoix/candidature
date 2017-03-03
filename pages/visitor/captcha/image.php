<?php

$builder=new Gregwar\Captcha\CaptchaBuilder();
$builder->build();

$builder->output();

SessionService::set("captcha-value",$builder->getPhrase());