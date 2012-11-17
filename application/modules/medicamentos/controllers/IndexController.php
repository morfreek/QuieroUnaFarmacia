<?php
class Medicamentos_IndexController extends Zend_Controller_Action{
    public function init(){
    	$this->view->active = 3;
    }


    public function indexAction(){
        //$this->view->headScript()->appendFile('/js/jquery/colorbox/1.3.19/jquery.colorbox-min.js');
        //$this->view->headLink()->appendStylesheet('/js/jquery/colorbox/1.3.19/v4/colorbox.css');
        // $this->view->headScript()->appendFile('/js/sistema/farmacias/listado.js');
        // $comuna_nombre = ($this->_getParam('comuna')) ? $this->_getParam('comuna'):false;
        // if(!$comuna_nombre){
        //     $location = new IBB_pichoGeoController();

        //     //$this->_redirect('/farmacias/'.$_SESSION['sys_comuna'].'/'); exit();
        // }
        $valor = ($this->_getParam('valor')) ? $this->_getParam('valor'):false;
        // $comuna_aux = new Farmacias_Model_Comuna();
        // $this->view->comuna = $comuna_aux->get_by_name($comuna_nombre);
        // $this->view->listado_regiones = $comuna_aux->listar_regiones();
        // $farmacias = new Farmacias_Model_Farmacia();
        // $this->view->listado_farmacias = $farmacias->listar($this->view->comuna->id,false,$this->view->opcion);
        // unset($farmacias);
        
        $medicamento = new Medicamentos_Model_Med();
        $string = new IBB_pichoStringController();
        $this->view->listado = ($valor) ? $medicamento->listar($string->generar_slug($valor)):array();
        $this->view->valor = $valor;
        //print_r($this->view->listado);
               
    }

    public function detalleAction(){
        $id = ($this->_getParam('id')) ? $this->_getParam('id'):false;
        // $comuna_aux = new Farmacias_Model_Comuna();
        // $this->view->comuna = $comuna_aux->get_by_name($comuna_nombre);
        // $this->view->listado_regiones = $comuna_aux->listar_regiones();
        // $farmacias = new Farmacias_Model_Farmacia();
        // $this->view->listado_farmacias = $farmacias->listar($this->view->comuna->id,false,$this->view->opcion);
        // unset($farmacias);
        
        $this->view->medicamento = new Medicamentos_Model_Med($id);
        
    }
}
?>