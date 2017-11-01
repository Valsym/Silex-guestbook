<?php
namespace MvcBox\Service;

class Urlgen {
  private $app = null;

  public function __construct($app) {
    $this->app = $app;
  }

  public function url() {	  
	
	$url  = 'http://'.$_SERVER["SERVER_NAME"];
	$url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
	$ru = $_SERVER["REQUEST_URI"]; 
	preg_match('/\/([\w-]+)\//', $ru, $matches);
	$url .= $matches[0];  //print_r($matches);
	return $url;
  }
    
}
