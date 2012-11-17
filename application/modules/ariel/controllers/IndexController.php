<?php
function debug($data,$debug=false){
    if(is_object($data) or is_array($data)){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }else{
        echo "<p>$data</p>";
    }
    ($debug)?exit:NULL;
}
class Ariel_IndexController extends Zend_Controller_Action{
    public function init(){}
    public function indexAction(){
        $this->_helper->viewRenderer->setNoRender();
        // die();
        include(ROOT_PATH.'/../library/JUNAR/JunarApi.php');
        $authkey = '97a7766fe9d6adcd9e042d3b00ab9532c902ef48';
        $junarAPIClient = new Junar($authkey);
        
        $string = new IBB_pichoStringController();
        $comuna = new Robin_Model_Comuna();
        $datas = array("MEDBI-62926");
        foreach ($datas as $aux){
            // echo 'Cargando FFARLO<br />';
            $datastream = $junarAPIClient->datastream($aux);
            $response = $datastream->invoke($params = array(), 'json_array');
            $result = $response[result];
            unset($result[0]);
            foreach ($result as $k=>$dato){
                $tratamiento_slug = $string->generar_slug($dato[7]);
                $tratamiento = new Ariel_Model_Tratamiento();
                $tratamiento_id = $tratamiento->get_id_by_name($dato[7],$tratamiento_slug);

                $p_activo_slug = $string->generar_slug($dato[1]);
                $p_activo = new Ariel_Model_PActivo();
                $p_activo_id = $p_activo->get_id_by_name($dato[1],$p_activo_slug);

                $laboratorio_slug = $string->generar_slug($dato[4]);
                $laboratorio = new Ariel_Model_Laboratorio();
                $laboratorio_id = $laboratorio->get_id_by_name($dato[4],$laboratorio_slug);

                unset($tratamiento, $p_activo, $laboratorio);
                // $dates = new IBB_pichoDateController($dato[6]);
                if($tratamiento_id and $p_activo_id and $laboratorio_id){
                    $medicamento = new Ariel_Model_Medicamento();
                    $medicamento_id = $medicamento->get_by_nombre($string->generar_slug($dato[2]), $p_activo_id, $laboratorio_id, $tratamiento_id);
                    if($medicamento_id){
                        $medicamento->actualizar($medicamento_id);
                        echo 'El medicamento '.$dato[2].' existe SE ACTUALIZO<br />';
                    }else{
                        $fecha_resolucion = array();
                        foreach(explode("-",str_replace("/","-",$dato[6])) as $k=>$aux){
                            $fecha_resolucion[(($k==0)?1:(($k==1)?0:$k))]= (($k==2)?"20".$aux:(($aux<10)?"0".$aux:$aux));
                        }
                        krsort($fecha_resolucion);
                        $data = array(
                            'PACT_ID'=>$p_activo_id,
                            'LAB_ID'=>$laboratorio_id,
                            'TRA_ID'=>$tratamiento_id,
                            'MED_PRODUCTO'=>$dato[2],
                            'MED_NOMBRE_SLUG'=>$string->generar_slug($dato[2]),
                            'MED_REGISTRO'=>$dato[3],
                            'MED_RESOLUCION'=>$dato[5],
                            'MED_FECHA_RESOLUCION'=>implode("-",$fecha_resolucion),
                            'MED_FECHA_ACTUALIZACION'=>date("Y-m-d H:i:s")
                        );
                        $medicamento->registrar($data);
                        echo 'El medicamento '.$dato[2].' NO existe se creo<br />';
                    }
                }
            }

            unset($medicamento);
            $medicamento = new Ariel_Model_Medicamento(1);
            debug($medicamento);
        }
        /*echo 'La farmacia de turno para su comuna es '.$farmacia_turno[2].' ubicada en '.$farmacia_turno[3].', en '.$farmacia_turno[1];*/
    }

    public function parseAction(){
        $this->_helper->viewRenderer->setNoRender();
        // debug($_SERVER);
        $ip = $_SERVER[REMOTE_ADDR];
        $d = file_get_contents("http://api.hostip.info/get_html.php?ip=".$ip);
        
        $pattern = '/City:(.*)/';
		preg_match($pattern, $d,$coincidencias);
		debug($coincidencias[1],true);
        
        print "<pre>";
        print_r($d);
        // preg_match('city:/[^.] IP%', $d, $matches);
        // print_r($matches);

        // $this->city = $matches;
    }
}