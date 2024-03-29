<?php defined('SYSPATH') or die('No direct script access.');

class Controller_RalfNotificacion extends Controller_Website{
    
    public function action_insertar() {
        if($this->get_rol()!='operador') {
            $this->redirect("error");
        }
        
        $caso_id = $this->request->param('id');
        if(empty ($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error='Error, Falta id de caso';
            $this->template->contenido = '';
            return;
        }
        
        $caso = ORM::factory('Caso', $caso_id);
        if(!$caso->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar caso';
            $this->template->contenido = '';
            return;
        }
        //Busco un documento ralf
        $documento = $caso->xmls
                ->where('TPXML_ID','IN', array(146))
                ->where('ESTADO','IN', array(1, 2))
                ->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;die();
        if(!$documento->loaded()) {
            $this->template->mensaje_error = 'Se debe agregar una RALF Verificación.';
            $this->template->contenido = '';
            return;
        }
        
        $ralf_anterior = $caso->xmls
                ->where('TPXML_ID','=', 147)
                ->where('ESTADO','!=', 3)
                ->find();
        if($ralf_anterior->loaded()) {
            $this->template->mensaje_error = 'Error, Ya se encuentra una Ralf insertada';
            $this->template->contenido = '';
            return;
        }
        
        $flag = TRUE;
        $medidas = ORM::factory('MedidaCorrectivaRalf145')->where('xml_id','=',$documento->XML_ID_ORIGEN)->find_all();
        foreach ($medidas as $medida) {
            if(in_array($medida->cumplimiento_medida, array(3, 4))) {
                $flag = FALSE;
                break;
            }
        }
        
        if($flag) {
            $this->template->mensaje_error = 'Error, RALF Notificación no es necesaria, todas las medidas cumplidas.';
            $this->template->contenido = '';
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
        if(!isset($xml_documento->ZONA_A->documento->cun)) {
            $cun = $documento->caso->CASO_CUN;
            $dom = dom_import_simplexml($xml_documento->ZONA_A->children());
            $dom->insertBefore(
                $dom->ownerDocument->createElement('cun', $cun),
                $dom->firstChild
            );
        }
           
        if(isset($xml_documento->ZONA_VERIFICACION)) {
            unset($xml_documento->ZONA_VERIFICACION);
        }

        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion=dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'RALF_Notificacion');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());

        $zona_notificacion_string=
    "<ZONA_NOTIFICACION_RALF>
        <causa_notificacion></causa_notificacion>
        <notificacion>
            <fecha_notificacion_autoridad></fecha_notificacion_autoridad>				
            <autoridad_receptora></autoridad_receptora>
            <region_autoridad_receptora></region_autoridad_receptora>
            <receptor_autoridad>
                <rut_profesional_autoridad></rut_profesional_autoridad>
                <apellido_paterno_autoridad></apellido_paterno_autoridad>
                <apellido_materno_autoridad></apellido_materno_autoridad>
                <nombres_autoridad></nombres_autoridad>
                <correo_elect_resp_Autoridad></correo_elect_resp_Autoridad>
            </receptor_autoridad>
        </notificacion>
        <aplicacion_multa>
            <tipo_multa></tipo_multa>
            <fecha_inicio_multa></fecha_inicio_multa>
            <fecha_fin_multa></fecha_fin_multa>
            <monto_multa></monto_multa>
            <recargo></recargo>
        </aplicacion_multa>
        <documentos_acompanan_notificacion>
            <nombre_documento></nombre_documento>
            <fecha_documento></fecha_documento>
            <autor_documento></autor_documento>
            <documento></documento>
            <extension></extension>
        </documentos_acompanan_notificacion>
        <representante_oa>
            <apellido_paterno></apellido_paterno>
            <apellido_materno></apellido_materno>
            <nombres></nombres>
            <rut></rut>
        </representante_oa>
    </ZONA_NOTIFICACION_RALF>
</RALF_Notificacion>";
        
        $ralf = str_replace('</RALF_Notificacion>', $zona_notificacion_string, $ralf_preparacion->saveXML());
        $ralf = simplexml_load_string($ralf);
        
        $zona_o = $ralf->addChild('ZONA_O', '');
        $zona_o->addChild('seguridad', 'Seguridad ISL');
        
        $xmlstring = ORM::factory('Xmlstring');
        $xmlstring->XMLSTRING = $ralf->saveXML();
        $xmlstring->save();
        
        unset($ralf);
        $xml_insert = ORM::factory('Xml');
        $xml_insert->XMLSTRING_ID = $xmlstring->XMLSTRING_ID;
        $xml_insert->ESTADO = 5;
        $xml_insert->CASO_ID = $caso->CASO_ID;
        $xml_insert->TPXML_ID = 147;
        $xml_insert->VALIDO = 0;
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN = $documento->XML_ID;//echo "<pre>";print_r($xml_insert);die();
        $xml_insert->save();
        
        $doc=simplexml_load_string($xmlstring->XMLSTRING);
        $doc->ZONA_A->documento->folio = $xml_insert->XML_ID;
        $xmlstring->XMLSTRING = $doc->saveXML();
        $xmlstring->save();
        $this->redirect("ralfNotificacion/crear/$xml_insert->XML_ID");
    }
    
    public function action_crear() {
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
            $this->redirect("documento/ralfNotificacion/$documento->XML_ID");
        }
        
        $documentostring = $documento->xmlstring;
        $ralf = simplexml_load_string($documentostring->XMLSTRING);
        $ralfTempBD = simplexml_load_string($documentostring->XMLSTRING);
		
        $errores_esquema = NULL;
        $errors = array();
        $mensaje_error = null;
        if (isset($_POST) AND Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_finalizar'])) {
                $post = Validation::factory($this->request->post())
                        ->rule('causa_notificacion', 'not_empty')->label('causa_notificacion', 'Causa notificación')
                        ->rule('rut','Utiles::whitespace',array(':value'))
                        ->rule('rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->rule('rut', 'not_empty')->rule('rut','Utiles::rut',array(':value'))
                        ->rule('rut', 'not_empty')->label('rut', 'Rut OA')
                        ->rule('apellido_paterno', 'Utiles::whitespace',array(':value'))
                        ->rule('apellido_paterno', 'not_empty')->label('apellido_paterno', 'Ap. paterno OA')
                        ->rule('apellido_materno', 'Utiles::whitespace',array(':value'))
                        ->rule('apellido_materno', 'not_empty')->label('apellido_materno', 'Ap. materno OA')
                        ->rule('nombres', 'Utiles::whitespace',array(':value'))
                        ->rule('nombres', 'not_empty')->label('nombres', 'Nombre OA')
                ;
                
                //REGLA NOTIFICACIONES
                $notificaciones = ORM::factory('Ralf147Notificaciones')->where('xml_id','=',$xml_id)->find_all();
                if(count($notificaciones) == 0) {
                    $post = $post->rule('notificaciones', 'not_empty')->label('notificaciones', 'Notificaciones');
                }
                if(count($notificaciones) > 2) {
                    $errors = $errors + array("notificaciones" => "No pueden haber más de 2 notificaciones");
                }
                
                //REGLA MULTAS
                $multas = ORM::factory('Ralf147Multas')->where('xml_id','=',$xml_id)->find_all();
                if(count($multas) == 0) {
                    $post = $post->rule('multas', 'not_empty')->label('multas', 'Multas');
                }
                if(count($multas) > 3) {
                    $errors = $errors + array("multas"=>"No pueden haber más de 3 multas");
                }
                //REGLA VALIDACION DOCUMENTOS ADJUNTOS
                $anexos = ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();
                if(count($anexos) == 0) {
                    $post = $post->rule('documentos_anexos', 'not_empty')->label('documentos_anexos', 'Documentos Anexos');
                }
                
                if($post->check() && count($errors) == 0) {
                    if(isset($ralf->ZONA_NOTIFICACION_RALF->causa_notificacion)) {
                        unset($ralf->ZONA_NOTIFICACION_RALF->causa_notificacion);
                    }
                    
                    if(isset($ralf->ZONA_NOTIFICACION_RALF->notificacion)) {
                        unset($ralf->ZONA_NOTIFICACION_RALF->notificacion);
                    }
                    
                    if(isset($ralf->ZONA_NOTIFICACION_RALF->aplicacion_multa)) {
                        unset($ralf->ZONA_NOTIFICACION_RALF->aplicacion_multa);
                    }
                    
                    if(isset($ralf->ZONA_NOTIFICACION_RALF->representante_oa)) {
                        unset($ralf->ZONA_NOTIFICACION_RALF->representante_oa);
                    }
                    
                    if(isset($ralf->ZONA_NOTIFICACION_RALF->documentos_acompanan_notificacion)) {
                        unset($ralf->ZONA_NOTIFICACION_RALF->documentos_acompanan_notificacion);
                    }
                    
                    $ralf_string = $ralf->saveXML();
                    
                    $ralf->ZONA_NOTIFICACION_RALF->causa_notificacion                   = $post["causa_notificacion"];
                    $ralf = Controller_RalfNotificacion::notificaciones($xml_id, $ralf);
                    $ralf = Controller_RalfNotificacion::multas($xml_id, $ralf);
                    $ralf = Controller_RalfNotificacion::documentos_anexos($xml_id, $ralf);
                    
                    $ralf->ZONA_NOTIFICACION_RALF->representante_oa->apellido_paterno   = $post["apellido_paterno"];
                    $ralf->ZONA_NOTIFICACION_RALF->representante_oa->apellido_materno   = $post["apellido_materno"];
                    $ralf->ZONA_NOTIFICACION_RALF->representante_oa->nombres            = $post["nombres"];
                    $ralf->ZONA_NOTIFICACION_RALF->representante_oa->rut                = $post["rut"];
                    
                    $ralf_bd = $ralf->saveXML();
                    
                    $ralf  = Documento::zona_o($ralf->saveXML());
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf), $documento->TPXML_ID);
                    $final = $ralf;
                    
                    $valido = Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_Notificacion.1.0.xsd');
                    if($valido['estado']) {
                        $documentostring->XMLSTRING = $ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO = 1;
                        $documento->ESTADO = 6;
                        $documento->save();
                   
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
                
                $ralf->ZONA_NOTIFICACION_RALF->causa_notificacion                                            = $post["causa_notificacion"];
                $ralf->ZONA_NOTIFICACION_RALF->representante_oa->apellido_paterno                            = $post["apellido_paterno"];
                $ralf->ZONA_NOTIFICACION_RALF->representante_oa->apellido_materno                            = $post["apellido_materno"];
                $ralf->ZONA_NOTIFICACION_RALF->representante_oa->nombres                                     = $post["nombres"];
                $ralf->ZONA_NOTIFICACION_RALF->representante_oa->rut                                         = $post["rut"];
                
                $documentostring->XMLSTRING = $ralf->saveXML();
                $documentostring->save();
                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
            }
        }
        
        $regiones = Utiles::regiones();
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf;
        $data['criterio_gravedad'] = $organismo = Kohana::$config->load('dominios.STCriterio_gravedad_RALF');
        $this->template->mensaje_error = $mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralfNotificacion/crear')
                ->set('data', $data)
                ->set('back_page', URL::site("caso/ver_caso/{$documento->CASO_ID}", 'http'))
                ->set('errors', $errors)
                ->set('default',  $this->values_default($ralf, $_POST))
                ->set('errores_esquema', $errores_esquema)
                ->set('xml_id', $xml_id)
                ->set('xml_id_origen', $documento->XML_ID_ORIGEN)
                ->set('documento', $documento)
                ->set('config_ralf', $this->config_ralf)
                ->set('regiones', $regiones)
            ;
        
    }
    
    public function values_default($ralf, $post) {
        if(empty($post)) {
            //causa_notificacion
            $default["causa_notificacion"]              = $ralf->ZONA_NOTIFICACION_RALF->causa_notificacion;
            //notificacion
            $default["fecha_notificacion_autoridad"]    = $ralf->ZONA_NOTIFICACION_RALF->notificacion->fecha_notificacion_autoridad;
            $default["autoridad_receptora"]             = $ralf->ZONA_NOTIFICACION_RALF->notificacion->autoridad_receptora;
            $default["region_autoridad_receptora"]      = $ralf->ZONA_NOTIFICACION_RALF->notificacion->region_autoridad_receptora;
            //receptor_autoridad
            $default["rut_profesional_autoridad"]       = $ralf->ZONA_NOTIFICACION_RALF->notificacion->receptor_autoridad->rut_profesional_autoridad;
            $default["apellido_paterno_autoridad"]      = $ralf->ZONA_NOTIFICACION_RALF->notificacion->receptor_autoridad->apellido_paterno_autoridad;
            $default["apellido_materno_autoridad"]      = $ralf->ZONA_NOTIFICACION_RALF->notificacion->receptor_autoridad->apellido_materno_autoridad;
            $default["nombres_autoridad"]               = $ralf->ZONA_NOTIFICACION_RALF->notificacion->receptor_autoridad->nombres_autoridad;
            $default["correo_elect_resp_Autoridad"]     = $ralf->ZONA_NOTIFICACION_RALF->notificacion->receptor_autoridad->correo_elect_resp_Autoridad;
            //representante_oa
            $default["apellido_paterno"]                = $ralf->ZONA_NOTIFICACION_RALF->representante_oa->apellido_paterno;
            $default["apellido_materno"]                = $ralf->ZONA_NOTIFICACION_RALF->representante_oa->apellido_materno;
            $default["nombres"]                         = $ralf->ZONA_NOTIFICACION_RALF->representante_oa->nombres;
            $default["rut"]                             = $ralf->ZONA_NOTIFICACION_RALF->representante_oa->rut;
            
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

        } else {
            //causa
            $default["causa_notificacion"]              = $post["causa_notificacion"];
            //representante_oa
            $default["apellido_paterno"]                = $post["apellido_paterno"];
            $default["apellido_materno"]                = $post["apellido_materno"];
            $default["nombres"]                         = $post["nombres"];
            $default["rut"]                             = $post["rut"];
            
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
        }
        return $default;
    }
    
    public static function notificaciones($xml_id, $ralf) {
        $xml_ralf = $ralf->ZONA_NOTIFICACION_RALF;
        

        $notificaciones = ORM::factory('Ralf147Notificaciones')->where('xml_id','=',$xml_id)->find_all();
        foreach ($notificaciones as $notificacion) {
            $xml = $xml_ralf->addChild('notificacion', '');
            $xml->addChild('fecha_notificacion_autoridad', htmlspecialchars($notificacion->fecha_notificacion_autoridad, ENT_QUOTES, 'UTF-8'));
            $xml->addChild('autoridad_receptora', htmlspecialchars($notificacion->autoridad_receptora, ENT_QUOTES, 'UTF-8'));
            $xml->addChild('region_autoridad_receptora', htmlspecialchars($notificacion->region_autoridad_receptora, ENT_QUOTES, 'UTF-8'));
            
            $receptor_autoridad = $xml->addChild('receptor_autoridad', '');
            $receptor_autoridad->addChild('rut_profesional_autoridad', htmlspecialchars($notificacion->rut_profesional_autoridad, ENT_QUOTES, 'UTF-8'));
            $receptor_autoridad->addChild('apellido_paterno_autoridad', htmlspecialchars($notificacion->apellido_paterno_autoridad, ENT_QUOTES, 'UTF-8'));
            $receptor_autoridad->addChild('apellido_materno_autoridad', htmlspecialchars($notificacion->apellido_materno_autoridad, ENT_QUOTES, 'UTF-8'));
            $receptor_autoridad->addChild('nombres_autoridad', htmlspecialchars($notificacion->nombres_autoridad, ENT_QUOTES, 'UTF-8'));
            $receptor_autoridad->addChild('correo_elect_resp_Autoridad', htmlspecialchars($notificacion->correo_elect_resp_autoridad, ENT_QUOTES, 'UTF-8'));
        }
        //echo "<pre>";print_r($ralf->ZONA_NOTIFICACION_RALF);die();
        return $ralf;

    }
    
    public static function multas($xml_id, $ralf) {
        $xml_ralf = $ralf->ZONA_NOTIFICACION_RALF;
        
        $multas = ORM::factory('Ralf147Multas')->where('xml_id','=',$xml_id)->find_all();
        foreach ($multas as $multa) {
            $xml = $xml_ralf->addChild('aplicacion_multa','');
            $xml->addChild('tipo_multa', htmlspecialchars($multa->tipo_multa, ENT_QUOTES, 'UTF-8'));
            $xml->addChild('fecha_inicio_multa', htmlspecialchars($multa->fecha_inicio_multa, ENT_QUOTES, 'UTF-8'));
            $xml->addChild('fecha_fin_multa', htmlspecialchars($multa->fecha_fin_multa, ENT_QUOTES, 'UTF-8'));
            $xml->addChild('monto_multa', htmlspecialchars($multa->monto_multa, ENT_QUOTES, 'UTF-8'));
            $xml->addChild('recargo', htmlspecialchars($multa->recargo, ENT_QUOTES, 'UTF-8'));
        }
        //echo "<pre>";print_r($ralf->ZONA_NOTIFICACION_RALF);die();
        return $ralf;

    }
    
    public static function documentos_anexos($xml_id, $ralf) {
        $xml_ralf = $ralf->ZONA_NOTIFICACION_RALF;
        $documentos_acompana = $xml_ralf->addChild('documentos_acompanan_notificacion','');

        $anexos = ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();
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
        //echo "<pre>";print_r($ralf->ZONA_NOTIFICACION_RALF);die();
        return $ralf;

    }
    
    public function action_agregar_notificacion() {   
        $this->auto_render = false;
        $xml_id = $this->request->param('id');
        $mensaje_error = "";
        $errors['antecedente'] = null;
        $errors['fecha'] = null;
        $errors['autor'] = null;
        $errors['nombre'] = null;
        
        $default['fecha_notificacion_autoridad'] = null;
        $default['autoridad_receptora'] = null;
        $default['region_autoridad_receptora'] = null;
        $default['apellido_paterno_autoridad'] = null;
        $default['apellido_materno_autoridad'] = null;
        $default['nombres_autoridad'] = null;
        $default['rut_profesional_autoridad'] = null;
        $default['correo_elect_resp_autoridad'] = null;
        
        if (isset($_POST) && Valid::not_empty($_POST)) {
            $cant = ORM::factory('Ralf147Notificaciones')->where('xml_id','=',$xml_id)->count_all();
            if($cant < 2) {
                if(isset ($_POST['boton_agegar_notificacion'])) {
                    $post = Validation::factory($this->request->post())
                            ->rule('fecha_notificacion_autoridad', 'not_empty')->label('fecha_notificacion_autoridad', 'Fecha notificación autoridad')
                            ->rule('fecha_notificacion_autoridad', 'date')
                            ->rule('fecha_notificacion_autoridad','Utiles::validateDate',array(':value'))
                            ->rule('autoridad_receptora', 'not_empty')->label('autoridad_receptora', 'Autoridad receptora')
                            ->rule('region_autoridad_receptora', 'not_empty')->label('region_autoridad_receptora', 'Región autoridad receptora')
                            ->rule('rut_profesional_autoridad','Utiles::whitespace',array(':value'))
                            ->rule('rut_profesional_autoridad', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                            ->rule('rut_profesional_autoridad', 'not_empty')->rule('rut_profesional_autoridad','Utiles::rut',array(':value'))
                            ->rule('rut_profesional_autoridad', 'not_empty')->label('rut_profesional_autoridad', 'Rut profesional autoridad')
                            ->rule('apellido_paterno_autoridad', 'Utiles::whitespace',array(':value'))
                            ->rule('apellido_paterno_autoridad', 'not_empty')->label('apellido_paterno_autoridad', 'Ap. paterno autoridad')
                            ->rule('apellido_materno_autoridad', 'Utiles::whitespace',array(':value'))
                            ->rule('apellido_materno_autoridad', 'not_empty')->label('apellido_materno_autoridad', 'Ap. materno autoridad')
                            ->rule('nombres_autoridad', 'Utiles::whitespace',array(':value'))
                            ->rule('nombres_autoridad', 'not_empty')->label('nombres_autoridad', 'Nombre autoridad')
                            ->rule('correo_elect_resp_autoridad', 'not_empty')->label('correo_elect_resp_autoridad', 'Email autoridad')
                            ->rule('correo_elect_resp_autoridad', 'email')
                            ;
                    if ($post->check()) {
                        $result = TRUE;
                        if($result) {
                            $notificacion = ORM::factory('Ralf147Notificaciones');
                            $notificacion->xml_id = $xml_id;
                            $notificacion->fecha_notificacion_autoridad = $post['fecha_notificacion_autoridad'];
                            $notificacion->autoridad_receptora = $post['autoridad_receptora'];
                            $notificacion->region_autoridad_receptora = $post['region_autoridad_receptora'];
                            $notificacion->rut_profesional_autoridad = $post['rut_profesional_autoridad'];
                            $notificacion->apellido_paterno_autoridad = $post['apellido_paterno_autoridad'];
                            $notificacion->apellido_materno_autoridad = $post['apellido_materno_autoridad'];
                            $notificacion->nombres_autoridad = $post['nombres_autoridad'];
                            $notificacion->correo_elect_resp_autoridad = $post['correo_elect_resp_autoridad'];
                            $notificacion->save();

                            $mensaje_error = "Notificación agregada exitosamente";
                        } else {
                            $mensaje_error = "Error carga";
                        }                                    
                    } else {
                        $default['fecha_notificacion_autoridad'] = $post['fecha_notificacion_autoridad'];
                        $default['autoridad_receptora'] = $post['autoridad_receptora'];
                        $default['region_autoridad_receptora'] = $post['region_autoridad_receptora'];
                        $default['apellido_paterno_autoridad'] = $post['apellido_paterno_autoridad'];
                        $default['apellido_materno_autoridad'] = $post['apellido_materno_autoridad'];
                        $default['nombres_autoridad'] = $post['nombres_autoridad'];
                        $default['rut_profesional_autoridad'] = $post['rut_profesional_autoridad'];
                        $default['correo_elect_resp_autoridad'] = $post['correo_elect_resp_autoridad'];

                        $errors = $post->errors('validate');                        
                        $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                    }   
                } 
            } else {
                $mensaje_error = "No se pueden agregar más de 2 notificaciones";
            }
        }
        
        $regiones = Utiles::regiones();
        $this->response->body (
            View::factory('ralfNotificacion/agregar_notificacion')
                ->set('errors', $errors)
                ->set('default', $default)
                ->set('xml_id', $xml_id)
                ->set('config_ralf', $this->config_ralf)
                ->set('mensaje_error', $mensaje_error)
                ->set('regiones', $regiones)
        );
    }
    
    public function action_agregar_notificacion_anexo() {
        $this->auto_render = false;
        $xml_id = $this->request->param('id');
        $res = array();
        $regiones = Utiles::regiones();
        
        $notificaciones = ORM::factory('Ralf147Notificaciones')->where('xml_id','=',$xml_id)->find_all();
        //echo Database::instance()->last_query;die();
        if(count($notificaciones) <= 2) {
            foreach($notificaciones as $a)  {
                $res[] = array(
                    $a->id,
                    $a->fecha_notificacion_autoridad,
                    $this->config_ralf['147']['autoridad_receptora'][$a->autoridad_receptora],
                    $regiones[$a->region_autoridad_receptora],
                    $a->nombres_autoridad,
                    $a->apellido_paterno_autoridad,
                    HTML::anchor("ralfNotificacion/ver_notificacion/{$a->id}",'Ver',array('class'=>'fancybox')) .
                    " | " . HTML::anchor("ralfNotificacion/borrar_notificacion/{$a->id}",'Borrar',array('class'=>'fancybox-small'))
                );
            }
            $this->response->body(json_encode($res));
        } else {
            
        }
    }
    
    public function action_borrar_notificacion() {
        $this->auto_render = false;
        $id = $this->request->param('id');
        $notificacion = ORM::factory('Ralf147Notificaciones', $id);
        $xml_id = $notificacion->xml_id;
        
        $borrado = false;        
        if(isset ($_POST['boton_aceptar'])) {            
            $notificacion->delete();
            $borrado = true;
        }

        $this->response->body (
            View::factory('ralfNotificacion/borrar_notificacion')
                ->set('borrado', $borrado)
                ->set('xml_id', $xml_id)
                ->set('id', $notificacion->id)
            );
    }
    
    public function action_ver_notificacion() {    
        $this->auto_render = false;
        $id = $this->request->param('id');
        
        $regiones = Utiles::regiones();
        $notificacion = ORM::factory('Ralf147Notificaciones')->where('id','=',$id)->find();
        //echo Database::instance()->last_query;die();
        $this->response->body(
            View::factory('ralfNotificacion/ver_notificacion')
                ->set('id', $id)
                ->set('notificacion', $notificacion)
                ->set('config_ralf', $this->config_ralf)
                ->set('regiones', $regiones)
        );
    }
    
    public function action_agregar_multa() {   
        $this->auto_render = false;
        $xml_id = $this->request->param('id');
        $mensaje_error = "";
        $errors = array();
        
        $default['tipo_multa'] = null;
        $default['fecha_inicio_multa'] = null;
        $default['fecha_fin_multa'] = null;
        $default['monto_multa'] = null;
        $default['recargo'] = null;
        
        if (isset($_POST) && Valid::not_empty($_POST)) {
            $cant_multas = ORM::factory('Ralf147Multas')->where('xml_id','=',$xml_id)->count_all();
            if($cant_multas < 3) {
                if(isset ($_POST['boton_agegar_multa'])) {
                    $post = Validation::factory($this->request->post())
                            ->rule('tipo_multa', 'not_empty')->label('tipo_multa', 'Tipo de Multa')
                            ->rule('fecha_inicio_multa', 'not_empty')->label('fecha_inicio_multa', 'Fecha inicio de multa')
                            ->rule('fecha_inicio_multa', 'date')
                            ->rule('fecha_inicio_multa','Utiles::validateDate',array(':value'))
                            ->rule('fecha_fin_multa', 'not_empty')->label('fecha_fin_multa', 'Fecha fin de multa')
                            ->rule('fecha_fin_multa', 'date')
                            ->rule('fecha_fin_multa','Utiles::validateDate',array(':value'))
                            ->rule('monto_multa', 'not_empty')->label('monto_multa', 'Monto de Multa')
                            ->rule('monto_multa', 'numeric')
                            ->rule('recargo', 'not_empty')->label('recargo', 'Recargo de Multa')
                            //->rule('recargo', 'decimal')
                            ;
                    
                    if(!empty($_POST["fecha_inicio_multa"])) {
                        if(($_POST["fecha_inicio_multa"] > $_POST["fecha_fin_multa"])) {
                            $errors = $errors + array("fecha_fin_multa" => "Fecha fin multa debe ser mayor o igual a fecha de inicio multa");
                        }
                    }
                    
                    if($post->check() && count($errors) == 0) {
                        $result = TRUE;
                        if($result) {
                            $multa = ORM::factory('Ralf147Multas');
                            $multa->xml_id = $xml_id;
                            $multa->tipo_multa = $post['tipo_multa'];
                            $multa->fecha_inicio_multa = $post['fecha_inicio_multa'];
                            $multa->fecha_fin_multa = $post['fecha_fin_multa'];
                            $multa->monto_multa = $post['monto_multa'];
                            $multa->recargo = $post['recargo'];
                            $multa->save();

                            $mensaje_error = "Multa agregada exitosamente";
                        } else {
                            $mensaje_error = "Error carga";
                        }                                    
                    } else {
                        $default['tipo_multa'] = $post['tipo_multa'];
                        $default['fecha_inicio_multa'] = $post['fecha_inicio_multa'];
                        $default['fecha_fin_multa'] = $post['fecha_fin_multa'];
                        $default['monto_multa'] = $post['monto_multa'];
                        $default['recargo'] = $post['recargo'];
                        
                        $errors = $post->errors('validate') + $errors;              
                        $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                    }   
                } 
            } else {
                $mensaje_error = "No se pueden agregar más de 3 multas";
            }
        }
                        
        $this->response->body (
            View::factory('ralfNotificacion/agregar_multa')
                ->set('errors', $errors)
                ->set('default', $default)
                ->set('xml_id', $xml_id)
                ->set('config_ralf', $this->config_ralf)
                ->set('mensaje_error', $mensaje_error)
        );
    }
   
    public function action_agregar_multa_anexo() {
        $this->auto_render = false;
        $xml_id = $this->request->param('id');
        $res = array();
        
        $multas = ORM::factory('Ralf147Multas')->where('xml_id','=',$xml_id)->find_all();
        if(count($multas) <= 3) {
            foreach($multas as $a)  {
                $res[] = array(
                    $a->id,
                    $this->config_ralf['147']['tipo_multa'][$a->tipo_multa],
                    $a->fecha_inicio_multa,
                    $a->fecha_fin_multa,
                    $a->monto_multa,
                    $a->recargo,
                    HTML::anchor("ralfNotificacion/borrar_multa/{$a->id}",'borrar',array('class'=>'fancybox-small'))
                );
            }
            $this->response->body(json_encode($res));
        } else {
            
        }
    }
    
    public function action_borrar_multa() {
        $this->auto_render = false;
        $id = $this->request->param('id');
        $multa = ORM::factory('Ralf147Multas', $id);
        $xml_id = $multa->xml_id;
        
        $borrado = false;        
        if(isset ($_POST['boton_aceptar'])) {            
            $multa->delete();
            $borrado = true;
        }

        $this->response->body (
            View::factory('ralfNotificacion/borrar_multa')
                ->set('borrado', $borrado)
                ->set('xml_id', $xml_id)
                ->set('multa_id',$multa->id)
            );
    }
}