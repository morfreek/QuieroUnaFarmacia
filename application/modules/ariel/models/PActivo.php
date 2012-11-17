<?php
class Ariel_Model_PActivo extends Zend_Db_Table_Abstract{
    protected $_name = 'PRINCIPIO_ACTIVO';    
    public $_error = false;
    public $_error_msg = '';
    
    public $id;


    public function __construct($id=false) {
        parent::__construct($config = array());
        if($comuna_id) $this->get($id);
    }

    public function get($id){
        
    }

    public function get_id_by_name($nombre,$nombre_slug){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from($this,array('PACT_ID'))
                ->where('PACT_NOMBRE_SLUG LIKE \''.$nombre_slug.'\'');

        if($resultado = $this->fetchRow($sql)){
            return $resultado->PACT_ID;
        }else{
            return $this->_insert($nombre,$nombre_slug);
        }
    }
    
    private function _insert($nombre,$nombre_slug){
        $data = array(
            'PACT_NOMBRE'=>$nombre,
            'PACT_NOMBRE_SLUG'=>$nombre_slug
        );
        return $this->insert($data);
    }
    
}
