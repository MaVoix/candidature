<?php

class Twig_Extension_Filter extends Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter("url_hide_protocol", function($url)
            {
                return preg_replace("/^([a-z]+:\\/{2,2})/isU", "", $url);
            }),
            new \Twig_SimpleFilter('phone', array($this, 'phoneFilter')),
            new \Twig_SimpleFilter('basename', function($sPath) {
                return basename($sPath);
            }
            ),
            new Twig_SimpleFilter("ucwords", function ($string)
            {
                return  mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
            }), new Twig_SimpleFilter("capitalizefirst", function ($string)
            {
                return  mb_convert_case(substr($string,0,1), MB_CASE_TITLE, "UTF-8").substr($string,1);
            })
        ];
    }

    public function getName()
    {
        return "app_twig_filters_extension";
    }

    public function phoneFilter($num)
    {
       // return ($num) ? '0' . substr($num, 0, 1) . '.' . substr($num, 1, 2) . '.' . substr($num, 3, 2) . '.' . substr($num, 5, 2) . '.' . substr($num, 7, 2) : '&nbsp;';
        //comment for international
        return $num;
    }





}