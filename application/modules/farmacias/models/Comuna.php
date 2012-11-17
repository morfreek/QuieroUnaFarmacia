<?php
class Farmacias_Model_Comuna extends Zend_Db_Table_Abstract{
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
                ->from(array('COM'=>'COMUNA'),array('COM_ID','COM_NOMBRE','COM_NOMBRE_SLUG'))
                ->joinInner(array('PRO'=>'PROVINCIA'),'PRO.PRO_ID=COM.PRO_ID',array('PRO_ID','PRO_ID'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))
                ->where('COM.COM_NOMBRE_SLUG LIKE \''.$comuna_nombre.'\'');
        //echo $sql->__toString();
        $resultado = $this->fetchRow($sql);
        if($resultado){
            $comuna = new stdClass();
            $comuna->id = $resultado->COM_ID;
            $comuna->nombre = $resultado->COM_NOMBRE;
            $comuna->nombre_slug = $resultado->COM_NOMBRE_SLUG;
            $comuna->region_nombre = $resultado->REG_NOMBRE;
            return $comuna;
        }
        return false;
    }
    
    public function listar_provincia($region_codigo=false){
        /* Se selecciona HEX() para poder ordernar los registros obtenidos en UTF8
         * ya qye que en el caso de utf8 ñ es Ã‘.
         */
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('PRO'=>'PROVINCIA'),array('PRO_ID','PRO_NOMBRE','HEX(PRO_NOMBRE) AS PRO_NAME_ORDER'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))
                ->order(array('PRO_NAME_ORDER ASC'));
        if($region_codigo){
            $sql->where('PRO.REG_ID=\''.$region_codigo.'\'');
        }        
        $resultados = $this->fetchAll($sql);
        $lista = array();
        foreach($resultados as $resultado){
            $provincia = new stdClass();
            $provincia->id = $resultado->PRO_ID;
            $provincia->nombre = $resultado->PRO_NOMBRE;
            $provincia->region_id = $resultado->REG_ID;
            $provincia->region_nombre = $resultado->REG_NOMBRE;
            $lista[] = $provincia;
        }
        unset($resultado,$resultados,$provincia);
        return $lista;
    }
    
    public function listar_comuna($provincia_codigo=false){
        /* Se selecciona HEX() para poder ordernar los registros obtenidos en UTF8
         * ya qye que en el caso de utf8 ñ es Ã‘.
         */
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('COM'=>'COMUNA'),array('COM_ID','COM_NOMBRE','HEX(COM_NOMBRE) AS COM_NAME_ORDER','COM_NOMBRE_SLUG'))
                ->joinInner(array('PRO'=>'PROVINCIA'),'PRO.PRO_ID=COM.PRO_ID',array('PRO_ID','PRO_NOMBRE','HEX(PRO_NOMBRE) AS PRO_NAME_ORDER'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))
                ->order(array('COM_NAME_ORDER'));
        if($provincia_codigo){
            $sql->where('PRO.PRO_ID=\''.$provincia_codigo.'\'');
        }        
        $resultados = $this->fetchAll($sql);
        $lista = array();
        foreach($resultados as $resultado){
            $comuna = new stdClass();
            $comuna->id = $resultado->COM_ID;
            $comuna->nombre = $resultado->COM_NOMBRE;
            $comuna->nombre_slug = $resultado->COM_NOMBRE_SLUG;
            $comuna->provincia_id = $resultado->PRO_ID;
            $comuna->provincia_nombre = $resultado->PRO_NOMBRE;
            $comuna->region_id = $resultado->REG_ID;
            $comuna->region_nombre = $resultado->REG_NOMBRE;
            $lista[] = $comuna;
        }
        unset($resultado,$resultados,$comuna);
        return $lista;
    }
    
    public function listar_regiones(){        
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('REG'=>'REGION'),array('REG_ID','REG_NOMBRE'))               
                ->order(array('REG_ORDEN ASC'));        
        $resultados = $this->fetchAll($sql);
        $lista = array();
        foreach($resultados as $resultado){
            $region = new stdClass();
            $region->id = $resultado->REG_ID;
            $region->nombre = $resultado->REG_NOMBRE;
            
            $lista[] = $region;
        }
        unset($resultado,$resultados,$region);
        return $lista;
    
    }
    
}
