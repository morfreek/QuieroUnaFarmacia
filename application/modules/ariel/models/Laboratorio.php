<?php
class Ariel_Model_Laboratorio extends Zend_Db_Table_Abstract{
    protected $_name = 'LABORATORIO';    
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
                ->from($this,array('LAB_ID'))
                ->where('LAB_NOMBRE_SLUG LIKE \''.$nombre_slug.'\'');

        if($resultado = $this->fetchRow($sql)){
            return $resultado->LAB_ID;
        }else{
            return $this->_insert($nombre,$nombre_slug);
        }
    }
    
    private function _insert($nombre,$nombre_slug){
        $data = array(
            'LAB_NOMBRE'=>$nombre,
            'LAB_NOMBRE_SLUG'=>$nombre_slug
        );
        return $this->insert($data);
    }
    
}
