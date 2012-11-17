<?php
class Registro_Model_Usuario extends Zend_Db_Table_Abstract{
    protected $_name = 'USUARIO';    
    
    
    protected function obtener($usuario_id){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('USR'=>'USUARIO'),array('USR_ID','USR_EMAIL','USR_NOMBRE'))               
                ->where('USR.USR_ID=\''.$usuario_id.'\'');
        $resultado = $this->fetchRow($sql);
        if($resultado){            
            $this->id = $resultado->USR_ID;
            $this->email = $resultado->USR_EMAIL;
            $this->nombre = $resultado->USR_NOMBRE;            
        }else{
            $this->_error = true;
            $this->_error_msg = '<p>El usuario no existe o ha caducado</p>';
        }
    }
    
    public function crear($nuevo){
        $data = array(
            'USR_EMAIL'=>$nuevo->email,
            'USR_NOMBRE'=>$nuevo->nombres,
            'USR_GENERO'=>$nuevo->genero,
            'USR_CLAVE'=>md5($nuevo->clave),            
        );
        return $this->insert($data);
        
    }
    
    public function login($email,$clave){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from('USUARIO','USR_ID')
                ->where('USR_EMAIL LIKE \''.$email.'\' AND USR_CLAVE LIKE \''.$clave.'\'');
        //echo $sql->__toString();
        $resultado = $this->fetchRow($sql);
        if($resultado){
            $this->obtener($resultado->USR_ID);
            return true;
        }else{
            return false;
        }
    }
}