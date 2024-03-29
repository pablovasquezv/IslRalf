<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ralf4 extends Controller_Website{
        
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
                ->where('TPXML_ID','IN', array(14))
                ->where('ESTADO','IN',array(1,2))->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;  die();      
        if(!$documento->loaded()){
            $this->template->mensaje_error='Se debe agregar una RALF3.';
            $this->template->contenido='';
            return;
        }
        
        $ralf_anterior=$caso->xmls->where('TPXML_ID','=', 15)->where('ESTADO','!=', 3)->find();
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
           
        if(isset($xml_documento->ZONA_R)) {
            unset($xml_documento->ZONA_R);
        }

        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion=dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'ralf4');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());
        
        //var_dump($ralf_preparacion->saveXML());
        //die();
        $cabecera='<ralf4 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xsi:schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_TYPES_1.0.xsd" id="z_padre">';
        $cab_old='<ralf4 schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" noNamespaceSchemaLocation="SIATEP_ZONA_OA_A.1.0.xsd" id="z_padre">';
        $ralf=str_replace($cab_old,$cabecera,$ralf_preparacion->saveXML());        
        
        //var_dump($ralf);
        //die();


        //faillon
        //agregar nuevo tag de acuerdo a los cambios en xsds
        //tomar como referencia ZONA_R de ralf3
        $zona_s_string="<ZONA_S>
        <cumplimiento_medidas>
            <medidas></medidas>
            <fecha_verificacion></fecha_verificacion>
            <documentos_anexos></documentos_anexos>
            <verificador>
                <apellido_paterno></apellido_paterno>
                <apellido_materno></apellido_materno>
                <nombres></nombres>
                <rut></rut>
            </verificador>
        </cumplimiento_medidas>
    </ZONA_S></ralf4>";
                
        
        
        $ralf=str_replace('</ralf4>',$zona_s_string,$ralf);                                
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
        $xml_insert->TPXML_ID=15;
        $xml_insert->VALIDO=0;  
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN=$documento->XML_ID;      
        $xml_insert->save();
        
        $doc=simplexml_load_string($xmlstring->XMLSTRING);        
        $doc->ZONA_A->documento->folio=$xml_insert->XML_ID;                
        $xmlstring->XMLSTRING=$doc->saveXML();
        $xmlstring->save();              
        $this->redirect("ralf4/crear/$xml_insert->XML_ID");                
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
		
		
        //$documento->ESTADO=5;
        //$documento->save();
        //  die(); 
        if($documento->VALIDO==1 && $documento->ESTADO!=5) {
            $this->redirect("documento/ralf4/$documento->XML_ID");
        }        
        $documentostring=$documento->xmlstring;
        $ralf=simplexml_load_string($documentostring->XMLSTRING);                
               
		//Aqui agregar metodo que valide si existe tag documentos_anexos
          if(!isset($ralf->ZONA_S->cumplimiento_medidas->documentos_anexos)){

            //print_r("No estadff");

            /*$dom = dom_import_simplexml($ralf->ZONA_Q->medidas_inmediatas[0]->fecha_notificacion_medidas_inmediatas);*/

            //echo "<p><p>";

            foreach($ralf->ZONA_S->cumplimiento_medidas->children() as $k){
                
                $k->getName();
            }

            $dom = dom_import_simplexml($ralf->ZONA_S->cumplimiento_medidas);

            $nodoReferencia = $dom->getElementsByTagName('verificador')->item(0);

           
            //print_r("Entra IF algo ");

            $dom->insertBefore(
                $dom->ownerDocument->createElement('documentos_anexos', ''), $nodoReferencia
            );

        }
      





		
        $errores_esquema=NULL;
        $errors = array();
        $mensaje_error = null;                
        if (isset($_POST) AND Valid::not_empty($_POST)) {            
            if(isset ($_POST['boton_finalizar'])) {                       
                $post = Validation::factory($_POST)                    
                        ->rule('fecha_verificacion', 'not_empty')
                        ->rule('fecha_verificacion','Utiles::validateDate',array(':value'))  
                        ->rule('fecha_verificacion', 'date')
                        ->label('fecha_verificacion', 'Fecha verificación')
                        ->rule('verificador_apellido_paterno','Utiles::whitespace',array(':value'))
                        ->rule('verificador_apellido_paterno', 'not_empty')->label('verificador_apellido_paterno', 'Ap. paterno')
                        ->rule('verificador_apellido_materno','Utiles::whitespace',array(':value'))
                        ->rule('verificador_apellido_materno', 'not_empty')->label('verificador_apellido_materno', 'Ap. materno')
                        ->rule('verificador_nombres','Utiles::whitespace',array(':value'))
                        ->rule('verificador_nombres', 'not_empty')->label('verificador_nombres', 'Nombre')
                        ->rule('verificador_rut','Utiles::whitespace',array(':value'))
                        ->rule('verificador_rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->rule('verificador_rut','not_empty')->rule('verificador_rut','Utiles::rut',array(':value'))
                        ->rule('verificador_rut', 'not_empty')->label('verificador_rut', 'Rut')                                                                                                    
                        ;                

                if(!empty($_POST["fecha_verificacion"])) {
                    if(!($_POST["fecha_verificacion"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                        $errors = $errors+array("fecha_verificacion"=>"Fecha Mayor o igual a fecha de accidente");
                    }
                }

                $medidas=ORM::factory('Cumplimiento_Medida')->where('xml_id','=',$xml_id)->find_all();
                if(count($medidas)==0) {
                    $errors = $errors+array("medidas"=>"Debe agregar medidas");
                }


                if($post->check() && count($errors)==0) {                    
                    $ralf->ZONA_S->cumplimiento_medidas->fecha_verificacion=$post["fecha_verificacion"];

                    $ralf->ZONA_S->cumplimiento_medidas->verificador->apellido_paterno=$post["verificador_apellido_paterno"];
                    $ralf->ZONA_S->cumplimiento_medidas->verificador->apellido_materno=$post["verificador_apellido_materno"];
                    $ralf->ZONA_S->cumplimiento_medidas->verificador->nombres=$post["verificador_nombres"];
                    $ralf->ZONA_S->cumplimiento_medidas->verificador->rut=strtoupper($post["verificador_rut"]);

                    /*
                     * faillons
                     * se agrega transformación del XML
                     * en caso que la ZONA_C venga con tag documento_identidad
                     */
                    if(isset($ralf->ZONA_C->empleado->trabajador->documento_identidad)){
                        $ralf = Documento::transformarZonaCAntigua($ralf);
                    }

                    $ralf_bd=$ralf->saveXML();

                    $ralf=Controller_Ralf4::documentos_anexos($xml_id,$ralf);                    
                    $ralf_string=$ralf->saveXML();
                     
                    $ralf=Controller_Ralf4::medidas($xml_id,$ralf);
                    $ralf = Documento::zona_o($ralf->saveXML()); 
                        
                            
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf),$documento->TPXML_ID);                                                                                            
                    $final=$ralf;
                    
                    $valido=Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_4.1.0.xsd');                                        
                    if($valido['estado']) {
                        $documentostring->XMLSTRING=$ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO=1;
                        $documento->ESTADO=6;
                        $documento->save();     

                        $ralf4=ORM::factory('Ralf4')->where('xml_id','=',$xml_id)->find();
                        /*
                        $ralf4->cumplimiento_medida_id=$post["cumplimiento_medida_id"];
                        $ralf4->cumplimiento_medida_medida=$post["cumplimiento_medida_medida"];
                        $ralf4->cumplimiento_medida_medida_implementada=$post["cumplimiento_medida_medida_implementada"];
                        $ralf4->cumplimiento_medida_ampliacion_plazo=$post["cumplimiento_medida_ampliacion_plazo"];
                        $ralf4->cumplimiento_medida_nueva_fecha_ampliacion_plazo=$post["cumplimiento_medida_nueva_fecha_ampliacion_plazo"];
                        $ralf4->cumplimiento_medida_observaciones=$post["cumplimiento_medida_observaciones"];
                        */
                        $ralf4->fecha_verificacion=$post["fecha_verificacion"];
                        $ralf4->verificador_apellido_paterno=$post["verificador_apellido_paterno"];
                        $ralf4->verificador_apellido_materno=$post["verificador_apellido_materno"];
                        $ralf4->verificador_nombres=$post["verificador_nombres"];
                        $ralf4->verificador_rut=strtoupper($post["verificador_rut"]); 
                        $ralf4->xml_id=$xml_id;
                        $ralf4->save();   
                   
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

                $ralf->ZONA_S->cumplimiento_medidas->fecha_verificacion=$post["fecha_verificacion"];

                $ralf->ZONA_S->cumplimiento_medidas->verificador->apellido_paterno=$post["verificador_apellido_paterno"];
                $ralf->ZONA_S->cumplimiento_medidas->verificador->apellido_materno=$post["verificador_apellido_materno"];
                $ralf->ZONA_S->cumplimiento_medidas->verificador->nombres=$post["verificador_nombres"];
                $ralf->ZONA_S->cumplimiento_medidas->verificador->rut=strtoupper($post["verificador_rut"]);
                

                $documentostring->XMLSTRING=$ralf->saveXML();
                $documentostring->save();
                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");                
            }
        }                        
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf; 
        $data['criterio_gravedad'] = $organismo=Kohana::$config->load('dominios.STCriterio_gravedad_RALF');        
        $this->template->mensaje_error=$mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralf4/crear')                 
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

            $default["fecha_verificacion"]=$ralf->ZONA_S->cumplimiento_medidas->fecha_verificacion;

            $default["verificador_apellido_paterno"]=$ralf->ZONA_S->cumplimiento_medidas->verificador->apellido_paterno;
            $default["verificador_apellido_materno"]=$ralf->ZONA_S->cumplimiento_medidas->verificador->apellido_materno;
            $default["verificador_nombres"]=$ralf->ZONA_S->cumplimiento_medidas->verificador->nombres;
            $default["verificador_rut"]=strtoupper($ralf->ZONA_S->cumplimiento_medidas->verificador->rut);                   
        } else {            
            $default["fecha_verificacion"]=$post["fecha_verificacion"];
            $default["verificador_apellido_paterno"]=$post["verificador_apellido_paterno"];
            $default["verificador_apellido_materno"]=$post["verificador_apellido_materno"];
            $default["verificador_nombres"]=$post["verificador_nombres"];
            $default["verificador_rut"]= strtoupper($post["verificador_rut"]);    
        }        
        return $default;        
    }

    public static function medidas($xml_id,$ralf) {                      
        $medidas_xml=$ralf->ZONA_S->cumplimiento_medidas->medidas;        
        $medidas=ORM::factory('Cumplimiento_Medida')->where('xml_id','=',$xml_id)->find_all();       
        foreach ($medidas as $m) {    
            $cumplimiento_medida=$medidas_xml->addChild('cumplimiento_medida',''); 

            $medida_id=htmlspecialchars($m->medida_id, ENT_QUOTES, 'UTF-8');     
            $cumplimiento_medida->addChild('id', $medida_id);   

            $medida=htmlspecialchars($m->medida, ENT_QUOTES, 'UTF-8');   
            $cumplimiento_medida->addChild('medida', $medida);   

            $medida_implementada=htmlspecialchars($m->medida_implementada, ENT_QUOTES, 'UTF-8');   
            $cumplimiento_medida->addChild('medida_implementada', $medida_implementada);   

            $ampliacion_plazo=htmlspecialchars($m->ampliacion_plazo, ENT_QUOTES, 'UTF-8');
            $cumplimiento_medida->addChild('ampliacion_plazo', $ampliacion_plazo);  

            if(!empty($m->nueva_fecha_ampliacion_plazo)) {
                $nueva_fecha_ampliacion_plazo=htmlspecialchars($m->nueva_fecha_ampliacion_plazo, ENT_QUOTES, 'UTF-8'); 
                $cumplimiento_medida->addChild('nueva_fecha_ampliacion_plazo', $nueva_fecha_ampliacion_plazo);      
            }
            if(!empty($m->observaciones)) {
                $observaciones=htmlspecialchars($m->observaciones, ENT_QUOTES, 'UTF-8');   
                $cumplimiento_medida->addChild('observaciones', $observaciones);   
            }

                  
        }               
        return $ralf;

    }

    public function action_borrar_medida() {
        $this->auto_render=false;
        $id = $this->request->param('id');
        $medida=ORM::factory('Cumplimiento_Medida',$id);        
        $xml_id = $medida->xml_id;      
        $borrado=false;  
        if(isset ($_POST['boton_aceptar'])) {       
            $borrado=true;     
            $medida->delete();
        }
        $this->response->body (
            View::factory('ralf4/borrar_medida')->set('medida',$medida)->set('borrado',$borrado)->set('xml_id',$xml_id)
            );
        
    }

    //funcion para generar el tag de documento anexo al XML
    public static function documentos_anexos($xml_id,$ralf) {     


        $documentos_anexos=$ralf->ZONA_S->cumplimiento_medidas->documentos_anexos;
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
}