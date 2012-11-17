<?php
class Registro_IndexController extends Zend_Controller_Action{
    public function init(){}
    public function indexAction(){
    	$this->view->active = 4;
        //unset($_SESSION['sys_usuario']);
        if(isset($_SESSION['sys_usuario'])){
            $this->_redirect('/');
            
        }
    }
    
    public function crearAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();       
        $error = '';
        $nuevo = new stdClass();
        ($this->_getParam('email')) ? $nuevo->email = trim($this->_getParam('email')):$error.='<p>El email es obligatorio</p>';
        ($this->_getParam('nombres')) ? $nuevo->nombres = trim($this->_getParam('nombres')):$error.='<p>Los nombres son obligatorios</p>';       
        ($this->_getParam('genero')) ? $nuevo->genero = trim($this->_getParam('genero')):$error.='<p>Los nombres son obligatorios</p>';
        ($this->_getParam('clave')) ? $nuevo->clave = trim($this->_getParam('clave')):$error.='<p>La clave es obligatoria</p>';
        
        if(!empty($error)){
            unset($nuevo);            
            $this->_redirect('/usuario/registro/');
        }else{
            
            $usuario = new Registro_Model_Usuario();
            $nuevo->id = $usuario->crear($nuevo);
            $_SESSION['sys_usuario'] = $nuevo;
            $this->_redirect('/');
            

        }
    }
    public function loginAction(){
        $this->view->active = 5;
        
        if(isset($_SESSION['sys_usuario'])){
            $this->_redirect('/');
        }  
    }
    public function logAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        
        ($this->_getParam('email')) ? $email = $this->_getParam('email'):$error='<p>No se pudo capturar el email</p>';
        ($this->_getParam('pass')) ? $clave = md5($this->_getParam('pass')):$error.='<p>No se pudo capturar la clave</p>';
        if(empty($error)){
            $usuario = new Registro_Model_Usuario();
            if($usuario->login($email,$clave)){
                $_SESSION['sys_usuario']  = new stdClass();
                $_SESSION['sys_usuario']->id = $usuario->id;  
                $_SESSION['sys_usuario']->nombre = $usuario->nombre;                
                $_SESSION['sys_usuario']->email = $usuario->email;
                
                $this->_redirect('/');
            }else{
                $_SESSION['sys_error'] = '<p>Email o clave incorrectas.</p>';
                $this->_redirect('/');
            }
        }else{
            $_SESSION['sys_error'] = $error;
            $this->_redirect('/');
        }
        
    }
    
    public function logoutAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout(); 
        if(isset($_SESSION['sys_usuario'])){
            unset($_SESSION['sys_usuario']);
        }
        $this->_redirect();
    }
}