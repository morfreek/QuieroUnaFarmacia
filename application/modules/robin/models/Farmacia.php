<?php
class Robin_Model_Farmacia extends Zend_Db_Table_Abstract{
    protected $_name = 'FARMACIA';    
    public $_error = false;
    public $_error_msg = '';

    public function __construct($farmacia_id=false) {
        parent::__construct($config = array());
        if($farmacia_id){
            $this->get($farmacia_id);
        }
    }
    
    /*TODO*/
    private function get($farmacia_id){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('FAR'=>'FARMACIA'),array('FAR_ID','FAR_NOMBRE','FAR_DIRECCION','FAR_LOGO'))
                ->joinLeft(array('FCOM'=>'FARMACIA_COMENTARIO'),'FCOM.FAR_ID=FAR.FAR_ID',array('COUNT(FCOM_FECHA_REGISTRO) AS NUMERO_COMENTARIOS'))
                ->joinLeft(array('FDEN'=>'FARMACIA_DENUNCIA'),'FDEN.FAR_ID=FAR.FAR_ID',array('COUNT(FDEN_FECHA_REGISTRO) AS NUMERO_DENUNCIAS'))
                ->joinLeft(array('FEVA'=>'FARMACIA_EVALUACION'),'FEVA.FAR_ID=FAR.FAR_ID',array('COUNT(FEVA_FECHA_REGISTRO) AS NUMEROS_VOTOS','ROUND(COUNT(FEVA_FECHA_REGISTRO)/SUM(FEVA_NOTA),2) AS NOTA_FINAL'))
                ->joinInner(array('COM'=>'COMUNA'),'COM.COM_ID=FAR.COM_ID',array('COM_ID','COM_NOMBRE'))
                ->joinInner(array('PRO'=>'PROVINCIA'),'COM.COM_ID=FAR.COM_ID',array('PRO_ID','PRO_NOMBRE'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))
                ->where('FAR.FAR_ID=\''.$farmacia_id.'\'');
        $resultado = $this->fetchRow($sql);
        if($resultado){
            $this->id = $resultado->FAR_ID;
            $this->nombre = $resultado->FAR_NOMBRE;
            $this->direccion = $resultado->FAR_DIRECCION;
            $this->logo = (!empty($resultado->FAR_LOGO)) ? $resultado->FAR_LOGO:'/img/farmacias/default.jpg';
            $farmacia->numero_comentarios = $resultado->NUMERO_COMENTARIOS;
            $farmacia->numero_denuncias = $resultado->NUMERO_DENUNCIAS;
            $farmacia->indicador_turno = (!empty($resultado->FTUR_HORARIO)) ? true:false;
            $farmacia->horario_turno = (!empty($resultado->FTUR_HORARIO)) ? $resultado->FTUR_HORARIO:false;
            $farmacia->nota_final = $resultado->NOTA_FINAL;
            
            $this->comuna_id = $resultado->COM_ID;
            $this->comuna_nombre = $resultado->COM_NOMBRE;
            $this->provincia_id = $resultado->PRO_ID;
            $this->provincia_nombre = $resultado->PRO_NOMBRE;
            $this->region_id = $resultado->REG_ID;
            $this->region_nombre = $resultado->REG_NOMBRE;            
        }else{
            $this->_error = true;
            $this->_error_msg = 'No se encontro farmacia';
        }
    }
    
    public function listar($comuna_id,$region_id=false,$opcion=array()){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('FAR'=>'FARMACIA'),array('FAR_ID','FAR_NOMBRE','FAR_DIRECCION','FAR_LOGO'))
                ->joinLeft(array('FCOM'=>'FARMACIA_COMENTARIO'),'FCOM.FAR_ID=FAR.FAR_ID',array('COUNT(FCOM_FECHA_REGISTRO) AS NUMERO_COMENTARIOS'))
                ->joinLeft(array('FDEN'=>'FARMACIA_DENUNCIA'),'FDEN.FAR_ID=FAR.FAR_ID',array('COUNT(FDEN_FECHA_REGISTRO) AS NUMERO_DENUNCIAS'))
                ->joinLeft(array('FEVA'=>'FARMACIA_EVALUACION'),'FEVA.FAR_ID=FAR.FAR_ID',array('COUNT(FEVA_FECHA_REGISTRO) AS NUMEROS_VOTOS','ROUND(COUNT(FEVA_FECHA_REGISTRO)/SUM(FEVA_NOTA),2) AS NOTA_FINAL'))
                ->joinLeft(array('FTUR'=>'FARMACIA_TURNO'),'FTUR.FAR_ID=FAR.FAR_ID AND FTUR.FTUR_FECHA_TURNO=\''.date('Y-m-d').'\'',array('FTUR_HORARIO'))
                ->joinInner(array('COM'=>'COMUNA'),'COM.COM_ID=FAR.COM_ID',array('COM_ID','COM_NOMBRE'))
                ->joinInner(array('PRO'=>'PROVINCIA'),'PRO.PRO_ID=COM.PRO_ID',array('PRO_ID','PRO_NOMBRE'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))                
                ->where('COM.COM_ID=\''.$comuna_id.'\'')
                ->group(array('FAR_ID'));
        
        if($region_id){
                $sql->where('REG.REG_ID=\''.$region_id.'\'');
        }
        $resultados = $this->fetchAll($sql);
        $lista = array();
        foreach($resultados as $resultado){
            $farmacia = new stdClass();
            $farmacia->id = $resultado->FAR_ID;
            $farmacia->nombre = $resultado->FAR_NOMBRE;
            $farmacia->direccion = $resultado->FAR_DIRECCION;
            $farmacia->logo = (!empty($resultado->FAR_LOGO)) ? $resultado->FAR_LOGO:'/img/farmacias/default.jpg';
            $farmacia->numero_comentarios = $resultado->NUMERO_COMENTARIOS;
            $farmacia->numero_denuncias = $resultado->NUMERO_DENUNCIAS;
            $farmacia->indicador_turno = (!empty($resultado->FTUR_HORARIO)) ? true:false;
            $farmacia->nota_final = $resultado->NOTA_FINAL;
            
            $farmacia->comuna_id = $resultado->COM_ID;
            $farmacia->comuna_nombre = $resultado->COM_NOMBRE;
            $farmacia->provincia_id = $resultado->PRO_ID;
            $farmacia->provincia_nombre = $resultado->PRO_NOMBRE;
            $farmacia->region_id = $resultado->REG_ID;
            $farmacia->region_nombre = $resultado->REG_NOMBRE;            
            $lista[] = $farmacia;
        }
        return $lista;
    }
    
    public function get_by_nombre($farmacia_nombre, $comuna_id){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from('FARMACIA','FAR_ID')
                ->where('FAR_NOMBRE_SLUG LIKE \''.$farmacia_nombre.'\' AND COM_ID=\''.$comuna_id.'\'');
        $resultado = $this->fetchRow($sql);
        return ($resultado) ? $resultado->FAR_ID:false;        
    }
    
    public function get_turno($farmacia_id, $turno_fecha){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from('FARMACIA_TURNO','FAR_ID')
                ->where('FAR_ID=\''.$farmacia_id.'\' AND FTUR_FECHA_TURNO=\''.$turno_fecha.'\'');
        //echo $sql->__toString();
        $resultado = $this->fetchRow($sql);
        return ($resultado) ? true:false;
    }


    public function registrar($data){
        return $this->insert($data);
    }
    
    public function actualizar($farmacia_id){
        $this->update(array('FAR_FECHA_ACTUALIZACION'=>date('Y-m-d H:i:s')),'FAR_ID=\''.$farmacia_id.'\'');
    }
    
    public function registrar_turno($farmacia_id,$fecha,$horario){
        $data = array(
            'FAR_ID'=>$farmacia_id,
            'FTUR_FECHA_TURNO'=>$fecha,
            'FTUR_HORARIO'=>$horario
        );
        $this->_name = 'FARMACIA_TURNO';
        $this->insert($data);
        $this->_name = 'FARMACIA';
    }
    
    public function actualizar_turno($farmacia_id,$fecha,$horario){
        $this->_name = 'FARMACIA_TURNO';
        $this->update(array('FTUR_HORARIO'=>$horario),'FAR_ID=\''.$farmacia_id.'\' AND FTUR_FECHA_TURNO=\''.$fecha.'\'');
        $this->_name = 'FARMACIA';
    }
}