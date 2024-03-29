<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ralf2 extends Controller_Website {

        
    public function action_insertar() {
        
        
        if($this->get_rol()!='operador') {             
            $this->redirect("error");
        }
        
        $caso_id=$this->request->param('id');        
        if(empty ($caso_id) || !is_numeric($caso_id)){
            $this->template->mensaje_error='Error, Error al cargar caso';
            $this->template->contenido='';
            return;
        }
        $caso=ORM::factory('Caso',$caso_id);
        
        if(!$caso->loaded()){
            $this->template->mensaje_error='Se debe agregar una denuncia al caso.';
            $this->template->contenido='';
            return;
        }
        //Busco un documento ralf1
        $documento=$caso->xmls
                ->where('TPXML_ID','IN', array(12,141))
                ->where('ESTADO','IN',array(1,2))->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;  die();      
        if(!$documento->loaded()){
            $this->template->mensaje_error='Se debe agregar una RALF1.';
            $this->template->contenido='';
            return;
        }

        $ralf_anterior=$caso->xmls->where('TPXML_ID','IN', array(13,142))->where('ESTADO','!=', 3)->find();
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
               
        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion=dom_import_simplexml($xml_documento);

        
        $nombre_xsd = '';
        if(strpos($xml_documento, 'ralf1') !== false){
            $nombre_xsd = 'SISESAT_RALF_1.1.0.xsd';
        }else{
            $nombre_xsd = 'SISESAT_RALF_Accidente.1.0.xsd';
        }

        Documento::clonishNode($documento_preparacion, 'RALF_Medidas');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());


                
        //$cabecera='<RALF_Medidas xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xsi:schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_TYPES_1.0.xsd" id="z_padre">';
        $cabecera='<RALF_Medidas id="z_padre">';        
        $ralf=str_replace('<RALF_Medidas schemaLocation="http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd    http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd" noNamespaceSchemaLocation="'.$nombre_xsd.'" id="z_padre">',$cabecera,$ralf_preparacion->saveXML());

        //faillon
        //modificacion de xml segun cambios a archivos xsd
        $zona_q_string="<ZONA_INMEDIATAS>
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
        $ralf=str_replace('</RALF_Medidas>',$zona_q_string,$ralf);
        
        

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
        $xml_insert->TPXML_ID=142;
        $xml_insert->VALIDO=0;  
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN=$documento->XML_ID;      
        $xml_insert->save();
        
        $doc=simplexml_load_string($xmlstring->XMLSTRING);        
        $doc->ZONA_A->documento->folio=$xml_insert->XML_ID;        
        $xmlstring->XMLSTRING=$doc->saveXML();
        $xmlstring->save();              
        $this->redirect("ralf2/crear/$xml_insert->XML_ID");                
    }
    
    public function action_crear() {
        
        if($this->get_rol()!='operador') {              
            $this->redirect("error");
        }
        
        $xml_id = $this->request->param('id');        

        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'Error, Falta id de ralf';
            $this->template->contenido='';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }    
		
		
        if($documento->VALIDO==1 && $documento->ESTADO!=5) {
            $this->redirect("documento/ver/$documento->XML_ID");
        }

        $documentostring=$documento->xmlstring;
        //$xml_documento = simplexml_load_string($documentostring->XMLSTRING);
        $xml_documento = simplexml_load_string($documentostring->XMLSTRING);
        
        $ralf = $documentostring->XMLSTRING;
        if(strpos($ralf, 'ralf2') !== false){

            $cabecera_old = '<ralf2 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xsi:schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_TYPES_1.0.xsd" id="z_padre">';

            $cabecera_new = '<RALF_Medidas id="z_padre">';

            $fin_xml_old = '</ralf2>';
            $fin_xml_new = '</RALF_Medidas>';

            $ralf = str_replace($cabecera_old, $cabecera_new, $ralf);
            $ralf = str_replace($fin_xml_old, $fin_xml_new, $ralf);

            //$xml_documento = simplexml_load_string($ralf);




            //$documento_preparacion = dom_import_simplexml($xml_documento);
            //Documento::clonishNode($documento_preparacion, 'RALF_Medidas');
            //$ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());
            
            //$cabecera='<RALF_Medidas id="z_padre">'; 
            //$ralf=str_replace('<RALF_Medidas schemalocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" nonamespaceschemalocation="SISESAT_TYPES_1.0.xsd" id="z_padre">',$cabecera,$ralf_preparacion->saveXML());
            //$ralf=str_replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xsi:schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_TYPES_1.0.xsd" id="z_padre">',$cabecera,$ralf_preparacion->saveXML());
        }
       

                
        //$cabecera='<RALF_Medidas xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xsi:schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_TYPES_1.0.xsd" id="z_padre">';
        //$cabecera='<RALF_Medidas id="z_padre">';     
        //$ralf=str_replace('<RALF_Medidas schemaLocation="http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd    http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd" noNamespaceSchemaLocation="'.$nombre_xsd.'" id="z_padre">',$cabecera,$ralf_preparacion->saveXML());

        
        $ralf = simplexml_load_string($ralf);
        $ralf->saveXML();

        		
        $errores_esquema=NULL;
        $errors = array();
        $mensaje_error = null;                
        if (isset($_POST) AND Valid::not_empty($_POST)) {            
            if(isset ($_POST['boton_finalizar'])) {                       
                $post = Validation::factory($_POST)
                        
                        ->rule('fecha_notificacion_medidas_inmediatas', 'not_empty')    
                        ->rule('fecha_notificacion_medidas_inmediatas','Utiles::validateDate',array(':value'))  
                        ->rule('fecha_notificacion_medidas_inmediatas', 'date') 
                        ->rule('fecha_notificacion_medidas_inmediatas','Utiles::whitespace',array(':value'))                   
                        ->label('fecha_notificacion_medidas_inmediatas', 'fecha notificación medidas inmediatas')                        
                        ->rule('nombres', 'not_empty')->label('nombres', 'Nombres')
                        ->rule('nombres','Utiles::whitespace',array(':value'))
                        ->rule('apellido_paterno', 'not_empty')
                        ->rule('apellido_paterno','Utiles::whitespace',array(':value'))
                        ->label('apellido_paterno', 'Ap. Paterno')
                        ->rule('apellido_materno', 'not_empty')
                        ->rule('apellido_materno','Utiles::whitespace',array(':value'))
                        ->label('apellido_materno', 'Ap. materno')
                        ->rule('rut', 'not_empty')
                        ->rule('rut','Utiles::whitespace',array(':value'))
                        ->rule('rut','not_empty')->rule('rut','Utiles::rut',array(':value'))
                        ->rule('rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->label('rut', 'Rut')
                        ;

                        if(!empty($post["cod_area"])) {
                            $post=$post->rule('cod_area', 'numeric')
                                ->rule('cod_area','Utiles::whitespace',array(':value'))
                                ->rule('numero','Utiles::whitespace',array(':value'))
                                ->rule('numero', 'not_empty')->rule('numero', 'numeric')->label('numero', 'Numero');

                        }

                        if(!empty($post["numero"])) {
                            $post=$post->rule('numero', 'numeric')
                                ->rule('cod_area','Utiles::whitespace',array(':value'))
                                ->rule('numero','Utiles::whitespace',array(':value'))
                                ->rule('cod_area', 'not_empty')->rule('cod_area', 'numeric')->label('cod_area', 'cod. area');
                        }                                                                    
                                                
                        ;                            
                if(!empty($_POST["fecha_notificacion_medidas_inmediatas"])) {
                    if(!($_POST["fecha_notificacion_medidas_inmediatas"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                        $errors = $errors+array("fecha_notificacion_medidas_inmediatas"=>"Fecha notificacion Mayor o igual a fecha de accidente");
                    }
                }

                if(!empty($_POST["fecha_accidente"])) {
                    if(!($_POST["fecha_accidente"]>=$ralf->ZONA_C->empleado->fecha_ingreso)) {
                        $errors = $errors+array("fecha_accidente"=>"Fecha accidente Mayor o igual a fecha de ingreso");
                    }elseif(!($_POST["fecha_accidente"]<=date('Y-m-d'))) {
                        $errors = $errors+array("fecha_accidente"=>"Fecha accidente Menor o igual a fecha actual");
                    }
                }

                $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();
                if(count($anexos)==0) {
                    $post=$post->rule('documentos_anexos', 'not_empty')->label('documentos_anexos', 'Documentos Anexos');                                    
                }

                $medidas_r2=ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_ralf2')->find_all();
                if(count($medidas_r2)==0) {
                    $errors = $errors+array("medidas"=>"Debe agregar medidas");
                }

                if($post->check() && count($errors)==0) {
                    if(isset($ralf->ZONA_Q)){
                        $ralf->ZONA_Q->medidas_inmediatas->fecha_notificacion_medidas_inmediatas=$post['fecha_notificacion_medidas_inmediatas'];
                        $ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_paterno=$post['apellido_paterno'];
                        $ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_materno=$post['apellido_materno'];
                        $ralf->ZONA_Q->medidas_inmediatas->investigador->nombres=$post['nombres'];
                        $ralf->ZONA_Q->medidas_inmediatas->investigador->rut=strtoupper($post['rut']);
                    }else{
                        $ralf->ZONA_INMEDIATAS->fecha_notificacion_medidas_inmediatas=$post['fecha_notificacion_medidas_inmediatas'];
                        $ralf->ZONA_INMEDIATAS->investigador->apellido_paterno=$post['apellido_paterno'];
                        $ralf->ZONA_INMEDIATAS->investigador->apellido_materno=$post['apellido_materno'];
                        $ralf->ZONA_INMEDIATAS->investigador->nombres=$post['nombres'];
                        $ralf->ZONA_INMEDIATAS->investigador->rut=strtoupper($post['rut']);
                    }
                    
                    

                    if(!empty($post['cod_area']) && !empty($post['numero'])) {
                        if(isset($ralf->ZONA_Q)){
                            $ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->cod_pais=56;
                            $ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->cod_area=$post['cod_area'];
                            $ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->numero=$post['numero'];
                        }else{
                            $ralf->ZONA_INMEDIATAS->telefono_investigador->cod_pais=56;
                            $ralf->ZONA_INMEDIATAS->telefono_investigador->cod_area=$post['cod_area'];
                            $ralf->ZONA_INMEDIATAS->telefono_investigador->numero=$post['numero'];
                        }
                        
                    }else{
                        if(isset($ralf->ZONA_Q)){
                            unset($ralf->ZONA_Q->medidas_inmediatas->telefono_investigador);
                        }else{
                            unset($ralf->ZONA_INMEDIATAS->telefono_investigador);    
                        }
                                                
                    }


                    /*
                     * faillons
                     * se agrega transformación del XML
                     * en caso que la ZONA_C venga con tag documento_identidad
                     */
                    if(isset($ralf->ZONA_C->empleado->trabajador->documento_identidad)){
                        $ralf = Documento::transformarZonaCAntigua($ralf);
                    }
                    
                    $ralf_bd=$ralf->saveXML();

                    $ralf=Controller_Ralf2::documentos_anexos($xml_id,$ralf);
                    //var_dump($ralf);

                    /*
                     * faillons se reemplaza zonaC si viene de Forma antigua
                     */
                    if(isset($ralf->ZONA_C->empleado->trabajador->rut)){
                        $ralf = Documento::transformarZonaCNueva($ralf);
                    }
                                    
                    $ralf_string=$ralf->saveXML();


                    $ralf=Controller_Ralf2::medidas($xml_id,$ralf);

                    $ralf = Documento::zona_o($ralf->saveXML());                    
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf),$documento->TPXML_ID);                   
                    //var_dump($ralf);
                    $valido=Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_Medidas.1.0.xsd');

                    if($valido['estado']) {                        
                        $documentostring->XMLSTRING=$ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO=1;
                        $documento->ESTADO=6;
                        $documento->save();            
                        $ralf2=ORM::factory('Ralf2')->where('xml_id','=',$xml_id)->find();
                        $medidas_string="";
                        foreach (ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_ralf2')->find_all() as $med) {
                            $medidas_string .=$med->medida."-";
                        }
                        $ralf2->medida=$medidas_string;
                        $ralf2->fecha_notificacion_medidas_inmediatas=$post['fecha_notificacion_medidas_inmediatas'];
                        $ralf2->apellido_paterno=$post['apellido_paterno'];
                        $ralf2->apellido_materno=$post['apellido_materno'];
                        $ralf2->nombres=$post['nombres'];
                        $ralf2->rut=strtoupper($post['rut']);
                        $ralf2->cod_area=$post['cod_area'];
                        $ralf2->numero=$post['numero'];
                        $ralf2->xml_id=$xml_id;
                        $ralf2->save();            
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
                
                if(isset($ralf->ZONA_Q)){                    
                    $ralf->ZONA_Q->medidas_inmediatas->fecha_notificacion_medidas_inmediatas=$post['fecha_notificacion_medidas_inmediatas'];
                    $ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_paterno=$post['apellido_paterno'];
                    $ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_materno=$post['apellido_materno'];
                    $ralf->ZONA_Q->medidas_inmediatas->investigador->nombres=$post['nombres'];
                    $ralf->ZONA_Q->medidas_inmediatas->investigador->rut=strtoupper($post['rut']);
                    $ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->cod_area=$post['cod_area'];
                    $ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->numero=$post['numero'];
                }else{
                    $ralf->ZONA_INMEDIATAS->fecha_notificacion_medidas_inmediatas=$post['fecha_notificacion_medidas_inmediatas'];
                    $ralf->ZONA_INMEDIATAS->investigador->apellido_paterno=$post['apellido_paterno'];
                    $ralf->ZONA_INMEDIATAS->investigador->apellido_materno=$post['apellido_materno'];
                    $ralf->ZONA_INMEDIATAS->investigador->nombres=$post['nombres'];
                    $ralf->ZONA_INMEDIATAS->investigador->rut=strtoupper($post['rut']);
                    $ralf->ZONA_INMEDIATAS->telefono_investigador->cod_area=$post['cod_area'];
                    $ralf->ZONA_INMEDIATAS->telefono_investigador->numero=$post['numero'];
                }
                
                $documentostring->XMLSTRING=$ralf->saveXML();
                $documentostring->save();
                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");                
            }
        }                
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf; 
        $data['criterio_gravedad'] = $organismo=Kohana::$config->load('dominios.STCriterio_gravedad_RALF');        
        $this->template->mensaje_error=$mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralf2/crear')                 
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
            $default['fecha_notificacion_medidas_inmediatas']=$ralf->ZONA_INMEDIATAS->fecha_notificacion_medidas_inmediatas;

            if(isset($ralf->ZONA_Q)){
                $default['apellido_paterno']=$ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_paterno;
                $default['apellido_materno']=$ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_materno;
                $default['nombres']=$ralf->ZONA_Q->medidas_inmediatas->investigador->nombres;
                $default['rut']=strtoupper($ralf->ZONA_Q->medidas_inmediatas->investigador->rut);
                $default['cod_area']=$ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->cod_area;
                $default['numero']=$ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->numero;
            }else{
                $default['apellido_paterno']=$ralf->ZONA_INMEDIATAS->investigador->apellido_paterno;
                $default['apellido_materno']=$ralf->ZONA_INMEDIATAS->investigador->apellido_materno;
                $default['nombres']=$ralf->ZONA_INMEDIATAS->investigador->nombres;
                $default['rut']=strtoupper($ralf->ZONA_INMEDIATAS->investigador->rut);
                $default['cod_area']=$ralf->ZONA_INMEDIATAS->telefono_investigador->cod_area;
                $default['numero']=$ralf->ZONA_INMEDIATAS->telefono_investigador->numero;
            }
                                     
        } else {            
            $default['fecha_notificacion_medidas_inmediatas']=$post['fecha_notificacion_medidas_inmediatas'];
            $default['apellido_paterno']=$post['apellido_paterno'];
            $default['apellido_materno']=$post['apellido_materno'];
            $default['nombres']=$post['nombres'];
            $default['rut']=strtoupper($post['rut']);
            $default['cod_area']=$post['cod_area'];
            $default['numero']=$post['numero'];
        }        
        return $default;        
    }
    
    /*
     * Funcion que genera la estructura XML
     * correspondiente a medidas inmediatas
     * para ser enviado el ralf2 a SUSESO
     */
    public static function medidas($xml_id,$ralf) {
        $medidas_xml=$ralf->ZONA_INMEDIATAS;      
        unset($medidas_xml->medidas_inmediatas);                
        $medidas=ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_ralf2')->find_all();
        
        $dom = dom_import_simplexml($ralf->ZONA_INMEDIATAS);
        $nodoReferencia = dom_import_simplexml($dom->getElementsByTagName('fecha_notificacion_medidas_inmediatas')->item(0));
        
        foreach ($medidas as $m) {
            $variable=htmlspecialchars($m->medida, ENT_QUOTES, 'UTF-8');
            $dom->insertBefore($dom->ownerDocument->createElement('medidas_inmediatas', $variable), $nodoReferencia);
        }              
        return $ralf;

    }
    
    /*
     * Funcion que borra medidas inmediatas
     */
    public function action_borrar_medida() {
        $this->auto_render=false;
        $id = $this->request->param('id');
        $medida = ORM::factory('Medida', $id);
        $borrado=false;
        $xml_id = $medida->xml_id;
        if(isset ($_POST['boton_aceptar'])) {            
            $medida->delete();    
            $borrado=true;
        }                
        $this->response->body (
            View::factory('ralf2/borrar_medida')->set('medida',$medida)->set('borrado',$borrado)->set('xml_id',$xml_id)
            );
    }
    
    /*
     * faillons
     * Funcion para generar la estructura xml correspondiente
     * al ralf2 para luego ser enviado a SUSESO
     */
    public static function documentos_anexos($xml_id,$ralf) {
        $documentos_anexos=$ralf->ZONA_INMEDIATAS->medidas_inmediatas_firmadas_por_empleador;
        
        $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();      
        foreach ($anexos as $anexo) {           
            $path=$anexo->ruta;
            $type = pathinfo($path, PATHINFO_EXTENSION);        
            $data = file_get_contents($path);
            $base64 = base64_encode($data);
                        
            $dom = dom_import_simplexml($ralf->ZONA_INMEDIATAS->medidas_inmediatas_firmadas_por_empleador);
            
            
            $documento_anexo=$documentos_anexos->addChild('documento_anexo', '');

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
    
    /*
     * faillons
     * Funcion para eliminar los archivos adjuntos del ralf2
     */
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
    
    /*
     * faillons
     * funcion para corregir aquelos ralf que tengan la estructura antigua
     * y se deba reemplazar la ZONA_Q
     *
     */
    public function regularizarZonaQ($ralf){
        //echo $ralf->saveXML();
        //eliminar antiguo tag de documentos anexos
        $dom = dom_import_simplexml($ralf->ZONA_Q->medidas_inmediatas);
        
        $nodoReferencia = dom_import_simplexml($dom->getElementsByTagName('documentos_anexos')->item(0));
        $dom->removeChild($nodoReferencia);


        //echo $ralf->saveXML();
        //$dom=dom_import_simplexml($ralf);
        //$ralf2 = $dom->documentElement;

        //$zona_q_ant = $dom->getElementsByTagName('documentos_anexos')->item(0);
        //$zona_q_remove = $dom->removeChild($zona_q_ant);
        //$dom->saveXML(); 
        //$dom->parentNode->removeChild($dom->ZONA_Q->documentos_anexos);
        //$ralf->parentNode->removeChild($ralf->ZONA_Q->documentos_anexos);

        //creacion de documento y primer tag de la nueva ZONA_INMEDIATAS
        $domNew = new DOMDocument('1.0', 'utf-8');
        $zona_inmediatas = $domNew->createElement(strtoupper('ZONA_INMEDIATAS'));

        //Se obtienen las medidas inmediatas del ralf antiguo
        $medidas_zona_q = $ralf->ZONA_Q->medidas_inmediatas->medidas;
        //echo $ralf->saveXML();
        foreach ($medidas_zona_q as $m_zona_q) {
            $valor = htmlspecialchars($m_zona_q->medida, ENT_QUOTES, 'UTF-8');
            
            // verifico si hay valor de lo contrario se deja el valor vacio
            // si no hago esto se crea tag y los siguientes addchild quedan detro de ese tag
            $valor_medida = (string)$valor?$valor:' ';
            $medidas_inmediatas = $domNew->createElement('medidas_inmediatas', $valor_medida);            
            $zona_inmediatas->appendChild($medidas_inmediatas);
        }

        /*
         * Se van creando los distintos tags dentro de la estructura
         * de ZONA_INMEDIATAS
         */
        $valor_fecha_notif = $ralf->ZONA_Q->medidas_inmediatas->fecha_notificacion_medidas_inmediatas?$ralf->ZONA_Q->medidas_inmediatas->fecha_notificacion_medidas_inmediatas:' ';
        $zona_inmediatas->appendChild($domNew->createElement('fecha_notificacion_medidas_inmediatas', $valor_fecha_notif));
        // $zona_inmediatas->appendChild($domNew->createElement('documentos_anexos',' '));
            
        /*
         * Creacion estructuctura investigador
         */
        $tag_investigador = $domNew->createElement('investigador');

        $valor_ap_paterno = (string)$ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_paterno?$ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_paterno:' ';
        $tag_investigador->appendChild($domNew->createElement('apellido_paterno',$valor_ap_paterno));

        $valor_ap_materno = (string)$ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_materno?$ralf->ZONA_Q->medidas_inmediatas->investigador->apellido_materno:' ';
        $tag_investigador->appendChild($domNew->createElement('apellido_materno',$valor_ap_materno));
            
        $valor_nombres = (string)$ralf->ZONA_Q->medidas_inmediatas->investigador->nombres?$ralf->ZONA_Q->medidas_inmediatas->investigador->nombres:' ';
        $tag_investigador->appendChild($domNew->createElement('nombres',$valor_nombres));
            
        $valor_rut = (string)$ralf->ZONA_Q->medidas_inmediatas->investigador->rut?$ralf->ZONA_Q->medidas_inmediatas->investigador->rut:' ';
        $tag_investigador->appendChild($domNew->createElement('rut',strtoupper($valor_rut)));

        $zona_inmediatas->appendChild($tag_investigador);

        /*
         * Creacion estructura telefono investigador
         */
        $tag_telefono_investigador = $domNew->createElement('telefono_investigador');

        $valor_cod_pais = (string)$ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->cod_pais?$ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->cod_pais:' ';
        $tag_telefono_investigador->appendChild($domNew->createElement('cod_pais',$valor_cod_pais));

        $valor_cod_area = (string)$ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->cod_area?$ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->cod_area:' ';
        $tag_telefono_investigador->appendChild($domNew->createElement('cod_area',$valor_cod_area));

        $valor_numero = (string)$ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->numero?$ralf->ZONA_Q->medidas_inmediatas->telefono_investigador->numero:' ';
        $tag_telefono_investigador->appendChild($domNew->createElement('numero',$valor_numero));
        $zona_inmediatas->appendChild($tag_telefono_investigador);      
        
        $zona_inmediatas->appendChild($domNew->createElement('medidas_inmediatas_firmadas_por_empleador',' '));
            
        $domNew->appendChild($zona_inmediatas);

        $zona_q_old = dom_import_simplexml($ralf->ZONA_Q);
        $zona_new = dom_import_simplexml($zona_inmediatas);
        
        /*
         * Se reemplaza estructura antigua por la nueva.
         */
        $nodeImport  = $zona_q_old->ownerDocument->importNode($zona_new, TRUE);
        $zona_q_old->parentNode->replaceChild($nodeImport, $zona_q_old);
        $ralf->saveXML();

        return $ralf;
    }
}