<?php defined('SYSPATH') or die('No direct script access.');

class Controller_RalfRecargoTasa extends Controller_Website{
        
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
                ->where('TPXML_ID','IN', array(147))
                ->where('ESTADO','IN', array(1, 2))
                ->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;die();
        if(!$documento->loaded()) {
            $this->template->mensaje_error = 'Se debe agregar una RALF Notificación.';
            $this->template->contenido = '';
            return;
        }
                


        $ralf_anterior = $caso->xmls
                ->where('TPXML_ID','=', 148)
                ->where('ESTADO','!=', 3)
                ->find();

        if($ralf_anterior->loaded()) {
            $this->template->mensaje_error = 'Error, Ya se encuentra una Ralf insertada';
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
        
        //Eliminar la zona anterior para poder firmarlo
        if(isset($xml_documento->ZONA_NOTIFICACION_RALF)) {
            unset($xml_documento->ZONA_NOTIFICACION_RALF);
        }

        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }
        
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
        Documento::clonishNode($documento_preparacion, 'RALF_Recargo_tasa');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());

        //agregar nuevo tag de acuerdo a los cambios en xsds
        $zona_recargo_tasa_string=
                                    "<ZONA_Recargo_Tasa>
                                    <Resolucion_recargo_tasa>
                                        <Tipo_resolucion_informada></Tipo_resolucion_informada>
                                        <Nro_de_resolucion></Nro_de_resolucion>
                                        <Fecha_de_resolucion></Fecha_de_resolucion>
                                    </Resolucion_recargo_tasa>
                                    <Recargo_tasa>
                                        <Causal_de_recargo></Causal_de_recargo>
                                        <Proceso_asociado_al_recargo></Proceso_asociado_al_recargo>
                                        <Otro_proceso_asociado_al_recargo></Otro_proceso_asociado_al_recargo>
                                        <Nro_Trabajadores_entidad_empleadora_TT></Nro_Trabajadores_entidad_empleadora_TT>
                                        <Magnitud_de_incumplimiento_TA></Magnitud_de_incumplimiento_TA>
                                        <Porcentaje_base_recargo></Porcentaje_base_recargo>
                                        <Porcentaje_del_recargo></Porcentaje_del_recargo>
                                        <Tasa_adicional_110></Tasa_adicional_110>
                                        <Recargo_resultante></Recargo_resultante>
                                        <Tasa_cot_adicional></Tasa_cot_adicional>
                                        <Tasa_adicional_con_recargo></Tasa_adicional_con_recargo>
                                        <Vigencia></Vigencia>
                                        <Declaracion_CT>
                                            <Centro_de_Trabajo>
                                                <CUV></CUV>
                                                <Rut_empleador_o_Rut_trabajador_independiente></Rut_empleador_o_Rut_trabajador_independiente>
                                                <Rut_empleador_principal></Rut_empleador_principal>
                                                <Geo_latitud></Geo_latitud>
                                                <Geo_longitud></Geo_longitud>
                                            </Centro_de_Trabajo>
                                        </Declaracion_CT>
                                    </Recargo_tasa>
                                </ZONA_Recargo_Tasa>
                                </RALF_Recargo_tasa>";

        $ralf=str_replace('</RALF_Recargo_tasa>', $zona_recargo_tasa_string, $ralf_preparacion->saveXML());
        
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
        $xml_insert->TPXML_ID = 148;
        $xml_insert->VALIDO = 0;
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN = $documento->XML_ID;
        $xml_insert->save();
        
        //Insertar 
        $ralfRecargoTasa = ORM::factory('RalfRecargoTasa');
        $ralfRecargoTasa->xml_id = $documento->XML_ID;
        $ralfRecargoTasa->save();

        $doc=simplexml_load_string($xmlstring->XMLSTRING);
        $doc->ZONA_A->documento->folio = $xml_insert->XML_ID;
        $xmlstring->XMLSTRING = $doc->saveXML();
        $xmlstring->save();
        $this->redirect("ralfRecargoTasa/crear/$xml_insert->XML_ID");
    }
    
    public function action_crear() {
        
        if($this->get_rol()!='operador') {
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
            $this->redirect("documento/ralfRecargoTasa/$documento->XML_ID");
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


        //NuevosSelects
        //NUEVOS SELECTS
        $list_tipo_resolucion_informada = array(1 => 'Aplica recargo', 
                                                2 => 'Modifica recargo', 
                                                3 => 'Pone término al recargo', 
                                                4 => 'Deja sin efecto recargo');
        $list_causal_de_recargo = array(1 => 'Causal letra a) artículo 15 D.S. 67', 
                                        2 => 'Causal letra b) artículo 15 D.S. 67', 
                                        3 => 'Causal letra c) artículo 15 D.S. 67', 
                                        4 => 'Causal letra d) artículo 15 D.S. 67', 
                                        5 => 'Causal letra e) artículo 15 D.S. 67', 
                                        6 => 'Causal del inciso final del articulo 66 Ley N°16.744');
        $list_proceso_asociado_al_recargo = array(  1 => 'Prescripción de medidas por accidente grave o fatal', 
                                                    2 => 'Prescripción de medidas por enfermedad profesional', 
                                                    3 => 'Prescripción de medidas por accidente del trabajo', 
                                                    4 => 'Prescripción de medidas por proceso de vigilancia', 
                                                    5 => 'Prescripción de medidas por asesoría IPER', 
                                                    6 => 'Prescripción de medidas por autoevaluación de riesgos críticos', 
                                                    7 => 'Otro');
        $list_porcentaje_base_recargo = array(  1 => '20%', 
                                                2 => '25%', 
                                                3 => '32%', 
                                                4 => '40%', 
                                                5 => '50%');
        if (isset($_POST) AND Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_finalizar'])) {
                $post = Validation::factory($this->request->post())
                ->rule('tipo_resolucion', 'Utiles::whitespace',array(':value'))
                ->rule('tipo_resolucion', 'not_empty')->label('tipo_resolucion', 'Tipo de resolución informada')
                ->rule('nro_resolucion', 'Utiles::whitespace',array(':value'))
                ->rule('nro_resolucion', 'not_empty')->label('nro_resolucion', 'Número de resolución')
                ->rule('fecha_de_resolucion', 'date')
                ->rule('fecha_de_resolucion','Utiles::validateDate',array(':value')) 
                ->rule('fecha_de_resolucion', 'not_empty')->label('fecha_de_resolucion', 'Fecha de resolución')   
                ->rule('causal_de_recargo', 'Utiles::whitespace',array(':value'))
                ->rule('causal_de_recargo', 'not_empty')->label('causal_de_recargo', 'Causal de recargo')
                ->rule('proceso_asociado_al_recargo', 'Utiles::whitespace',array(':value'))
                ->rule('proceso_asociado_al_recargo', 'not_empty')->label('proceso_asociado_al_recargo', 'Proceso asociado al recargo')
                ->rule('nro_trabajadores_entidad_empleadora_TT', 'Utiles::whitespace',array(':value'))
                ->rule('nro_trabajadores_entidad_empleadora_TT', 'not_empty')->label('nro_trabajadores_entidad_empleadora_TT', 'Número total de trabajadores')
                ->rule('magnitud_de_incumplimiento_TA', 'Utiles::whitespace',array(':value'))
                ->rule('magnitud_de_incumplimiento_TA', 'not_empty')->label('magnitud_de_incumplimiento_TA', 'Magnitud del incumplimiento')
                ->rule('porcentaje_base_recargo', 'Utiles::whitespace',array(':value'))
                ->rule('porcentaje_base_recargo', 'not_empty')->label('porcentaje_base_recargo', 'Porcentaje de base del recargo')
                ->rule('porcentaje_del_recargo', 'Utiles::whitespace',array(':value'))
                ->rule('porcentaje_del_recargo', 'not_empty')->label('porcentaje_del_recargo', 'Porcentaje del recargo')
                ->rule('tasa_adicional_110', 'Utiles::whitespace',array(':value'))
                ->rule('tasa_adicional_110', 'not_empty')->label('tasa_adicional_110', 'Tasa adicional DS 110')
                ->rule('recargo_resultante', 'Utiles::whitespace',array(':value'))
                ->rule('recargo_resultante', 'not_empty')->label('recargo_resultante', 'Recargo resultante')
                ->rule('tasa_cot_adicional', 'Utiles::whitespace',array(':value'))
                ->rule('tasa_cot_adicional', 'not_empty')->label('tasa_cot_adicional', 'Tasa de cotización adicional')
                ->rule('tasa_adicional_con_recargo', 'Utiles::whitespace',array(':value'))
                ->rule('tasa_adicional_con_recargo', 'not_empty')->label('tasa_adicional_con_recargo', 'Tasa adicional con recargo')
                ->rule('fecha_vigencia', 'Utiles::whitespace',array(':value'))
                ->rule('fecha_vigencia', 'not_empty')->label('fecha_vigencia', 'Vigencia')
                ;
                
                if ($post["proceso_asociado_al_recargo"] == 7) {
                    $post = $post->rule('otro_proceso_asociado_al_recargo', 'Utiles::whitespace',array(':value'))
                    ->rule('otro_proceso_asociado_al_recargo', 'not_empty')->label('otro_proceso_asociado_al_recargo', 'Otro proceso asociado al recargo');
                }

                if($post->check() && count($errors) == 0) {
                    //Guarda Los datos en el XML
                    $ralfLimpio = $ralf;
                    $ralf = Controller_RalfRecargoTasa::setearCtRecargoTasa($ralf, $post);
                    $ralf_bd = $ralfTempBD->saveXML();
                    //para enviar al navegar el xml resultado
                    $ralf->saveXML();
                    $ralf = Documento::zona_o($ralf->saveXML());
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf), $documento->TPXML_ID);
                    $valido = Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_Recargo_tasa.1.0.xsd');
                    //$valido['estado'] = true;
                    if($valido['estado']) {
                        $documentostring->XMLSTRING = $ralf_bd;
                        $documentostring->save();
                        $xmlRalf = ORM::factory('Xml')->where('xml_id','=',$xml_id)->find();
                        $xmlString = $documentostring->XMLSTRING;
                        $ralfRecargoTasaBD = ORM::factory('RalfRecargoTasa')->where('xml_id','=',$xml_id)->find();
                        //MEJORAS CAMBIAR POR UNA FUNCION
                        //Updatear tabla ralfRecargoTasa
                        $ralfRecargoTasaBD->tipo_resolucion_informada        = $ralfLimpio->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Tipo_resolucion_informada;
                        $ralfRecargoTasaBD->nro_resolucion                   = $ralfLimpio->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Nro_de_resolucion;                    
                        $ralfRecargoTasaBD->fecha_resolucion                 = $ralfLimpio->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Fecha_de_resolucion;
                        $ralfRecargoTasaBD->causal_recargo                   = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Causal_de_recargo;
                        $ralfRecargoTasaBD->proceso_asociado_recargo         = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Proceso_asociado_al_recargo;
                        if ($post["proceso_asociado_al_recargo"] == 7) {
                            $ralfRecargoTasaBD->otro_proceso_asociado_recargo    = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Otro_proceso_asociado_al_recargo;
                        }
                        $ralfRecargoTasaBD->nro_total_trabajadores           = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Nro_Trabajadores_entidad_empleadora_TT;
                        $ralfRecargoTasaBD->magnitud_incumplimiento          = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Magnitud_de_incumplimiento_TA;
                        $ralfRecargoTasaBD->porcentaje_base_recargo          = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Porcentaje_base_recargo;
                        $ralfRecargoTasaBD->porcentaje_recargo               = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Porcentaje_del_recargo;
                        $ralfRecargoTasaBD->tase_adicional_110               = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_adicional_110;
                        $ralfRecargoTasaBD->recargo_resultante               = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Recargo_resultante;
                        $ralfRecargoTasaBD->tasa_cot_adicional               = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_cot_adicional;
                        $ralfRecargoTasaBD->tasa_adicional_recargo           = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_adicional_con_recargo;
                        $ralfRecargoTasaBD->vigencia                         = $ralfLimpio->ZONA_Recargo_Tasa->Recargo_tasa->Vigencia;
                        $ralfRecargoTasaBD->centro_trabajo                   = $ralfLimpio->ZONA_Recargo_Tasa->Declaracion_CT->Centro_de_Trabajo;
                        $ralfRecargoTasaBD->save();

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
                $ralf = Controller_RalfRecargoTasa::setearCtRecargoTasa($ralf, $post);
                $documentostring->XMLSTRING = $ralf->saveXML();
                $documentostring->save();
                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
            }
        }
        
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf;
        $this->template->mensaje_error = $mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralfRecargoTasa/crear')
                ->set('data', $data)
                ->set('back_page', URL::site("caso/ver_caso/{$documento->CASO_ID}", 'http'))
                ->set('errors',$errors)
                ->set('default',  $this->values_default($ralf,$_POST))
                ->set('errores_esquema', $errores_esquema)
                ->set('xml_id', $xml_id)
                ->set('xml_id_origen', $documento->XML_ID_ORIGEN)
                ->set('documento', $documento)
            ;
        
    }
    
    public function values_default($ralf, $post) {
        if(empty($post)) {
            //Resolucion_recargo_tasa
            $default["tipo_resolucion"]                         = $ralf->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Tipo_resolucion_informada;
            $default["nro_resolucion"]                          = $ralf->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Nro_de_resolucion;
            $default["fecha_de_resolucion"]                     = $ralf->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Fecha_de_resolucion;
            //Recargo_tasa
            $default["causal_de_recargo"]                       = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Causal_de_recargo;
            $default["proceso_asociado_al_recargo"]             = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Proceso_asociado_al_recargo;
            $default["otro_proceso_asociado_al_recargo"]        = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Otro_proceso_asociado_al_recargo;
            $default["nro_trabajadores_entidad_empleadora_TT"]  = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Nro_Trabajadores_entidad_empleadora_TT;
            $default["magnitud_de_incumplimiento_TA"]           = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Magnitud_de_incumplimiento_TA;
            $default["porcentaje_base_recargo"]                 = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Porcentaje_base_recargo;
            $default["porcentaje_del_recargo"]                  = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Porcentaje_del_recargo;
            $default["tasa_adicional_110"]                      = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_adicional_110;
            $default["recargo_resultante"]                      = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Recargo_resultante;
            $default["tasa_cot_adicional"]                      = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_cot_adicional;
            $default["tasa_adicional_con_recargo"]              = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_adicional_con_recargo;
            $default["fecha_vigencia"]                          = $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Vigencia;
            //TO DO CENTROS DE TRABAJO
        } else {
            //Resolucion_recargo_tasa
            $default["tipo_resolucion"]                         = $post["tipo_resolucion"];
            $default["nro_resolucion"]                          = $post["nro_resolucion"];
            $default["fecha_de_resolucion"]                     = $post["fecha_de_resolucion"];
            //Recargo_tasa
            $default["causal_de_recargo"]                       = $post["causal_de_recargo"];
            $default["proceso_asociado_al_recargo"]             = $post["proceso_asociado_al_recargo"];
            $default["otro_proceso_asociado_al_recargo"]        = $post["otro_proceso_asociado_al_recargo"];
            $default["nro_trabajadores_entidad_empleadora_TT"]  = $post["nro_trabajadores_entidad_empleadora_TT"];
            $default["magnitud_de_incumplimiento_TA"]           = $post["magnitud_de_incumplimiento_TA"];
            $default["porcentaje_base_recargo"]                 = $post["porcentaje_base_recargo"];
            $default["porcentaje_del_recargo"]                  = $post["porcentaje_del_recargo"];
            $default["tasa_adicional_110"]                      = $post["tasa_adicional_110"];
            $default["recargo_resultante"]                      = $post["recargo_resultante"];
            $default["tasa_cot_adicional"]                      = $post["tasa_cot_adicional"];
            $default["tasa_adicional_con_recargo"]              = $post["tasa_adicional_con_recargo"];
            $default["fecha_vigencia"]                          = $post["fecha_vigencia"];
            //TO DO CENTROS DE TRABAJO
        }
        return $default;
    }

    public function setearCtRecargoTasa($ralf, $post) {
        $ralf->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Tipo_resolucion_informada                                = $post["tipo_resolucion"];
        $ralf->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Nro_de_resolucion                                        = $post["nro_resolucion"];
        $ralf->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Fecha_de_resolucion                                      = $post["fecha_de_resolucion"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Causal_de_recargo                                                   = $post["causal_de_recargo"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Proceso_asociado_al_recargo                                         = $post["proceso_asociado_al_recargo"];
        if ($post["proceso_asociado_al_recargo"] == 7) {
            $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Otro_proceso_asociado_al_recargo                                = $post["otro_proceso_asociado_al_recargo"];
        }
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Nro_Trabajadores_entidad_empleadora_TT                              = $post["nro_trabajadores_entidad_empleadora_TT"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Magnitud_de_incumplimiento_TA                                       = $post["magnitud_de_incumplimiento_TA"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Porcentaje_base_recargo                                             = $post["porcentaje_base_recargo"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Porcentaje_del_recargo                                              = $post["porcentaje_del_recargo"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_adicional_110                                                  = $post["tasa_adicional_110"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Recargo_resultante                                                  = $post["recargo_resultante"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_cot_adicional                                                  = $post["tasa_cot_adicional"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_adicional_con_recargo                                          = $post["tasa_adicional_con_recargo"];
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Vigencia                                                            = $post["fecha_vigencia"];
        //Declaracion_CT
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Declaracion_CT->Centro_de_Trabajo->CUV                                            = $ralf->ZONA_ZCT->centro_de_trabajo->CUV;
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Declaracion_CT->Centro_de_Trabajo->Rut_empleador_o_Rut_trabajador_independiente   = $ralf->ZONA_ZCT->centro_de_trabajo->rut_empleador_principal;
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Declaracion_CT->Centro_de_Trabajo->Rut_empleador_principal                        = $ralf->ZONA_ZCT->centro_de_trabajo->rut_empleador_principal;
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Declaracion_CT->Centro_de_Trabajo->Geo_latitud                                    = $ralf->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_latitud; 
        $ralf->ZONA_Recargo_Tasa->Recargo_tasa->Declaracion_CT->Centro_de_Trabajo->Geo_longitud                                   = $ralf->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_longitud;
        return $ralf;
    }
    
}