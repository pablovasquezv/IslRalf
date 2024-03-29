<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_CentroTrabajo extends Controller_Website {

    public function action_obtener_centro_trabajo() {
        $urlRestObtenerListaCompletaCT = Kohana::$config->load('ws_adminCT.obtenerListaCompletaCT');
        $urlCrearCentroTrabajo = Kohana::$config->load('ws_adminCT.crearCentroTrabajo');
        $urlObtenerListasCTPorEmpresaRutCT = Kohana::$config->load('ws_adminCT.obtenerListasCTPorEmpresaRutCT');

        $this->auto_render = false;
        $rut_empleador = $this->request->param('id');
        $xml_id = $this->request->param('id2');

        $xml = ORM::factory('Xml')->where('XML_ID','=',$xml_id)->find();
        $caso_id = $xml->CASO_ID;

        $comunas = Model_St_Comuna::obtener();
        $default["rut_empleador"] = "";

        $default["caso_id"] = "";
        $default["xml_id"] = "";

        $default["nuevo_tipo_empresa_ct"] = "";

        $default["nuevo_tipo_calle_ct"] = "";
        $default["nuevo_calle_ct"] = "";
        $default["nuevo_numero_ct"] = "";
        $default["nuevo_comuna_ct"] = "";

        $default["nuevo_latitud_ct"] = "";
        $default["nuevo_longitud_ct"] = "";

        $default["nuevo_desc_act_trab"] = "";
        $default["nuevo_num_trab_total"] = "";
        $default["nuevo_num_trab_hombre"] = "";
        $default["nuevo_num_trab_mujer"] = "";

        $default["desc_act_trab"] = "";
        $default["num_trab_total"] = "";
        $default["num_trab_hombre"] = "";
        $default["num_trab_mujer"] = "";
        $default["fecha_inicio_nuevo_ct"] = "";
        $default["fecha_termino_nuevo_ct"] = "";

        $default["tiene_fecha_termino_nuevo_ct"] = "";
        $default["experto_prevencion_riesgos"] = "";

        $errors = "";
        $mensaje_error = "";

        $xmlRalf1 = ORM::factory('Xml')->where('CASO_ID', '=',$caso_id)->where('TPXML_ID','=','141')->find();
        $ralf1 = ORM::factory('Ralf1')->where('xml_id','=',$xmlRalf1->XML_ID)->find();

        $this->response->body (
            View::factory('centroTrabajo/buscar_ct')
                ->set('errors', $errors)
                ->set('default', $default)
                ->set('rut_empleador', $rut_empleador)
                ->set('mensaje_error', $mensaje_error)
                ->set('urlRestObtenerListaCompletaCT', $urlRestObtenerListaCompletaCT)
                ->set('comunas',$comunas)
                ->set('urlCrearCentroTrabajo', $urlCrearCentroTrabajo)
                ->set('urlObtenerListasCTPorEmpresaRutCT', $urlObtenerListasCTPorEmpresaRutCT)
                ->set('caso_id', $caso_id)
                ->set('xml_id', $xml_id)
                ->set('correo_electronico_informante_oa',  $ralf1->correo_electronico_informante_oa)

        );
    }

    public function action_crear_ct(){

        if (isset($_POST)) {
            if(isset ($_POST['boton_crear_medida'])) {
                $post = Validation::factory($_POST)
                        ->rule('correlativoProyectoContrato', 'not_empty')->label('correlativoProyectoContrato', 'Correlativo Proy/Contr')
                        ->rule('cuv', 'not_empty')->label('cuv', 'CUV')
                        ->rule('descripcionActividadTrabajadoresCt', 'not_empty')->label('descripcionActividadTrabajadoresCt', 'Act. Trabajadores')
                        ->rule('estadoCentroTrabajo', 'not_empty')->label('estadoCentroTrabajo', 'Estado')
                        ->rule('geoLatitud', 'not_empty')->label('geoLatitud', 'Latitud')
                        ->rule('geoLongitud', 'not_empty')->label('geoLongitud', 'Longitud')
                        ->rule('localidadCt', 'not_empty')->label('localidadCt', 'Localidad')
                        ->rule('nombreCalleCt', 'not_empty')->label('nombreCalleCt', 'Calle')
                        ->rule('nombreCentroTrabajo', 'not_empty')->label('nombreCentroTrabajo', 'Nombre CT')
                        ->rule('nombreEmpleadorPrincipal', 'not_empty')->label('nombreEmpleadorPrincipal', 'Nombre Empleador')
                        ->rule('numeroCt', 'not_empty')->label('numeroCt', 'Numero')
                        ->rule('rutEmpleadorPrincipal', 'not_empty')->label('rutEmpleadorPrincipal', 'RUT Empleador Princ.')
                        ->rule('tieneFechTerm', 'not_empty')->label('tieneFechTerm', 'Tiene Fecha Termino')
                        ->rule('tipoCalleCt', 'not_empty')->label('tipoCalleCt', 'Tipo Calle');

                if($_POST["estadoCentroTrabajo"] == 1) {
                    $post = $post->rule('tipoEmpresa', 'not_empty')->label('tipoEmpresa', 'Tipo Empresa')
                            ->rule('nTrabajadoresPropiosCt', 'not_empty')->label('nTrabajadoresPropiosCt', 'Total Trabajadores')
                            ->rule('nTrabajadoresPropiosCt','Utiles::nonNegativeInteger',array(':value'))
                            ->rule('nTrabajadoresHombreCt', 'not_empty')->label('nTrabajadoresHombreCt', 'Num. Trabajadores H.')
                            ->rule('nTrabajadoresMujerCt', 'not_empty')->label('nTrabajadoresMujerCt', 'Num. Trabajadores M.')
                            ->rule('comParConstituido', 'not_empty')->label('comParConstituido', 'Comite Paritario')
                            ->rule('expertoPrevencionRiesgos', 'not_empty')->label('expertoPrevencionRiesgos', 'Prevencionista Experto')

                            ->rule('fechaInicioCt', 'not_empty')->label('fechaInicioCt', 'Fecha Inicio CT')
                            ->rule('fechaInicioCt', 'date')
                            ->rule('fechaInicioCt','Utiles::validateDate',array(':value'))
                            ;

                    if($_POST["nTrabajadoresPropiosCt"] < 1) {
                        $post = $post->rule('nTrabajadoresPropiosCt', 'not_empty')->label('horasSemanaDedicaCt', 'Hrs. Dedica CT');
                    }

                    if($_POST["tieneFechTerm"] == 1) {
                        $post = $post->rule('fechaTerminoCt', 'not_empty')->label('fechaTerminoCt', 'Fecha Termino CT')
                                ->rule('fechaTerminoCt', 'date')
                                ->rule('fechaTerminoCt','Utiles::validateDate',array(':value'));

                        if(!empty($_POST["tieneFechTerm"])) {
                            if(!($_POST["fechaInicioCt"]>=$_POST["tieneFechTerm"])) {
                                $errors = $errors + array("tieneFechTerm"=>"Fecha termino debe ser Mayor o igual a fecha de inicio");
                            }
                        }
                    }

                    if($_POST["expertoPrevencionRiesgos"] == 1 || $_POST["estadoCentroTrabajo"] == 1){
                        $post=$post->rule('horasSemanaDedicaCt', 'not_empty')->label('horasSemanaDedicaCt', 'Hrs. Dedica CT');
                    }

                }

            }
        }
    }

    public function action_obtener_comunas() {
        $this->auto_render = false;
        $comunas = Model_St_Comuna::obtener();

        $this->response->body(json_encode($comunas));
    }

    public function action_obtener_datos_ingreso_ct() {

        $this->auto_render = false;

        $rut_empleador  = $this->request->post('rut_empleador');
        $caso_id        = $this->request->post('caso_id');
        $xml_id         = $this->request->post('xml_id');

        $fecha_actual = strtotime("now");

        $parametrosNuevoCt = array();

        $parametrosNuevoCt['idSistema'] = 3;

        //obtener datos del caso
        $caso = ORM::factory('Caso')->where('CASO_ID','=',$caso_id)->find();
        $xmlRalf1 = ORM::factory('Xml')->where('CASO_ID', '=',$caso_id)->where('TPXML_ID','=','141')->where('ESTADO','!=','3')->find();
        $xmlRalf2 = ORM::factory('Xml')->where('CASO_ID', '=',$caso_id)->where('TPXML_ID','=','142')->where('ESTADO','!=','3')->find();
        $xmlRalf3 = ORM::factory('Xml')->where('CASO_ID', '=',$caso_id)->where('TPXML_ID','=','143')->where('ESTADO','!=','3')->find();

        //obtener información de los ralf anteriores
        $ralf1 = ORM::factory('Ralf1')->where('xml_id','=',$xmlRalf1->XML_ID)->find();
        $ralf3 = ORM::factory('Ralf3')->where('xml_id','=',$xmlRalf3->XML_ID)->find();
        $ralf2 = ORM::factory('Ralf2')->where('xml_id','=',$xmlRalf2->XML_ID)->find();

        //ZID
        $parametrosNuevoCt['folio']                     = $xml_id;
        $parametrosNuevoCt['fechaEmision']              = $fecha_actual;
        $parametrosNuevoCt['rutProfesionalOa']          = $ralf1->rut;
        $parametrosNuevoCt['apellidopatProfesionalOa']  = $ralf1->apellido_paterno;
        $parametrosNuevoCt['apellidomatProfesionalOa']  = $ralf1->apellido_materno;
        $parametrosNuevoCt['nombresProfesionalOa']      = $ralf1->nombres;
        $parametrosNuevoCt['correoProfesionalOa']       = $ralf1->correo_electronico_informante_oa;

        $empleador = ORM::factory('Empleador')->where('rut_empleador','=',$rut_empleador)->find();

        //ZEM
        $parametrosNuevoCt['rutEmpleador']                  = $rut_empleador;
        $parametrosNuevoCt['razonSocial']                   = str_replace('&', '&amp;', $empleador->nombre_empleador);
        $parametrosNuevoCt['tipoCalle']                     = $empleador->tipo_calle;
        $parametrosNuevoCt['nombreCalle']                   = $empleador->nombre_calle;
        $parametrosNuevoCt['numero']                        = $empleador->numero;
        $parametrosNuevoCt['restoDireccion']                = trim($empleador->resto_direccion);
        $parametrosNuevoCt['localidad']                     = $empleador->localidad;
        $parametrosNuevoCt['comuna']                        = $empleador->comuna;
        $parametrosNuevoCt['ciiuEmpleadorEvaluado']         = $empleador->ciiu_empleador;
        $parametrosNuevoCt['ciiuGiroEmpleadorEvaluado']     = $empleador->ciiu_texto;
        $parametrosNuevoCt['caracterOrganizacion']          = $empleador->propiedad_empresa;
        $parametrosNuevoCt['nTrabajadoresPropios']          = (is_null($empleador->n_trabajadores) || $empleador->n_trabajadores == "") ? 0 : $empleador->n_trabajadores;
        $parametrosNuevoCt['nTrabajadoresHombre']           = (is_null($empleador->n_trabajadores_hombre) || $empleador->n_trabajadores_hombre == "") ? 0 : $empleador->n_trabajadores_hombre;
        $parametrosNuevoCt['nTrabajadoresMujer']            = (is_null($empleador->n_trabajadores_mujer) || $empleador->n_trabajadores_mujer == "") ? 0 : $empleador->n_trabajadores_mujer;;
        $parametrosNuevoCt['reglamHigSeg']                  = $ralf3->reg_ohys_al_dia;

        if($ralf3->reg_ohys_al_dia == 1) {
            $parametrosNuevoCt['reglamHigSegAgenRies']          = 3;
            $parametrosNuevoCt['reglamOrdSeg']                  = 1;
            $parametrosNuevoCt['reglamOrdSegAgenRies']          = 3;
        } else {
            $parametrosNuevoCt['reglamOrdSeg']                  = 2;
        }

        $parametrosNuevoCt['deptoPrevRiesgos']              = $ralf3->depto_pre_rie_real;

        //ZCT
        $parametrosNuevoCt['estadoCentroTrabajo']                   = 1;
        $parametrosNuevoCt['rutEmpleadorPrincipal']                 = $rut_empleador;
        $parametrosNuevoCt['nombreEmpleadorPrincipal']              = str_replace('&', '&amp;', $empleador->nombre_empleador);
        $parametrosNuevoCt['correlativoProyectoContrato']           = 1;
        $parametrosNuevoCt['comParConstituido']                     = $ralf3->exist_comites_lugar_acc;
        $parametrosNuevoCt['expertoPrevencionRiesgos']              = $ralf3->invest_es_experto;

        //ZPP
        $parametrosNuevoCt['presenciaPeligro']      = 1;
        $parametrosNuevoCt['fechaDeteccionPeligro'] = $ralf2->fecha_notificacion_medidas_inmediatas;
        $parametrosNuevoCt['origen']                = 3;
        $parametrosNuevoCt['cun']                   = $caso->CASO_CUN;

        header('Content-type: application/json; charset=utf-8');
        $this->response->body(json_encode($parametrosNuevoCt));
    }
    
    public function action_ws_crear_ct() {
        $this->auto_render = false;
        
        $urlCrearCentroTrabajo = Kohana::$config->load('ws_adminCT.crearCentroTrabajo');
        $json_ct = $this->request->post('json_ct');//print_r($json_ct);die();
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_PORT => "8080",
        CURLOPT_URL => $urlCrearCentroTrabajo,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $json_ct,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        header('Content-type: application/json; charset=utf-8');
        $this->response->body($response);
        
    }

}
