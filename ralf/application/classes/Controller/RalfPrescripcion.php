<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_RalfPrescripcion extends Controller_Website {

    public function action_insertar() {
        if ($this->get_rol() != 'operador') {
            $this->redirect("error");
        }

        $caso_id = $this->request->param('id');
        if (empty($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error = 'Error, Falta id de caso';
            $this->template->contenido = '';
            return;
        }
        $caso = ORM::factory('Caso', $caso_id);

        if (!$caso->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar caso';
            $this->template->contenido = '';
            return;
        }
        //Busco un documento ralf
        $documento = $caso->xmls
                        ->where('TPXML_ID', 'IN', array(144))
                        ->where('ESTADO', 'IN', array(1, 2))
                        ->order_by('FECHA_CREACION', 'DESC')
                        ->find();
        //echo Database::instance()->last_query;die();
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Se debe agregar una RALF Causas.';
            $this->template->contenido = '';
            return;
        }

        $ralf_anterior = $caso->xmls->where('TPXML_ID', '=', 145)->where('ESTADO','!=', 3)->find();
        if ($ralf_anterior->loaded()) {
            $this->template->mensaje_error = 'Error, Ya se encuentra una Ralf insertada';
            $this->template->contenido = '';
            return;
        }
        //Se cargan los datos del documento
        $xml_documento = simplexml_load_string($documento->xmlstring->XMLSTRING);

        //Se eliminan zonas que no se utilizaran
        $xml_documento->ZONA_A->documento->folio = '';
        $fecha_creacion = date('Y-m-d');
        $hora_creacion = date('H:i:s');
        $xml_documento->ZONA_A->documento->fecha_emision = $fecha_creacion . 'T' . $hora_creacion;

        //si no viene cun agregar el del caso
        if (!isset($xml_documento->ZONA_A->documento->cun)) {
            $cun = $documento->caso->CASO_CUN;
            $dom = dom_import_simplexml($xml_documento->ZONA_A->children());
            $dom->insertBefore(
                $dom->ownerDocument->createElement('cun', $cun),
                $dom->firstChild
            );
        }

        if (isset($xml_documento->ZONA_CAUSAS)) {
            unset($xml_documento->ZONA_CAUSAS);
        }

        if (isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion = dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'RALF_Prescripcion');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());

        //faillon
        //agregar nuevo tag de acuerdo a los cambios en xsds
        //tomar como referencia ZONA_R de ralf3
        $zona_prescripcion_string = "<ZONA_PRESCRIPCION>
            <fecha_prescripcion_medida></fecha_prescripcion_medida>
            </ZONA_PRESCRIPCION>
        </RALF_Prescripcion>";

        $ralf = str_replace('</RALF_Prescripcion>', $zona_prescripcion_string, $ralf_preparacion->saveXML());
        $ralf = simplexml_load_string($ralf);

        $dom = dom_import_simplexml($ralf);
        $nodoReferencia = dom_import_simplexml($dom->getElementsByTagName('ZONA_C')->item(0));
        $dom->insertBefore($dom->ownerDocument->createElement('ZONA_ZCT'), $nodoReferencia);

        $str_zct = '
<ZONA_ZCT>
    <centro_de_trabajo>
        <CUV></CUV>
        <estado_centro_trabajo></estado_centro_trabajo>
        <rut_empleador_principal></rut_empleador_principal>
        <nombre_empleador_principal></nombre_empleador_principal>
        <nombre_centro_trabajo></nombre_centro_trabajo>
        <correlativo_proyecto_contrato></correlativo_proyecto_contrato>
        <tipo_empresa></tipo_empresa>
        <geolocalizacion>
            <geo_latitud></geo_latitud>
            <geo_longitud></geo_longitud>
        </geolocalizacion>
        <direccion_centro_trabajo>
            <tipo_calle_ct></tipo_calle_ct>
            <nombre_calle_ct></nombre_calle_ct>
            <numero_ct></numero_ct>
            <resto_direccion_ct></resto_direccion_ct>
            <localidad_ct></localidad_ct>
            <comuna_ct></comuna_ct>
        </direccion_centro_trabajo>
        <descripcion_actividad_trabajadores_ct></descripcion_actividad_trabajadores_ct>
        <n_trabajadores_propios_ct></n_trabajadores_propios_ct>
        <n_trabajadores_hombre_ct></n_trabajadores_hombre_ct>
        <n_trabajadores_mujer_ct></n_trabajadores_mujer_ct>
        <com_par_constituido></com_par_constituido>
        <experto_prevencion_riesgos></experto_prevencion_riesgos>
        <horas_semana_dedica_ct></horas_semana_dedica_ct>
        <fecha_inicio_ct></fecha_inicio_ct>
        <tiene_fech_term></tiene_fech_term>
        <fecha_termino_ct></fecha_termino_ct>
    </centro_de_trabajo>
</ZONA_ZCT>';

        $ralf = str_replace('<ZONA_ZCT/>', $str_zct, $ralf->saveXML());

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
        $xml_insert->TPXML_ID = 145;
        $xml_insert->VALIDO = 0;
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN = $documento->XML_ID;
        $xml_insert->save();

        $doc = simplexml_load_string($xmlstring->XMLSTRING);
        $doc->ZONA_A->documento->folio = $xml_insert->XML_ID;
        $xmlstring->XMLSTRING = $doc->saveXML();
        $xmlstring->save();
        $this->redirect("ralfPrescripcion/crear/$xml_insert->XML_ID");
    }

    public function action_crear() {

        if ($this->get_rol() != 'operador') {
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

        if ($documento->VALIDO == 1 && $documento->ESTADO != 5) {
            $this->redirect("documento/ralfPrescripcion/$documento->XML_ID");
        }
        $documentostring = $documento->xmlstring;
        $ralf = simplexml_load_string($documentostring->XMLSTRING);
        $ralfTempBD = simplexml_load_string($documentostring->XMLSTRING);


        $estados_ct = array('Activo' => 1, 'Caduco' => 2);
        $estados_ct2 = array(1 => 'Activo', 2 => 'Caduco');
        $tìpos_calle = array('Avenida' => 1, 'Calle' => 2, 'Pasaje' => 3);
        $tipos_empresa = array('Principal' => 1, 'Contratista' => 2, 'Subcontratista' => 2, 'De servicios transitorios' => 4);
        $comunas = Model_St_Comuna::obtener();
        $si_no = array('Si' => 1, 'No' => 2);
        $cod_comuna_ct = "";


        $errores_esquema = NULL;
        $errors = array();
        $mensaje_error = null;
        if (isset($_POST) AND Valid::not_empty($_POST)) {
            if (isset($_POST['boton_finalizar'])) {
                //REGLAS VALIDACION CENTRO TRABAJO
                $post = Validation::factory($this->request->post())
                                ->rule('correlativo_proyecto_contrato', 'not_empty')->label('correlativo_proyecto_contrato', 'Correlativo Proy/Contr')
                                ->rule('cuv', 'not_empty')->label('cuv', 'CUV')
                                ->rule('descripcion_actividad_trabajadores_ct', 'not_empty')->label('descripcion_actividad_trabajadores_ct', 'Act. Trabajadores')
                                ->rule('estado_centro_trabajo', 'not_empty')->label('estado_centro_trabajo', 'Estado CT')
                                ->rule('geo_latitud', 'not_empty')->label('geo_latitud', 'Latitud')
                                ->rule('geo_longitud', 'not_empty')->label('geo_longitud', 'Longitud')
                                ->rule('nombre_calle_ct', 'not_empty')->label('nombre_calle_ct', 'Calle')
                                ->rule('comuna_ct', 'not_empty')->label('comuna_ct', 'Comuna')
                                ->rule('nombre_centro_trabajo', 'not_empty')->label('nombre_centro_trabajo', 'Nombre CT')
                                ->rule('nombre_empleador_principal', 'not_empty')->label('nombre_empleador_principal', 'Nombre Empleador')
                                ->rule('numero_ct', 'not_empty')->label('numero_ct', 'Numero')
                                ->rule('rut_empleador_principal', 'not_empty')->label('rut_empleador_principal', 'RUT Empleador Princ.')
                                ->rule('tiene_fech_term', 'not_empty')->label('tiene_fech_term', 'Tiene Fecha Termino')
                                ->rule('tipo_calle_ct', 'not_empty')->label('tipo_calle_ct', 'Tipo Calle')
                                ->rule('fecha_prescripcion_medida', 'not_empty')->label('fecha_prescripcion_medida', 'Fecha Prescripción Medida')
                                ->rule('fecha_prescripcion_medida', 'date')
                                ->rule('fecha_prescripcion_medida', 'Utiles::validateDate', array(':value'))
                                ->rule('fecha_prescripcion_medida', 'Utiles::validaFechaMenorHoy', array(':value'))
                                ->rule('investigador_apellido_paterno', 'Utiles::whitespace', array(':value'))
                                ->rule('investigador_apellido_paterno', 'not_empty')->label('investigador_apellido_paterno', 'Ap. paterno')
                                ->rule('investigador_apellido_materno', 'Utiles::whitespace', array(':value'))
                                ->rule('investigador_apellido_materno', 'not_empty')->label('investigador_apellido_materno', 'Ap. materno')
                                ->rule('investigador_nombres', 'Utiles::whitespace', array(':value'))
                                ->rule('investigador_nombres', 'not_empty')->label('investigador_nombres', 'Nombre')
                                ->rule('investigador_rut', 'Utiles::whitespace', array(':value'))
                                ->rule('investigador_rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                                ->rule('investigador_rut', 'not_empty')->rule('investigador_rut', 'Utiles::rut', array(':value'))
                                ->rule('investigador_rut', 'not_empty')->label('investigador_rut', 'Rut')
                ;


                if ($_POST["estado_centro_trabajo"] == "Activo") {
                    $post = $post->rule('tipo_empresa', 'not_empty')->label('tipo_empresa', 'Tipo Empresa')
                                    ->rule('n_trabajadores_propios_ct', 'not_empty')->label('n_trabajadores_propios_ct', 'Total Trabajadores')
                                    ->rule('n_trabajadores_propios_ct', 'Utiles::nonNegativeInteger', array(':value'))
                                    ->rule('n_trabajadores_hombre_ct', 'not_empty')->label('n_trabajadores_hombre_ct', 'Num. Trabajadores H.')
                                    ->rule('n_trabajadores_hombre_ct', 'Utiles::nonNegativeInteger', array(':value'))
                                    ->rule('n_trabajadores_mujer_ct', 'not_empty')->label('n_trabajadores_mujer_ct', 'Num. Trabajadores M.')
                                    ->rule('n_trabajadores_mujer_ct', 'Utiles::nonNegativeInteger', array(':value'))
                                    ->rule('com_par_constituido', 'not_empty')->label('com_par_constituido', 'Comite Paritario')
                                    ->rule('experto_prevencion_riesgos', 'not_empty')->label('experto_prevencion_riesgos', 'Prevencionista Experto')
                                    ->rule('fecha_inicio_ct', 'not_empty')->label('fecha_inicio_ct', 'Fecha Inicio CT')
                                    ->rule('fecha_inicio_ct', 'date')
                                    ->rule('fecha_inicio_ct', 'Utiles::validateDate', array(':value'))
                                    ->rule('tiene_fech_term', 'not_empty')->label('tiene_fech_term', 'Si CT tiene fecha termino')
                    ;

                    if ($_POST["tiene_fech_term"] == "Si") {
                        $post = $post->rule('fecha_termino_ct', 'not_empty')->label('fecha_termino_ct', 'Fecha Termino CT')
                                ->rule('fecha_termino_ct', 'date')
                                ->rule('fecha_termino_ct', 'Utiles::validateDate', array(':value'));

                        if (!empty($_POST["fecha_termino_ct"])) {
                            if ((date('Y-m-d', strtotime($_POST["fecha_inicio_ct"])) > date('Y-m-d', strtotime($_POST["fecha_termino_ct"])))) {
                                $errors = $errors + array("fecha_termino_ct" => "Fecha termino debe ser Mayor o igual a fecha de inicio");
                            }
                        }
                    }

                    if ($_POST["experto_prevencion_riesgos"] == "Si") {
                        $post = $post->rule('horas_semana_dedica_ct', 'not_empty')->label('horas_semana_dedica_ct', 'Hrs. Dedica CT');
                    }
                }

                //REGLA VALIDACION MEDIDAS CORRECTIVAS
                $medidas = ORM::factory('MedidaCorrectivaRalf145')->where('xml_id', '=', $xml_id)->find_all();

                if (count($medidas) == 0) {
                    $post = $post->rule('medidas_correctivas', 'not_empty')->label('medidas_correctivas', 'Medidas Correctivas');
                }

                //REGLA VALIDACION DOCUMENTOS ADJUNTOS
                $anexos = ORM::factory('Adjunto')->where('xml_id', '=', $xml_id)->where('origen', '=', 'documentos_anexos')->find_all();

                if (count($anexos) == 0) {
                    $post = $post->rule('documentos_anexos', 'not_empty')->label('documentos_anexos', 'Documentos Anexos');
                }

                if ($post->check() && count($errors) == 0) {

                    if (isset($ralf->ZONA_PRESCRIPCION->prescripcion_medidas)) {
                        unset($ralf->ZONA_PRESCRIPCION->prescripcion_medidas);
                    }

                    if (isset($ralf->ZONA_PRESCRIPCION->documentos_acompanan_prescripcion)) {
                        unset($ralf->ZONA_PRESCRIPCION->documentos_acompanan_prescripcion);
                    }

                    if (isset($ralf->ZONA_PRESCRIPCION->investigador)) {
                        unset($ralf->ZONA_PRESCRIPCION->investigador);
                    }

                    /* SETAR ZONA ZCT */
                    $ralf = Controller_RalfPrescripcion::setearZCT($ralf, $post);
                    $ralfTempBD = Controller_RalfPrescripcion::setearZCT($ralfTempBD, $post);

                    $ralf->ZONA_PRESCRIPCION->fecha_prescripcion_medida = $post["fecha_prescripcion_medida"];
                    $ralfTempBD->ZONA_PRESCRIPCION->fecha_prescripcion_medida = $post["fecha_prescripcion_medida"];

                    $ralf_string = $ralf->saveXML();

                    $ralf = Controller_RalfPrescripcion::medidas_correctivas($xml_id, $ralf);
                    $ralf = Controller_RalfPrescripcion::documentos_anexos($xml_id, $ralf);

                    $ralf->ZONA_PRESCRIPCION->investigador->apellido_paterno = $post["investigador_apellido_paterno"];
                    $ralf->ZONA_PRESCRIPCION->investigador->apellido_materno = $post["investigador_apellido_materno"];
                    $ralf->ZONA_PRESCRIPCION->investigador->nombres = $post["investigador_nombres"];
                    $ralf->ZONA_PRESCRIPCION->investigador->rut = $post["investigador_rut"];

                    $ralfTempBD->ZONA_PRESCRIPCION->investigador->apellido_paterno = $post["investigador_apellido_paterno"];
                    $ralfTempBD->ZONA_PRESCRIPCION->investigador->apellido_materno = $post["investigador_apellido_materno"];
                    $ralfTempBD->ZONA_PRESCRIPCION->investigador->nombres = $post["investigador_nombres"];
                    $ralfTempBD->ZONA_PRESCRIPCION->investigador->rut = $post["investigador_rut"];

                    $ralf_bd = $ralfTempBD->saveXML();

                    //para enviar al navegar el xml resultado
                    $ralf->saveXML();

                    $ralf = Documento::zona_o($ralf->saveXML());
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf), $documento->TPXML_ID);

                    $valido = Utiles::valida_xml($ralf, dirname(__FILE__) . '/../../../media/xsd/ralf/SISESAT_RALF_Prescripcion.1.0.xsd');
                    if ($valido['estado']) {
                        $documentostring->XMLSTRING = $ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO = 1;
                        $documento->ESTADO = 6;
                        $documento->save();

                        $RalfPrescripcion = ORM::factory('RalfPrescripcion')->where('xml_id', '=', $xml_id)->find();
                        $xmlRalf = ORM::factory('Xml')->where('xml_id', '=', $xml_id)->find();
                        $casoRalf = ORM::factory('Caso')->where('CASO_ID', '=', $xmlRalf->CASO_ID)->find();

                        $trabajador = ORM::factory('Trabajador')->where('TRA_ID', '=', $casoRalf->TRA_ID)->find();
                        $empleador = ORM::factory('Empleador')->where('EMP_ID', '=', $casoRalf->EMP_ID)->find();

                        $region = ORM::factory('Region')->where('id', '=', $casoRalf->REGION_ID)->find();

                        $RalfPrescripcion->investigador_apellido_paterno = $post["investigador_apellido_paterno"];
                        $RalfPrescripcion->investigador_apellido_materno = $post["investigador_apellido_materno"];
                        $RalfPrescripcion->investigador_nombres = $post["investigador_nombres"];
                        $RalfPrescripcion->investigador_rut = $post["investigador_rut"];
                        $RalfPrescripcion->fecha_creacion = $xmlRalf->FECHA_CREACION;
                        $RalfPrescripcion->trabajador_run = $trabajador->rut;
                        $RalfPrescripcion->trabajador_nombres = $trabajador->nombres;
                        $RalfPrescripcion->trabajador_apellido_paterno = $trabajador->apellido_paterno;
                        $RalfPrescripcion->trabajador_apellido_materno = $trabajador->apellido_materno;
                        $RalfPrescripcion->empresa_rut = $empleador->rut_empleador;
                        $RalfPrescripcion->empresa_razon_social = $empleador->nombre_empleador;
                        $RalfPrescripcion->region = $region->nombre;
                        $RalfPrescripcion->xml_id = $xml_id;
                        $RalfPrescripcion->save();

                        $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
                    } else {
                        $ralf = simplexml_load_string($ralf);
                        $errores_esquema = $valido['mensaje'];
                        $mensaje_error = "Operación fallida. Hay " . count($errores_esquema) . " error(es).";
                    }
                } else {
                    $errors = $post->errors('validate') + $errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            } elseif (isset($_POST['boton_incompleta'])) {
                $post = Validation::factory($_POST);

                $ralf = Controller_RalfPrescripcion::setearZCT($ralf, $post);
                $ralf->ZONA_PRESCRIPCION->fecha_prescripcion_medida = $post["fecha_prescripcion_medida"];
                $ralf->ZONA_PRESCRIPCION->investigador->apellido_paterno = $post["investigador_apellido_paterno"];
                $ralf->ZONA_PRESCRIPCION->investigador->apellido_materno = $post["investigador_apellido_materno"];
                $ralf->ZONA_PRESCRIPCION->investigador->nombres = $post["investigador_nombres"];
                $ralf->ZONA_PRESCRIPCION->investigador->rut = $post["investigador_rut"];
                
                $documentostring->XMLSTRING = $ralf->saveXML();
                $documentostring->save();
                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
            }
        }
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf;
        $data['criterio_gravedad'] = $organismo = Kohana::$config->load('dominios.STCriterio_gravedad_RALF');
        $caso = ORM::factory('Caso', $documento->CASO_ID); 
        $this->template->mensaje_error = $mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralfPrescripcion/crear')
                ->set('data', $data)
                ->set('back_page', URL::site("caso/ver_caso/{$documento->CASO_ID}", 'http'))
                ->set('errors', $errors)
                ->set('default', $this->values_default($ralf, $_POST))
                ->set('errores_esquema', $errores_esquema)
                ->set('xml_id', $xml_id)
                ->set('documento', $documento)
                ->set('caso', $caso)
        ;
    }

    public function values_default($ralf, $post) {
        if (empty($post)) {
            $default["investigador_apellido_paterno"] = $ralf->ZONA_PRESCRIPCION->investigador->apellido_paterno;
            $default["investigador_apellido_materno"] = $ralf->ZONA_PRESCRIPCION->investigador->apellido_materno;
            $default["investigador_nombres"] = $ralf->ZONA_PRESCRIPCION->investigador->nombres;
            $default["investigador_rut"] = $ralf->ZONA_PRESCRIPCION->investigador->rut;
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
            $default["fecha_termino_ct"] = $ralf->ZONA_ZCT->centro_de_trabajo->fecha_termino_ct;
            $default["fecha_prescripcion_medida"] = $ralf->ZONA_PRESCRIPCION->fecha_prescripcion_medida;
        } else {

            $estados_ct = array('Activo' => 1, 'Caduco' => 2);
            $tipos_calle = array('Avenida' => 1, 'Calle' => 2, 'Pasaje' => 3);
            $tipos_empresa = array('Principal' => 1, 'Contratista' => 2, 'Subcontratista' => 2, 'De servicios transitorios' => 4);
            $comunas = Model_St_Comuna::obtenerSinFiltroLlaveGlosa();
            $si_no = array('Si' => 1, 'No' => 2);

            $default["investigador_apellido_paterno"] = $post["investigador_apellido_paterno"];
            $default["investigador_apellido_materno"] = $post["investigador_apellido_materno"];
            $default["investigador_nombres"] = $post["investigador_nombres"];
            $default["investigador_rut"] = $post["investigador_rut"];
            $default["estado_centro_trabajo"] = (($post["estado_centro_trabajo"] != "")) ? $estados_ct[$post["estado_centro_trabajo"]] : "";
            $default["rut_empleador_principal"] = $post["rut_empleador_principal"];
            $default["nombre_empleador_principal"] = $post["nombre_empleador_principal"];
            $default["nombre_centro_trabajo"] = $post["nombre_centro_trabajo"];
            $default["correlativo_proyecto_contrato"] = $post["correlativo_proyecto_contrato"];
            $default["tipo_empresa"] = (($post["tipo_empresa"] != "")) ? $tipos_empresa[$post["tipo_empresa"]] : "";
            $default["cuv"] = $post["cuv"];
            $default["geo_latitud"] = $post["geo_latitud"];
            $default["geo_longitud"] = $post["geo_longitud"];
            $default["tipo_calle_ct"] = (($post["tipo_calle_ct"] != "")) ? $tipos_calle[$post["tipo_calle_ct"]] : "";
            $default["nombre_calle_ct"] = $post["nombre_calle_ct"];
            $default["numero_ct"] = $post["numero_ct"];
            $default["resto_direccion_ct"] = $post["resto_direccion_ct"];
            $default["localidad_ct"] = $post["localidad_ct"];
            $default["comuna_ct"] = (($post["comuna_ct"] != "")) ? $comunas[$post["comuna_ct"]] : "";

            $default["descripcion_actividad_trabajadores_ct"] = $post["descripcion_actividad_trabajadores_ct"];
            $default["n_trabajadores_propios_ct"] = $post["n_trabajadores_propios_ct"];
            $default["n_trabajadores_hombre_ct"] = $post["n_trabajadores_hombre_ct"];
            $default["n_trabajadores_mujer_ct"] = $post["n_trabajadores_mujer_ct"];

            $default["com_par_constituido"] = (($post["com_par_constituido"] != "")) ? $si_no[$post["com_par_constituido"]] : "";
            $default["experto_prevencion_riesgos"] = (($post["experto_prevencion_riesgos"] != "")) ? $si_no[$post["experto_prevencion_riesgos"]] : "";
            $default["horas_semana_dedica_ct"] = $post["horas_semana_dedica_ct"];
            $default["fecha_inicio_ct"] = $post["fecha_inicio_ct"];
            $default["tiene_fech_term"] = (($post["tiene_fech_term"] != "")) ? $si_no[$post["tiene_fech_term"]] : "";
            $default["fecha_termino_ct"] = $post["fecha_termino_ct"];
            $default["fecha_prescripcion_medida"] = $post["fecha_prescripcion_medida"];
        }
        
        return $default;
    }

    //funcion para generar el tag de documento anexo al XML
    public static function documentos_anexos($xml_id, $ralf) {
        $documentos_notif_causas = $ralf->ZONA_PRESCRIPCION;

        $documentos_notif_causas->addChild('documentos_acompanan_prescripcion', '');

        $documentos_anexos = $ralf->ZONA_PRESCRIPCION->documentos_acompanan_prescripcion;
        $anexos = ORM::factory('Adjunto')->where('xml_id', '=', $xml_id)->where('origen', '=', 'documentos_anexos')->find_all();

        foreach ($anexos as $anexo) {
            $path = $anexo->ruta;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $base64 = base64_encode($data);
            $documento_anexo = $documentos_anexos->addChild('documento_anexo', '');

            $nombre_documento = htmlspecialchars($anexo->nombre_documento, ENT_QUOTES, 'UTF-8');
            $documento_anexo->addChild('nombre_documento', $nombre_documento);

            $fecha_documento = htmlspecialchars($anexo->fecha_documento, ENT_QUOTES, 'UTF-8');
            $documento_anexo->addChild('fecha_documento', $fecha_documento);

            $autor_documento = htmlspecialchars($anexo->autor_documento, ENT_QUOTES, 'UTF-8');
            $documento_anexo->addChild('autor_documento', $autor_documento);
            
            $documento_anexo->addChild('documento', $base64);

            $tipo = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
            $documento_anexo->addChild('extension', $tipo);
        }
        return $ralf;
    }

    public function action_borrar_adjunto() {
        $this->auto_render = false;
        $adjunto_id = $this->request->param('id');
        $adjunto = ORM::factory('Adjunto', $adjunto_id);
        $adjunto_origen = $adjunto->origen;
        $nombre_documento = $adjunto->nombre_documento;
        $xml_id = $adjunto->xml_id;
        $borrado = false;
        if (isset($_POST['boton_aceptar'])) {
            $adjunto->delete();
            $borrado = true;
        }

        $this->response->body(
            View::factory('ralf2/borrar_adjunto')->set('borrado', $borrado)->set('xml_id', $xml_id)
                ->set('adjunto_origen', $adjunto_origen)
                ->set('nombre_documento', $nombre_documento)
        );
    }

    public function action_medidas_crear() {
        $this->auto_render = false;
        $mensaje_error = "";
        $errors = array();
        $xml_id = $this->request->param('id');

        $documento = ORM::factory('Xml', $xml_id);
        $documentostring = $documento->xmlstring;
        $ralf = simplexml_load_string($documentostring->XMLSTRING);

        $default["tipo"] = '';
        $default["medida_inmediata"] = '';
        $default["descripcion"] = '';
        $default["plazo_cumplimiento"] = '';
        $default["codigo_causa"] = '';
        $default["glosa_causa"] = '';

        if (isset($_POST)) {
            if (isset($_POST['boton_crear_medida'])) {
                $post = Validation::factory($_POST)
                            ->rule('tipo', 'not_empty')->label('tipo', 'Tipo')
                            ->rule('medida_inmediata', 'not_empty')->label('medida_inmediata', 'Medida Inmediata')
                            ->rule('descripcion', 'not_empty')->label('descripcion', 'Medida')
                            ->rule('codigo_causa', 'not_empty')->label('codigo_causa', 'Cod. Causa')
                            ->rule('codigo_causa', 'Utiles::validaCodigoCausa', array(':value'))
                            ->rule('glosa_causa', 'not_empty')->label('glosa_causa', 'Glosa Causa')
                            ->rule('plazo_cumplimiento', 'date')
                            ->rule('plazo_cumplimiento', 'Utiles::validateDate', array(':value'))
                            ->rule('plazo_cumplimiento', 'Utiles::fecha_minima', array(':value'))
                            ->rule('plazo_cumplimiento', 'not_empty')->label('plazo_cumplimiento', 'Plazo')
                ;

                if (!empty($_POST["plazo_cumplimiento"])) {
                    if (!($_POST["plazo_cumplimiento"] >= $ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                        $errors = $errors + array("plazo_cumplimiento" => "Fecha debe ser Mayor o igual a fecha de accidente");
                    }
                }

                if ($post->check() && count($errors) == 0) {
                    $cmc = ORM::factory('MedidaCorrectivaRalf145');
                    $cmc->tipo = $post['tipo'];
                    $cmc->medida_inmediata = $post['medida_inmediata'];
                    $cmc->descripcion = $post['descripcion'];
                    $cmc->plazo_cumplimiento = $post['plazo_cumplimiento'];
                    $cmc->codigo_causa = $post['codigo_causa'];
                    $cmc->glosa_causa = $post['glosa_causa'];
                    $cmc->xml_id = $xml_id;
                    $cmc->save();
                    $mensaje_error = "Medida correctiva agregada";
                } else {
                    $default['tipo'] = $post['tipo'];
                    $default['medida_inmediata'] = $post['medida_inmediata'];
                    $default['descripcion'] = $post['descripcion'];
                    $default['plazo_cumplimiento'] = $post['plazo_cumplimiento'];
                    $default['codigo_causa'] = $post['codigo_causa'];
                    $default['glosa_causa'] = $post['glosa_causa'];
                    $errors = $post->errors('validate') + $errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }
        
        $this->response->body(
            View::factory('ralfPrescripcion/crear_medidas_ralf145')
                ->set('errors', $errors)
                ->set('default', $default)
                ->set('xml_id', $xml_id)
                ->set('mensaje_error', $mensaje_error)
        );
    }

    public function action_borrar_medida() {
        $this->auto_render = false;
        $medida_id = $this->request->param('id');
        $medida = ORM::factory('MedidaCorrectivaRalf145', $medida_id);
        $xml_id = $medida->xml_id;
        $borrado = false;
        if (isset($_POST['boton_aceptar'])) {
            $medida->delete();
            $borrado = true;
        }
        $this->response->body(
            View::factory('ralfPrescripcion/borrar_medida')
                ->set('medida', $medida)
                ->set('borrado', $borrado)
                ->set('xml_id', $xml_id)
        );
    }

    public function action_obtener_medidas_correctivas() {
        $this->auto_render = false;
        $xml_id = $this->request->param('id');
        $adjuntos = array();
        foreach (ORM::factory('MedidaCorrectivaRalf145')->where('xml_id', '=', $xml_id)->find_all() as $a) {
            $adjuntos[] = array(
                $a->id,
                $a->codigo_causa,
                $a->glosa_causa,
                $a->tipo,
                $a->descripcion,
                $a->medida_inmediata,
                $a->plazo_cumplimiento,
                HTML::anchor("ralfPrescripcion/borrar_medida/{$a->id}", 'borrar', array('class' => 'fancybox-small'))
            );
        }
        $this->response->body(json_encode($adjuntos));
    }

    public function action_guardar_causa() {
        $this->auto_render = false;
        $mensaje_error = "";
        $errors = array();
        $medida_id = $this->request->param('id');
        $xml_id = $this->request->param('id2');

        $documento = ORM::factory('Xml', $xml_id);
        $documentostring = $documento->xmlstring;
        $ralf = simplexml_load_string($documentostring->XMLSTRING);

        $default["codigo_causa"] = '';
        $default["glosa_causa"] = '';

        if (isset($_POST)) {
            if (isset($_POST['boton_guardar_causa'])) {
                $post = Validation::factory($_POST)
                            ->rule('codigo_causa', 'not_empty')->label('codigo_causa', 'Codigo Causa')
                            ->rule('glosa_causa', 'not_empty')->label('glosa_causa', 'Glosa Causa');

                if ($post->check() && count($errors) == 0) {
                    $cmc = ORM::factory('CausasRalf145');
                    $cmc->codigo_causa = $post['codigo_causa'];
                    $cmc->glosa_causa = $post['glosa_causa'];
                    $cmc->xml_id = $xml_id;
                    $cmc->medida_ralf145 = $medida_id;
                    $cmc->save();
                    $mensaje_error = "Causa agregada";
                } else {
                    $default['codigo_causa'] = $post['codigo_causa'];
                    $default['glosa_causa'] = $post['glosa_causa'];
                    $errors = $post->errors('validate') + $errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }
        $this->response->body(
            View::factory('ralfPrescripcion/agregar_causa_ralf145')
                ->set('errors', $errors)
                ->set('default', $default)
                ->set('xml_id', $xml_id)
                ->set('mensaje_error', $mensaje_error)
        );
    }

    public function action_obtener_causas_ralf145() {
        $this->auto_render = false;
        $xml_id = $this->request->param('id');
        $adjuntos = array();
        foreach (ORM::factory('CausasRalf145')->where('xml_id', '=', $xml_id)->find_all() as $c) {
            $adjuntos[] = array(
                $c->id,
                $c->codigo_causa,
                $c->glosa_causa,
                $c->xml_id,
                $c->medida_ralf145,
                HTML::anchor("ralfPrescripcion/borrar_causa145/{$c->id}", 'borrar', array('class' => 'fancybox-small'))
            );
        }
        
        $this->response->body(json_encode($adjuntos));
    }

    public function action_borrar_causa145() {
        $this->auto_render = false;
        $causa_id = $this->request->param('id');
        $causa = ORM::factory('CausasRalf145', $causa_id);
        $xml_id = $medida->xml_id;
        $borrado = false;
        if (isset($_POST['boton_aceptar'])) {
            $medida->delete();
            $borrado = true;
        }
        $this->response->body(
            View::factory('ralfPrescripcion/borrar_causa145')
                ->set('causa', $causa)
                ->set('borrado', $borrado)
                ->set('causa_id', $causa_id)
        );
    }

    /*
     * funcion para generar xml correspondiente
     * a prescripcion medidas
     */
    public static function medidas_correctivas($xml_id, $ralf) {

        $medidas_correctivas_xml = $ralf->ZONA_PRESCRIPCION;
        $medida_correctiva = $ralf->ZONA_PRESCRIPCION;

        $medidas_correctivas = ORM::factory('MedidaCorrectivaRalf145')->where('xml_id', '=', $xml_id)->find_all();
        foreach ($medidas_correctivas as $m) {
            $medida_correctiva = $medidas_correctivas_xml->addChild('prescripcion_medidas', '');


            $folio_medida_prescrita = htmlspecialchars($m->id, ENT_QUOTES, 'UTF-8');
            $medida_correctiva->addChild('folio_medida_prescrita', $folio_medida_prescrita);

            $tipo_medida_prescrita = htmlspecialchars($m->tipo, ENT_QUOTES, 'UTF-8');
            $medida_correctiva->addChild('tipo_medida_prescrita', $tipo_medida_prescrita);

            /* Por ahora no se usara */
            /*
              $codigo_medida = htmlspecialchars($m->codigo, ENT_QUOTES, 'UTF-8');
              $medida_correctiva->addChild('codigo_medida', $codigo_medida);
             */

            $descripcion_medida_prescrita = htmlspecialchars($m->descripcion, ENT_QUOTES, 'UTF-8');
            $medida_correctiva->addChild('descripcion_medida_prescrita', $descripcion_medida_prescrita);

            $medida_inmediata = htmlspecialchars($m->medida_inmediata, ENT_QUOTES, 'UTF-8');
            $medida_correctiva->addChild('medida_inmediata', $medida_inmediata);

            $fecha_plazo_cumplimiento_medida = htmlspecialchars($m->plazo_cumplimiento, ENT_QUOTES, 'UTF-8');

            $fechaFormat = strtotime($fecha_plazo_cumplimiento_medida);
            $fechaNueva = date('Y-m-d', $fechaFormat);

            $medida_correctiva->addChild('fecha_plazo_cumplimiento_medida', $fechaNueva);

            $codigo_causa = htmlspecialchars($m->codigo_causa, ENT_QUOTES, 'UTF-8');
            $medida_correctiva->addChild('codigo_causa', $codigo_causa);
        }

        return $ralf;
    }

    public function setearZCT($ralf, $post) {

        $estados_ct = array('Activo' => 1, 'Caduco' => 2);
        $tipos_calle = array('Avenida' => 1, 'Calle' => 2, 'Pasaje' => 3);
        $tipos_empresa = array('Principal' => 1, 'Contratista' => 2, 'Subcontratista' => 3, 'De servicios transitorios' => 4);
        $comunas = Model_St_Comuna::obtener();
        $si_no = array('Si' => 1, 'No' => 2);
        $cod_comuna_ct = "";

        foreach ($comunas as $codigo => $glosa) {
            if ($glosa == $post["comuna_ct"]) {
                $cod_comuna_ct = $codigo;
                break;
            }
        }
        
        $ralf->ZONA_ZCT->centro_de_trabajo->CUV = $post["cuv"];
        $ralf->ZONA_ZCT->centro_de_trabajo->estado_centro_trabajo = (($post["estado_centro_trabajo"] != "")) ? $estados_ct[$post["estado_centro_trabajo"]] : "";
        $ralf->ZONA_ZCT->centro_de_trabajo->rut_empleador_principal = $post["rut_empleador_principal"];
        $ralf->ZONA_ZCT->centro_de_trabajo->nombre_empleador_principal = $post["nombre_empleador_principal"];
        $ralf->ZONA_ZCT->centro_de_trabajo->nombre_centro_trabajo = $post["nombre_centro_trabajo"];
        $ralf->ZONA_ZCT->centro_de_trabajo->correlativo_proyecto_contrato = $post["correlativo_proyecto_contrato"];
        $ralf->ZONA_ZCT->centro_de_trabajo->tipo_empresa = (($post["tipo_empresa"] != "")) ? $tipos_empresa[$post["tipo_empresa"]] : "";
        $ralf->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_latitud = $post["geo_latitud"];
        $ralf->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_longitud = $post["geo_longitud"];
        $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->tipo_calle_ct = (($post["tipo_calle_ct"] != "")) ? $tipos_calle[$post["tipo_calle_ct"]] : "";
        $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->nombre_calle_ct = $post["nombre_calle_ct"];
        $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->numero_ct = $post["numero_ct"];
        $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->resto_direccion_ct = $post["resto_direccion_ct"];
        $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->localidad_ct = $post["localidad_ct"];
        $ralf->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->comuna_ct = $cod_comuna_ct;
        $ralf->ZONA_ZCT->centro_de_trabajo->descripcion_actividad_trabajadores_ct = $post["descripcion_actividad_trabajadores_ct"];
        $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_propios_ct = $post["n_trabajadores_propios_ct"];
        $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_hombre_ct = $post["n_trabajadores_hombre_ct"];
        $ralf->ZONA_ZCT->centro_de_trabajo->n_trabajadores_mujer_ct = $post["n_trabajadores_mujer_ct"];
        $ralf->ZONA_ZCT->centro_de_trabajo->com_par_constituido = (($post["com_par_constituido"] != "")) ? $si_no[$post["com_par_constituido"]] : "";
        
        //EXPERTO CT
        $ralf->ZONA_ZCT->centro_de_trabajo->experto_prevencion_riesgos = (($post["experto_prevencion_riesgos"] != "")) ? $si_no[$post["experto_prevencion_riesgos"]] : "";
        if ($post["experto_prevencion_riesgos"] == 'Si' && $post["horas_semana_dedica_ct"] != "") {
            unset($ralf->ZONA_ZCT->centro_de_trabajo->fecha_inicio_ct);
            unset($ralf->ZONA_ZCT->centro_de_trabajo->tiene_fech_term);
            $ralf->ZONA_ZCT->centro_de_trabajo->horas_semana_dedica_ct = $post["horas_semana_dedica_ct"];
        } else {
            unset($ralf->ZONA_ZCT->centro_de_trabajo->horas_semana_dedica_ct);
        }
        
        //FECHAS CT
        $ralf->ZONA_ZCT->centro_de_trabajo->fecha_inicio_ct = $post["fecha_inicio_ct"];
        if ($post["tiene_fech_term"] == "") {
            $ralf->ZONA_ZCT->centro_de_trabajo->tiene_fech_term = "";
        } else {
            $ralf->ZONA_ZCT->centro_de_trabajo->tiene_fech_term = $si_no[$post["tiene_fech_term"]];
        }

        if ($post["fecha_termino_ct"] != "") {
            unset($ralf->ZONA_ZCT->centro_de_trabajo->fecha_termino_ct);
            $ralf->ZONA_ZCT->centro_de_trabajo->fecha_termino_ct = $post["fecha_termino_ct"];
        } else {
            unset($ralf->ZONA_ZCT->centro_de_trabajo->fecha_termino_ct);
        }
        
        return $ralf;
    }

}
