<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Caso extends Controller_Website {

    public function action_index() {
        //print_r("MOSTRAR");
        if ($this->get_rol() == 'admin') {
            $this->template->mensaje_error = NULL;
            $casos = ORM::factory('Caso')->where('ESTADO', '=', 'activo')->find_all();
            $this->template->contenido = View::factory('caso/admin_index')
                                            ->set('casos', $casos);
        } else {
            $user = $this->get_usuario();
            $casos = ORM::factory('Caso')
                    ->where('ESTADO', '=', 'activo')
                    ->where('REGION_ID', '=', $user->region_id)
                    ->find_all();
            //echo Database::instance()->last_query;die();
            $this->template->mensaje_error = NULL;
            $this->template->contenido = View::factory('caso/index')
                                            ->set('casos', $casos)
                                            ->set('user', $user)
                                            ->set('usuario_region', $this->get_user_region());
        }
    }
    public function action_filtrado() {
        $filtro = $this->request->post('filtro_origen_comun');
        if ($this->get_rol() == 'admin') {
            $this->template->mensaje_error = NULL;
            if($filtro == 0){
                $casos = ORM::factory('Caso')
                    ->where('ESTADO', '=', 'activo')
                    ->find_all();
            }else{
                if($filtro == 1){
                    $casos = ORM::factory('Caso')
                            ->where('ESTADO', '=', 'activo')
                            ->where('ORIGEN_COMUN', '=', "SI")
                            ->find_all();
                }else{
                    $casos = ORM::factory('Caso')
                            ->where('ESTADO', '=', 'activo')
                            ->where('ORIGEN_COMUN', '=', "NO")
                            ->find_all();
                }
            }
            $this->template->contenido = View::factory('caso/admin_index')
                                            ->set('casos', $casos);
        } else {
            $user = $this->get_usuario();
            if($filtro == 0){
                $casos = ORM::factory('Caso')
                    ->where('ESTADO', '=', 'activo')
                    ->where('REGION_ID', '=', $user->region_id)
                    ->find_all();
            }else{
                if($filtro == 1){
                    $casos = ORM::factory('Caso')
                            ->where('ESTADO', '=', 'activo')
                            ->where('REGION_ID', '=', $user->region_id)
                            ->where('ORIGEN_COMUN', '=', "SI")
                            ->find_all();
                }else{
                    $casos = ORM::factory('Caso')
                            ->where('ESTADO', '=', 'activo')
                            ->where('REGION_ID', '=', $user->region_id)
                            ->where('ORIGEN_COMUN', '=', "NO")
                            ->find_all();
                }
            }
            
            //echo Database::instance()->last_query;die();
            $this->template->mensaje_error = NULL;
            $this->template->contenido = View::factory('caso/index')
                                            ->set('casos', $casos)
                                            ->set('user', $user)
                                            ->set('usuario_region', $this->get_user_region());
        }
    }
    //FUNCION QUE BUSCA CASO POR CUN
    public function action_buscar_caso() {
        $user = $this->get_usuario();
        if ($this->get_rol() != 'operador') {
            $this->redirect("error");
        }

        $errors = array();
        $mensaje_error = null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            $post = Validation::factory($_POST)
                        ->rule('cun', 'not_empty')
                        ->rule('cun', 'numeric')
                        ->rule('cun', 'min_length', array(':value', 4))
                        ->rule('cun', 'max_length', array(':value', 9))
            ;

            if ($post->check()) {
                $caso = ORM::factory('Caso')->where('CASO_CUN', '=', $post['cun'])->find();
                //echo Database::instance()->last_query;die();
                if ($caso->loaded()) {
                    if ($caso->REGION_ID == $user->region_id) {
                        if ($caso->ESTADO == 'activo') {
                            $this->redirect("caso/ver_caso/{$caso->CASO_ID}");
                        } elseif ($caso->ESTADO == 'anulado') {
                            $errors["cun"] = "El Cun {$caso->CASO_CUN} ha sido anulado!";
                        $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                        } else {
                            $this->redirect("caso/ingresar/{$caso->CASO_ID}");
                        }
                    } else {
                        $errors["cun"] = "El Caso {$caso->CASO_ID} pertenece a otra región";
                        $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                    }
                } else {
                    $response = Ws_Spm::busca_caso($post['cun']);
                    $response = $response + array('cun' => $post['cun']);
                    if ($response['xml']) {
                        $xml = simplexml_load_string($response['xml']);
                        $region = Ws_Spm::region_id($xml);

                        if ($region != $user->region_id) {
                            $errors["cun"] = "El Caso {$caso->CASO_ID} pertenece a otra región";
                            $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                        } else {
                            $result = Ws_Spm::ingresa_caso($response); //print_r($result);die();
                            if ($result['r']) {
                                $caso = ORM::factory('Caso')->where('CASO_CUN', '=', $post['cun'])->find();
                                $this->redirect("caso/ingresar/{$caso->CASO_ID}");
                            } else {
                                $errors["cun"] = "Error al ingresar caso desde SPM. ";
                                $errors["cun"] = $result['mje'];
                                $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                            }
                        }
                    } else {
                        $errors["cun"] = "Caso no encontrado";
                        $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                    }
                }
            } else {
                $errors = $post->errors('validate');
                $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                //echo Debug::vars($mensaje);
            }
        }
        //$this->template->titulo = __('Buscar caso');
        $this->template->mensaje_error = $mensaje_error;
        $this->template->contenido = View::factory('caso/buscar_caso')->set('errors', $errors);
    }

    public function action_ver_caso() {
        if ($this->get_rol() != 'operador') {
            $this->redirect("error");
        }

        $caso_id = $this->request->param('id');
        $mensaje_error = NULL;
        if (empty($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error = 'Error, Falta Id de caso';
            $this->template->contenido = '';
            return;
        }

        $caso = ORM::factory('Caso', $caso_id);
        if (!$caso->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar caso';
            $this->template->contenido = '';
            return;
        }

        if ($caso->ESTADO == 'inactivo') {
            $this->template->mensaje_error = 'Error, Debe ingresar el Caso';
            $this->template->contenido = '';
            return;
        }

        $this->template->mensaje_error = $mensaje_error;
        $this->template->titulo = __('Ver Caso');
        $this->template->contenido = View::factory('caso/ver_caso')->set('caso', $caso)->set('usuario_region', $this->get_user_region());
    }

    public function action_ver_caso_admin() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $caso_id = $this->request->param('id');
        $mensaje_error = NULL;
        if (empty($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error = 'Error, Falta Id de caso';
            $this->template->contenido = '';
            return;
        }

        $caso = ORM::factory('Caso', $caso_id);
        if (!$caso->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar caso';
            $this->template->contenido = '';
            return;
        }

        $this->template->mensaje_error = $mensaje_error;
        $this->template->titulo = __('Ver Caso');
        $this->template->contenido = View::factory('caso/ver_caso_admin')->set('caso', $caso);
    }

    public function action_ingresar() {
        if ($this->get_rol() != 'operador') {
            $this->redirect("error");
        }

        $caso_id = $this->request->param('id');
        $mensaje_error = NULL;
        if (empty($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error = 'Error, Falta Id de caso';
            $this->template->contenido = '';
            return;
        }

        $caso = ORM::factory('Caso', $caso_id);
        if (!$caso->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar caso';
            $this->template->contenido = '';
            return;
        }

        $denuncia = $caso->xmls->where('TPXML_ID', '<', 12)->find();

        $xml_denuncia = simplexml_load_string($denuncia->xmlstring->XMLSTRING);
        $rutEstructuraOld = isset($xml_denuncia->ZONA_C->empleado->trabajador->rut);

        if ($rutEstructuraOld) {
            $xml_denuncia = Documento::transformarZonaCNueva($xml_denuncia);
        }

        if ($denuncia->TPXML_ID == 1) {
            $dir = dirname(__FILE__) . '/../../../media/xsd/SIATEP_DIAT_OA_1.0.xsd';
        }

        if ($denuncia->TPXML_ID == 3) {
            $dir = dirname(__FILE__) . '/../../../media/xsd/SIATEP_DIAT_EM_1.0.xsd';
        }

        if ($denuncia->TPXML_ID == 5) {
            $dir = dirname(__FILE__) . '/../../../media/xsd/SIATEP_DIAT_OT_1.0.xsd';
        }

        $valido = Utiles::valida_xml($xml_denuncia->saveXML(), $dir);
        if (!$valido['estado']) {
            $denuncia->VALIDO = 0;
        }
        $denuncia->save();

        $errors = array();
        $mensaje_error = null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if (isset($_POST['boton_ingresar'])) {
                $caso->ESTADO = 'activo';
                $caso->save();
                $this->redirect("caso/ver_caso/{$caso->CASO_ID}");
            }
        }

        $this->template->mensaje_error = $mensaje_error;
        $this->template->titulo = __('Ver Caso');
        $this->template->contenido = View::factory('caso/ingresar')
                                        ->set('caso', $caso)
                                        ->set('usuario_region', $this->get_user_region());
    }
    
    public function action_anular_documento() {
        $this->auto_render = false;
        $caso_id = $this->request->param('id');
        $xml_id = $this->request->param('id2');
        
        $caso = ORM::factory('Caso')->where('CASO_ID','=', $caso_id)->find();
        $cun = $caso->CASO_CUN;
        
        $docs = ORM::factory('Xml')
                ->where('CASO_ID','=', $caso_id)
                ->where('ESTADO','!=', 3)
                ->order_by('XML_ID', 'DESC')
                ->limit(1)
                ->find();
        
        if($docs->XML_ID == $xml_id) {
            $documento = ORM::factory('Xml', $xml_id);

            $borrado = false;
            $res = array(
                'return' => '',
                'error_message' =>''
            );
            if(isset ($_POST['boton_aceptar'])) {
                $documento->ESTADO = 3;
                $documento->save();
                $borrado = true;
                
                $response = Suseso::anular_ws($docs->tipo_xml->TPXML_ID, $cun, $xml_id);
                $res['return'] = $response['return'];
                $res['error_message'] = $response['error_message'];
                
                if($response['return'] != '-40') {
                    $intento_envio = ORM::factory('Intento_Envios');
                    $intento_envio->XML_ID = $xml_id;
                    $intento_envio->codigo_retorno = $response['return'];
                    $intento_envio->retorno_completo = "transaction ".$response['transaction']." ".$response['error_message'];
                    $intento_envio->save();
                }
            }

            $this->response->body (
                View::factory('caso/anular_documento')
                    ->set('documento', $documento)
                    ->set('borrado', $borrado)
                    ->set('xml_id', $xml_id)
                    ->set('res', $res)
            );
        } else {
            $this->response->body (
                View::factory('caso/error_anular_documento')
            );
        }
    }

    public function action_termino_anticipado() {
        if ($this->get_rol() != 'operador') {
            $this->redirect("error");
        }

        $caso_id = $this->request->param('id');
        $mensaje_error = NULL;
        if (empty($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error = 'Error, Falta Id de caso';
            $this->template->contenido = '';
            return;
        }

        $caso = ORM::factory('Caso', $caso_id);
        if (!$caso->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar caso';
            $this->template->contenido = '';
            return;
        }

        if ($caso->ESTADO == 'inactivo') {
            $this->template->mensaje_error = 'Error, Debe ingresar el Caso';
            $this->template->contenido = '';
            return;
        }
        //Traer Select para opciones del Termino Anticipado
        $lista_tipo_termino = ORM::factory('TipoTermino')->where('vigencia','=','SI')->where('edoc','=',$caso->ultimo_tipo_documento())->find_all();
        $this->template->mensaje_error = $mensaje_error;
        $this->template->titulo = __('Termino Anticipado');
        $this->template->contenido = View::factory('caso/termino_anticipado')->set('caso', $caso)->set('usuario_region', $this->get_user_region())->set('lista_tipo_termino', $lista_tipo_termino);     
    }
    
    public function guardar_termino_anticipado($id_caso,$id_tipo_termino){

        $termino_anticipado_insert=ORM::factory('TerminoAnticipado');
        $termino_anticipado_insert->ID_TIPO_TERMINO = $id_tipo_termino;
        $termino_anticipado_insert->CASO_ID = $id_caso;
        $termino_anticipado_insert->save();
        
    }

    public function action_terminar_caso() {
        if ($this->get_rol() != 'operador') {
            $this->redirect("error");
        }

        $caso_id = $this->request->param('id');
        $tipo_termino_id =  $this->request->param('id2');

        $mensaje_error = NULL;
        if (empty($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error = 'Error, Falta Id de caso';
            $this->template->contenido = '';
            return;
        }

        $caso = ORM::factory('Caso', $caso_id);
        if (!$caso->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar caso';
            $this->template->contenido = '';
            return;
        }

        if (empty($tipo_termino_id) || !is_numeric($tipo_termino_id)) {
            $this->template->mensaje_error = 'Error, Falta Id del Tipo Caso';
            $this->template->contenido = '';
            return;
        }

        //INVOCAR EL INSERT A LA BD
        Controller_Caso::guardar_termino_anticipado($caso_id, $tipo_termino_id);

        $prueba_servicio = Controller_Caso::prueba_servicio();
        //UPDATEAR CASO A TERMINADO
        $caso->ESTADO = 'terminado';
        $caso->save();

        //Traer el termino anticipado seleccionado
        $tipo_termino = ORM::factory('TipoTermino')->where('id_tipo_termino','=',$tipo_termino_id)->find();

        $descripcion_tipo = $tipo_termino->DESCRIPCION;

        $this->template->mensaje_error = $mensaje_error;
        $this->template->titulo = __('Termino Anticipado');
        $this->template->contenido = View::factory('caso/terminar_caso')->set('caso', $caso)->set('usuario_region', $this->get_user_region())->set('descripcion', $descripcion_tipo)->set('prueba_servicio', $prueba_servicio);
        
        
    }


    public function prueba_servicio(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "172.16.6.177:8080/microsesat/sistema/fechaHora",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        return $response;
        //header('Content-type: application/json; charset=utf-8');
        //$this->response->body($response);
        
    }

    public function action_ingreso_visita(){
        $caso_id = $this->request->param('id');
        $xml_id =  $this->request->param('id2');
        $usuario = Auth::instance()->get_user();

        $xml_string = ORM::factory('Xml', $xml_id);

        $xmlstring_id = $xml_string->XMLSTRING_ID;

        $caso = ORM::factory('Caso', $caso_id);
        $tipo_ralf = $caso->ultimo_tipo_documento();
        
        if($tipo_ralf == 141){
            $texto_ralf = "RALF_Accidente";
        }else if($tipo_ralf == 142){
            $texto_ralf = "RALF_Medidas";
        }else if($tipo_ralf == 145){
            $texto_ralf = "RALF_Prescripcion";
        }else if($tipo_ralf == 146){
            $texto_ralf = "RALF_Verificacion";
        }   

        //EJECUTAR SERVICIO
        
        //IP DEV
        //$ip_servicio = "172.16.6.124:8080/integracion-ralf/ralf";
        //IP QA
        $ip_servicio = "172.16.6.123:8080/integracion-ralf/ralf";
        //IP PROD
        //$ip_servicio = "172.16.6.161:8080/integracion-ralf/ralf";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $ip_servicio,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>"{\"username\":\"$usuario\",\"idXml\":$xmlstring_id,\"tipoRalf\": \"$texto_ralf\"}",
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
          ),
        ));        
        $response = curl_exec($curl);        
        curl_close($curl);

        $obj = json_decode($response);
        $response_estado = $obj->estado;

        if($response_estado == "ERROR"){
            $mensaje_error = $obj->mensaje;
        }else if($response_estado == "WARN"){
            $mensaje_error = $obj->mensaje;
        }else {
            $mensaje_error = $obj->mensaje;
        }

        
        $prueba_servicio = $response;
        $this->template->mensaje_error = $mensaje_error;
        $this->template->titulo = __('Servicio');        
        $back_page = URL::site('caso/ver_caso/'.$caso->CASO_ID, 'http');
        $boton_html = Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')"));
        $this->template->contenido = "<div align='right'>".$boton_html."</div>";
    }

}
