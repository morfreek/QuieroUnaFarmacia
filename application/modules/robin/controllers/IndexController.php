<?php
class Robin_IndexController extends Zend_Controller_Action
{
    public function init(){
        
    }
    
    public function indexAction(){
        die();
        include(ROOT_PATH.'/../library/JUNAR/JunarApi.php');
        $authkey = '97a7766fe9d6adcd9e042d3b00ab9532c902ef48';
        $junarAPIClient = new Junar($authkey);
        
        $string = new IBB_pichoStringController();
        $comuna = new Robin_Model_Comuna();
        $datas = array("FARAR-59454","FARCO","FARAT","FARAR","FARAN","FARME","FARMA-65691","FAROH","FARGR","FARBI","FARVA","FARTA","FARMA","FARLO-37301","FARLO");
        //foreach ($datas as $aux){
        echo 'Cargando FARME<br />';
        $datastream = $junarAPIClient->datastream('FARME');
        $response = $datastream->invoke($params = array(), 'json_array');
        foreach ($response[result] as $k=>$dato){
            $comuna_nombre = $string->generar_slug($dato[1]);
            //echo $nombre_farmacia.'<br />';
            $comuna_aux = $comuna->get_by_name($comuna_nombre);
            if($comuna_aux){
                $farmacia = new Robin_Model_Farmacia();
                $farmacia_id = $farmacia->get_by_nombre($string->generar_slug($dato[2]), $comuna_aux->id);
                if($farmacia_id){
                    $farmacia->actualizar($farmacia_id);
                    echo 'La farmacia '.$dato[2].' - '.$dato[1].' existe SE ACTUALIZO<br />';
                }else{
                    $data = array(
                        'FAR_NOMBRE'=>$dato[2],
                        'FAR_DIRECCION'=>$dato[3],
                        'FAR_FECHA_ACTUALIZACION'=>date('Y-m-d H:i:s'),
                        'COM_ID'=>$comuna_aux->id,
                        'FAR_NOMBRE_SLUG'=>$string->generar_slug($dato[2])
                    );
                    $farmacia_id = $farmacia->registrar($data);
                    echo 'La farmacia '.$dato[2].' NO existe se creo<br />';
                }
                if($farmacia_id){
                    if(!$farmacia->get_turno($farmacia_id, date('Y').'-'.$dato[5].'-'.$dato[4])){
                        echo 'Turno NO existe se creo<br />';
                        if($dato[5]!=6 and $dato[4]!=31)
                        $farmacia->registrar_turno($farmacia_id, date('Y').'-'.$dato[5].'-'.$dato[4],$dato[6]);
                    }else{
                        $farmacia->actualizar_turno($farmacia_id, date('Y').'-'.$dato[5].'-'.$dato[4],$dato[6]);
                        echo 'Turno ya EXISTE <br />';
                    }
                }else{
                    echo 'ERROR 2<br />';
                }
            }
            
            //if($k>2) break; 
            
        }
        //}
        /*echo 'La farmacia de turno para su comuna es '.$farmacia_turno[2].' ubicada en '.$farmacia_turno[3].', en '.$farmacia_turno[1];*/
    }
    
    public function comunasAction(){
        $this->_helper->viewRenderer->setNoRender();
        $comuna = new Robin_Model_Comuna();
        $comuna->homologar();
    }
    
    public function farmaciasAction(){
        $comuna_nombre = ($this->_getParam('c')) ? $this->_getParam('c'):'concepcion';
        $comuna_aux = new Robin_Model_Comuna();
        $comuna = $comuna_aux->get_by_name($comuna_nombre);
        $farmacias = new Robin_Model_Farmacia();
        $this->view->listado_farmacias = $farmacias->listar($comuna->id);
        unset($farmacias);
    }
    
    
}