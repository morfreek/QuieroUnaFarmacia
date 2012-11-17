<?php
class Robin_Model_Comuna extends Zend_Db_Table_Abstract{
    protected $_name = 'COMUNA';    
    public $_error = false;
    public $_error_msg = '';
    
    public $comuna_id;


    public function __construct($comuna_id=false) {
        parent::__construct($config = array());
        if($comuna_id) $this->get_comuna($comuna_id);
    }
    
    public function get_comuna($comuna_id){
        
    }
    
    public function homologar(){
        /*$sql = $this->select()->setIntegrityCheck(false)
                ->from(array('COM'=>'COMUNA'),array('COM_ID','COM_NOMBRE'));
        $resultados = $this->fetchAll($sql);
        $string = new IBB_pichoStringController();
        foreach ($resultados as $comuna){
            $comuna_nombre = $string->generar_slug($comuna->COM_NOMBRE);
            echo $comuna->COM_NOMBRE.' -> '.$comuna_nombre.'<br />';
            $this->update(array('COM_NOMBRE_SLUG'=>$comuna_nombre),'COM_ID=\''.$comuna->COM_ID.'\'');
        }*/
    }
    
    public function get_by_name($comuna_nombre){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('COM'=>'COMUNA'),array('COM_ID','COM_NOMBRE'))
                ->joinInner(array('PRO'=>'PROVINCIA'),'PRO.PRO_ID=COM.PRO_ID',array('PRO_ID','PRO_ID'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))
                ->where('COM.COM_NOMBRE_SLUG LIKE \''.$comuna_nombre.'\'');
        //echo $sql->__toString();
        $resultado = $this->fetchRow($sql);
        if($resultado){
            $comuna = new stdClass();
            $comuna->id = $resultado->COM_ID;
            $comuna->nombre = $resultado->COM_NOMBRE;
            $comuna->region_nombre = $resultado->REG_NOMBRE;
            return $comuna;
        }
        return false;
    }
    
}
