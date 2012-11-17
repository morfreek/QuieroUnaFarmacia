<?php
class IndexController extends Zend_Controller_Action{
    public function init(){
    	$this->view->active = 1;
    }

    public function indexAction(){
        $this->view->headScript()->appendFile('/js/sistema/farmacias/index.js');
        $comuna = new Farmacias_Model_Comuna();
        if(isset($_SESSION['sys_comuna'])){
                $this->view->comuna = $comuna->get_by_name($_SESSION['sys_comuna']);
        }else{
            $location = new IBB_pichoGeoController();
            $string = new IBB_pichoStringController();
            $this->view->comuna = $comuna->get_by_name($string->generar_slug($location->city));                
        }
        $comuna_aux = new Farmacias_Model_Comuna();
        $this->view->listado_regiones = $comuna_aux->listar_regiones();
    }
    
    public function comunasAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest()){
            $option = '<option value="">Seleccione su comuna</option>';
            if($this->_getParam('region')){
                $comunas = new Farmacias_Model_Comuna();
                foreach ($comunas->listar_provincia($this->_getParam('region')) as $provincia){
                    $option .= '<optgroup label="'.$provincia->nombre.'">';
                        foreach ($comunas->listar_comuna($provincia->id) as $comuna){
                            $option .= '<option value="'.$comuna->nombre_slug.'">'.$comuna->nombre.'</option>';
                        }
                    $option .= '</optgroup>';
                }
                $operation = true;
                unset($comuna);
            }else{                
                $operation = false;
            }
            echo json_encode(array('result'=>$option,'operation'=>$operation));
        }else{
            echo "Solicitud no permitida";
        }
    }
    
    public function cambiarComunaAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $_SESSION['sys_comuna'] = $this->_getParam('comuna');
        $this->_redirect('/farmacias/'.$_SESSION['sys_comuna'].'/');
    }
    
    
    public function miUbicacionAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        if(isset($_SESSION['sys_comuna'])){
            unset($_SESSION['sys_comuna']);
        }
        $this->_redirect('/farmacias/'); exit();
    }
    
    public function calificarAction(){
        if(!$_POST){
            $this->_redirect('/farmacias/'); exit();
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $farmacia_id = $this->_getParam("id_farmacia");
        $usuario_id = $_SESSION['sys_usuario']->id;
        $nota = $this->_getParam("nota");
        
        $farmacia = new Farmacias_Model_Farmacia();
        $farmacia->calificar($farmacia_id,$usuario_id,$nota);
        $this->_redirect($_SERVER['HTTP_REFERER']); exit();
    }

    public function denunciarAction(){
        if(!$_POST){
            $this->_redirect('/farmacias/'); exit();
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $farmacia_id = $this->_getParam("id_farmacia");
        $usuario_id = $_SESSION['sys_usuario']->id;
        $motivo = $this->_getParam("motivo");
        $comentario = $this->_getParam("comentario");
        
        $farmacia = new Farmacias_Model_Farmacia();
        $farmacia->denunciar($farmacia_id,$usuario_id,$motivo,$comentario);
        $this->_redirect($_SERVER['HTTP_REFERER']); exit();
    }

    public function comentarAction(){
        if(!$_POST){
            $this->_redirect('/farmacias/'); exit();
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $farmacia_id = $this->_getParam("id_farmacia");
        $usuario_id = $_SESSION['sys_usuario']->id;
        $comentario = $this->_getParam("comentario");
        
        $farmacia = new Farmacias_Model_Farmacia();
        $farmacia->comentar($farmacia_id,$usuario_id,$comentario);
        $this->_redirect($_SERVER['HTTP_REFERER']); exit();
    }
}
?>