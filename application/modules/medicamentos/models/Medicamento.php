<?php
class Medicamentos_Model_Medicamento extends Zend_Db_Table_Abstract{
    protected $_name = 'MEDICAMENTO';    
    public $_error = false;
    public $_error_msg = '';

    public function __construct($id=false) {
        parent::__construct($config = array());
        if($id){
            $this->get($id);
        }
    }

    /*TODO*/
    private function get($id){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('MED'=>'MEDICAMENTO'),array('MED_ID','MED_PRODUCTO','MED_REGISTRO','MED_RESOLUCION','MED_FECHA_RESOLUCION','MED_FECHA_ACTUALIZACION'))
                ->joinInner(array('PACT'=>'PRINCIPIO_ACTIVO'),'PACT.PACT_ID=MED.PACT_ID',array('PACT_ID','PACT_NOMBRE'))
                ->joinInner(array('LAB'=>'LABORATORIO'),'LAB.LAB_ID=MED.LAB_ID',array('LAB_ID','LAB_NOMBRE'))
                ->joinInner(array('TRA'=>'TRATAMIENTO'),'TRA.TRA_ID=MED.TRA_ID',array('TRA_ID','TRA_NOMBRE'))
                ->where('MED.MED_ID=\''.$id.'\'');
        $resultado = $this->fetchRow($sql);
        if($resultado){
            $this->id = $resultado->MED_ID;
            $this->producto = $resultado->MED_PRODUCTO;
            $this->registro = $resultado->MED_REGISTRO;
            $this->resolucion = $resultado->MED_RESOLUCION;
            $this->fecha_emicion = $resultado->MED_FECHA_RESOLUCION;
            $this->fecha_actualizacion = $resultado->MED_FECHA_ACTUALIZACION;

            $this->principio_activo_id = $resultado->PACT_ID;
            $this->principio_activo_nombre = $resultado->PACT_NOMBRE;

            $this->laboratorio_id = $resultado->LAB_ID;
            $this->laboratorio_nombre = $resultado->LAB_NOMBRE;

            $this->tratamiento_id = $resultado->TRA_ID;
            $this->tratamiento_nombre = $resultado->TRA_NOMBRE;
            
            $this->bioequivalentes = $this->listar($this->principio_activo_id,false,false,$this->id);
        }else{
            $this->_error = true;
            $this->_error_msg = 'No se encontro medicamento';
        }
    }
    
    public function listar($p_activo_id=false, $laboratorio_id=false, $tratamiento_id=false, $id=false, $opcion=array()){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('MED'=>'MEDICAMENTO'),array('MED_ID','MED_PRODUCTO','MED_REGISTRO','MED_RESOLUCION','MED_FECHA_RESOLUCION','MED_FECHA_ACTUALIZACION'))
                ->joinInner(array('PACT'=>'PRINCIPIO_ACTIVO'),'PACT.PACT_ID=MED.PACT_ID',array('PACT_ID','PACT_NOMBRE'))
                ->joinInner(array('LAB'=>'LABORATORIO'),'LAB.LAB_ID=MED.LAB_ID',array('LAB_ID','LAB_NOMBRE'))
                ->joinInner(array('TRA'=>'TRATAMIENTO'),'TRA.TRA_ID=MED.TRA_ID',array('TRA_ID','TRA_NOMBRE'));
        
        ($p_activo_id) ? $sql->where('PACT.PACT_ID=\''.$p_activo_id.'\'') : NULL;
        ($laboratorio_id) ? $sql->where('LAB.LAB_ID=\''.$laboratorio_id.'\'') : NULL;
        ($tratamiento_id) ? $sql->where('TRA.TRA_ID=\''.$tratamiento_id.'\'') : NULL;
        ($id) ? $sql->where('MED.MED_ID<>\''.$id.'\'') : NULL;

        $lista = array();
        foreach($this->fetchAll($sql) as $resultado){
            $aux = new stdClass();
            $aux->id = $resultado->MED_ID;
            $aux->producto = $resultado->MED_PRODUCTO;
            $aux->registro = $resultado->MED_REGISTRO;
            $aux->resolucion = $resultado->MED_RESOLUCION;
            $aux->fecha_emicion = $resultado->MED_FECHA_RESOLUCION;
            $aux->fecha_actualizacion = $resultado->MED_FECHA_ACTUALIZACION;

            $aux->principio_activo_id = $resultado->PACT_ID;
            $aux->principio_activo_nombre = $resultado->PACT_NOMBRE;

            $aux->laboratorio_id = $resultado->LAB_ID;
            $aux->laboratorio_nombre = $resultado->LAB_NOMBRE;

            $aux->tratamiento_id = $resultado->TRA_ID;
            $aux->tratamiento_nombre = $resultado->TRA_NOMBRE;          
            $lista[] = $aux;
        }
        return $lista;
    }
    
    public function get_by_nombre($nombre_slug, $p_activo_id, $laboratorio_id, $tratamiento_id){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from($this,'MED_ID')
                ->where('MED_NOMBRE_SLUG LIKE \''.$nombre_slug.'\'')
                ->where('PACT_ID=\''.$p_activo_id.'\'')
                ->where('LAB_ID=\''.$laboratorio_id.'\'')
                ->where('TRA_ID =\''.$tratamiento_id.'\'');
        $resultado = $this->fetchRow($sql);
        return ($resultado) ? $resultado->MED_ID:false;
    }

    public function registrar($data){
        return $this->insert($data);
    }
    
    public function actualizar($id){
        $this->update(array('MED_FECHA_ACTUALIZACION'=>date('Y-m-d H:i:s')),'MED_ID=\''.$id.'\'');
    }
}