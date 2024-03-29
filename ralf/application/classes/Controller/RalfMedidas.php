<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_RalfMedidas extends Controller_Website {

    public function action_insertar() {

        if ($this->get_rol() != 'operador') {
            $this->redirect("error");
        }

        $caso_id = $this->request->param('id');
        if (empty($caso_id) || !is_numeric($caso_id)) {
            $this->template->mensaje_error = 'Error, Error al cargar caso';
            $this->template->contenido = '';
            return;
        }
        $caso = ORM::factory('Caso', $caso_id);

        if (!$caso->loaded()) {
            $this->template->mensaje_error = 'Se debe agregar una denuncia al caso.';
            $this->template->contenido = '';
            return;
        }
        //Busco un documento ralfMedidas
        $documento = $caso->xmls
                        ->where('TPXML_ID', 'IN', array(141))
                        ->where('ESTADO', 'IN', array(1, 2))->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;  die();
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Se debe agregar un RALF Accidente.';
            $this->template->contenido = '';
            return;
        }

        $ralf_anterior = $caso->xmls->where('TPXML_ID', '=', 142)->where('ESTADO', '!=', 3)->find();
        if ($ralf_anterior->loaded()) {
            $this->template->mensaje_error = 'Error, Ya se encuentra una Ralf Medidas insertado';
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
                    $dom->ownerDocument->createElement('cun', $cun), $dom->firstChild
            );
        }

        if (isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion = dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'RALF_Medidas');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());

        //faillon
        //modificacion de xml segun cambios a archivos xsd
        $zona_inmediatas_string = "<ZONA_INMEDIATAS>
        <medidas_inmediatas></medidas_inmediatas>
        <fecha_notificacion_medidas_inmediatas></fecha_notificacion_medidas_inmediatas>
        <investigador>
            <apellido_paterno></apellido_paterno>
            <apellido_materno></apellido_materno>
            <nombres></nombres>
            <rut></rut>
        </investigador>
        <telefono_investigador>
            <cod_pais></cod_pais>
            <cod_area></cod_area>
            <numero></numero>
        </telefono_investigador>
        <medidas_inmediatas_firmadas_por_empleador></medidas_inmediatas_firmadas_por_empleador>
    </ZONA_INMEDIATAS></RALF_Medidas>";
        $ralf = str_replace('</RALF_Medidas>', $zona_inmediatas_string, $ralf_preparacion->saveXML());

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
        $xml_insert->TPXML_ID = 142;
        $xml_insert->VALIDO = 0;
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN = $documento->XML_ID;
        $xml_insert->save();

        $doc = simplexml_load_string($xmlstring->XMLSTRING);
        $doc->ZONA_A->documento->folio = $xml_insert->XML_ID;
        $xmlstring->XMLSTRING = $doc->saveXML();
        $xmlstring->save();
        $this->redirect("ralfMedidas/crear/$xml_insert->XML_ID");
    }

    public function action_crear() {

        if ($this->get_rol() != 'operador') {
            $this->redirect("error");
        }

        $xml_id = $this->request->param('id');

        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'Error, Falta id de ralf';
            $this->template->contenido = '';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar documento';
            $this->template->contenido = '';
            return;
        }

        if ($documento->VALIDO == 1 && $documento->ESTADO != 5) {
            $this->redirect("documento/ver/$documento->XML_ID");
        }
        $documentostring = $documento->xmlstring;
        $ralf = simplexml_load_string($documentostring->XMLSTRING);

        $errores_esquema = NULL;
        $errors = array();
        $mensaje_error = null;
        if (isset($_POST) AND Valid::not_empty($_POST)) {
            if (isset($_POST['boton_finalizar'])) {
                $post = Validation::factory($_POST)
                        ->rule('fecha_notificacion_medidas_inmediatas', 'not_empty')
                        ->rule('fecha_notificacion_medidas_inmediatas', 'Utiles::validateDate', array(':value'))
                        ->rule('fecha_notificacion_medidas_inmediatas', 'date')
                        ->rule('fecha_notificacion_medidas_inmediatas', 'Utiles::whitespace', array(':value'))
                        ->label('fecha_notificacion_medidas_inmediatas', 'fecha notificación medidas inmediatas')
                        ->rule('nombres', 'not_empty')->label('nombres', 'Nombres')
                        ->rule('nombres', 'Utiles::whitespace', array(':value'))
                        ->rule('apellido_paterno', 'not_empty')
                        ->rule('apellido_paterno', 'Utiles::whitespace', array(':value'))
                        ->label('apellido_paterno', 'Ap. Paterno')
                        ->rule('apellido_materno', 'not_empty')
                        ->rule('apellido_materno', 'Utiles::whitespace', array(':value'))
                        ->label('apellido_materno', 'Ap. materno')
                        ->rule('rut', 'not_empty')
                        ->rule('rut', 'Utiles::whitespace', array(':value'))
                        ->rule('rut', 'not_empty')->rule('rut', 'Utiles::rut', array(':value'))
                        ->rule('rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->label('rut', 'Rut')
                ;

                if (!empty($post["cod_area"])) {
                    $post = $post->rule('cod_area', 'numeric')
                                    ->rule('cod_area', 'Utiles::whitespace', array(':value'))
                                    ->rule('numero', 'Utiles::whitespace', array(':value'))
                                    ->rule('numero', 'not_empty')->rule('numero', 'numeric')->label('numero', 'Numero');
                }

                if (!empty($post["numero"])) {
                    $post = $post->rule('numero', 'numeric')
                                    ->rule('cod_area', 'Utiles::whitespace', array(':value'))
                                    ->rule('numero', 'Utiles::whitespace', array(':value'))
                                    ->rule('cod_area', 'not_empty')->rule('cod_area', 'numeric')->label('cod_area', 'cod. area');
                }

                if (!empty($_POST["fecha_notificacion_medidas_inmediatas"])) {
                    if (!($_POST["fecha_notificacion_medidas_inmediatas"] >= $ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                        $errors = $errors + array("fecha_notificacion_medidas_inmediatas" => "Fecha notificacion Mayor o igual a fecha de accidente");
                    }
                }

                if (!empty($_POST["fecha_accidente"])) {
                    if (!($_POST["fecha_accidente"] >= $ralf->ZONA_C->empleado->fecha_ingreso)) {
                        $errors = $errors + array("fecha_accidente" => "Fecha accidente Mayor o igual a fecha de ingreso");
                    } elseif (!($_POST["fecha_accidente"] <= date('Y-m-d'))) {
                        $errors = $errors + array("fecha_accidente" => "Fecha accidente Menor o igual a fecha actual");
                    }
                }

                $anexos = ORM::factory('Adjunto')->where('xml_id', '=', $xml_id)->where('origen', '=', 'documentos_anexos')->find_all();
                if (count($anexos) == 0) {
                    $post = $post->rule('documentos_anexos', 'not_empty')->label('documentos_anexos', 'Documentos Anexos');
                }

                $medidas_r2 = ORM::factory('Medida')->where('xml_id', '=', $xml_id)->where('origen', '=', 'medidas_ralf2')->find_all();
                if (count($medidas_r2) == 0) {
                    $errors = $errors + array("medidas" => "Debe agregar medidas");
                }

                if ($post->check() && count($errors) == 0) {

                    $ralf->ZONA_INMEDIATAS->fecha_notificacion_medidas_inmediatas = $post['fecha_notificacion_medidas_inmediatas'];
                    $ralf->ZONA_INMEDIATAS->investigador->apellido_paterno = $post['apellido_paterno'];
                    $ralf->ZONA_INMEDIATAS->investigador->apellido_materno = $post['apellido_materno'];
                    $ralf->ZONA_INMEDIATAS->investigador->nombres = $post['nombres'];
                    $ralf->ZONA_INMEDIATAS->investigador->rut = $post['rut'];

                    if (!empty($post['cod_area']) && !empty($post['numero'])) {
                        $ralf->ZONA_INMEDIATAS->telefono_investigador->cod_pais = 56;
                        $ralf->ZONA_INMEDIATAS->telefono_investigador->cod_area = $post['cod_area'];
                        $ralf->ZONA_INMEDIATAS->telefono_investigador->numero = $post['numero'];
                    } else {
                        unset($ralf->ZONA_INMEDIATAS->telefono_investigador);
                    }

                    //faillon
                    //logica de documentos anexos
                    $ralf_bd = $ralf->saveXML();
                    $ralf = Controller_RalfMedidas::documentos_anexos($xml_id, $ralf);

                    if (isset($ralf->ZONA_C->empleado->trabajador->rut)) {
                        $ralf = Documento::transformarZonaCNueva($ralf);
                    }

                    $ralf_string = $ralf->saveXML();

                    //echo $ralf->saveXML();

                    $ralf = Controller_RalfMedidas::medidas($xml_id, $ralf);
                    $ralf = Documento::zona_o($ralf->saveXML());

                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf), $documento->TPXML_ID);
                    $valido = Utiles::valida_xml($final, dirname(__FILE__) . '/../../../media/xsd/ralf/SISESAT_RALF_Medidas.1.0.xsd');
                    if ($valido['estado']) {
                        $documentostring->XMLSTRING = $ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO = 1;
                        $documento->ESTADO = 6;
                        $documento->save();
                        $ralf2 = ORM::factory('Ralf2')->where('xml_id', '=', $xml_id)->find();
                        $medidas_string = "";
                        foreach (ORM::factory('Medida')->where('xml_id', '=', $xml_id)->where('origen', '=', 'medidas_ralf2')->find_all() as $med) {
                            $medidas_string .= $med->medida . "-";
                        }
                        $ralf2->medida = $medidas_string;
                        $ralf2->fecha_notificacion_medidas_inmediatas = $post['fecha_notificacion_medidas_inmediatas'];
                        $ralf2->apellido_paterno = $post['apellido_paterno'];
                        $ralf2->apellido_materno = $post['apellido_materno'];
                        $ralf2->nombres = $post['nombres'];
                        $ralf2->rut = $post['rut'];
                        $ralf2->cod_area = $post['cod_area'];
                        $ralf2->numero = $post['numero'];
                        $ralf2->xml_id = $xml_id;
                        $ralf2->tipo_xml = 142;
                        $ralf2->save();
                        $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
                    } else {
                        $ralf = simplexml_load_string($final);
                        $errores_esquema = $valido['mensaje'];
                        $mensaje_error = "Operación fallida. Hay " . count($errores_esquema) . " error(es).";
                    }
                } else {
                    $errors = $post->errors('validate') + $errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            } elseif (isset($_POST['boton_incompleta'])) {
                $post = Validation::factory($_POST);

                $ralf->ZONA_INMEDIATAS->fecha_notificacion_medidas_inmediatas = $post['fecha_notificacion_medidas_inmediatas'];
                $ralf->ZONA_INMEDIATAS->investigador->apellido_paterno = $post['apellido_paterno'];
                $ralf->ZONA_INMEDIATAS->investigador->apellido_materno = $post['apellido_materno'];
                $ralf->ZONA_INMEDIATAS->investigador->nombres = $post['nombres'];
                $ralf->ZONA_INMEDIATAS->investigador->rut = $post['rut'];
                $ralf->ZONA_INMEDIATAS->telefono_investigador->cod_area = $post['cod_area'];
                $ralf->ZONA_INMEDIATAS->telefono_investigador->numero = $post['numero'];
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
        $this->template->contenido = $this->template->contenido = View::factory('ralfMedidas/crear')
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
            $default['fecha_notificacion_medidas_inmediatas'] = $ralf->ZONA_INMEDIATAS->fecha_notificacion_medidas_inmediatas;

            //============= COMENTADO DOCUMENTOS ANEXOS ================
            //$default['documentos_anexos']=$ralf->ZONA_Q->medidas_inmediatas->documentos_anexos;
            //Faltaria agregar tag de documentos anexos?
            $default['apellido_paterno'] = $ralf->ZONA_INMEDIATAS->investigador->apellido_paterno;
            $default['apellido_materno'] = $ralf->ZONA_INMEDIATAS->investigador->apellido_materno;
            $default['nombres'] = $ralf->ZONA_INMEDIATAS->investigador->nombres;
            $default['rut'] = $ralf->ZONA_INMEDIATAS->investigador->rut;
            $default['cod_area'] = $ralf->ZONA_INMEDIATAS->telefono_investigador->cod_area;
            $default['numero'] = $ralf->ZONA_INMEDIATAS->telefono_investigador->numero;
        } else {
            $default['fecha_notificacion_medidas_inmediatas'] = $post['fecha_notificacion_medidas_inmediatas'];

            //========== COMENTADO DOCUMENTOS ANEXOS =============
            //$default['documentos_anexos']=$ralf->ZONA_Q->medidas_inmediatas->documentos_anexos;
            $default['apellido_paterno'] = $post['apellido_paterno'];
            $default['apellido_materno'] = $post['apellido_materno'];
            $default['nombres'] = $post['nombres'];
            $default['rut'] = $post['rut'];
            $default['cod_area'] = $post['cod_area'];
            $default['numero'] = $post['numero'];
        }
        return $default;
    }

    public static function medidas($xml_id, $ralf) {

        $medidas_xml = $ralf->ZONA_INMEDIATAS;
        unset($medidas_xml->medidas_inmediatas);
        $medidas = ORM::factory('Medida')->where('xml_id', '=', $xml_id)->where('origen', '=', 'medidas_ralf2')->find_all();

        $dom = dom_import_simplexml($ralf->ZONA_INMEDIATAS);
        $nodoReferencia = dom_import_simplexml($dom->getElementsByTagName('fecha_notificacion_medidas_inmediatas')->item(0));

        foreach ($medidas as $m) {
            $variable = htmlspecialchars($m->medida, ENT_QUOTES, 'UTF-8');
            $dom->insertBefore($dom->ownerDocument->createElement('medidas_inmediatas', $variable), $nodoReferencia);
        }

        /* foreach ($medidas as $m) {
          $variable=htmlspecialchars($m->medida, ENT_QUOTES, 'UTF-8');
          $medidas_xml->addChild('medidas_inmediatas', $variable);
          } */
        //echo $ralf->saveXML();
        return $ralf;
    }

    public function action_borrar_medida() {
        $this->auto_render = false;
        $id = $this->request->param('id');
        $medida = ORM::factory('Medida', $id);
        $borrado = false;
        $xml_id = $medida->xml_id;
        
        if (isset($_POST['boton_aceptar'])) {
            $medida->delete();
            $borrado = true;
        }
        
        $this->response->body(
                View::factory('ralfMedidas/borrar_medida')->set('medida', $medida)->set('borrado', $borrado)->set('xml_id', $xml_id)
        );
    }

    public static function documentos_anexos($xml_id, $ralf) {
        $documentos_anexos = $ralf->ZONA_INMEDIATAS->medidas_inmediatas_firmadas_por_empleador;
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
                View::factory('ralfMedidas/borrar_adjunto')->set('borrado', $borrado)->set('xml_id', $xml_id)
                        ->set('adjunto_origen', $adjunto_origen)
                        ->set('nombre_documento', $nombre_documento)
        );
    }

}
