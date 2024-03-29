<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ralf5 extends Controller_Website{
        
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
        //Busco un documento ralf1
        $documento=$caso->xmls
                ->where('TPXML_ID','IN', array(15))
                ->where('ESTADO','IN',array(1,2))->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;  die();      
        if(!$documento->loaded()){
            $this->template->mensaje_error='Se debe agregar una RALF4.';
            $this->template->contenido='';
            return;
        }
        $ralf_anterior=$caso->xmls->where('TPXML_ID','=', 16)->where('ESTADO','!=', 3)->find();
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
           
        if(isset($xml_documento->ZONA_S)) {
            unset($xml_documento->ZONA_S);
        }

        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion=dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'ralf5');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());
        
        //var_dump($ralf_preparacion->saveXML());
        //die();
        $cabecera='<ralf5 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xsi:schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_TYPES_1.0.xsd" id="z_padre">';
        $cab_old='<ralf5 schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" noNamespaceSchemaLocation="SISESAT_TYPES_1.0.xsd" id="z_padre">';
        $ralf=str_replace($cab_old,$cabecera,$ralf_preparacion->saveXML());        
        
        //var_dump($ralf);
        //die();

       

        $zona_t_string='<ZONA_T>
        <acciones_adoptadas>
            <fecha_informe_acciones_adoptadas></fecha_informe_acciones_adoptadas>   
            <constatacion_incumplimiento_medidas>
                <medidas_no_implementadas fecha_verificacion="">
                    <medida1></medida1>
                </medidas_no_implementadas>
                <medidas_no_implementadas_plazo_ampliado fecha_verificacion="">
                    <medida2></medida2>
                </medidas_no_implementadas_plazo_ampliado>
            </constatacion_incumplimiento_medidas>
            <aplicacion_multa_art_80_ley></aplicacion_multa_art_80_ley>
            <monto_multa></monto_multa>
            <fecha_multa></fecha_multa>
            <recargo_ds67_a15></recargo_ds67_a15>
            <recargo_ds67_a5></recargo_ds67_a5>
            <fecha_inicio_recargo_a15></fecha_inicio_recargo_a15>
            <fecha_termino_recargo_a15></fecha_termino_recargo_a15>
            <comunicacion_dir_trabajo></comunicacion_dir_trabajo>
            <nro_comunic_dir_trabajo></nro_comunic_dir_trabajo>
            <fecha_comunic_dir_trabajo></fecha_comunic_dir_trabajo>
            <comunicacion_seremi></comunicacion_seremi>
            <identificacion_seremi></identificacion_seremi>
            <nro_comunic_seremi></nro_comunic_seremi>
            <fecha_comunic_seremi></fecha_comunic_seremi>
            <plan_esp_trabajo_empresa></plan_esp_trabajo_empresa>
            <fecha_ini_plan_trabajo_empresa></fecha_ini_plan_trabajo_empresa>
            <resumen_plan_trabajo></resumen_plan_trabajo>
            <documentos_anexos></documentos_anexos>
            <representante_oa>
                <apellido_paterno></apellido_paterno>
                <apellido_materno></apellido_materno>
                <nombres></nombres>
                <rut></rut>
            </representante_oa>
        </acciones_adoptadas>
    </ZONA_T></ralf5>';
                
        
        
        $ralf=str_replace('</ralf5>',$zona_t_string,$ralf);                                
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
        $xml_insert->TPXML_ID=16;
        $xml_insert->VALIDO=0;  
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN=$documento->XML_ID;      
        $xml_insert->save();
        
        $doc=simplexml_load_string($xmlstring->XMLSTRING);        
        $doc->ZONA_A->documento->folio=$xml_insert->XML_ID;                
        $xmlstring->XMLSTRING=$doc->saveXML();
        $xmlstring->save();              
        $this->redirect("ralf5/crear/$xml_insert->XML_ID");                
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
            $this->redirect("documento/ralf5/$documento->XML_ID");
        }        
        $documentostring=$documento->xmlstring;
        $ralf=simplexml_load_string($documentostring->XMLSTRING);      



        
               
        $errores_esquema=NULL;
        $errors = array();
        $mensaje_error = null;                
        if (isset($_POST) AND Valid::not_empty($_POST)) {            
            if(isset ($_POST['boton_finalizar'])) {                       
                $post = Validation::factory($_POST)
                    ->rule('fecha_informe_acciones_adoptadas', 'not_empty')
                    ->rule('fecha_informe_acciones_adoptadas','Utiles::validateDate',array(':value'))                    
                    ->rule('fecha_informe_acciones_adoptadas', 'date') 
                    ->label('fecha_informe_acciones_adoptadas', 'Fecha informe acciones adoptadas')                    
                    ->rule('aplicacion_multa_art_80_ley','Utiles::whitespace',array(':value'))
                    ->rule('aplicacion_multa_art_80_ley', 'not_empty')->label('aplicacion_multa_art_80_ley', 'aplicación multa art. 80 ley')
                    
                    ->rule('recargo_ds67_a15','Utiles::whitespace',array(':value'))
                    ->rule('recargo_ds67_a15', 'not_empty')->label('recargo_ds67_a15', 'recargo_ds67_a15')
                    ->rule('recargo_ds67_a5', 'not_empty')->label('recargo_ds67_a5', 'recargo ds67 a5')
                    
                    
                    ->rule('comunicacion_dir_trabajo','Utiles::whitespace',array(':value'))
                    ->rule('comunicacion_dir_trabajo', 'not_empty')->label('comunicacion_dir_trabajo', 'comunicación dir trabajo')
                    
                    ->rule('comunicacion_seremi','Utiles::whitespace',array(':value'))
                    ->rule('comunicacion_seremi', 'not_empty')->label('comunicacion_seremi', 'Comunicación seremi')
                    
                    ->rule('plan_esp_trabajo_empresa','Utiles::whitespace',array(':value'))
                    ->rule('plan_esp_trabajo_empresa', 'not_empty')->label('plan_esp_trabajo_empresa', 'Plan esp trabajo empresa')
                    
                    ->rule('representante_oa_apellido_paterno','Utiles::whitespace',array(':value'))
                    ->rule('representante_oa_apellido_paterno', 'not_empty')->label('representante_oa_apellido_paterno', 'Ap. paterno')
                    
                    ->rule('representante_oa_apellido_materno','Utiles::whitespace',array(':value'))
                    ->rule('representante_oa_apellido_materno', 'not_empty')->label('representante_oa_apellido_materno', 'Ap. materno')
                    ->rule('representante_oa_nombres','Utiles::whitespace',array(':value'))
                    ->rule('representante_oa_nombres', 'not_empty')->label('representante_oa_nombres', 'Nombres')
                    
                    ->rule('representante_oa_rut','Utiles::whitespace',array(':value'))
                    ->rule('representante_oa_rut','not_empty')->rule('representante_oa_rut','Utiles::rut',array(':value'))
                    ->rule('representante_oa_rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                    ->rule('representante_oa_rut', 'not_empty')->label('representante_oa_rut', 'Rut')

                    ->rule('medidas_no_implementadas_fecha_verificacion', 'not_empty')
                    ->rule('medidas_no_implementadas_fecha_verificacion', 'date') 
                    ->rule('medidas_no_implementadas_fecha_verificacion','Utiles::validateDate',array(':value'))
                    ->rule('medidas_no_implementadas_fecha_verificacion','Utiles::fecha_minima',array(':value'))                    
                    ->rule('medidas_no_implementadas_fecha_verificacion','Utiles::whitespace',array(':value'))
                    ->label('medidas_no_implementadas_fecha_verificacion', 'Fecha verificación')

                    ->rule('medidas_no_implementadas_plazo_ampliado_fecha_verificacion','Utiles::whitespace',array(':value'))
                    ->rule('medidas_no_implementadas_plazo_ampliado_fecha_verificacion', 'not_empty')
                    ->rule('medidas_no_implementadas_plazo_ampliado_fecha_verificacion','Utiles::validateDate',array(':value'))
                    ->rule('medidas_no_implementadas_plazo_ampliado_fecha_verificacion','Utiles::fecha_minima',array(':value'))
                    ->rule('medidas_no_implementadas_plazo_ampliado_fecha_verificacion', 'date')
                    ->label('medidas_no_implementadas_plazo_ampliado_fecha_verificacion', 'Fecha verificación')
                        ;

                        if ($post['aplicacion_multa_art_80_ley'] == 1) {
                            $post = $post->rule('monto_multa', 'not_empty')->label('monto_multa', 'Monto multa')
                                ->rule('monto_multa', 'numeric')
                                ->rule('monto_multa', 'max_length', array(':value', 24))     
                                ->rule('monto_multa','Utiles::nonNegativeInteger',array(':value'))  
                                ->rule('fecha_multa', 'not_empty')
                                ->rule('fecha_multa','Utiles::validateDate',array(':value')) 
                                ->rule('fecha_multa','Utiles::whitespace',array(':value'))
                                ->rule('fecha_multa', 'date')
                                ->label('fecha_multa', 'Fecha multa');

                                if(!empty($_POST["fecha_multa"])) {
                                    if(!($_POST["fecha_multa"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                                        $errors = $errors+array("fecha_multa"=>"Fecha debe ser Mayor o igual a fecha de accidente");
                                    }
                                } 
                        }
                        if ($post['recargo_ds67_a15'] == 1) {
                            $post = $post->rule('fecha_inicio_recargo_a15', 'not_empty')
                                ->rule('fecha_inicio_recargo_a15', 'date') 
                                ->rule('fecha_inicio_recargo_a15','Utiles::validateDate',array(':value')) 
                                ->rule('fecha_inicio_recargo_a15','Utiles::fecha_minima',array(':value'))                    
                                ->rule('fecha_inicio_recargo_a15','Utiles::whitespace',array(':value'))
                                ->label('fecha_inicio_recargo_a15', 'Fecha inicio recargo a15')
                                ->rule('fecha_termino_recargo_a15', 'not_empty')
                                ->rule('fecha_termino_recargo_a15','Utiles::validateDate',array(':value')) 
                                ->rule('fecha_termino_recargo_a15','Utiles::whitespace',array(':value'))
                                ->rule('fecha_termino_recargo_a15', 'date')
                                ->label('fecha_termino_recargo_a15', 'Fecha termino recargo a15');

                                if(!empty($_POST["fecha_inicio_recargo_a15"])) {
                                    if(!($_POST["fecha_inicio_recargo_a15"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                                        $errors = $errors+array("fecha_inicio_recargo_a15"=>"Fecha debe ser Mayor o igual a fecha de accidente");
                                    }
                                } 
                                if(!empty($_POST["fecha_termino_recargo_a15"])) {
                                    if(!empty($_POST["fecha_inicio_recargo_a15"])) {
                                        if(!($_POST["fecha_termino_recargo_a15"]>=$_POST["fecha_inicio_recargo_a15"])) {
                                            $errors = $errors+array("fecha_termino_recargo_a15"=>"Fecha termino debe ser Mayor o igual a fecha de inicio");
                                        }                    
                                    }
                                }
                        }
                        if ($post['comunicacion_dir_trabajo'] == 1) {
                            $post = $post->rule('nro_comunic_dir_trabajo', 'not_empty')
                            ->rule('nro_comunic_dir_trabajo', 'numeric')
                            ->rule('nro_comunic_dir_trabajo', 'max_length', array(':value', 24))     
                            ->rule('nro_comunic_dir_trabajo','Utiles::nonNegativeInteger',array(':value'))  
                            ->label('nro_comunic_dir_trabajo', 'Nº comunición dir. trabajo')
                            ->rule('fecha_comunic_dir_trabajo','Utiles::validateDate',array(':value'))
                            ->rule('fecha_comunic_dir_trabajo', 'not_empty')                            
                            ->rule('fecha_comunic_dir_trabajo','Utiles::whitespace',array(':value'))
                            ->rule('fecha_comunic_dir_trabajo', 'date')
                            ->label('fecha_comunic_dir_trabajo', 'Fecha comunicación dir. trabajo');

                            if(!empty($_POST["fecha_comunic_dir_trabajo"])) {
                                if(!($_POST["fecha_comunic_dir_trabajo"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                                    $errors = $errors+array("fecha_comunic_dir_trabajo"=>"Fecha debe ser Mayor o igual a fecha de accidente");
                                }
                            }
                        }
                        if ($post['comunicacion_seremi'] == 1) {
                           $post = $post->rule('identificacion_seremi', 'not_empty')->label('identificacion_seremi', 'Identificación seremi')
                            ->rule('nro_comunic_seremi','Utiles::whitespace',array(':value'))
                            ->rule('nro_comunic_seremi', 'max_length', array(':value', 24))     
                            ->rule('nro_comunic_seremi', 'numeric')
                            ->rule('nro_comunic_seremi','Utiles::nonNegativeInteger',array(':value'))  
                            ->rule('nro_comunic_seremi', 'not_empty')->label('nro_comunic_seremi', 'Nº comunición seremi')
                            ->rule('fecha_comunic_seremi', 'not_empty')
                            ->rule('fecha_comunic_seremi','Utiles::validateDate',array(':value'))
                            ->rule('fecha_comunic_seremi','Utiles::whitespace',array(':value'))
                            ->rule('fecha_comunic_seremi', 'date')
                            ->label('fecha_comunic_seremi', 'Fecha comunición seremi');

                            if(!empty($_POST["fecha_comunic_seremi"])) {
                                if(!($_POST["fecha_comunic_seremi"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                                    $errors = $errors+array("fecha_comunic_seremi"=>"Fecha debe ser Mayor o igual a fecha de accidente");
                                }
                            }


                       }
                       if ($post['plan_esp_trabajo_empresa'] == 1) {
                           $post = $post->rule('fecha_ini_plan_trabajo_empresa', 'not_empty')
                            ->rule('fecha_ini_plan_trabajo_empresa','Utiles::whitespace',array(':value'))
                            ->rule('fecha_ini_plan_trabajo_empresa','Utiles::validateDate',array(':value'))
                            ->rule('fecha_ini_plan_trabajo_empresa', 'date')
                           ->rule('fecha_ini_plan_trabajo_empresa', 'not_empty')->label('fecha_ini_plan_trabajo_empresa', 'Fecha ini plan trabajo empresa')
                           ->rule('resumen_plan_trabajo','Utiles::whitespace',array(':value'))
                           ->rule('resumen_plan_trabajo', 'not_empty')->label('resumen_plan_trabajo', 'Resumen plan trabajo');

                           if(!empty($_POST["fecha_ini_plan_trabajo_empresa"])) {
                                if(!($_POST["fecha_ini_plan_trabajo_empresa"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                                    $errors = $errors+array("fecha_ini_plan_trabajo_empresa"=>"Fecha debe ser Mayor o igual a fecha de accidente");
                                }
                            }
                       }


                $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos_ralf5')->find_all();
                if(count($anexos)==0) {
                    $post=$post->rule('documentos_anexos_ralf5', 'not_empty')->label('documentos_anexos_ralf5', 'Documentos Anexos');                                    
                }              
                if(!empty($_POST["fecha_informe_acciones_adoptadas"])) {
                    if(!($_POST["fecha_informe_acciones_adoptadas"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                        $errors = $errors+array("fecha_informe_acciones_adoptadas"=>"Fecha debe ser Mayor o igual a fecha de accidente");
                    }
                }                  

                
                
                if($post->check() && count($errors)==0) {

                    $zona_t='<ZONA_T>
        <acciones_adoptadas>
            <fecha_informe_acciones_adoptadas></fecha_informe_acciones_adoptadas>   
            <constatacion_incumplimiento_medidas>
                <medidas_no_implementadas fecha_verificacion="">
                    <medida1></medida1>
                </medidas_no_implementadas>
                <medidas_no_implementadas_plazo_ampliado fecha_verificacion="">
                    <medida2></medida2>
                </medidas_no_implementadas_plazo_ampliado>
            </constatacion_incumplimiento_medidas>
            <aplicacion_multa_art_80_ley></aplicacion_multa_art_80_ley>
            <monto_multa></monto_multa>
            <fecha_multa></fecha_multa>
            <recargo_ds67_a15></recargo_ds67_a15>
            <recargo_ds67_a5></recargo_ds67_a5>
            <fecha_inicio_recargo_a15></fecha_inicio_recargo_a15>
            <fecha_termino_recargo_a15></fecha_termino_recargo_a15>
            <comunicacion_dir_trabajo></comunicacion_dir_trabajo>
            <nro_comunic_dir_trabajo></nro_comunic_dir_trabajo>
            <fecha_comunic_dir_trabajo></fecha_comunic_dir_trabajo>
            <comunicacion_seremi></comunicacion_seremi>
            <identificacion_seremi></identificacion_seremi>
            <nro_comunic_seremi></nro_comunic_seremi>
            <fecha_comunic_seremi></fecha_comunic_seremi>
            <plan_esp_trabajo_empresa></plan_esp_trabajo_empresa>
            <fecha_ini_plan_trabajo_empresa></fecha_ini_plan_trabajo_empresa>
            <resumen_plan_trabajo></resumen_plan_trabajo>
            <documentos_anexos></documentos_anexos>
            <representante_oa>
                <apellido_paterno></apellido_paterno>
                <apellido_materno></apellido_materno>
                <nombres></nombres>
                <rut></rut>
            </representante_oa>
        </acciones_adoptadas>
    </ZONA_T>';

                    $xml_zona_t = simplexml_load_string($zona_t);

                    $xml_zona_t->acciones_adoptadas->fecha_informe_acciones_adoptadas=$post["fecha_informe_acciones_adoptadas"];                    
                    $xml_zona_t->acciones_adoptadas->aplicacion_multa_art_80_ley=$post["aplicacion_multa_art_80_ley"];
                    $xml_zona_t->acciones_adoptadas->monto_multa=$post["monto_multa"];
                    $xml_zona_t->acciones_adoptadas->fecha_multa=$post["fecha_multa"];
                    if($post['aplicacion_multa_art_80_ley'] != 1) {
                        unset($xml_zona_t->acciones_adoptadas->monto_multa);
                        unset($xml_zona_t->acciones_adoptadas->fecha_multa);
                    }                    
                    $xml_zona_t->acciones_adoptadas->recargo_ds67_a15=$post["recargo_ds67_a15"];
                    $xml_zona_t->acciones_adoptadas->recargo_ds67_a5=$post["recargo_ds67_a5"];
                    $xml_zona_t->acciones_adoptadas->fecha_inicio_recargo_a15=$post["fecha_inicio_recargo_a15"];
                    $xml_zona_t->acciones_adoptadas->fecha_termino_recargo_a15=$post["fecha_termino_recargo_a15"];
                    if ($post['recargo_ds67_a15'] != 1) {
                        unset($xml_zona_t->acciones_adoptadas->fecha_inicio_recargo_a15);
                        unset($xml_zona_t->acciones_adoptadas->fecha_termino_recargo_a15);
                    }
                    $xml_zona_t->acciones_adoptadas->comunicacion_dir_trabajo=$post["comunicacion_dir_trabajo"];
                    $xml_zona_t->acciones_adoptadas->nro_comunic_dir_trabajo=$post["nro_comunic_dir_trabajo"];
                    $xml_zona_t->acciones_adoptadas->fecha_comunic_dir_trabajo=$post["fecha_comunic_dir_trabajo"];
                    if ($post['comunicacion_dir_trabajo'] != 1) {
                        unset($xml_zona_t->acciones_adoptadas->nro_comunic_dir_trabajo);
                        unset($xml_zona_t->acciones_adoptadas->fecha_comunic_dir_trabajo);
                    }
                   
                    $xml_zona_t->acciones_adoptadas->comunicacion_seremi=$post["comunicacion_seremi"];
                    $xml_zona_t->acciones_adoptadas->identificacion_seremi=$post["identificacion_seremi"];
                    $xml_zona_t->acciones_adoptadas->nro_comunic_seremi=$post["nro_comunic_seremi"];
                    $xml_zona_t->acciones_adoptadas->fecha_comunic_seremi=$post["fecha_comunic_seremi"];
                    if ($post['comunicacion_seremi'] != 1) {
                        unset($xml_zona_t->acciones_adoptadas->identificacion_seremi);
                        unset($xml_zona_t->acciones_adoptadas->nro_comunic_seremi);
                        unset($xml_zona_t->acciones_adoptadas->fecha_comunic_seremi);
                    }
                    $xml_zona_t->acciones_adoptadas->plan_esp_trabajo_empresa=$post["plan_esp_trabajo_empresa"];
                    $xml_zona_t->acciones_adoptadas->fecha_ini_plan_trabajo_empresa=$post["fecha_ini_plan_trabajo_empresa"];
                    $xml_zona_t->acciones_adoptadas->resumen_plan_trabajo=$post["resumen_plan_trabajo"];
                    if ($post['plan_esp_trabajo_empresa'] != 1) {
                        unset($xml_zona_t->acciones_adoptadas->fecha_ini_plan_trabajo_empresa);
                        unset($xml_zona_t->acciones_adoptadas->resumen_plan_trabajo);
                    }
                    $xml_zona_t->acciones_adoptadas->representante_oa->apellido_paterno=$post["representante_oa_apellido_paterno"];
                    $xml_zona_t->acciones_adoptadas->representante_oa->apellido_materno=$post["representante_oa_apellido_materno"];
                    $xml_zona_t->acciones_adoptadas->representante_oa->nombres=$post["representante_oa_nombres"];
                    $xml_zona_t->acciones_adoptadas->representante_oa->rut=strtoupper($post["representante_oa_rut"]);

                    $xml_zona_t->acciones_adoptadas->constatacion_incumplimiento_medidas[0]->medidas_no_implementadas["fecha_verificacion"]=$post["medidas_no_implementadas_fecha_verificacion"];
                    $xml_zona_t->acciones_adoptadas->constatacion_incumplimiento_medidas[0]->medidas_no_implementadas_plazo_ampliado["fecha_verificacion"]=$post["medidas_no_implementadas_plazo_ampliado_fecha_verificacion"];

                    $variable = preg_replace("/<ZONA_T.*ZONA_T>/ms", str_replace('<?xml version="1.0"?>', '', $xml_zona_t->asXml()), $ralf->asXml());
                    //var_dump($variable); die();

                    $ralf = simplexml_load_string($variable);

                    /*
                     * faillons
                     * se agrega transformación del XML
                     * en caso que la ZONA_C venga con tag documento_identidad
                     */
                    if(isset($ralf->ZONA_C->empleado->trabajador->documento_identidad)){
                        $ralf = Documento::transformarZonaCAntigua($ralf);
                    }


                    $ralf_bd=$ralf->saveXML();
                    $ralf=Controller_Ralf5::documentos_anexos($xml_id,$ralf);                    
                    $ralf_string=$ralf->saveXML();                        


                    $medida1=Controller_Ralf5::medida1($xml_id);                              
                    $ralf_string=str_replace('<medida1></medida1>',$medida1,$ralf_string);       
                    $ralf_string=str_replace('<medida1/>',$medida1,$ralf_string);       

                    $medida2=Controller_Ralf5::medida2($xml_id);                              
                    $ralf_string=str_replace('<medida2></medida2>',$medida2,$ralf_string);       
                    $ralf_string=str_replace('<medida2/>',$medida2,$ralf_string);       

                    
                    $ralf = Documento::zona_o($ralf_string);

                         
                            
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf),$documento->TPXML_ID);                                                        
                    $valido=Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_5.1.0.xsd');                                        
                    
                    if($valido['estado']) {
                        $documentostring->XMLSTRING=$ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO=1;
                        $documento->ESTADO=6;
                        $documento->save();          

                        $ralf5=ORM::factory('Ralf5')->where('xml_id','=',$xml_id)->find();
                        $ralf5->fecha_informe_acciones_adoptadas=$post["fecha_informe_acciones_adoptadas"];            
                        $ralf5->aplicacion_multa_art_80_ley=$post["aplicacion_multa_art_80_ley"];
                        $ralf5->monto_multa=$post["monto_multa"];
                        $ralf5->fecha_multa=$post["fecha_multa"];
                        $ralf5->recargo_ds67_a15=$post["recargo_ds67_a15"];
                        $ralf5->recargo_ds67_a5=$post["recargo_ds67_a5"];
                        $ralf5->fecha_inicio_recargo_a15=$post["fecha_inicio_recargo_a15"];
                        $ralf5->fecha_termino_recargo_a15=$post["fecha_termino_recargo_a15"];
                        $ralf5->comunicacion_dir_trabajo=$post["comunicacion_dir_trabajo"];
                        $ralf5->nro_comunic_dir_trabajo=$post["nro_comunic_dir_trabajo"];
                        $ralf5->fecha_comunic_dir_trabajo=$post["fecha_comunic_dir_trabajo"];
                        $ralf5->comunicacion_seremi=$post["comunicacion_seremi"];
                        $ralf5->identificacion_seremi=$post["identificacion_seremi"];
                        $ralf5->nro_comunic_seremi=$post["nro_comunic_seremi"];
                        $ralf5->fecha_comunic_seremi=$post["fecha_comunic_seremi"];
                        $ralf5->plan_esp_trabajo_empresa=$post["plan_esp_trabajo_empresa"];
                        $ralf5->fecha_ini_plan_trabajo_empresa=$post["fecha_ini_plan_trabajo_empresa"];
                        $ralf5->resumen_plan_trabajo=$post["resumen_plan_trabajo"];
                        $ralf5->representante_oa_apellido_paterno=$post["representante_oa_apellido_paterno"];
                        $ralf5->representante_oa_apellido_materno=$post["representante_oa_apellido_materno"];
                        $ralf5->representante_oa_nombres=$post["representante_oa_nombres"];
                        $ralf5->representante_oa_rut=strtoupper($post["representante_oa_rut"]);
                        $ralf5->medidas_no_implementadas_fecha_verificacion=$post["medidas_no_implementadas_fecha_verificacion"];
                        $ralf5->medidas_no_implementadas_plazo_ampliado_fecha_verificacion=$post["medidas_no_implementadas_plazo_ampliado_fecha_verificacion"];
                        $ralf5->xml_id=$xml_id;
                        $ralf5->save();   
              
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
                $ralf->ZONA_T->acciones_adoptadas->fecha_informe_acciones_adoptadas=$post["fecha_informe_acciones_adoptadas"];                
                $ralf->ZONA_T->acciones_adoptadas->aplicacion_multa_art_80_ley=$post["aplicacion_multa_art_80_ley"];
                $ralf->ZONA_T->acciones_adoptadas->monto_multa=$post["monto_multa"];
                $ralf->ZONA_T->acciones_adoptadas->fecha_multa=$post["fecha_multa"];
                $ralf->ZONA_T->acciones_adoptadas->recargo_ds67_a15=$post["recargo_ds67_a15"];
                $ralf->ZONA_T->acciones_adoptadas->recargo_ds67_a5=$post["recargo_ds67_a5"];
                $ralf->ZONA_T->acciones_adoptadas->fecha_inicio_recargo_a15=$post["fecha_inicio_recargo_a15"];
                $ralf->ZONA_T->acciones_adoptadas->fecha_termino_recargo_a15=$post["fecha_termino_recargo_a15"];
                $ralf->ZONA_T->acciones_adoptadas->comunicacion_dir_trabajo=$post["comunicacion_dir_trabajo"];
                $ralf->ZONA_T->acciones_adoptadas->nro_comunic_dir_trabajo=$post["nro_comunic_dir_trabajo"];
                $ralf->ZONA_T->acciones_adoptadas->fecha_comunic_dir_trabajo=$post["fecha_comunic_dir_trabajo"];
                $ralf->ZONA_T->acciones_adoptadas->comunicacion_seremi=$post["comunicacion_seremi"];
                $ralf->ZONA_T->acciones_adoptadas->identificacion_seremi=$post["identificacion_seremi"];
                $ralf->ZONA_T->acciones_adoptadas->nro_comunic_seremi=$post["nro_comunic_seremi"];
                $ralf->ZONA_T->acciones_adoptadas->fecha_comunic_seremi=$post["fecha_comunic_seremi"];
                $ralf->ZONA_T->acciones_adoptadas->plan_esp_trabajo_empresa=$post["plan_esp_trabajo_empresa"];
                $ralf->ZONA_T->acciones_adoptadas->fecha_ini_plan_trabajo_empresa=$post["fecha_ini_plan_trabajo_empresa"];
                $ralf->ZONA_T->acciones_adoptadas->resumen_plan_trabajo=$post["resumen_plan_trabajo"];
                $ralf->ZONA_T->acciones_adoptadas->representante_oa->apellido_paterno=$post["representante_oa_apellido_paterno"];
                $ralf->ZONA_T->acciones_adoptadas->representante_oa->apellido_materno=$post["representante_oa_apellido_materno"];
                $ralf->ZONA_T->acciones_adoptadas->representante_oa->nombres=$post["representante_oa_nombres"];
                $ralf->ZONA_T->acciones_adoptadas->representante_oa->rut=strtoupper($post["representante_oa_rut"]);
                
                $ralf->ZONA_T->acciones_adoptadas->constatacion_incumplimiento_medidas[0]->medidas_no_implementadas["fecha_verificacion"]=$post["medidas_no_implementadas_fecha_verificacion"];
                $ralf->ZONA_T->acciones_adoptadas->constatacion_incumplimiento_medidas[0]->medidas_no_implementadas_plazo_ampliado["fecha_verificacion"]=$post["medidas_no_implementadas_plazo_ampliado_fecha_verificacion"];
                

                $documentostring->XMLSTRING=$ralf->saveXML();
                $documentostring->save();
                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");                
            }
        }                        
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf; 
        $data['criterio_gravedad'] = $organismo=Kohana::$config->load('dominios.STCriterio_gravedad_RALF');        
        $this->template->mensaje_error=$mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralf5/crear')                 
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
            
            $default["fecha_informe_acciones_adoptadas"]=$ralf->ZONA_T->acciones_adoptadas->fecha_informe_acciones_adoptadas;            
            $default["aplicacion_multa_art_80_ley"]=$ralf->ZONA_T->acciones_adoptadas->aplicacion_multa_art_80_ley;
            $default["monto_multa"]=$ralf->ZONA_T->acciones_adoptadas->monto_multa;
            $default["fecha_multa"]=$ralf->ZONA_T->acciones_adoptadas->fecha_multa;
            $default["recargo_ds67_a15"]=$ralf->ZONA_T->acciones_adoptadas->recargo_ds67_a15;
            $default["recargo_ds67_a5"]=$ralf->ZONA_T->acciones_adoptadas->recargo_ds67_a5;
            $default["fecha_inicio_recargo_a15"]=$ralf->ZONA_T->acciones_adoptadas->fecha_inicio_recargo_a15;
            $default["fecha_termino_recargo_a15"]=$ralf->ZONA_T->acciones_adoptadas->fecha_termino_recargo_a15;
            $default["comunicacion_dir_trabajo"]=$ralf->ZONA_T->acciones_adoptadas->comunicacion_dir_trabajo;
            $default["nro_comunic_dir_trabajo"]=$ralf->ZONA_T->acciones_adoptadas->nro_comunic_dir_trabajo;
            $default["fecha_comunic_dir_trabajo"]=$ralf->ZONA_T->acciones_adoptadas->fecha_comunic_dir_trabajo;
            $default["comunicacion_seremi"]=$ralf->ZONA_T->acciones_adoptadas->comunicacion_seremi;
            $default["identificacion_seremi"]=$ralf->ZONA_T->acciones_adoptadas->identificacion_seremi;
            $default["nro_comunic_seremi"]=$ralf->ZONA_T->acciones_adoptadas->nro_comunic_seremi;
            $default["fecha_comunic_seremi"]=$ralf->ZONA_T->acciones_adoptadas->fecha_comunic_seremi;
            $default["plan_esp_trabajo_empresa"]=$ralf->ZONA_T->acciones_adoptadas->plan_esp_trabajo_empresa;
            $default["fecha_ini_plan_trabajo_empresa"]=$ralf->ZONA_T->acciones_adoptadas->fecha_ini_plan_trabajo_empresa;
            $default["resumen_plan_trabajo"]=$ralf->ZONA_T->acciones_adoptadas->resumen_plan_trabajo;
            $default["representante_oa_apellido_paterno"]=$ralf->ZONA_T->acciones_adoptadas->representante_oa->apellido_paterno;
            $default["representante_oa_apellido_materno"]=$ralf->ZONA_T->acciones_adoptadas->representante_oa->apellido_materno;
            $default["representante_oa_nombres"]=$ralf->ZONA_T->acciones_adoptadas->representante_oa->nombres;
            $default["representante_oa_rut"]=strtoupper($ralf->ZONA_T->acciones_adoptadas->representante_oa->rut);

            $default["medidas_no_implementadas_fecha_verificacion"]=$ralf->ZONA_T->acciones_adoptadas->constatacion_incumplimiento_medidas[0]->medidas_no_implementadas["fecha_verificacion"];
            $default["medidas_no_implementadas_plazo_ampliado_fecha_verificacion"]=$ralf->ZONA_T->acciones_adoptadas->constatacion_incumplimiento_medidas[0]->medidas_no_implementadas_plazo_ampliado["fecha_verificacion"];            
        } else {
            
            $default["fecha_informe_acciones_adoptadas"]=$post["fecha_informe_acciones_adoptadas"];            
            $default["aplicacion_multa_art_80_ley"]=$post["aplicacion_multa_art_80_ley"];
            $default["monto_multa"]=$post["monto_multa"];
            $default["fecha_multa"]=$post["fecha_multa"];
            $default["recargo_ds67_a15"]=$post["recargo_ds67_a15"];
            $default["recargo_ds67_a5"]=$post["recargo_ds67_a5"];
            $default["fecha_inicio_recargo_a15"]=$post["fecha_inicio_recargo_a15"];
            $default["fecha_termino_recargo_a15"]=$post["fecha_termino_recargo_a15"];
            $default["comunicacion_dir_trabajo"]=$post["comunicacion_dir_trabajo"];
            $default["nro_comunic_dir_trabajo"]=$post["nro_comunic_dir_trabajo"];
            $default["fecha_comunic_dir_trabajo"]=$post["fecha_comunic_dir_trabajo"];
            $default["comunicacion_seremi"]=$post["comunicacion_seremi"];
            $default["identificacion_seremi"]=$post["identificacion_seremi"];
            $default["nro_comunic_seremi"]=$post["nro_comunic_seremi"];
            $default["fecha_comunic_seremi"]=$post["fecha_comunic_seremi"];
            $default["plan_esp_trabajo_empresa"]=$post["plan_esp_trabajo_empresa"];
            $default["fecha_ini_plan_trabajo_empresa"]=$post["fecha_ini_plan_trabajo_empresa"];
            $default["resumen_plan_trabajo"]=$post["resumen_plan_trabajo"];
            $default["representante_oa_apellido_paterno"]=$post["representante_oa_apellido_paterno"];
            $default["representante_oa_apellido_materno"]=$post["representante_oa_apellido_materno"];
            $default["representante_oa_nombres"]=$post["representante_oa_nombres"];
            $default["representante_oa_rut"]=strtoupper($post["representante_oa_rut"]);
            $default["medidas_no_implementadas_fecha_verificacion"]=$post["medidas_no_implementadas_fecha_verificacion"];
            $default["medidas_no_implementadas_plazo_ampliado_fecha_verificacion"]=$post["medidas_no_implementadas_plazo_ampliado_fecha_verificacion"];
        }        
        return $default;        
    }

    public static function documentos_anexos($xml_id,$ralf) {        
        $documentos_anexos=$ralf->ZONA_T->acciones_adoptadas->documentos_anexos;
        $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos_ralf5')->find_all();      
        foreach ($anexos as $anexo) {        	
        	$path=$anexo->ruta;
			$type = pathinfo($path, PATHINFO_EXTENSION);        
        	$data = file_get_contents($path);        
        	//$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        	$base64 = base64_encode($data);        
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

    public static function medida1($xml_id) {
        $medidas=ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_no_implementadas')->find_all();      
        //echo Database::instance()->last_query;
        $retorno="";
        if(count($medidas)>0){            
            foreach ($medidas as $m) {
                $variable=htmlspecialchars($m->medida, ENT_QUOTES, 'UTF-8');   
                $retorno.="<medida>{$variable}</medida>";              
            }            
        }        
        return $retorno;
    }

    public static function medida2($xml_id) {
        $medidas=ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_no_implementadas_plazo_ampliado')->find_all();      
        //echo Database::instance()->last_query;
        $retorno="";
        if(count($medidas)>0){            
            foreach ($medidas as $m) {
                $variable=htmlspecialchars($m->medida, ENT_QUOTES, 'UTF-8');   
                $retorno.="<medida>{$variable}</medida>";              
            }            
        }        
        return $retorno;
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
            View::factory('ralf5/borrar_adjunto')->set('borrado',$borrado)->set('xml_id',$xml_id)
            ->set('adjunto_origen',$adjunto_origen)
            ->set('nombre_documento',$nombre_documento)
            );
    }

    public function action_borrar_medida() {            
        $this->auto_render=false;
        $id = $this->request->param('id');
        $medida=ORM::factory('Medida',$id);        
        $xml_id = $medida->xml_id;
        $borrado=false;        
        if(isset ($_POST['boton_aceptar'])) {            
            $medida->delete();
            $borrado=true;
        }

        $this->response->body (
            View::factory('ralf5/borrar_medida')->set('medida',$medida)->set('borrado',$borrado)->set('xml_id',$xml_id)
            );
    }

    public function action_borrar_medida2() {            
        $this->auto_render=false;
        $id = $this->request->param('id');
        $medida=ORM::factory('Medida',$id);        
        $xml_id = $medida->xml_id;
        $borrado=false;        
        if(isset ($_POST['boton_aceptar'])) {            
            $medida->delete();
            $borrado=true;
        }

        $this->response->body (
            View::factory('ralf5/borrar_medida2')->set('medida',$medida)->set('borrado',$borrado)->set('xml_id',$xml_id)
            );
    }

    
}