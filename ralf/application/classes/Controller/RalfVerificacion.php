<?php defined('SYSPATH') or die('No direct script access.');

class Controller_RalfVerificacion extends Controller_Website{
        
    public function action_insertar() {
        if($this->get_rol()!='operador') {              
            $this->redirect("error");
        }
        
        $caso_id = $this->request->param('id');        
        if(empty ($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error='Error, Falta id de caso';
            $this->template->contenido='';
            return;
        }
        
        $caso=ORM::factory('Caso',$caso_id);
        if(!$caso->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar caso';
            $this->template->contenido='';
            return;
        }
        //Busco un documento ralf
        $documento = $caso->xmls
                ->where('TPXML_ID','IN', array(145))
                ->where('ESTADO','IN',array(1,2))->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;  die();
        if(!$documento->loaded()) {
            $this->template->mensaje_error='Se debe agregar una RALF Prescripción.';
            $this->template->contenido='';
            return;
        }
        
        $ralf_anterior = $caso->xmls->where('TPXML_ID','=', 146)->where('ESTADO','!=', 3)->find();
        if($ralf_anterior->loaded()) {
            $this->template->mensaje_error='Error, Ya se encuentra una Ralf insertada';
            $this->template->contenido='';
            return;
        }
        //Se cargan los datos del documento       
        $xml_documento = simplexml_load_string($documento->xmlstring->XMLSTRING);
        
        //Se eliminan zonas que no se utilizaran
        $xml_documento->ZONA_A->documento->folio='';
        $fecha_creacion = date('Y-m-d');
        $hora_creacion = date('H:i:s');        
        $xml_documento->ZONA_A->documento->fecha_emision = $fecha_creacion . 'T' . $hora_creacion;
        
        //si no viene cun agregar el del caso
        if(!isset($xml_documento->ZONA_A->documento->cun))
        {
            $cun = $documento->caso->CASO_CUN;
            $dom = dom_import_simplexml($xml_documento->ZONA_A->children());
            $dom->insertBefore(
                $dom->ownerDocument->createElement('cun', $cun),
                $dom->firstChild
            );
        }
           
        if(isset($xml_documento->ZONA_PRESCRIPCION)) {
            unset($xml_documento->ZONA_PRESCRIPCION);
        }

        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion=dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'RALF_Verificacion');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());

        $zona_verificacion_string=
    "<ZONA_VERIFICACION>
        <verificacion_medidas>
            <verificacion_medida></verificacion_medida>
        </verificacion_medidas>
        <documentos_acompanan_verificacion>
            <documento_anexo>
                <nombre_documento></nombre_documento>				
                <fecha_documento></fecha_documento>
                <autor_documento></autor_documento>
                <documento></documento>
                <extension></extension>
            </documento_anexo>
        </documentos_acompanan_verificacion>
        <verificador>
            <apellido_paterno></apellido_paterno>
            <apellido_materno></apellido_materno>
            <nombres></nombres>
            <rut></rut>
        </verificador>
    </ZONA_VERIFICACION>
</RALF_Verificacion>";
        
        $ralf = str_replace('</RALF_Verificacion>', $zona_verificacion_string, $ralf_preparacion->saveXML());
        $ralf = simplexml_load_string($ralf);
        
        $zona_o = $ralf->addChild('ZONA_O', '');
        $zona_o->addChild('seguridad', 'Seguridad ISL');
        
        $xmlstring=  ORM::factory('Xmlstring');
        $xmlstring->XMLSTRING = $ralf->saveXML();
        $xmlstring->save();
        
        unset($ralf);
        $xml_insert=ORM::factory('Xml');
        $xml_insert->XMLSTRING_ID = $xmlstring->XMLSTRING_ID;
        $xml_insert->ESTADO = 5;
        $xml_insert->CASO_ID = $caso->CASO_ID;
        $xml_insert->TPXML_ID = 146;
        $xml_insert->VALIDO = 0;
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN = $documento->XML_ID;      
        $xml_insert->save();
        
        $doc=simplexml_load_string($xmlstring->XMLSTRING);        
        $doc->ZONA_A->documento->folio = $xml_insert->XML_ID;                
        $xmlstring->XMLSTRING = $doc->saveXML();
        $xmlstring->save();              
        $this->redirect("ralfVerificacion/verificar/$xml_insert->XML_ID");                
    }
    
    public function action_verificar() {
        if($this->get_rol() != 'operador') {
            $this->redirect("error");
        }
        
        $xml_id = $this->request->param('id');

        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'error';
            return;
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'error';
            return;
        }

        if($documento->VALIDO == 1 && $documento->ESTADO != 5) {
            $this->redirect("documento/ralfVerificacion/$documento->XML_ID");
        }
        
        $documentostring = $documento->xmlstring;
        $ralf = simplexml_load_string($documentostring->XMLSTRING);
        $ralfTempBD = simplexml_load_string($documentostring->XMLSTRING);

        $estados_ct = array('Activo' => 1,'Caduco' => 2);
        $estados_ct2 = array(1 => 'Activo',2 => 'Caduco');
        $tìpos_calle = array('Avenida' => 1,'Calle' => 2,'Pasaje' => 3);
        $tipos_empresa = array('Principal' => 1,'Contratista' => 2,'Subcontratista' => 2,'De servicios transitorios' => 4);
        $comunas = Model_St_Comuna::obtener();
        $si_no = array('Si'=>1,'No'=>2);
        $cod_comuna_ct = "";
		
        $errores_esquema = NULL;
        $errors = array();
        $mensaje_error = null;
        if (isset($_POST) AND Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_finalizar'])) {
                $post = Validation::factory($this->request->post())
                        ->rule('investigador_apellido_paterno', 'Utiles::whitespace',array(':value'))
                        ->rule('investigador_apellido_paterno', 'not_empty')->label('investigador_apellido_paterno', 'Ap. paterno')
                        ->rule('investigador_apellido_materno', 'Utiles::whitespace',array(':value'))
                        ->rule('investigador_apellido_materno', 'not_empty')->label('investigador_apellido_materno', 'Ap. materno')
                        ->rule('investigador_nombres', 'Utiles::whitespace',array(':value'))
                        ->rule('investigador_nombres', 'not_empty')->label('investigador_nombres', 'Nombre')
                        ->rule('investigador_rut','Utiles::whitespace',array(':value'))
                        ->rule('investigador_rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->rule('investigador_rut', 'not_empty')->rule('investigador_rut','Utiles::rut',array(':value'))
                        ->rule('investigador_rut', 'not_empty')->label('investigador_rut', 'Rut')
                        ;

                //REGLA VALIDACION MEDIDAS CORRECTIVAS
                $medidas = ORM::factory('MedidaCorrectivaRalf145')->where('xml_id','=',$documento->XML_ID_ORIGEN)->find_all();
                //echo Database::instance()->last_query;die();
                if(count($medidas) == 0) {
                    $post = $post->rule('medidas_correctivas', 'not_empty')->label('medidas_correctivas', 'Medidas Correctivas');
                } else {
                    foreach ($medidas as $m) {
                        if($m->cumplimiento_medida == "" || $m->observacion_verificacion == ""
                                || $m->fecha_verificacion == "" || $m->fecha_cumplimiento == "") {
                            $errors['medidas_correctivas'] = 'Debe verificar todas las medidas (Folio: '.$m->id.')';
                        }
                    }
                }
                
                //REGLA VALIDACION DOCUMENTOS ADJUNTOS
                $anexos = ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();
                if(count($anexos) == 0) {
                    $post = $post->rule('documentos_anexos', 'not_empty')->label('documentos_anexos', 'Documentos Anexos');
                }
                
                if($post->check() && count($errors) == 0) {

                    if(isset($ralf->ZONA_VERIFICACION->verificacion_medidas)) {
                        unset($ralf->ZONA_VERIFICACION->verificacion_medidas);
                    }

                    if(isset($ralf->ZONA_VERIFICACION->documentos_acompanan_verificacion)) {
                        unset($ralf->ZONA_VERIFICACION->documentos_acompanan_verificacion);
                    }
                    
                    if(isset($ralf->ZONA_VERIFICACION->verificador)) {
                        unset($ralf->ZONA_VERIFICACION->verificador);
                    }
                    
                    $ralf_string = $ralf->saveXML();

                    $ralf = Controller_RalfVerificacion::verificacion_medidas($documento->XML_ID_ORIGEN, $ralf);
                    $ralf = Controller_RalfVerificacion::documentos_anexos($xml_id, $ralf);
                    
                    $ralf->ZONA_VERIFICACION->verificador->apellido_paterno = $post["investigador_apellido_paterno"];
                    $ralf->ZONA_VERIFICACION->verificador->apellido_materno = $post["investigador_apellido_materno"];
                    $ralf->ZONA_VERIFICACION->verificador->nombres          = $post["investigador_nombres"];
                    $ralf->ZONA_VERIFICACION->verificador->rut              = $post["investigador_rut"];
                
                    $ralf_bd = $ralf->saveXML();
                    
                    $ralf  = Documento::zona_o($ralf->saveXML());
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf), $documento->TPXML_ID);
                    $final = $ralf;
                    
                    $valido = Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_Verificacion.1.0.xsd');
                    if($valido['estado']) {
                        $documentostring->XMLSTRING = $ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO = 1;
                        $documento->ESTADO = 6;
                        $documento->save();

                        $RalfVerificacion = ORM::factory('RalfVerificacion')->where('xml_id','=',$xml_id)->find();
                        $medidas_correctivas = ORM::factory('MedidaCorrectivaRalf145')->where('xml_id','=',$documento->XML_ID_ORIGEN)->find_all();
                        $xmlRalf = ORM::factory('Xml')->where('xml_id','=',$xml_id)->find();
                        $casoRalf = ORM::factory('Caso')->where('CASO_ID','=',$xmlRalf->CASO_ID)->find();

                        $trabajador = ORM::factory('Trabajador')->where('TRA_ID','=',$casoRalf->TRA_ID)->find();
                        $empleador = ORM::factory('Empleador')->where('EMP_ID','=',$casoRalf->EMP_ID)->find();

                        $region = ORM::factory('Region')->where('id','=',$casoRalf->REGION_ID)->find();
                        
                        foreach ($medidas_correctivas as $mc) {
                            $RalfVerificacion->fecha_creacion = $xmlRalf->FECHA_CREACION;
                            $RalfVerificacion->trabajador_run = $trabajador->rut;
                            $RalfVerificacion->trabajador_nombres = $trabajador->nombres;
                            $RalfVerificacion->trabajador_apellido_paterno = $trabajador->apellido_paterno;
                            $RalfVerificacion->trabajador_apellido_materno = $trabajador->apellido_materno;
                            $RalfVerificacion->empresa_rut = $empleador->rut_empleador;
                            $RalfVerificacion->empresa_razon_social = $empleador->nombre_empleador;
                            $RalfVerificacion->region = $region->nombre;
                            $RalfVerificacion->xml_id = $xml_id;
                            $RalfVerificacion->descripcion = $mc->descripcion;
                            $RalfVerificacion->medida_inmediata = $mc->medida_inmediata;
                            $RalfVerificacion->plazo_cumplimiento = $mc->plazo_cumplimiento;
                            $RalfVerificacion->glosa_causa = $mc->glosa_causa;
                            $RalfVerificacion->fecha_verificacion = $mc->fecha_verificacion;
                            $RalfVerificacion->cumplimiento_medida = $mc->cumplimiento_medida;
                            $RalfVerificacion->fecha_cumplimiento = $mc->fecha_cumplimiento;
                            $RalfVerificacion->observacion_verificacion = $mc->observacion_verificacion;
                            $RalfVerificacion->verificador_rut = $post["investigador_rut"];
                            $RalfVerificacion->verificador_nombres = $post["investigador_nombres"];
                            $RalfVerificacion->verificador_apellido_paterno = $post["investigador_apellido_paterno"];
                            $RalfVerificacion->verificador_apellido_materno = $post["investigador_apellido_materno"];

                            $RalfVerificacion->save();
                        }
                   
                        $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
                    } else {
                        $ralf = simplexml_load_string($final);
                        $errores_esquema = $valido['mensaje'];
                        $mensaje_error = "Operación fallida. Hay " . count($errores_esquema) . " error(es).";
                    }                    
                } else {
                    $errors = $post->errors('validate') + $errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                };
            } elseif(isset($_POST['boton_incompleta'])) {
                $post = Validation::factory($_POST);
                
                $medida = ORM::factory('MedidaCorrectivaRalf145')->where('XML_ID','=',$documento->XML_ID_ORIGEN)->limit(1)->find();
                
                $ralf->ZONA_VERIFICACION->verificacion_medidas->verificacion_medida->fecha_verificacion = $medida->fecha_verificacion;
                $ralf->ZONA_VERIFICACION->verificacion_medidas->verificacion_medida->folio_medida_prescrita = $medida->id;
                
                $ralf->ZONA_VERIFICACION->verificacion_medidas->verificacion_medida->datos_verificacion->cumplimiento_medida = $medida->cumplimiento_medida;
                $ralf->ZONA_VERIFICACION->verificacion_medidas->verificacion_medida->datos_verificacion->observacion_verificacion = $medida->observacion_verificacion;
                $ralf->ZONA_VERIFICACION->verificacion_medidas->verificacion_medida->datos_verificacion->fecha_cumple_medida_empleador = $medida->fecha_cumplimiento;
                
                $ralf->ZONA_VERIFICACION->verificador->apellido_paterno = $post["investigador_apellido_paterno"];
                $ralf->ZONA_VERIFICACION->verificador->apellido_materno = $post["investigador_apellido_materno"];
                $ralf->ZONA_VERIFICACION->verificador->nombres = $post["investigador_nombres"];
                $ralf->ZONA_VERIFICACION->verificador->rut = $post["investigador_rut"];

                $documentostring->XMLSTRING = $ralf->saveXML();
                $documentostring->save();
                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
            }
        }

        $caso = ORM::factory('Caso', $documento->CASO_ID); 
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf;
        $data['criterio_gravedad'] = $organismo = Kohana::$config->load('dominios.STCriterio_gravedad_RALF');
        $this->template->mensaje_error = $mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralfVerificacion/verificar')
                ->set('data', $data)
                ->set('back_page', URL::site("caso/ver_caso/{$documento->CASO_ID}", 'http'))
                ->set('errors',$errors)
                ->set('default',  $this->values_default($ralf, $_POST))
                ->set('errores_esquema', $errores_esquema)
                ->set('xml_id', $xml_id)
                ->set('xml_id_origen', $documento->XML_ID_ORIGEN)
                ->set('documento', $documento)
                ->set('caso', $caso)
            ;
        
    }
    
    public function values_default($ralf, $post) {
        //echo "<pre>";print_r($ralf->ZONA_ZCT);die();
        if(empty($post)) {
            $default["investigador_apellido_paterno"]   = $ralf->ZONA_VERIFICACION->verificador->apellido_paterno;
            $default["investigador_apellido_materno"]   = $ralf->ZONA_VERIFICACION->verificador->apellido_materno;
            $default["investigador_nombres"]            = $ralf->ZONA_VERIFICACION->verificador->nombres;
            $default["investigador_rut"]                = $ralf->ZONA_VERIFICACION->verificador->rut;
            
            $default["estado_centro_trabajo"] = $ralf->ZONA_ZCT->centro_de_trabajo->estado_centro_trabajo;
            $default["rut_empleador_principal"] = $ralf->ZONA_ZCT->centro_de_trabajo->rut_empleador_principal;
            $default["nombre_empleador_principal"] = $ralf->ZONA_ZCT->centro_de_trabajo->nombre_empleador_principal;
            $default["nombre_centro_trabajo"] = $ralf->ZONA_ZCT->centro_de_trabajo->nombre_centro_trabajo;
            $default["correlativo_proyecto_contrato"] = $ralf->ZONA_ZCT->centro_de_trabajo->correlativo_proyecto_contrato;
            $default["tipo_empresa"] = $ralf->ZONA_ZCT->centro_de_trabajo->tipo_empresa;
            $default["cuv"] = $ralf->ZONA_ZCT->centro_de_trabajo->CUV;
            $default["geo_latitud"] = $ralf->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_latitud;
            $default["geo_longitud"] = $ralf->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_longitud;
            $default["tipo_calle_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->tipo_calle_ct;
            $default["nombre_calle_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->nombre_calle_ct;
            $default["numero_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->numero_ct;
            $default["resto_direccion_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->resto_direccion_ct;
            $default["localidad_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->localidad_ct;
            $default["comuna_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->comuna_ct;

            $default["descripcion_actividad_trabajadores_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->descripcion_actividad_trabajadores_ct;
            $default["n_trabajadores_propios_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_propios_ct;
            $default["n_trabajadores_hombre_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_hombre_ct;
            $default["n_trabajadores_mujer_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_mujer_ct;

            $default["com_par_constituido"] = $ralf->ZONA_ZCT->centro_de_trabajo->com_par_constituido;
            $default["experto_prevencion_riesgos"] = $ralf->ZONA_ZCT->centro_de_trabajo->experto_prevencion_riesgos;
            $default["horas_semana_dedica_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->horas_semana_dedica_ct;
            $default["fecha_inicio_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->fecha_inicio_ct;
            $default["tiene_fech_term"] = $ralf->ZONA_ZCT->centro_de_trabajo->tiene_fech_term;
            $default["fecha_termino_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->comuna_ct;

            $default["fecha_prescripcion_medida"] = $ralf->ZONA_PRESCRIPCION->fecha_prescripcion_medida;

        } else {
            $estados_ct = array('Activo' => 1,'Caduco' => 2);
            $tipos_calle = array('Avenida' => 1,'Calle' => 2,'Pasaje' => 3);
            $tipos_empresa = array('Principal' => 1,'Contratista' => 2,'Subcontratista' => 2,'De servicios transitorios' => 4);
            $comunas = Model_St_Comuna::obtenerSinFiltroLlaveGlosa();
            $si_no = array('Si'=>1,'No'=>2);

            $default["investigador_apellido_paterno"] = $post["investigador_apellido_paterno"];
            $default["investigador_apellido_materno"] = $post["investigador_apellido_materno"];
            $default["investigador_nombres"] = $post["investigador_nombres"];
            $default["investigador_rut"] = $post["investigador_rut"];
            $default["estado_centro_trabajo"] = $ralf->ZONA_ZCT->centro_de_trabajo->estado_centro_trabajo;
            $default["rut_empleador_principal"] = $ralf->ZONA_ZCT->centro_de_trabajo->rut_empleador_principal;
            $default["nombre_empleador_principal"] = $ralf->ZONA_ZCT->centro_de_trabajo->nombre_empleador_principal;
            $default["nombre_centro_trabajo"] = $ralf->ZONA_ZCT->centro_de_trabajo->nombre_centro_trabajo;
            $default["correlativo_proyecto_contrato"] = $ralf->ZONA_ZCT->centro_de_trabajo->correlativo_proyecto_contrato;
            $default["tipo_empresa"] = $ralf->ZONA_ZCT->centro_de_trabajo->tipo_empresa;
            $default["cuv"] = $ralf->ZONA_ZCT->centro_de_trabajo->CUV;
            $default["geo_latitud"] = $ralf->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_latitud;
            $default["geo_longitud"] = $ralf->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_longitud;
            $default["tipo_calle_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->tipo_calle_ct;
            $default["nombre_calle_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->nombre_calle_ct;
            $default["numero_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->numero_ct;
            $default["resto_direccion_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->resto_direccion_ct;
            $default["localidad_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->localidad_ct;
            $default["comuna_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->comuna_ct;

            $default["descripcion_actividad_trabajadores_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->descripcion_actividad_trabajadores_ct;
            $default["n_trabajadores_propios_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_propios_ct;
            $default["n_trabajadores_hombre_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_hombre_ct;
            $default["n_trabajadores_mujer_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_mujer_ct;

            $default["com_par_constituido"] = $ralf->ZONA_ZCT->centro_de_trabajo->com_par_constituido;
            $default["experto_prevencion_riesgos"] = $ralf->ZONA_ZCT->centro_de_trabajo->experto_prevencion_riesgos;
            $default["horas_semana_dedica_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->horas_semana_dedica_ct;
            $default["fecha_inicio_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->fecha_inicio_ct;
            $default["tiene_fech_term"] = $ralf->ZONA_ZCT->centro_de_trabajo->tiene_fech_term;
            $default["fecha_termino_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->comuna_ct;

            $default["fecha_prescripcion_medida"] = $ralf->ZONA_PRESCRIPCION->fecha_prescripcion_medida;
        }
        return $default;
    }
    
    public function action_medida_verificar() {    
        $this->auto_render = false;
        $mensaje_error = "";
        $errors = array();
        
        $medida_id = $this->request->param('id');
        $medida = ORM::factory('MedidaCorrectivaRalf145', $medida_id);
        //echo Database::instance()->last_query;die();
        $xml_id = $medida->xml_id;        
        $borrado=false;
        
        $default["fecha_verificacion"] = (is_null($medida->fecha_verificacion)) ? "" : date("Y-m-d",strtotime($medida->fecha_verificacion));
        $default["fecha_cumplimiento"] = (is_null($medida->fecha_cumplimiento)) ? "" : date("Y-m-d",strtotime($medida->fecha_cumplimiento));
        $default["cumplimiento_medida"] = $medida->cumplimiento_medida;
        $default["observacion_verificacion"] = $medida->observacion_verificacion;
        
        if (isset($_POST)) {
            if(isset ($_POST['boton_verificar_medida'])) {
                $post = Validation::factory($_POST)
                ->rule('cumplimiento_medida', 'not_empty')->label('cumplimiento_medida', 'Cumplimiento Medida')
                ->rule('observacion_verificacion', 'not_empty')->label('observacion_verificacion', 'Observación Verificación')
                ->rule('fecha_verificacion', 'date')
                ->rule('fecha_verificacion','Utiles::validateDate',array(':value')) 
                ->rule('fecha_verificacion','Utiles::fecha_minima',array(':value'))
                ->rule('fecha_verificacion', 'not_empty')->label('fecha_verificacion', 'Fecha Verificación')
                ->rule('fecha_cumplimiento', 'date')
                ->rule('fecha_cumplimiento','Utiles::validateDate',array(':value')) 
                ->rule('fecha_cumplimiento','Utiles::fecha_minima',array(':value'))
                ->rule('fecha_cumplimiento', 'not_empty')->label('fecha_cumplimiento', 'Fecha Cumplimiento')
                ;

                if(!empty($_POST["fecha_verificacion"]) && !empty($_POST["fecha_cumplimiento"])) {
                    if($_POST["fecha_verificacion"] > $_POST["fecha_cumplimiento"]) {
                        $errors = $errors+array("fecha_cumplimiento"=>"Fecha cumplimiento debe ser mayor o igual a fecha de verificación");
                    }
                }
                
                if($post->check() && count($errors) == 0) {
                    $cmc = ORM::factory('MedidaCorrectivaRalf145', $medida_id);
                    $cmc->fecha_verificacion = $post['fecha_verificacion'];
                    $cmc->cumplimiento_medida = $post['cumplimiento_medida'];
                    $cmc->fecha_cumplimiento = $post['fecha_cumplimiento'];
                    $cmc->observacion_verificacion = $post['observacion_verificacion'];
                    
                    $cmc->save();
                    $mensaje_error = "Verificación agregada";
                } else {
                    $default["fecha_verificacion"] = $post['fecha_verificacion'];
                    $default["fecha_cumplimiento"] = $post['fecha_cumplimiento'];
                    $default["cumplimiento_medida"] = $post['cumplimiento_medida'];
                    $default["observacion_verificacion"] = $post['observacion_verificacion'];
                    
                    $errors = $post->errors('validate') + $errors;                                    
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }
        
        $this->response->body(
            View::factory('ralfVerificacion/verificar_medida')
                ->set('errors', $errors)
                ->set('mensaje_error',$mensaje_error)
                ->set('default', $default)
                ->set('medida_id', $medida_id)
                ->set('tipo', $medida->tipo)
                ->set('descripcion', $medida->descripcion)
                ->set('glosa_causa', $medida->glosa_causa)
                ->set('codigo_causa', $medida->codigo_causa)
                ->set('borrado', $borrado)
                ->set('xml_id', $xml_id)
        );
    }
    
    public static function verificacion_medidas($xml_id_origen, $ralf) {
        $verificacion_xml = $ralf->ZONA_VERIFICACION;
        $verficacion = $ralf->ZONA_VERIFICACION;
        $verficacion_medidas = $verificacion_xml->addChild('verificacion_medidas','');
        
        $medidas_correctivas = ORM::factory('MedidaCorrectivaRalf145')->where('xml_id','=',$xml_id_origen)->find_all();
        foreach ($medidas_correctivas as $m) {
            
            $verficacion = $verficacion_medidas->addChild('verificacion_medida','');
            
            //verificacion_medida
            $fecha_verificacion = date('Y-m-d', strtotime(htmlspecialchars($m->fecha_verificacion, ENT_QUOTES, 'UTF-8')));
            $verficacion->addChild('fecha_verificacion', $fecha_verificacion);
            
            $verficacion->addChild('folio_medida_prescrita', htmlspecialchars($m->id, ENT_QUOTES, 'UTF-8'));
            
            //datos_verificacion
            $datos_verificacion = $verficacion->addChild('datos_verificacion','');
            
            $datos_verificacion->addChild('cumplimiento_medida', htmlspecialchars($m->cumplimiento_medida, ENT_QUOTES, 'UTF-8'));
            
            $datos_verificacion->addChild('observacion_verificacion', htmlspecialchars($m->observacion_verificacion, ENT_QUOTES, 'UTF-8'));
            
            $fecha_cumplimiento = date('Y-m-d', strtotime(htmlspecialchars($m->fecha_cumplimiento, ENT_QUOTES, 'UTF-8')));
            $datos_verificacion->addChild('fecha_cumple_medida_empleador', $fecha_cumplimiento);

        }
        return $ralf;

    }
    
    //funcion para generar el tag de documento anexo al XML
    public static function documentos_anexos($xml_id, $ralf) {
        $documentos_notif_causas = $ralf->ZONA_VERIFICACION;
        $documentos_acompana = $documentos_notif_causas->addChild('documentos_acompanan_verificacion','');

        $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();
        foreach ($anexos as $anexo) {           
            $path = $anexo->ruta;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = base64_encode($data);
            
            $documento_anexo = $documentos_acompana->addChild('documento_anexo','');
            $documento_anexo->addChild('nombre_documento', htmlspecialchars($anexo->nombre_documento, ENT_QUOTES, 'UTF-8'));
            $documento_anexo->addChild('fecha_documento', htmlspecialchars($anexo->fecha_documento, ENT_QUOTES, 'UTF-8'));
            $documento_anexo->addChild('autor_documento', htmlspecialchars($anexo->autor_documento, ENT_QUOTES, 'UTF-8'));
            $documento_anexo->addChild('documento', $base64);
            $documento_anexo->addChild('extension', htmlspecialchars($type, ENT_QUOTES, 'UTF-8'));
        }
        //echo "<pre>";print_r($ralf->ZONA_VERIFICACION);die();
        return $ralf;

    }
    
    public function action_medida_ver() {    
        $this->auto_render = false;
        $mensaje_error = "";
        $errors = array();
        
        $medida_id = $this->request->param('id');
        $medida = ORM::factory('MedidaCorrectivaRalf145', $medida_id);
        //echo Database::instance()->last_query;die();
        $xml_id = $medida->xml_id;        
        $borrado=false;
        
        $default["fecha_verificacion"] = (is_null($medida->fecha_verificacion)) ? "" : date("Y-m-d",strtotime($medida->fecha_verificacion));
        $default["fecha_cumplimiento"] = (is_null($medida->fecha_cumplimiento)) ? "" : date("Y-m-d",strtotime($medida->fecha_cumplimiento));
        $default["cumplimiento_medida"] = $medida->cumplimiento_medida;
        $default["observacion_verificacion"] = $medida->observacion_verificacion;
        
        $this->response->body(
            View::factory('ralfVerificacion/ver_medida')
                ->set('errors', $errors)
                ->set('mensaje_error',$mensaje_error)
                ->set('default', $default)
                ->set('medida_id', $medida_id)
                ->set('tipo', $medida->tipo)
                ->set('descripcion', $medida->descripcion)
                ->set('glosa_causa', $medida->glosa_causa)
                ->set('codigo_causa', $medida->codigo_causa)
                ->set('borrado', $borrado)
                ->set('xml_id', $xml_id)
        );
    }
    
}
