<?php
class Fernando_IndexController extends Zend_Controller_Action{
    public function init(){}
    public function indexAction(){
    	$this->view->headScript()->appendFile("http://maps.google.com/maps/api/js?sensor=true");
    	$this->view->headScript()->appendFile("/js/libs/gmaps/gmaps.js");
    	// $this->view->headScript()->appendFile("http://j.maxmind.com/app/geoip.js");
    }
}