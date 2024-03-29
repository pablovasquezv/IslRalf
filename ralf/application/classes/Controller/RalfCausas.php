<?php defined('SYSPATH') or die('No direct script access.');

class Controller_RalfCausas extends Controller_Website{

    public function action_insertar() {
        if($this->get_rol()!='operador') {
            $this->redirect("error");
        }

        $caso_id=$this->request->param('id');
        if(empty ($caso_id) || !is_numeric($caso_id)){
            $this->template->mensaje_error='Error, Falta id de caso';
            $this->template->contenido='';
            return;
        }
        $caso=ORM::factory('Caso',$caso_id);

        if(!$caso->loaded()){
            $this->template->mensaje_error='Error, Error al cargar caso';
            $this->template->contenido='';
            return;
        }
        //Busco un documento ralf
        $documento=$caso->xmls
                ->where('TPXML_ID','IN', array(143))
                ->where('ESTADO','IN',array(1,2))->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;  die();
        if(!$documento->loaded()){
            $this->template->mensaje_error='Se debe agregar una RALF Investigación.';
            $this->template->contenido='';
            return;
        }

        $ralf_anterior=$caso->xmls->where('TPXML_ID','=', 144)->where('ESTADO','!=', 3)->find();
        if($ralf_anterior->loaded()){
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
        $xml_documento->ZONA_A->documento->fecha_emision=$fecha_creacion . 'T' . $hora_creacion;

        //si no viene cun agregar el del caso
        if(!isset($xml_documento->ZONA_A->documento->cun))
        {
            $cun = $documento->caso->CASO_CUN;
            $dom = dom_import_simplexml($xml_documento->ZONA_A->children());
            $dom->insertBefore(
                $dom->ownerDocument->createElement('cun', $cun),
                //$dom->ownerDocument->createElement('cun', 82080),
                $dom->firstChild
            );
        }

        if(isset($xml_documento->ZONA_INVESTIGACION)) {
            unset($xml_documento->ZONA_INVESTIGACION);
        }

        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion=dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'RALF_Causas');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());


        //faillon
        //agregar nuevo tag de acuerdo a los cambios en xsds
        //tomar como referencia ZONA_R de ralf3
        $zona_causas_string="<ZONA_CAUSAS>
        <causas_del_accidente>
            <lesion></lesion>
        </causas_del_accidente>
    </ZONA_CAUSAS></RALF_Causas>";


        $ralf=str_replace('</RALF_Causas>',$zona_causas_string,$ralf_preparacion->saveXML());
        $ralf = simplexml_load_string($ralf);


        $zona_o = $ralf->addChild('ZONA_O', '');
        $zona_o->addChild('seguridad', 'Seguridad ISL');
        $xmlstring=  ORM::factory('Xmlstring');


        $xmlstring->XMLSTRING=$ralf->saveXML();
        $xmlstring->save();


        unset($ralf);
        $xml_insert=ORM::factory('Xml');
        $xml_insert->XMLSTRING_ID=$xmlstring->XMLSTRING_ID;
        $xml_insert->ESTADO=5;
        $xml_insert->CASO_ID=$caso->CASO_ID;
        $xml_insert->TPXML_ID=144;
        $xml_insert->VALIDO=0;
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN=$documento->XML_ID;
        $xml_insert->save();

        $doc=simplexml_load_string($xmlstring->XMLSTRING);
        $doc->ZONA_A->documento->folio=$xml_insert->XML_ID;
        $xmlstring->XMLSTRING=$doc->saveXML();
        $xmlstring->save();
        $this->redirect("ralfCausas/crear/$xml_insert->XML_ID");
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


        if($documento->VALIDO==1 && $documento->ESTADO!=5) {
            $this->redirect("documento/ralfCausas/$documento->XML_ID");
        }
        $documentostring=$documento->xmlstring;
        $ralf=simplexml_load_string($documentostring->XMLSTRING);
        $ralfTempBD=simplexml_load_string($documentostring->XMLSTRING);


        $errores_esquema=NULL;
        $errors = array();
        $mensaje_error = null;
        if (isset($_POST) AND Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_finalizar'])) {
                $post = Validation::factory($_POST)
                        ->rule('investigador_apellido_paterno','Utiles::whitespace',array(':value'))
                        ->rule('investigador_apellido_paterno', 'not_empty')->label('investigador_apellido_paterno', 'Ap. paterno')
                        ->rule('investigador_apellido_materno','Utiles::whitespace',array(':value'))
                        ->rule('investigador_apellido_materno', 'not_empty')->label('investigador_apellido_materno', 'Ap. materno')
                        ->rule('investigador_nombres','Utiles::whitespace',array(':value'))
                        ->rule('investigador_nombres', 'not_empty')->label('investigador_nombres', 'Nombre')
                        ->rule('investigador_rut','Utiles::whitespace',array(':value'))
                        ->rule('investigador_rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->rule('investigador_rut','not_empty')->rule('investigador_rut','Utiles::rut',array(':value'))
                        ->rule('investigador_rut', 'not_empty')->label('investigador_rut', 'Rut');


                $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();

                if(count($anexos)==0) {
                    $post=$post->rule('documentos_anexos', 'not_empty')->label('documentos_anexos', 'Documentos Anexos');
                }

                $arbol_causas = ORM::factory('ArbolCausas')->where('xml_id','=',$xml_id)->find();
               /* echo $xml_id;
                echo '<pre>';
                var_dump($arbol_causas);
                echo '</pre>';*/
                //var_dump(count($arbol_causas));
                if(is_null($arbol_causas->arbol_id) || empty($arbol_causas->arbol_id)){
                    $post=$post->rule('err_arbol_causas', 'not_empty')->label('err_arbol_causas', 'Arbol Causas');
                }

                if($post->check() && count($errors)==0) {
                    if(isset($ralf->ZONA_CAUSAS->causas_del_accidente->investigador)){
                        unset($ralf->ZONA_CAUSAS->causas_del_accidente->investigador);
                    }

                    if(isset($ralf->ZONA_CAUSAS->documentos_acompanan_notificacion_causas)){
                        unset($ralf->ZONA_CAUSAS->documentos_acompanan_notificacion_causas);
                    }

                    $cabecera_arbol=ORM::factory('ArbolCausas')->where('xml_id','=',$xml_id)->find();

                    $ralf = Controller_RalfCausas::arbol_causas($xml_id,$ralf);

                    $ralf->ZONA_CAUSAS->causas_del_accidente->lesion = $cabecera_arbol->lesion;

                    $ralf_string=$ralf->saveXML();

                    $ralf=Controller_RalfCausas::documentos_anexos($xml_id,$ralf);

                    $ralf->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_paterno=$post["investigador_apellido_paterno"];
                    $ralf->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_materno=$post["investigador_apellido_materno"];
                    $ralf->ZONA_CAUSAS->causas_del_accidente->investigador->nombres=$post["investigador_nombres"];
                    $ralf->ZONA_CAUSAS->causas_del_accidente->investigador->rut=$post["investigador_rut"];

                    $ralfTempBD->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_paterno=$post["investigador_apellido_paterno"];
                    $ralfTempBD->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_materno=$post["investigador_apellido_materno"];
                    $ralfTempBD->ZONA_CAUSAS->causas_del_accidente->investigador->nombres=$post["investigador_nombres"];
                    $ralfTempBD->ZONA_CAUSAS->causas_del_accidente->investigador->rut=$post["investigador_rut"];


                    $ralf_bd=$ralfTempBD->saveXML();

                    //echo $ralfTempBD->saveXML();

                    $ralf  = Documento::zona_o($ralf->saveXML());
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf),$documento->TPXML_ID);
                    $final=$ralf;

                    $valido=Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_Causas.1.0.xsd');
                    if($valido['estado']) {
                        $documentostring->XMLSTRING=$ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO=1;
                        $documento->ESTADO=6;
                        $documento->save();

                        $ralfCausas=ORM::factory('RalfCausas')->where('xml_id','=',$xml_id)->find();
                        $xmlRalf=ORM::factory('Xml')->where('xml_id','=',$xml_id)->find();
                        $casoRalf = ORM::factory('Caso')->where('CASO_ID','=',$xmlRalf->CASO_ID)->find();

                        $trabajador = ORM::factory('Trabajador')->where('TRA_ID','=',$casoRalf->TRA_ID)->find();
                        $empleador = ORM::factory('Empleador')->where('EMP_ID','=',$casoRalf->EMP_ID)->find();

                        $region = ORM::factory('Region')->where('id','=',$casoRalf->REGION_ID)->find();

                        $ralfCausas->investigador_apellido_paterno=$post["investigador_apellido_paterno"];
                        $ralfCausas->investigador_apellido_materno=$post["investigador_apellido_materno"];
                        $ralfCausas->investigador_nombres=$post["investigador_nombres"];
                        $ralfCausas->investigador_rut=$post["investigador_rut"];
                        $ralfCausas->fecha_creacion = $xmlRalf->FECHA_CREACION;
                        $ralfCausas->trabajador_run = $trabajador->rut;
                        $ralfCausas->trabajador_nombres = $trabajador->nombres;
                        $ralfCausas->trabajador_apellido_paterno = $trabajador->apellido_paterno;
                        $ralfCausas->trabajador_apellido_materno = $trabajador->apellido_materno;
                        $ralfCausas->empresa_rut = $empleador->rut_empleador;
                        $ralfCausas->empresa_razon_social = $empleador->nombre_empleador;
                        $ralfCausas->region = $region->nombre;
                        $ralfCausas->xml_id=$xml_id;
                        $ralfCausas->save();

                        $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
                    } else {
                        $ralf=simplexml_load_string($final);
                        $errores_esquema = $valido['mensaje'];
                        $mensaje_error = "Operación fallida. Hay " . count($errores_esquema) . " error(es).";
                    }
                }else {
                    $errors = $post->errors('validate')+$errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }elseif(isset ($_POST['boton_incompleta'])) {
                $post = Validation::factory($_POST);

                $ralf->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_paterno=$post["investigador_apellido_paterno"];
                $ralf->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_materno=$post["investigador_apellido_materno"];
                $ralf->ZONA_CAUSAS->causas_del_accidente->investigador->nombres=$post["investigador_nombres"];
                $ralf->ZONA_CAUSAS->causas_del_accidente->investigador->rut=$post["investigador_rut"];

                $documentostring->XMLSTRING=$ralf->saveXML();
                $documentostring->save();
                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");
            }
        }
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf;
        $data['criterio_gravedad'] = $organismo=Kohana::$config->load('dominios.STCriterio_gravedad_RALF');
        $this->template->mensaje_error=$mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralfCausas/crear')
                ->set('data', $data)
                ->set('back_page', URL::site("caso/ver_caso/{$documento->CASO_ID}", 'http'))
                ->set('errors',$errors)
                ->set('default',  $this->values_default($ralf,$_POST))
                ->set('errores_esquema',$errores_esquema)
                ->set('xml_id',$xml_id)
                ->set('documento', $documento)
            ;

    }

    public function values_default($ralf,$post) {
        if(empty($post)) {

            $default["investigador_apellido_paterno"]=$ralf->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_paterno;
            $default["investigador_apellido_materno"]=$ralf->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_materno;
            $default["investigador_nombres"]=$ralf->ZONA_CAUSAS->causas_del_accidente->investigador->nombres;
            $default["investigador_rut"]=$ralf->ZONA_CAUSAS->causas_del_accidente->investigador->rut;
        } else {

            $default["investigador_apellido_paterno"]=$post["investigador_apellido_paterno"];
            $default["investigador_apellido_materno"]=$post["investigador_apellido_materno"];
            $default["investigador_nombres"]=$post["investigador_nombres"];
            $default["investigador_rut"]=$post["investigador_rut"];
        }
        return $default;
    }

    //funcion para generar el tag de documento anexo al XML
    public static function documentos_anexos($xml_id,$ralf) {
        $documentos_notif_causas = $ralf->ZONA_CAUSAS->causas_del_accidente;

        $documentos_notif_causas->addChild('documentos_acompanan_notificacion_causas','');

        $documentos_anexos=$ralf->ZONA_CAUSAS->causas_del_accidente->documentos_acompanan_notificacion_causas;
        $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();

        foreach ($anexos as $anexo) {
            $path=$anexo->ruta;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $base64 = base64_encode($data);
            $documento_anexo=$documentos_anexos->addChild('documento_anexo','');

            $nombre_documento=htmlspecialchars($anexo->nombre_documento, ENT_QUOTES, 'UTF-8');
            $documento_anexo->addChild('nombre_documento', $nombre_documento);

            $fecha_documento=htmlspecialchars($anexo->fecha_documento, ENT_QUOTES, 'UTF-8');
            $documento_anexo->addChild('fecha_documento', $fecha_documento);

            $autor_documento=htmlspecialchars($anexo->autor_documento, ENT_QUOTES, 'UTF-8');
            $documento_anexo->addChild('autor_documento', $autor_documento);

            $documento_anexo->addChild('documento', $base64);


            $tipo=htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
            $documento_anexo->addChild('extension', $tipo);
        }
        return $ralf;

    }

    public function action_borrar_adjunto() {
        $this->auto_render=false;
        $adjunto_id = $this->request->param('id');
        $adjunto = ORM::factory('Adjunto', $adjunto_id);
        $adjunto_origen=$adjunto->origen;
        $nombre_documento=$adjunto->nombre_documento;
        $xml_id = $adjunto->xml_id;
        $borrado=false;
        if(isset ($_POST['boton_aceptar'])) {
            $adjunto->delete();
            $borrado=true;
        }

        $this->response->body (
            View::factory('ralf2/borrar_adjunto')->set('borrado',$borrado)->set('xml_id',$xml_id)
            ->set('adjunto_origen',$adjunto_origen)
            ->set('nombre_documento',$nombre_documento)
            );
    }

    public function arbol_causas($xml_id,$ralf){
        $arbol_causas_xml=$ralf->ZONA_CAUSAS->causas_del_accidente;
        $nodo_causa=$ralf->ZONA_CAUSAS->causas_del_accidente;

        $cabecera_arbol=ORM::factory('ArbolCausas')->where('xml_id','=',$xml_id)->find();
        $nodos_arbol=ORM::factory('ArbolCausasNodo')->where('arbol_id','=',$cabecera_arbol->arbol_id)->find_all();

        foreach ($nodos_arbol as $nodo) {
            $nodo_causa=$arbol_causas_xml->addChild('nodo_causa','');
            //$nodo_causa->addChild('nodo_causa','');

            $vector_nodo = str_replace("[", "", $nodo->vector_nodo);
            $vector_nodo = str_replace("]", "", $vector_nodo);
            $vector_nodo = str_replace(",", ".", $vector_nodo);

            $ubicacion_nodo = htmlspecialchars($vector_nodo, ENT_QUOTES, 'UTF-8');
            $nodo_causa->addChild('ubicacion_nodo', $ubicacion_nodo);

            $codigo_causa = htmlspecialchars($nodo->causa_id, ENT_QUOTES, 'UTF-8');
            $nodo_causa->addChild('codigo_causa', $codigo_causa);

            $glosa_causa = htmlspecialchars($nodo->hecho, ENT_QUOTES, 'UTF-8');
            $nodo_causa->addChild('glosa_causa', $glosa_causa);

        }

        return $ralf;
    }

    public function action_borrar_arbol_causas(){
        $this->auto_render=false;
        $arbol_id = $this->request->param('id');
        $arbol = ORM::factory('ArbolCausas')->where('arbol_id','=', $arbol_id)->find();
        $nodos = ORM::factory('ArbolCausasNodo')->where('arbol_id','=', $arbol_id)->find_all();

        $xml_id = $arbol->xml_id;
        $borrado=false;

        if(isset ($_POST['boton_aceptar'])) {
            foreach ($nodos as $nodo) {
                $nodo->delete();
            }

            $arbol->delete();
            $borrado=true;
        }

        $this->response->body (
            View::factory('ralfCausas/borrar_arbol_causas')->set('borrado',$borrado)->set('xml_id',$xml_id)
            );

    }
}