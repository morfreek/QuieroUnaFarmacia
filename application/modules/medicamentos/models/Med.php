<?php
class Medicamentos_Model_Med extends Zend_Db_Table_Abstract{
    protected $_name = 'MEDICAMENTO';    
    public $_error = false;
    public $_error_msg = '';

    public function __construct($id=false) {
        parent::__construct($config = array());
        if($id){
            $this->get($id);
        }
    }
    
    public function get($id){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('MED'=>'MEDICAMENTO'),array('MED_ID','MED_PRODUCTO','MED_REGISTRO','MED_RESOLUCION','MED_FECHA_RESOLUCION','MED_FECHA_ACTUALIZACION'))
                ->joinInner(array('PACT'=>'PRINCIPIO_ACTIVO'),'PACT.PACT_ID=MED.PACT_ID',array('PACT_ID','PACT_NOMBRE','PACT_NOMBRE_SLUG'))
                ->joinInner(array('LAB'=>'LABORATORIO'),'LAB.LAB_ID=MED.LAB_ID',array('LAB_ID','LAB_NOMBRE'))
                ->joinInner(array('TRA'=>'TRATAMIENTO'),'TRA.TRA_ID=MED.TRA_ID',array('TRA_ID','TRA_NOMBRE'))
                ->where('MED.MED_ID=\''.$id.'\'');
        $resultado = $this->fetchRow($sql);
        
        $this->id = $resultado->MED_ID;
        $this->producto = $resultado->MED_PRODUCTO;
        $this->principio_nombre = $resultado->PACT_NOMBRE;
        $this->principio_slug = $resultado->PACT_NOMBRE_SLUG;
        $this->laboratorio_nombre = $resultado->LAB_NOMBRE;
        $this->tratamiento_nombre = $resultado->TRA_NOMBRE;
    }
    
    public function listar($valor){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('MED'=>'MEDICAMENTO'),array('MED_ID','MED_PRODUCTO','MED_REGISTRO','MED_RESOLUCION','MED_FECHA_RESOLUCION','MED_FECHA_ACTUALIZACION'))
                ->joinInner(array('PACT'=>'PRINCIPIO_ACTIVO'),'PACT.PACT_ID=MED.PACT_ID',array('PACT_ID','PACT_NOMBRE'))
                ->joinInner(array('LAB'=>'LABORATORIO'),'LAB.LAB_ID=MED.LAB_ID',array('LAB_ID','LAB_NOMBRE'))
                ->joinInner(array('TRA'=>'TRATAMIENTO'),'TRA.TRA_ID=MED.TRA_ID',array('TRA_ID','TRA_NOMBRE'))
                ->where('PACT.PACT_NOMBRE_SLUG LIKE \'%'.$valor.'%\' OR LAB.LAB_NOMBRE_SLUG LIKE \'%'.$valor.'%\' OR TRA.TRA_NOMBRE_SLUG LIKE \'%'.$valor.'%\' OR MED.MED_NOMBRE_SLUG LIKE \'%'.$valor.'%\'');
        $resultados = $this->fetchAll($sql);
        //echo $sql->__toString();
        $lista = array();
        foreach($resultados as $resultado){
            $medicamento = new stdClass();
            $medicamento->id = $resultado->MED_ID;
            $medicamento->producto = $resultado->MED_PRODUCTO;
            $medicamento->principio_nombre = $resultado->PACT_NOMBRE;
            $medicamento->laboratorio_nombre = $resultado->LAB_NOMBRE;
            $medicamento->tratamiento_nombre = $resultado->TRA_NOMBRE;
            $lista[]=$medicamento;
        }
        return $lista;
        
    }
    
    public function listar_bio($principio){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('MED'=>'MEDICAMENTO'),array('MED_ID','MED_PRODUCTO','MED_REGISTRO','MED_RESOLUCION','MED_FECHA_RESOLUCION','MED_FECHA_ACTUALIZACION'))
                ->joinInner(array('PACT'=>'PRINCIPIO_ACTIVO'),'PACT.PACT_ID=MED.PACT_ID',array('PACT_ID','PACT_NOMBRE'))
                ->joinInner(array('LAB'=>'LABORATORIO'),'LAB.LAB_ID=MED.LAB_ID',array('LAB_ID','LAB_NOMBRE'))
                ->joinInner(array('TRA'=>'TRATAMIENTO'),'TRA.TRA_ID=MED.TRA_ID',array('TRA_ID','TRA_NOMBRE'))
                ->where('PACT.PACT_NOMBRE_SLUG LIKE \'%'.$principio.'%\'');
        $resultados = $this->fetchAll($sql);
        //echo $sql->__toString();
        $lista = array();
        foreach($resultados as $resultado){
            $medicamento = new stdClass();
            $medicamento->id = $resultado->MED_ID;
            $medicamento->producto = $resultado->MED_PRODUCTO;
            $medicamento->principio_nombre = $resultado->PACT_NOMBRE;
            $medicamento->laboratorio_nombre = $resultado->LAB_NOMBRE;
            $medicamento->tratamiento_nombre = $resultado->TRA_NOMBRE;
            $lista[]=$medicamento;
        }
        return $lista;
        
    }
}