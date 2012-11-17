<?php
class Farmacias_Model_Farmacia extends Zend_Db_Table_Abstract{
    protected $_name = 'FARMACIA';    
    public $_error = false;
    public $_error_msg = '';

    public function __construct($farmacia=false, $comuna=false) {
        parent::__construct($config = array());
        if($farmacia){
            if(is_numeric($farmacia)){
                $this->get($farmacia, $comuna);
            }else{
                $this->get_by_slug($farmacia, $comuna);
            }            
        }
    }
    
    private function get_by_slug($farmacia, $comuna=false){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('FAR'=>'FARMACIA'),array('FAR_ID','FAR_NOMBRE','FAR_DIRECCION','FAR_LOGO'))
                ->joinLeft(array('FCOM'=>'FARMACIA_COMENTARIO'),'FCOM.FAR_ID=FAR.FAR_ID',array('COUNT(FCOM_FECHA_REGISTRO) AS NUMERO_COMENTARIOS'))
                ->joinLeft(array('FDEN'=>'FARMACIA_DENUNCIA'),'FDEN.FAR_ID=FAR.FAR_ID',array('COUNT(FDEN_FECHA_REGISTRO) AS NUMERO_DENUNCIAS'))
                ->joinLeft(array('FEVA'=>'FARMACIA_EVALUACION'),'FEVA.FAR_ID=FAR.FAR_ID',array('COUNT(FEVA_FECHA_REGISTRO) AS NUMEROS_VOTOS','ROUND(SUM(FEVA_NOTA)/COUNT(FEVA_FECHA_REGISTRO),0) AS NOTA_FINAL'))
                ->joinLeft(array('FTUR'=>'FARMACIA_TURNO'),'FTUR.FAR_ID=FAR.FAR_ID AND FTUR.FTUR_FECHA_TURNO=\''.date('Y-m-d').'\'',array('FTUR_HORARIO','FTUR_FECHA_TURNO'))                                
                ->joinInner(array('COM'=>'COMUNA'),'COM.COM_ID=FAR.COM_ID',array('COM_ID','COM_NOMBRE'))
                ->joinInner(array('PRO'=>'PROVINCIA'),'PRO.PRO_ID=COM.PRO_ID',array('PRO_ID','PRO_NOMBRE'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))
                ->where('FAR.FAR_NOMBRE_SLUG LIKE \''.$farmacia.'\'')
                ->where('COM.COM_ID = \''.$comuna.'\'')
                ->group(array('FAR_ID'));
        // echo $sql->__toString();
        $resultado = $this->fetchRow($sql);
        if($resultado){
            $this->id = $resultado->FAR_ID;
            $this->nombre = $resultado->FAR_NOMBRE;
            $this->direccion = $resultado->FAR_DIRECCION;
            $this->logo = (!empty($resultado->FAR_LOGO)) ? $resultado->FAR_LOGO:'/img/layout/logo-farmacia.png';
            $this->numero_comentarios = $resultado->NUMERO_COMENTARIOS;
            $this->numero_denuncias = $resultado->NUMERO_DENUNCIAS;
            $this->indicador_turno = (!empty($resultado->FTUR_HORARIO)) ? true:false;
            $this->horario_turno = (!empty($resultado->FTUR_HORARIO)) ? $resultado->FTUR_HORARIO:false;
            $this->nota_final = $resultado->NOTA_FINAL;

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
    
    public function listar_denuncias(){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('FDEN'=>'FARMACIA_DENUNCIA'),array('FDEN_FECHA_REGISTRO','FDEN_MOTIVO','FDEN_DESCRIPCION','FDEN_IMAGEN'))
                ->where('FDEN.FAR_ID=\''.$this->id.'\'')
                ->order(array('FDEN.FDEN_FECHA_REGISTRO DESC'));
        $resultados = $this->fetchAll($sql);
        $lista = array();
        foreach($resultados as $resultado){
            $denuncia = new stdClass();
            $denuncia->fecha_registro = new IBB_pichoDateController($resultado->FDEN_FECHA_REGISTRO);
            $denuncia->motivo = ($resultado->FDEN_MOTIVO==1) ? 'Mal servicio':'Incumplimiento de turno';
            $denuncia->descripcion = $resultado->FDEN_DESCRIPCION;
            $denuncia->imagen = (!empty($resultado->FDEN_IMAGEN)) ? $resultado->FDEN_IMAGEN:false;
            $lista[] = $denuncia;
        }
        return $lista;
    }
    
    public function listar_comentarios(){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('FCOM'=>'FARMACIA_COMENTARIO'),array('FCOM_FECHA_REGISTRO','FCOM_TEXTO'))
                ->joinInner(array('USU'=>'USUARIO'),'USU.USR_ID	=FCOM.USR_ID',array('USR_NOMBRE'))
                ->where('FCOM.FAR_ID=\''.$this->id.'\'')
                ->order(array('FCOM.FCOM_FECHA_REGISTRO DESC'));
        // echo $sql->__toString();
        $resultados = $this->fetchAll($sql);
        $lista = array();
        foreach($resultados as $resultado){
            $comentario = new stdClass();
            $comentario->fecha_registro = new IBB_pichoDateController($resultado->FCOM_FECHA_REGISTRO);
            $comentario->texto = $resultado->FCOM_TEXTO;
            $comentario->usuario = $resultado->USR_NOMBRE;
            $lista[] = $comentario;
        }
        // print_r($lista);
        return $lista;
    }
        
    private function get($farmacia_id, $comuna=false){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('FAR'=>'FARMACIA'),array('FAR_ID','FAR_NOMBRE','FAR_DIRECCION','FAR_LOGO'))
                ->joinLeft(array('FCOM'=>'FARMACIA_COMENTARIO'),'FCOM.FAR_ID=FAR.FAR_ID',array('COUNT(FCOM_FECHA_REGISTRO) AS NUMERO_COMENTARIOS'))
                ->joinLeft(array('FDEN'=>'FARMACIA_DENUNCIA'),'FDEN.FAR_ID=FAR.FAR_ID',array('COUNT(FDEN_FECHA_REGISTRO) AS NUMERO_DENUNCIAS'))
                ->joinLeft(array('FEVA'=>'FARMACIA_EVALUACION'),'FEVA.FAR_ID=FAR.FAR_ID',array('COUNT(FEVA_FECHA_REGISTRO) AS NUMEROS_VOTOS','ROUND(SUM(FEVA_NOTA)/COUNT(FEVA_FECHA_REGISTRO),2) AS NOTA_FINAL'))
                ->joinLeft(array('FTUR'=>'FARMACIA_TURNO'),'FTUR.FAR_ID=FAR.FAR_ID AND FTUR.FTUR_FECHA_TURNO=\''.date('Y-m-d').'\'',array('FTUR_HORARIO','FTUR_FECHA_TURNO'))                
                ->joinInner(array('COM'=>'COMUNA'),'COM.COM_ID=FAR.COM_ID',array('COM_ID','COM_NOMBRE'))
                ->joinInner(array('PRO'=>'PROVINCIA'),'COM.COM_ID=FAR.COM_ID',array('PRO_ID','PRO_NOMBRE'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))
                ->where('FAR.FAR_ID=\''.$farmacia_id.'\'')
                ->where('COM.COM_ID = \''.$comuna.'\'');
        $resultado = $this->fetchRow($sql);
        if($resultado){
            $this->id = $resultado->FAR_ID;
            $this->nombre = $resultado->FAR_NOMBRE;
            $this->direccion = $resultado->FAR_DIRECCION;
            $this->logo = (!empty($resultado->FAR_LOGO)) ? $resultado->FAR_LOGO:'/img/layout/logo-farmacia.png';
            $this->numero_comentarios = $resultado->NUMERO_COMENTARIOS;
            $this->numero_denuncias = $resultado->NUMERO_DENUNCIAS;
            $this->indicador_turno = (!empty($resultado->FTUR_HORARIO)) ? true:false;
            $this->horario_turno = (!empty($resultado->FTUR_HORARIO)) ? $resultado->FTUR_HORARIO:false;
            $this->nota_final = $resultado->NOTA_FINAL;
            
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
    
    public function listar($comuna_id,$region_id=false,$opcion=false,$excluir_id=false){
        $sql = $this->select()->setIntegrityCheck(false)
                ->from(array('FAR'=>'FARMACIA'),array('FAR_ID','FAR_NOMBRE','FAR_DIRECCION','FAR_LOGO','FAR_NOMBRE_SLUG'))
                ->joinLeft(array('FCOM'=>'FARMACIA_COMENTARIO'),'FCOM.FAR_ID=FAR.FAR_ID',array('COUNT(FCOM_FECHA_REGISTRO) AS NUMERO_COMENTARIOS'))
                ->joinLeft(array('FDEN'=>'FARMACIA_DENUNCIA'),'FDEN.FAR_ID=FAR.FAR_ID',array('COUNT(FDEN_FECHA_REGISTRO) AS NUMERO_DENUNCIAS'))
                ->joinLeft(array('FEVA'=>'FARMACIA_EVALUACION'),'FEVA.FAR_ID=FAR.FAR_ID',array('COUNT(FEVA_FECHA_REGISTRO) AS NUMERO_VOTOS','ROUND(SUM(FEVA_NOTA)/COUNT(FEVA_FECHA_REGISTRO),0) AS NOTA_FINAL'))
                ->joinLeft(array('FTUR'=>'FARMACIA_TURNO'),'FTUR.FAR_ID=FAR.FAR_ID AND FTUR.FTUR_FECHA_TURNO=\''.date('Y-m-d').'\'',array('FTUR_HORARIO','FTUR_FECHA_TURNO'))
                ->joinInner(array('COM'=>'COMUNA'),'COM.COM_ID=FAR.COM_ID',array('COM_ID','COM_NOMBRE'))
                ->joinInner(array('PRO'=>'PROVINCIA'),'PRO.PRO_ID=COM.PRO_ID',array('PRO_ID','PRO_NOMBRE'))
                ->joinInner(array('REG'=>'REGION'),'REG.REG_ID=PRO.REG_ID',array('REG_ID','REG_NOMBRE'))                
                ->where('COM.COM_ID=\''.$comuna_id.'\'')
                ->group(array('FAR.FAR_ID'));
        
        switch($opcion){
            case 'denuncias':
                $sql->order(array('NUMERO_DENUNCIAS DESC'));
                break;
            case 'comentarios':
                $sql->order(array('NUMERO_COMENTARIOS DESC'));
                break;
            default:
                $sql->order(array('NOTA_FINAL DESC'));
                break;
        }
        if($excluir_id){
            $sql->where('FAR.FAR_ID<>\''.$excluir_id.'\'');
        }
        if($region_id){
                $sql->where('REG.REG_ID=\''.$region_id.'\'');
        }
        $resultados = $this->fetchAll($sql);
        // echo $sql->__toString();
        $lista = array();
        foreach($resultados as $resultado){
            $farmacia = new stdClass();
            $farmacia->id = $resultado->FAR_ID;
            $farmacia->nombre = $resultado->FAR_NOMBRE;
            $farmacia->nombre_slug = $resultado->FAR_NOMBRE_SLUG;
            $farmacia->direccion = $resultado->FAR_DIRECCION;
            $farmacia->logo = (!empty($resultado->FAR_LOGO)) ? $resultado->FAR_LOGO:'http://placehold.it/64x64';
            
            
            $farmacia->numero_comentarios = $resultado->NUMERO_COMENTARIOS;
            $farmacia->numero_denuncias = $resultado->NUMERO_DENUNCIAS;
            $farmacia->indicador_turno = (!empty($resultado->FTUR_HORARIO)) ? true:false;
            $farmacia->horario_turno = (!empty($resultado->FTUR_HORARIO)) ? $resultado->FTUR_HORARIO:false;
            $farmacia->fecha_turno = (!empty($resultado->FTUR_HORARIO)) ? new IBB_pichoDateController($resultado->FTUR_FECHA_TURNO):false;
            
            $farmacia->numero_votos = $resultado->NUMERO_VOTOS;
            $farmacia->nota_final = ($resultado->NOTA_FINAL) ? $resultado->NOTA_FINAL:0;
            
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

    public function calificar($farmacia_id,$usuario_id,$nota){
         $sql = $this->select()->setIntegrityCheck(false)
                ->from('FARMACIA_EVALUACION',array('total'=>'COUNT(*)'))
                ->where('FAR_ID=\''.$farmacia_id.'\' AND USR_ID=\''.$usuario_id.'\'');
        $voto = $this->fetchRow($sql)->total;

        $data = array("FEVA_FECHA_REGISTRO"=>date("Y-m-d H:i:s"),"FEVA_NOTA"=>$nota);

        $this->_name="FARMACIA_EVALUACION";
        if($voto){
            $this->update($data,'FAR_ID=\''.$farmacia_id.'\' AND USR_ID=\''.$usuario_id.'\'');
        }else{
            $data["FAR_ID"]=$farmacia_id;
            $data["USR_ID"]=$usuario_id;
            $this->insert($data);
        }
        $this->_name="FARMACIA";
        return true;
    }

    public function denunciar($farmacia_id,$usuario_id,$motivo,$comentario){
        $data = array("FDEN_FECHA_REGISTRO"=>date("Y-m-d H:i:s"),"FAR_ID"=>$farmacia_id,
        "FDEN_MOTIVO"=>$motivo,"FDEN_DESCRIPCION"=>$comentario,"USR_ID"=>$usuario_id);

        $this->_name="FARMACIA_DENUNCIA";
        $this->insert($data);
        $this->_name="FARMACIA";
        return true;
    }

    public function comentar($farmacia_id,$usuario_id,$comentario){
        $data = array("FCOM_FECHA_REGISTRO"=>date("Y-m-d H:i:s"),"FAR_ID"=>$farmacia_id,"FCOM_TEXTO"=>$comentario,"USR_ID"=>$usuario_id);

        $this->_name="FARMACIA_COMENTARIO";
        $this->insert($data);
        $this->_name="FARMACIA";
        return true;
    }


}