<?php
class myClass
{
	const JQUERY_UI_CSS 	= "jquery-ui-1.10.1.custom.min.css";
	var $lambda;

    function __construct() {
       $this->lambda = function() {echo 'hello lambda world' . $this::JQUERY_UI_CSS . "\n\n";};
       // no errors here, so I assume that this is legal
       $this->beta = function($nom) {echo 'hello '. $nom. "\n\n";};
       $this->gamma = function (){
       		echo $this::JQUERY_UI_CSS;
       };
    }
}