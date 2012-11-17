<?php
class Farmacias_IndexController extends Zend_Controller_Action{
    public function init(){
    	$this->view->active = 2;        
    }

    public function indexAction(){
        $this->view->headScript()->appendFile('/js/sistema/farmacias/listado.js');
        $this->view->headScript()->appendFile("http://maps.google.com/maps/api/js?sensor=true");
    	$this->view->headScript()->appendFile("/js/libs/gmaps/gmaps.js");
        
        
        $comuna_nombre = ($this->_getParam('comuna')) ? $this->_getParam('comuna'):false;
        if(isset($_SESSION['sys_comuna'])) $this->view->comuna_original = false; else $this->view->comuna_original = true;
        if(!$comuna_nombre){            
            if(isset($_SESSION['sys_comuna'])){
                $this->_redirect('/farmacias/'.$_SESSION['sys_comuna'].'/'); exit();
            }else{
                $location = new IBB_pichoGeoController();
                $string = new IBB_pichoStringController();
                $this->_redirect('/farmacias/'.$string->generar_slug($location->city).'/'); exit();
            }
             
        }
        $this->view->opcion = ($this->_getParam('opcion')) ? $this->_getParam('opcion'):false;
        $comuna_aux = new Farmacias_Model_Comuna();
        $this->view->comuna = $comuna_aux->get_by_name($comuna_nombre);
        $this->view->listado_regiones = $comuna_aux->listar_regiones();
        $farmacias = new Farmacias_Model_Farmacia();
        $this->view->listado_farmacias = $farmacias->listar($this->view->comuna->id,false,$this->view->opcion);
        unset($farmacias);
    }
    
    public function detalleAction(){
        
        $comuna_nombre = ($this->_getParam('comuna')) ? $this->_getParam('comuna'):false;
        if(!$comuna_nombre){            
            if(isset($_SESSION['sys_comuna'])){
                $this->_redirect('/farmacias/'.$_SESSION['sys_comuna'].'/'); exit();
            }else{
                $location = new IBB_pichoGeoController();
                $string = new IBB_pichoStringController();
                $this->_redirect('/farmacias/'.$string->generar_slug($location->city).'/'); exit();
            }
             
        }
        $comuna = new Farmacias_Model_Comuna();
        $nombre_slug = ($this->_getParam('nombre')) ? $this->_getParam('nombre'):false;        
        $this->view->headScript()->appendFile("http://maps.google.com/maps/api/js?sensor=true");
    	$this->view->headScript()->appendFile("/js/libs/gmaps/gmaps.js");
        
        $this->view->usuario = ($_SESSION['sys_usuario'])?$_SESSION['sys_usuario']:false;
        
        $this->view->farmacia = new Farmacias_Model_Farmacia($nombre_slug, $comuna->get_by_name($comuna_nombre)->id);

        $this->view->listado_farmacias = $this->view->farmacia->listar($comuna->get_by_name($comuna_nombre)->id,false,false,$this->view->farmacia->id);
    }
}
?>