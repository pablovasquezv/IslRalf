<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ralf extends Controller_Website {

      
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
        //Busco un documento que este enviada a suseso
        $documento=$caso->xmls
                ->where('TPXML_ID','IN', array(1,2,3,4,5,6))
                ->where('ESTADO','=',1)->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;  die();      
        if(!$documento->loaded()){
            $this->template->mensaje_error='Se debe agregar una denuncia al caso.';
            $this->template->contenido='';
            return;
        }

        $ralf_anterior=$caso->xmls->where('TPXML_ID','IN', array(12,141))->where('ESTADO','!=', 3)->find();
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

        if(isset($xml_documento->ZONA_D)) {
            unset($xml_documento->ZONA_D);
        }elseif(isset($xml_documento->ZONA_E)) {
            unset($xml_documento->ZONA_E);
        }
        
        if(isset($xml_documento->ZONA_H)) {
            unset($xml_documento->ZONA_H);
        }
        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }
        if(isset($xml_documento->ZONA_G)) {
            unset($xml_documento->ZONA_G);
        }
        if(isset($xml_documento->ZONA_F)) {
            unset($xml_documento->ZONA_F);
        }
        
        
        // Cambiar nombre de documento
        $documento_preparacion=dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'RALF_Accidente');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());
        
        //$cabecera='<RALF_Accidente xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xsi:schemaLocation="http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd    http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_RALF_Accidente.1.0.xsd" id="z_padre">';

        $cabecera='<RALF_Accidente id="z_padre">';        
        $ralf=str_replace('<RALF_Accidente>',$cabecera,$ralf_preparacion->saveXML());
        
        $ralf = simplexml_load_string($ralf);        
        
        //COMPLEMENTAMOS ZONA B VACIA  
        $zona_b=$ralf->ZONA_B->empleador;  
        $zona_b->addChild('rut_representante_legal', '');
        $zona_b->addChild('nombre_representante_legal', '');
        $zona_b->addChild('tasa_ds110', '');
        $zona_b->addChild('tasa_ds67', '');
        $zona_b->addChild('ultima_eval_ds67', '');
        $zona_b->addChild('nro_sucursales', '');
        $zona_b->addChild('promedio_anual_trabajadores', '');                        
        $zona_p_vacia=$ralf->addChild('ZONA_P', '');
        $accidente_fatal = $zona_p_vacia->addChild('accidente_fatal', '');
        $accidente_fatal->addChild('fecha_accidente', '');
        $accidente_fatal->addChild('hora_accidente', '');
        
        $direccion_accidente=$accidente_fatal->addChild('direccion_accidente', '');
        $direccion_accidente->addChild('tipo_calle', '');
        $direccion_accidente->addChild('nombre_calle', '');
        $direccion_accidente->addChild('numero', '');
        $direccion_accidente->addChild('resto_direccion', '');
        $direccion_accidente->addChild('localidad', '');
        $direccion_accidente->addChild('comuna', '');
        
        $gravedad=$accidente_fatal->addChild('gravedad', '');        
        
        $accidente_fatal->addChild('fecha_defuncion', '');
        $accidente_fatal->addChild('lugar_defuncion', '');
        $accidente_fatal->addChild('lugar_defuncion_otro', '');        
        $accidente_fatal->addChild('descripcion_accidente_ini', '');
        
        $informante_oa=$accidente_fatal->addChild('informante_oa', '');
        $informante_oa->addChild('apellido_paterno', '');
        $informante_oa->addChild('apellido_materno', '');
        $informante_oa->addChild('nombres', '');
        $informante_oa->addChild('rut', '');
        
        $telefono_informante_oa=$accidente_fatal->addChild('telefono_informante_oa', '');
        $telefono_informante_oa->addChild('cod_pais', '');
        $telefono_informante_oa->addChild('cod_area', '');
        $telefono_informante_oa->addChild('numero', '');
        $accidente_fatal->addChild('correo_electronico_informante_oa', '');
        
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
        $xml_insert->TPXML_ID=141;
        $xml_insert->VALIDO=0;  
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN=$documento->XML_ID;      
        $xml_insert->save();
        
        $doc=simplexml_load_string($xmlstring->XMLSTRING);        
        $doc->ZONA_A->documento->folio=$xml_insert->XML_ID;        
        $xmlstring->XMLSTRING=$doc->saveXML();
        $xmlstring->save();
        $this->redirect("ralf/crear/$xml_insert->XML_ID");
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
        $xml_documento=simplexml_load_string($documentostring->XMLSTRING);

        
        $ralf = $documentostring->XMLSTRING;
        if(strpos($ralf, 'ralf1') !== false){

            $cabecera_old = '<ralf1 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xsi:schemaLocation="http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd    http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_RALF_1.1.0.xsd" id="z_padre">';

            $cabecera_new = '<RALF_Accidente id="z_padre">';

            $fin_xml_old = '</ralf1>';
            $fin_xml_new = '</RALF_Accidente>';

            $ralf = str_replace($cabecera_old, $cabecera_new, $ralf);
            $ralf = str_replace($fin_xml_old, $fin_xml_new, $ralf);



            // $documento_preparacion = dom_import_simplexml($xml_documento);
            // Documento::clonishNode($documento_preparacion, 'RALF_Accidente');
            // $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());
            
            // $cabecera='<RALF_Accidente id="z_padre">';  
            // $ralf=str_replace('<RALF_Accidente schemaLocation="http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd    http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd" noNamespaceSchemaLocation="SISESAT_RALF_1.1.0.xsd" id="z_padre">',$cabecera,$ralf_preparacion->saveXML());
        }

        /*$documento_preparacion = dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'RALF_Accidente');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());
        
        //$cabecera='<RALF_Accidente xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xsi:schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_RALF_Accidente.1.0.xsd" id="z_padre">';

        $cabecera='<RALF_Accidente id="z_padre">';
        $ralf=str_ireplace('<ralf_accidente schemalocation="http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd    http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd" nonamespaceschemalocation="SISESAT_RALF_1.1.0.xsd" id="z_padre">',$cabecera,$ralf_preparacion->saveXML(),$cont);*/

        
        $ralf = simplexml_load_string($ralf);
        $ralf->saveXML();


               
        $errores_esquema=NULL;
        $errors = array();
        $mensaje_error = null;                                 
        if (isset($_POST) AND Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_finalizar'])) {                       
                $post = Validation::factory($_POST)
                        

                        //INICIO ZONA B COMPLEMENTO
                        ->rule('ciiu_empleador', 'not_empty')->label('ciiu_empleador', 'Actividad principal')
                        ->rule('comuna_empleador', 'not_empty')->label('comuna_empleador', 'Comuna empleador')
                        ->rule('rut_representante_legal', 'not_empty') 
                        ->rule('rut_representante_legal','Utiles::whitespace',array(':value')) 
                        ->rule('rut_representante_legal','not_empty')->rule('rut_representante_legal','Utiles::rut',array(':value'))
                        ->rule('rut_representante_legal', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->label('rut_representante_legal', 'Rut representante legal')
                        ->rule('nombre_representante_legal', 'not_empty')
                        ->rule('nombre_representante_legal','Utiles::whitespace',array(':value')) 
                        ->label('nombre_representante_legal', 'Nombre')
                        ->rule('tasa_ds110', 'not_empty')                        
                        ->rule('tasa_ds110','Utiles::whitespace',array(':value'))
                        ->rule('tasa_ds110','Utiles::is_float',array(':value')) 
                        ->rule('tasa_ds110','Utiles::nonNegativeInteger',array(':value'))
                        ->label('tasa_ds110', 'Tasa ds110')

                        ->rule('tipo_calle', 'not_empty')      
                        ->label('tipo_calle', 'Tipo calle')
                        
                        ->rule('tasa_ds67', 'not_empty')
                        ->rule('tasa_ds67','Utiles::is_float',array(':value')) 
                        ->rule('tasa_ds67','Utiles::whitespace',array(':value')) 
                        ->rule('tasa_ds67','Utiles::nonNegativeInteger',array(':value'))
                        ->label('tasa_ds67', 'Tasa ds67')
                        
                        ->rule('ultima_eval_ds67', 'not_empty')
                        ->rule('ultima_eval_ds67','Utiles::whitespace',array(':value')) 
                        ->label('ultima_eval_ds67', 'Última evalaluación ds67')
                        ->rule('nro_sucursales', 'numeric')
                        ->rule('nro_sucursales', 'max_length', array(':value', 24))     
                        ->rule('nro_sucursales','Utiles::whitespace',array(':value')) 
                        ->rule('nro_sucursales','Utiles::nonNegativeInteger',array(':value'))                         
                        ->rule('nro_sucursales', 'not_empty')->label('nro_sucursales', 'Nº sucursales')

                        

                        ->rule('promedio_anual_trabajadores', 'not_empty')
                        ->rule('promedio_anual_trabajadores','Utiles::whitespace',array(':value')) 
                        ->rule('promedio_anual_trabajadores','Utiles::is_float',array(':value')) 
                        ->rule('promedio_anual_trabajadores','Utiles::nonNegativeInteger',array(':value'))
                        ->rule('promedio_anual_trabajadores','Utiles::mayorqueuno',array(':value'))
                        ->label('promedio_anual_trabajadores', 'Promedio anual trabajadores')                       
                        //INICIO ZONA B COMPLEMENTO

                        //INICIO ZONA P

                        ->rule('fecha_accidente', 'not_empty')
                        ->rule('fecha_accidente','Utiles::whitespace',array(':value')) 
                        ->rule('fecha_accidente','Utiles::validateDate',array(':value')) 
                        ->rule('fecha_accidente', 'date')
                        ->label('fecha_accidente', 'Fecha accidente')

                        ->rule('nombre_calle', 'not_empty')
                        ->rule('nombre_calle','Utiles::whitespace',array(':value')) 
                        ->label('nombre_calle', 'Nombre Calle')
                        ->rule('numero', 'not_empty')
                        ->rule('numero', 'numeric')
                        ->rule('numero','Utiles::whitespace',array(':value')) 
                        ->label('numero', 'Nº')
                        ->rule('comuna', 'not_empty')->label('comuna', 'Comuna')
                        
                        ->rule('descripcion_accidente_ini', 'not_empty')
                        ->rule('descripcion_accidente_ini','Utiles::whitespace',array(':value')) 
                        ->label('descripcion_accidente_ini', 'descripción accidente')
                        ->rule('nombres', 'not_empty')
                        ->rule('nombres','Utiles::whitespace',array(':value')) 
                        ->label('nombres', 'Nombres')
                        ->rule('apellido_paterno', 'not_empty')
                        ->rule('apellido_paterno','Utiles::whitespace',array(':value')) 
                        ->label('apellido_paterno', 'Ap. paterno')
                        ->rule('apellido_materno', 'not_empty')
                        ->rule('apellido_materno','Utiles::whitespace',array(':value')) 
                        ->label('apellido_materno', 'Ap. materno')
                        ->rule('rut', 'not_empty')
                        ->rule('rut','Utiles::whitespace',array(':value')) 
                        ->rule('rut','not_empty')->rule('rut','Utiles::rut',array(':value'))
                        ->rule('rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->label('rut', 'Rut')
                        
                        ->rule('ciuo_trabajador', 'not_empty')->label('ciuo_trabajador', 'Ciuo trabajador')
                        ->rule('pais_nacionalidad', 'not_empty')->label('pais_nacionalidad', 'Nacionalidad trabajador')


                        ->rule('resto_direccion','Utiles::whitespace',array(':value')) 
                        ->label('resto_direccion', 'Resto')
                        ->rule('localidad','Utiles::whitespace',array(':value')) 
                        ->label('localidad', 'Localidad')
                        ->rule('fecha_defuncion','Utiles::whitespace',array(':value')) 
                        ->rule('fecha_defuncion','Utiles::validateDate',array(':value')) 
                        ->rule('fecha_defuncion', 'date')
                        ->label('fecha_defuncion', 'Fecha Defuncion')
                        ->rule('lugar_defuncion_otro','Utiles::whitespace',array(':value')) 
                        ->label('lugar_defuncion_otro', 'lugar defuncion otro')
                        ;


                        $criterios_array=array();
                        if(isset($_POST["criterio_1"])) {
                            $post=$post->rule('fecha_defuncion', 'not_empty')->label('fecha_defuncion', 'fecha_defuncion');
                            $post=$post->rule('lugar_defuncion', 'not_empty')->label('lugar_defuncion', 'lugar_defuncion');
                            $criterios_array[]=1;
                        }

                        if(isset($_POST["criterio_2"])) {
                            $criterios_array[]=2;
                        }
                        if(isset($_POST["criterio_3"])) {
                            $criterios_array[]=3;
                        }
                        if(isset($_POST["criterio_4"])) {
                            $criterios_array[]=4;
                        }
                        if(isset($_POST["criterio_5"])) {
                            $criterios_array[]=5;
                        }
                        if(isset($_POST["criterio_6"])) {
                            $criterios_array[]=6;
                        }
                        if(isset($_POST["criterio_7"])) {
                            $criterios_array[]=7;
                        }
                        if(isset($_POST["criterio_8"])) {
                            $criterios_array[]=8;
                        }
                        if(count($criterios_array)>0) {                            
                            if(count($criterios_array)>1 && in_array(1, $criterios_array)) {
                                $errors = $errors+array("criterio"=>'Solo se acepta 1 criterio si viene al seleccionar "Muerte del trabajador"');
                            }
                        }else {
                            $errors = $errors+array("criterio"=>"Debe indicar criterio de gravedad");                       
                        }
                        if(isset($post['correo_electronico_informante_oa']) && !empty($post['correo_electronico_informante_oa'])) {                            
                            $post=$post->rule('correo_electronico_informante_oa', 'email')
                                ->rule('correo_electronico_informante_oa', 'not_empty')->label('correo_electronico_informante_oa', 'Email');
                        }

                        if($post['lugar_defuncion']==4) {                            
                            $post=$post->rule('lugar_defuncion_otro', 'not_empty')->label('lugar_defuncion_otro', 'lugar defuncion otro');
                        }
                        
                        if(!empty($post["cod_area"])) {
                            $post=$post->rule('cod_area', 'numeric')->rule('telefono_informante_oa', 'not_empty')->rule('telefono_informante_oa', 'numeric')->label('telefono_informante_oa', 'numero');

                        }

                        if(!empty($post["telefono_informante_oa"])) {
                            $post=$post->rule('telefono_informante_oa', 'numeric')->rule('cod_area', 'not_empty')->rule('cod_area', 'numeric')->label('cod_area', 'cod_area');
                        }   



                if(!empty($_POST["fecha_accidente"])) {
                    if(!($_POST["fecha_accidente"]>=$ralf->ZONA_C->empleado->fecha_ingreso)) {
                        $errors = $errors+array("fecha_accidente"=>"Fecha accidente debe ser Mayor o igual a fecha de ingreso");
                    }elseif(!($_POST["fecha_accidente"]<=date('Y-m-d'))) {
                        $errors = $errors+array("fecha_accidente"=>"Fecha accidente debe ser Menor o igual a fecha actual");
                    }
                }

                if(!empty($_POST["fecha_defuncion"])) {
                	if(!empty($_POST["fecha_accidente"])) {
                		if(!($_POST["fecha_defuncion"]>=$_POST["fecha_accidente"])) {
                        	$errors = $errors+array("fecha_defuncion"=>"Fecha defuncion debe ser Mayor o igual a fecha de accidente");
                		}elseif(!($_POST["fecha_defuncion"]<=date('Y-m-d'))) {
                        $errors = $errors+array("fecha_defuncion"=>"Fecha defuncion debe ser Menor o igual a fecha actual");
                        }
                    }
                }
                if($post->check() && count($errors)==0) {                	
                	//ZONA_B
                	$zona_b="<ZONA_B>
        <empleador>
            <rut_empleador></rut_empleador>
            <nombre_empleador></nombre_empleador>
            <direccion_empleador>
                <tipo_calle></tipo_calle>
                <nombre_calle></nombre_calle>
                <numero></numero>
                <resto_direccion></resto_direccion>
                <localidad></localidad>
                <comuna></comuna>
            </direccion_empleador>
            <ciiu_empleador></ciiu_empleador>
                <ciiu_texto></ciiu_texto>
            <n_trabajadores></n_trabajadores>
            <n_trabajadores_hombre></n_trabajadores_hombre>
            <n_trabajadores_mujer></n_trabajadores_mujer>
            <tipo_empresa></tipo_empresa>
            <ciiu2_empleador></ciiu2_empleador>
            <ciiu2_texto></ciiu2_texto>
            <propiedad_empresa></propiedad_empresa>
            <telefono_empleador>
                <cod_pais></cod_pais>
                <cod_area></cod_area>
                <numero></numero>
            </telefono_empleador>
	    <rut_representante_legal></rut_representante_legal>
            <nombre_representante_legal></nombre_representante_legal>
            <tasa_ds110></tasa_ds110>
            <tasa_ds67></tasa_ds67>
            <ultima_eval_ds67></ultima_eval_ds67>
            <nro_sucursales></nro_sucursales>
            <promedio_anual_trabajadores></promedio_anual_trabajadores>
        </empleador>
    </ZONA_B>";


                	//ZONA P
    				$zona_p = simplexml_load_string("<ZONA_P></ZONA_P>");                	
			        $accidente_fatal = $zona_p->addChild('accidente_fatal', '');
			        $accidente_fatal->addChild('fecha_accidente', '');
			        $accidente_fatal->addChild('hora_accidente', '');
			        
			        $direccion_accidente=$accidente_fatal->addChild('direccion_accidente', '');
			        $direccion_accidente->addChild('tipo_calle', '');
			        $direccion_accidente->addChild('nombre_calle', '');
			        $direccion_accidente->addChild('numero', '');
			        $direccion_accidente->addChild('resto_direccion', '');
			        $direccion_accidente->addChild('localidad', '');
			        $direccion_accidente->addChild('comuna', '');
			        
			        $gravedad=$accidente_fatal->addChild('gravedad', '');        
			        
			        $accidente_fatal->addChild('fecha_defuncion', '');
			        $accidente_fatal->addChild('lugar_defuncion', '');
			        $accidente_fatal->addChild('lugar_defuncion_otro', '');        
			        $accidente_fatal->addChild('descripcion_accidente_ini', '');
			        
			        $informante_oa=$accidente_fatal->addChild('informante_oa', '');
			        $informante_oa->addChild('apellido_paterno', '');
			        $informante_oa->addChild('apellido_materno', '');
			        $informante_oa->addChild('nombres', '');
			        $informante_oa->addChild('rut', '');
			        
			        $telefono_informante_oa=$accidente_fatal->addChild('telefono_informante_oa', '');
			        $telefono_informante_oa->addChild('cod_pais', '');
			        $telefono_informante_oa->addChild('cod_area', '');
			        $telefono_informante_oa->addChild('numero', '');
			        $accidente_fatal->addChild('correo_electronico_informante_oa', '');

			        

			        $xml_b = simplexml_load_string($zona_b);
			        //echo $accidente_fatal->saveXML();
			        //die();


		        	$xml_b->empleador->rut_empleador=strtoupper($ralf->ZONA_B->empleador->rut_empleador);
		        	$xml_b->empleador->nombre_empleador=$ralf->ZONA_B->empleador->nombre_empleador;

		        	$xml_b->empleador->direccion_empleador->tipo_calle=$ralf->ZONA_B->empleador->direccion_empleador->tipo_calle;
		        	if(!isset($ralf->ZONA_B->empleador->direccion_empleador->tipo_calle)) {
		        		unset($xml_b->empleador->direccion_empleador->tipo_calle);
		        	}
		        	
		        	$xml_b->empleador->direccion_empleador->nombre_calle=$ralf->ZONA_B->empleador->direccion_empleador->nombre_calle;
		        	if(!isset($ralf->ZONA_B->empleador->direccion_empleador->nombre_calle)) {
		        		unset($xml_b->empleador->direccion_empleador->nombre_calle);
		        	}

		        	$xml_b->empleador->direccion_empleador->numero=$ralf->ZONA_B->empleador->direccion_empleador->numero;
		        	if(!isset($ralf->ZONA_B->empleador->direccion_empleador->numero)){
		        		unset($xml_b->empleador->direccion_empleador->numero);
		        	}

		        	$xml_b->empleador->direccion_empleador->resto_direccion=$ralf->ZONA_B->empleador->direccion_empleador->resto_direccion;
		        	if(!isset($ralf->ZONA_B->empleador->direccion_empleador->resto_direccion)) {
		        		unset($xml_b->empleador->direccion_empleador->resto_direccion);
		        	}

		        	
		        	$xml_b->empleador->direccion_empleador->localidad=$ralf->ZONA_B->empleador->direccion_empleador->localidad;
		        	if(!isset($ralf->ZONA_B->empleador->direccion_empleador->localidad)) {
		        		unset($xml_b->empleador->direccion_empleador->localidad);
		        	}

					$xml_b->empleador->direccion_empleador->comuna=$ralf->ZONA_B->empleador->direccion_empleador->comuna;
					if(!isset($ralf->ZONA_B->empleador->direccion_empleador->comuna)) {
						unset($xml_b->empleador->direccion_empleador->comuna);
					}		        	

		        	
		        	$xml_b->empleador->ciiu_empleador=$ralf->ZONA_B->empleador->ciiu_empleador;
		        	$xml_b->empleador->ciiu_texto=$ralf->ZONA_B->empleador->ciiu_texto;
		        	$xml_b->empleador->n_trabajadores=$ralf->ZONA_B->empleador->n_trabajadores;
		        	$xml_b->empleador->n_trabajadores_hombre=$ralf->ZONA_B->empleador->n_trabajadores_hombre;
                    if(!isset($ralf->ZONA_B->empleador->n_trabajadores_hombre)) {
                        unset($xml_b->empleador->n_trabajadores_hombre);
                    }   
		        	$xml_b->empleador->n_trabajadores_mujer=$ralf->ZONA_B->empleador->n_trabajadores_mujer;
                    if(!isset($ralf->ZONA_B->empleador->n_trabajadores_mujer)) {
                        unset($xml_b->empleador->n_trabajadores_mujer);
                    }   
		        	$xml_b->empleador->tipo_empresa=$ralf->ZONA_B->empleador->tipo_empresa;
		        	$xml_b->empleador->ciiu2_empleador=$ralf->ZONA_B->empleador->ciiu2_empleador;
		        	$xml_b->empleador->ciiu2_texto=$ralf->ZONA_B->empleador->ciiu2_texto;
		        	$xml_b->empleador->propiedad_empresa=$ralf->ZONA_B->empleador->propiedad_empresa;


		        	$xml_b->empleador->telefono_empleador->cod_pais=$ralf->ZONA_B->empleador->telefono_empleador->cod_pais;
		        	if(!isset($ralf->ZONA_B->empleador->telefono_empleador->cod_pais)){
		        		unset($xml_b->empleador->telefono_empleador->cod_pais);
		        	}
		        	$xml_b->empleador->telefono_empleador->cod_area=$ralf->ZONA_B->empleador->telefono_empleador->cod_area;
		        	if(!isset($ralf->ZONA_B->empleador->telefono_empleador->cod_area)) {
		        		unset($xml_b->empleador->telefono_empleador->cod_area);
		        	}
		        	
		        	$xml_b->empleador->telefono_empleador->numero=$ralf->ZONA_B->empleador->telefono_empleador->numero;
		        	if(!isset($ralf->ZONA_B->empleador->telefono_empleador->numero)) {
		        		unset($xml_b->empleador->telefono_empleador);
		        	}		        	

                    if ($post['ciiu_empleador'] != '') {
                        $xml_b->empleador->ciiu_empleador=$post['ciiu_empleador'];
                        $st_ciiu = ORM::factory('St_Ciiu')->where('codigo','=',$post['ciiu_empleador'])->find();
                        if ($st_ciiu->loaded()) {
                            $st_ciiu_dato = $st_ciiu->nombre;
                            $nombre = explode(" - ", $st_ciiu_dato);
                            if (isset($nombre[1])) {
                                $xml_b->empleador->ciiu_texto = $nombre[1];
                            }
                        }
                    }

                    if ($post['ciiu2_empleador'] != '') {
                    	$st_ciiu = ORM::factory('St_Ciiu')->where('codigo','=',$post['ciiu2_empleador'])->find();                    	
                        if ($st_ciiu->loaded()) {
                            $st_ciiu_dato = $st_ciiu->nombre;
                            $nombre = explode(" - ", $st_ciiu_dato);
                            if (isset($nombre[1])) {
                                $ciiu2_texto_string=$nombre[1];                                
                            }else {
                            	$ciiu2_texto_string=$post['ciiu2_empleador'];
                            }
                        }
                        $xml_b->empleador->ciiu2_empleador=$post['ciiu2_empleador'];
                        $xml_b->empleador->ciiu2_texto = $ciiu2_texto_string;
                    }else {
                    	unset($xml_b->empleador->ciiu2_empleador);
                        unset($xml_b->empleador->ciiu2_texto);

                    }                 
                    $xml_b->empleador->direccion_empleador->comuna=$post['comuna_empleador'];

                    $xml_b->empleador->rut_representante_legal=strtoupper($post['rut_representante_legal']);                    
                    $xml_b->empleador->nombre_representante_legal=$post['nombre_representante_legal'];
                    $xml_b->empleador->tasa_ds110=$post['tasa_ds110'];
                    $xml_b->empleador->tasa_ds67=$post['tasa_ds67'];
                    $xml_b->empleador->ultima_eval_ds67=$post['ultima_eval_ds67'];
                    $xml_b->empleador->nro_sucursales=$post['nro_sucursales'];
                    $xml_b->empleador->promedio_anual_trabajadores=$post['promedio_anual_trabajadores'];


                    $ralf->ZONA_C->empleado->ciuo_trabajador=$post['ciuo_trabajador'];
                    $ralf->ZONA_C->empleado->trabajador->pais_nacionalidad=$post['pais_nacionalidad'];

                    $variable = preg_replace("/<ZONA_B.*ZONA_B>/ms", str_replace('<?xml version="1.0"?>', '', $xml_b->asXml()), $ralf->asXml());
                    #var_dump($variable); die();

                   $ralf = simplexml_load_string($variable);                      
                   
                  

                    //FIN ZONA B                                      
                    $accidente_fatal->fecha_accidente=$post['fecha_accidente'];

                    $accidente_fatal->hora_accidente=$post['hora_accidente_hr'].":".$post['hora_accidente_mm'].":".$post['hora_accidente_ss'];
                    $accidente_fatal->direccion_accidente->tipo_calle=(Int)$post['tipo_calle'];                    
                    $accidente_fatal->direccion_accidente->nombre_calle=$post['nombre_calle'];
                    $accidente_fatal->direccion_accidente->numero=$post['numero'];                    
                    
                    
                    $accidente_fatal->direccion_accidente->localidad=$post['localidad'];    
                    if(empty($post['localidad'])){
                        unset($accidente_fatal->direccion_accidente->localidad);
                    }

                    $accidente_fatal->direccion_accidente->resto_direccion=$post['resto_direccion'];
                    if(empty($post['resto_direccion'])){
                        unset($accidente_fatal->direccion_accidente->resto_direccion);
                    }                    
                    $accidente_fatal->direccion_accidente->comuna=$post['comuna'];

                    $gravedad=$accidente_fatal->gravedad;


                    unset($gravedad->criterio_gravedad);
                    foreach($criterios_array as $cri) {
                        $variable=htmlspecialchars($cri, ENT_QUOTES, 'UTF-8');
                        $gravedad->addChild('criterio_gravedad', $variable);
                    }
                    $accidente_fatal->fecha_defuncion=$post['fecha_defuncion'];
                	$accidente_fatal->lugar_defuncion=$post['lugar_defuncion'];                    
                    if(!isset($_POST["criterio_1"])) {
                        unset($accidente_fatal->fecha_defuncion);
                        unset($accidente_fatal->lugar_defuncion);
                        unset($accidente_fatal->lugar_defuncion_otro);
                    } else {
                        $accidente_fatal->lugar_defuncion_otro=$post['lugar_defuncion_otro'];                            
                        if($post['lugar_defuncion']!=4) {                       
                            unset($accidente_fatal->lugar_defuncion_otro);
                        }   
                    }
                    
                                                         
                    $accidente_fatal->descripcion_accidente_ini=$post['descripcion_accidente_ini'];
                    $accidente_fatal->informante_oa->apellido_paterno=$post['apellido_paterno'];
                    $accidente_fatal->informante_oa->apellido_materno=$post['apellido_materno'];
                    $accidente_fatal->informante_oa->nombres=$post['nombres'];
                    $accidente_fatal->informante_oa->rut=strtoupper($post['rut']); 

                    if(isset($post['cod_area']) && !empty($post['cod_area']) && isset($post['telefono_informante_oa']) && !empty($post['telefono_informante_oa'])) {                    	                    
                        $accidente_fatal->telefono_informante_oa->cod_pais=56;
                        $accidente_fatal->telefono_informante_oa->cod_area=$post['cod_area'];
                        $accidente_fatal->telefono_informante_oa->numero=$post['telefono_informante_oa'];
                    }else {                    	
                        unset($accidente_fatal->telefono_informante_oa);
                    }  

                    if(isset($post['correo_electronico_informante_oa']) && !empty($post['correo_electronico_informante_oa'])) {                    	
                        $accidente_fatal->correo_electronico_informante_oa=$post['correo_electronico_informante_oa'];    
                    }else {
                        unset($accidente_fatal->correo_electronico_informante_oa);
                    } 
                    

                    $variable = preg_replace("/<ZONA_P.*ZONA_P>/ms", str_replace('<?xml version="1.0"?>', '', $zona_p->asXml()), $ralf->asXml());
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
                    
                    $ralf = Documento::zona_o($ralf->saveXML());     

                    //var_dump($documento->TPXML_ID)
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf),$documento->TPXML_ID);                  

                    $valido=Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_Accidente.1.0.xsd');                                   
                    if($valido['estado']) {
                        $documentostring->XMLSTRING=$ralf_bd;
                        $documentostring->save();
                        $documento->VALIDO=1;
                        $documento->ESTADO=6;
                        $documento->save();   

                        $ralf1=ORM::factory('Ralf1')->where('xml_id','=',$xml_id)->find();
                        $ralf1->rut_representante_legal=strtoupper($post['rut_representante_legal']);
                        $ralf1->nombre_representante_legal=$post['nombre_representante_legal'];
                        $ralf1->tasa_ds110=$post['tasa_ds110'];
                        $ralf1->tasa_ds67=$post['tasa_ds67'];
                        $ralf1->ultima_eval_ds67=$post['ultima_eval_ds67'];
                        $ralf1->nro_sucursales=$post['nro_sucursales'];
                        $ralf1->promedio_anual_trabajadores=$post['promedio_anual_trabajadores'];
                        $ralf1->fecha_accidente=$post['fecha_accidente'];
                        $ralf1->hora_accidente=$post['hora_accidente_hr'].":".$post['hora_accidente_mm'].":".$post['hora_accidente_ss'];
                        $ralf1->tipo_calle=$post['tipo_calle'];
                        $ralf1->nombre_calle=$post['nombre_calle'];
                        $ralf1->numero=$post['numero'];
                        $ralf1->resto_direccion=$post['resto_direccion'];
                        $ralf1->localidad=$post['localidad'];
                        $ralf1->comuna=$post['comuna'];

                        $cri_s="";
                        foreach($criterios_array as $cri) {
                            $cri_s .="{$cri}-";
                            
                        }
                        $ralf1->criterio_gravedad=$cri_s;
                        $ralf1->fecha_defuncion=$post['fecha_defuncion'];
                        $ralf1->lugar_defuncion=$post['lugar_defuncion'];
                        $ralf1->lugar_defuncion_otro=$post['lugar_defuncion_otro'];

                        $ralf1->descripcion_accidente_ini=$post['descripcion_accidente_ini'];
                        $ralf1->apellido_paterno=$post['apellido_paterno'];
                        $ralf1->apellido_materno=$post['apellido_materno'];
                        $ralf1->nombres=$post['nombres'];
                        $ralf1->rut=strtoupper($post['rut']);
                        $ralf1->cod_area=$post['cod_area'];
                        $ralf1->telefono_informante_oa=$post['telefono_informante_oa'];
                        $ralf1->correo_electronico_informante_oa=$post['correo_electronico_informante_oa'];
                        $ralf1->xml_id=$xml_id;
                        $ralf1->save();
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
                if ($post['ciiu_empleador'] != '') {
                    $ralf->ZONA_B->empleador->ciiu_empleador=$post['ciiu_empleador'];
                    $st_ciiu = ORM::factory('St_Ciiu', $post['ciiu_empleador']);
                    if ($st_ciiu->loaded()) {
                        $st_ciiu_dato = $st_ciiu->nombre;
                        $nombre = explode(" - ", $st_ciiu_dato);
                        if (isset($nombre[1])) {
                            $ralf->ZONA_B->empleador->ciiu_texto = $nombre[1];
                        }
                    }
                }

                if ($post['ciiu2_empleador'] != '') {
                    $ralf->ZONA_B->empleador->ciiu2_empleador=$post['ciiu2_empleador'];
                    $st_ciiu = ORM::factory('St_Ciiu', $post['ciiu2_empleador']);
                    if ($st_ciiu->loaded()) {
                        $st_ciiu_dato = $st_ciiu->nombre;
                        $nombre = explode(" - ", $st_ciiu_dato);
                        if (isset($nombre[1])) {
                            $ralf->ZONA_B->empleador->ciiu2_texto = $nombre[1];
                        }
                    }
                }

                $criterios_array=array();
                if(isset($_POST["criterio_1"])) {                    
                    $criterios_array[]=1;
                }
                if(isset($_POST["criterio_2"])) {
                    $criterios_array[]=2;
                }
                if(isset($_POST["criterio_3"])) {
                    $criterios_array[]=3;
                }
                if(isset($_POST["criterio_4"])) {
                    $criterios_array[]=4;
                }
                if(isset($_POST["criterio_5"])) {
                    $criterios_array[]=5;
                }
                if(isset($_POST["criterio_6"])) {
                    $criterios_array[]=6;
                }
                if(isset($_POST["criterio_7"])) {
                    $criterios_array[]=7;
                }
                if(isset($_POST["criterio_8"])) {
                    $criterios_array[]=8;
                }

                $ralf->ZONA_B->empleador->direccion_empleador->comuna=$post['comuna_empleador'];

                $ralf->ZONA_B->empleador->rut_representante_legal=strtoupper($post['rut_representante_legal']);  

                $ralf->ZONA_B->empleador->nombre_representante_legal=$post['nombre_representante_legal'];
                $ralf->ZONA_B->empleador->tasa_ds110=$post['tasa_ds110'];
                $ralf->ZONA_B->empleador->tasa_ds67=$post['tasa_ds67'];
                $ralf->ZONA_B->empleador->ultima_eval_ds67=$post['ultima_eval_ds67'];
                $ralf->ZONA_B->empleador->nro_sucursales=$post['nro_sucursales'];
                $ralf->ZONA_B->empleador->promedio_anual_trabajadores=$post['promedio_anual_trabajadores'];

                $ralf->ZONA_C->empleado->ciuo_trabajador=$post['ciuo_trabajador'];
                $ralf->ZONA_C->empleado->trabajador->pais_nacionalidad=$post['pais_nacionalidad'];
           
                $ralf->ZONA_P->accidente_fatal->fecha_accidente=$post['fecha_accidente'];                
                $ralf->ZONA_P->accidente_fatal->hora_accidente=$post['hora_accidente_hr'].":".$post['hora_accidente_mm'].":".$post['hora_accidente_ss'];
                $ralf->ZONA_P->accidente_fatal->direccion_accidente->tipo_calle=(Int)$post['tipo_calle'];                    
                $ralf->ZONA_P->accidente_fatal->direccion_accidente->nombre_calle=$post['nombre_calle'];
                $ralf->ZONA_P->accidente_fatal->direccion_accidente->numero=$post['numero'];
                $ralf->ZONA_P->accidente_fatal->direccion_accidente->resto_direccion=$post['resto_direccion'];
                $ralf->ZONA_P->accidente_fatal->direccion_accidente->localidad=$post['localidad'];

                $ralf->ZONA_P->accidente_fatal->direccion_accidente->comuna=$post['comuna'];
                $gravedad=$ralf->ZONA_P->accidente_fatal->gravedad;
                unset($gravedad->criterio_gravedad);
                foreach($criterios_array as $cri) {
                    $variable=htmlspecialchars($cri, ENT_QUOTES, 'UTF-8');      
                    $gravedad->addChild('criterio_gravedad', $variable);
                }
                $ralf->ZONA_P->accidente_fatal->fecha_defuncion=$post['fecha_defuncion'];
                $ralf->ZONA_P->accidente_fatal->lugar_defuncion=$post['lugar_defuncion'];
                $ralf->ZONA_P->accidente_fatal->lugar_defuncion_otro=$post['lugar_defuncion_otro'];

                $ralf->ZONA_P->accidente_fatal->descripcion_accidente_ini=$post['descripcion_accidente_ini'];
                $ralf->ZONA_P->accidente_fatal->informante_oa->apellido_paterno=$post['apellido_paterno'];
                $ralf->ZONA_P->accidente_fatal->informante_oa->apellido_materno=$post['apellido_materno'];
                $ralf->ZONA_P->accidente_fatal->informante_oa->nombres=$post['nombres'];
                $ralf->ZONA_P->accidente_fatal->informante_oa->rut=strtoupper($post['rut']);                    
                $ralf->ZONA_P->accidente_fatal->telefono_informante_oa->cod_pais=56;
                $ralf->ZONA_P->accidente_fatal->telefono_informante_oa->cod_area=$post['cod_area'];
                $ralf->ZONA_P->accidente_fatal->telefono_informante_oa->numero=$post['telefono_informante_oa'];
                $ralf->ZONA_P->accidente_fatal->correo_electronico_informante_oa=$post['correo_electronico_informante_oa'];


                $documentostring->XMLSTRING=$ralf->saveXML();
                $documentostring->save();


                $this->redirect("caso/ver_caso/{$documento->CASO_ID}");                
            }
        }
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $ralf; 
        $data['criterio_gravedad'] = $organismo=Kohana::$config->load('dominios.STCriterio_gravedad_RALF');        
        $this->template->mensaje_error=$mensaje_error;
        $this->template->contenido = $this->template->contenido = View::factory('ralf/crear')                 
                ->set('data', $data)
                ->set('back_page', URL::site("caso/ver_caso/{$documento->CASO_ID}", 'http'))
                ->set('errors',$errors)
                ->set('default',  $this->values_default($ralf,$_POST))
                ->set('errores_esquema',$errores_esquema)
                ->set('xml_id',$xml_id)                
                        
            ;
        
    }
    
    public function values_default($ralf,$post) {
        
        if(empty($post)) {          
            $default['ciiu_empleador']=$ralf->ZONA_B->empleador->ciiu_empleador;
            $default['ciiu2_empleador']=$ralf->ZONA_B->empleador->ciiu2_empleador;
            $default['comuna_empleador']=$ralf->ZONA_B->empleador->direccion_empleador->comuna;

            $default['rut_representante_legal']=strtoupper($ralf->ZONA_B->empleador->rut_representante_legal);
            $default['nombre_representante_legal']=$ralf->ZONA_B->empleador->nombre_representante_legal;
            $default['tasa_ds110']=$ralf->ZONA_B->empleador->tasa_ds110;
            $default['tasa_ds67']=$ralf->ZONA_B->empleador->tasa_ds67;
            $default['ultima_eval_ds67']=$ralf->ZONA_B->empleador->ultima_eval_ds67;
            $default['nro_sucursales']=$ralf->ZONA_B->empleador->nro_sucursales;
            $default['promedio_anual_trabajadores']=$ralf->ZONA_B->empleador->promedio_anual_trabajadores;

            $default['ciuo_trabajador'] = $ralf->ZONA_C->empleado->ciuo_trabajador;
            $default['pais_nacionalidad'] = $ralf->ZONA_C->empleado->trabajador->pais_nacionalidad;

            $default['fecha_accidente']=$ralf->ZONA_P->accidente_fatal->fecha_accidente;
            $default['hora_accidente']=$ralf->ZONA_P->accidente_fatal->hora_accidente;
            $default['tipo_calle']=$ralf->ZONA_P->accidente_fatal->direccion_accidente->tipo_calle;
            $default['nombre_calle']=$ralf->ZONA_P->accidente_fatal->direccion_accidente->nombre_calle;
            $default['numero']=$ralf->ZONA_P->accidente_fatal->direccion_accidente->numero;
            $default['resto_direccion']=$ralf->ZONA_P->accidente_fatal->direccion_accidente->resto_direccion;
            $default['localidad']=$ralf->ZONA_P->accidente_fatal->direccion_accidente->localidad;

            $default['comuna']=$ralf->ZONA_P->accidente_fatal->direccion_accidente->comuna;
            
            $default['fecha_defuncion']=$ralf->ZONA_P->accidente_fatal->fecha_defuncion;
            $default['lugar_defuncion']=$ralf->ZONA_P->accidente_fatal->lugar_defuncion;
            $default['lugar_defuncion_otro']=$ralf->ZONA_P->accidente_fatal->lugar_defuncion_otro;
        
            $default['descripcion_accidente_ini']=$ralf->ZONA_P->accidente_fatal->descripcion_accidente_ini;
            $default['apellido_paterno']=$ralf->ZONA_P->accidente_fatal->informante_oa->apellido_paterno;
            $default['apellido_materno']=$ralf->ZONA_P->accidente_fatal->informante_oa->apellido_materno;
            $default['nombres']=$ralf->ZONA_P->accidente_fatal->informante_oa->nombres;
            $default['rut']=strtoupper($ralf->ZONA_P->accidente_fatal->informante_oa->rut);            
            $default['cod_area']=$ralf->ZONA_P->accidente_fatal->telefono_informante_oa->cod_area;
            $default['telefono_informante_oa']=$ralf->ZONA_P->accidente_fatal->telefono_informante_oa->numero;
            $default['correo_electronico_informante_oa']=$ralf->ZONA_P->accidente_fatal->correo_electronico_informante_oa;            
        } else {
            $default['ciiu_empleador']=$post['ciiu_empleador'];
            $default['ciiu2_empleador']=$post['ciiu2_empleador'];
            $default['comuna_empleador']=$post['comuna_empleador'];

            $default['rut_representante_legal']=strtoupper($post['rut_representante_legal']);                    
            $default['nombre_representante_legal']=$post['nombre_representante_legal'];
            $default['tasa_ds110']=$post['tasa_ds110'];
            $default['tasa_ds67']=$post['tasa_ds67'];
            $default['ultima_eval_ds67']=$post['ultima_eval_ds67'];
            $default['nro_sucursales']=$post['nro_sucursales'];
            $default['promedio_anual_trabajadores']=$post['promedio_anual_trabajadores'];

            $default['ciuo_trabajador'] = $post['ciuo_trabajador'];
            $default['pais_nacionalidad'] = $post['pais_nacionalidad'];

            $default['fecha_accidente']=$post['fecha_accidente'];
            $default['hora_accidente']=$post['hora_accidente_hr'].":".$post['hora_accidente_mm'].":".$post['hora_accidente_ss'];
            $default['tipo_calle']=$post['tipo_calle'];
            $default['nombre_calle']=$post['nombre_calle'];
            $default['numero']=$post['numero'];
            $default['resto_direccion']=$post['resto_direccion'];       
            $default['localidad']=$post['localidad'];       
            $default['comuna']=$post['comuna'];            
            $default['fecha_defuncion']=$post['fecha_defuncion'];
            $default['lugar_defuncion']=$post['lugar_defuncion'];
            $default['descripcion_accidente_ini']=$post['descripcion_accidente_ini'];
            $default['lugar_defuncion']=$post['lugar_defuncion'];

            $default['lugar_defuncion_otro']=$post['lugar_defuncion_otro'];


            $default['descripcion_accidente_ini']=$post['descripcion_accidente_ini'];
            $default['nombres']=$post['nombres'];
            $default['apellido_paterno']=$post['apellido_paterno'];
            $default['apellido_materno']=$post['apellido_materno'];
            $default['rut']=strtoupper($post['rut']);
            $default['correo_electronico_informante_oa']=$post['correo_electronico_informante_oa'];
            $default['cod_area']=$post['cod_area'];
            $default['telefono_informante_oa']=$post['telefono_informante_oa'];
        }        
        return $default;        
    }
}