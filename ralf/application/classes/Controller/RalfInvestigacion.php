<?php defined('SYSPATH') or die('No direct script access.');

class Controller_RalfInvestigacion extends Controller_Website{
        
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
        //Busco un documento ralf medidas
        $documento=$caso->xmls
                ->where('TPXML_ID','IN', array(142))
                ->where('ESTADO','IN',array(1,2))->order_by('FECHA_CREACION', 'DESC')->find();
        //echo Database::instance()->last_query;  die();      
        if(!$documento->loaded()){
            $this->template->mensaje_error='Se debe agregar una RALF Medidas.';
            return;
        }
        $ralf_anterior=$caso->xmls->where('TPXML_ID','=', 143)->where('ESTADO','!=', 3)->find();
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
                $dom->firstChild
            );
        }
           
        if(isset($xml_documento->ZONA_INMEDIATAS)) {
            unset($xml_documento->ZONA_INMEDIATAS);
        }

        if(isset($xml_documento->ZONA_O)) {
            unset($xml_documento->ZONA_O);
        }

        // Cambiar nombre de documento
        $documento_preparacion=dom_import_simplexml($xml_documento);
        Documento::clonishNode($documento_preparacion, 'RALF_Investigacion');
        $ralf_preparacion = simplexml_load_string($documento_preparacion->ownerDocument->saveXML());  
    
        $zona_investigacion_string="<ZONA_INVESTIGACION> 
    <investigacion_acc> 
        <fecha_inicio_investigacion_acc></fecha_inicio_investigacion_acc> 
        <fecha_termino_investigacion_acc></fecha_termino_investigacion_acc> 
        <hora_ingreso></hora_ingreso> 
        <hora_salida></hora_salida> 
        <jornada_momento_accidente></jornada_momento_accidente> 
        <jornada_momento_accidente_otro></jornada_momento_accidente_otro> 
        <trabajo_habitual_cual></trabajo_habitual_cual> 
        <trabajo_habitual></trabajo_habitual> 
        <antiguedad> 
            <annos></annos> 
            <meses></meses> 
            <dias></dias> 
        </antiguedad> 
        <lugar_trabajo></lugar_trabajo> 
        <nro_comites_funcio></nro_comites_funcio> 
        <nro_comites_ds54_a1></nro_comites_ds54_a1> 
        <exist_comites_lugar_acc></exist_comites_lugar_acc> 
        <cumb_ob_info_ds40_a21></cumb_ob_info_ds40_a21> 
        <reg_ohys_al_dia></reg_ohys_al_dia> 
        <depto_pre_rie_teorico></depto_pre_rie_teorico> 
        <depto_pre_rie_real></depto_pre_rie_real> 
        <exp_pre_em> 
            <apellido_paterno></apellido_paterno> 
            <apellido_materno></apellido_materno> 
            <nombres></nombres> 
            <rut></rut> 
        </exp_pre_em> 
        <tipo_cont_exp_pre_em></tipo_cont_exp_pre_em> 
        <tipo_cont_exp_pre_em_otro></tipo_cont_exp_pre_em_otro> 
        <nro_dias_jor_parcial_cont_exp_pre_emp></nro_dias_jor_parcial_cont_exp_pre_emp> 
        <nro_reg_a_s_exp_pre_em></nro_reg_a_s_exp_pre_em> 
        <cat_exp_pre_em></cat_exp_pre_em> 
        <programa_pre_rie></programa_pre_rie> 
        <trabajador_reg_subcontratacion></trabajador_reg_subcontratacion> 
        <registro_ac_antec_a66bis></registro_ac_antec_a66bis> 
        <comite_par_fae_emp_ppal></comite_par_fae_emp_ppal> 
        <depto_pre_rie_emp_ppal></depto_pre_rie_emp_ppal> 
        <imp_sist_gest_sst_emp_ppal></imp_sist_gest_sst_emp_ppal> 
        <fiscalizacion_con_multas_mat_sst></fiscalizacion_con_multas_mat_sst> 
        <organismo_multas></organismo_multas> 
        <circunstancias_accidente></circunstancias_accidente>
        <desc_acc_invest></desc_acc_invest> 
        <justificacion_no_laboral></justificacion_no_laboral>
        <vehiculo_involucrado></vehiculo_involucrado> 
        <codificacion_vehiculo_involucrado></codificacion_vehiculo_involucrado> 
        <antecedentes_informacion_acc></antecedentes_informacion_acc> 
        <investigador_acc> 
            <apellido_paterno></apellido_paterno> 
            <apellido_materno></apellido_materno> 
            <nombres></nombres> 
            <rut></rut> 
        </investigador_acc> 
        <prof_invest_acc></prof_invest_acc> 
        <invest_es_experto></invest_es_experto> 
        <categoria_experto></categoria_experto> 
        <nro_reg_a_s_invest_acc></nro_reg_a_s_invest_acc> 
        <documentos_acompanan_investigacion></documentos_acompanan_investigacion>
    </investigacion_acc> 
</ZONA_INVESTIGACION></RALF_Investigacion>";
        
        
        $ralf =str_replace('</RALF_Investigacion>',$zona_investigacion_string,$ralf_preparacion->saveXML());                        
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
        $xml_insert->TPXML_ID=143;
        $xml_insert->VALIDO=0;  
        //Nuevos documentos
        $xml_insert->XML_ID_ORIGEN=$documento->XML_ID;      
        $xml_insert->save();
        
        $doc=simplexml_load_string($xmlstring->XMLSTRING);        
        $doc->ZONA_A->documento->folio=$xml_insert->XML_ID;                
        $xmlstring->XMLSTRING=$doc->saveXML();
        $xmlstring->save();              
        $this->redirect("ralfInvestigacion/crear/$xml_insert->XML_ID");                
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
        //var_dump($documentostring);
        $ralf=simplexml_load_string($documentostring->XMLSTRING);                
               
        $errores_esquema=NULL;
        $errors = array();
        $mensaje_error = null;             
        if (isset($_POST) AND Valid::not_empty($_POST)) {            
            if(isset ($_POST['boton_finalizar'])) {                       
                $post = Validation::factory($_POST)
                        ->rule('fecha_inicio_investigacion_acc', 'date')
                        ->rule('fecha_inicio_investigacion_acc','Utiles::validateDate',array(':value')) 
                        ->rule('fecha_inicio_investigacion_acc', 'not_empty')->label('fecha_inicio_investigacion_acc', 'Fecha inicio')
                        ->rule('fecha_termino_investigacion_acc', 'date')
                        ->rule('fecha_termino_investigacion_acc','Utiles::validateDate',array(':value')) 
                        ->rule('fecha_termino_investigacion_acc', 'not_empty')->label('fecha_termino_investigacion_acc', 'Fecha termino')                                            
                        ->rule('jornada_momento_accidente','Utiles::whitespace',array(':value'))
                        ->rule('jornada_momento_accidente', 'not_empty')->label('jornada_momento_accidente', 'Jornada al momento acc.')
                        
                        ->rule('trabajo_habitual_cual', 'not_empty')
                        ->rule('trabajo_habitual_cual','Utiles::whitespace',array(':value'))
                        ->label('trabajo_habitual_cual', 'Trabajo habitual cual')
                        ->rule('trabajo_habitual','Utiles::whitespace',array(':value'))
                        ->rule('trabajo_habitual', 'not_empty')->label('trabajo_habitual', 'Trabajo habitual')
                        ->rule('antiguedad_annos','Utiles::whitespace',array(':value'))
                        ->rule('antiguedad_annos','Utiles::nonNegativeInteger',array(':value'))    
                        ->rule('antiguedad_annos', 'not_empty')
                        ->rule('antiguedad_annos', 'numeric')
                        ->label('antiguedad_annos', 'Años antiguedad')
                        ->rule('antiguedad_meses','Utiles::whitespace',array(':value'))
                        ->rule('antiguedad_meses','Utiles::nonNegativeInteger',array(':value'))    
                        ->rule('antiguedad_meses', 'not_empty')
                        ->rule('antiguedad_meses', 'numeric')
                        ->label('antiguedad_meses', 'Meses antiguedad')
                        ->rule('antiguedad_dias','Utiles::whitespace',array(':value'))
                        ->rule('antiguedad_dias','Utiles::nonNegativeInteger',array(':value'))    
                        ->rule('antiguedad_dias', 'not_empty')
                        ->rule('antiguedad_dias', 'numeric')
                        ->label('antiguedad_dias', 'días antiguedad')
                        ->rule('lugar_trabajo','Utiles::whitespace',array(':value'))
                        ->rule('lugar_trabajo', 'not_empty')->label('lugar_trabajo', 'Lugar trabajo')
                        
                        ->rule('nro_comites_funcio','Utiles::whitespace',array(':value'))
                        ->rule('nro_comites_funcio','Utiles::nonNegativeInteger',array(':value')) 
                        ->rule('nro_comites_funcio', 'not_empty')
                        ->rule('nro_comites_funcio', 'numeric')
                        ->label('nro_comites_funcio', 'Nº comites')
                        ->rule('nro_comites_ds54_a1','Utiles::whitespace',array(':value'))
                        ->rule('nro_comites_ds54_a1','Utiles::nonNegativeInteger',array(':value')) 
                        ->rule('nro_comites_ds54_a1', 'not_empty')
                        ->rule('nro_comites_ds54_a1', 'numeric')
                        ->label('nro_comites_ds54_a1', 'Nº comites ds54 a1')
                        ->rule('exist_comites_lugar_acc','Utiles::whitespace',array(':value'))
                        ->rule('exist_comites_lugar_acc', 'not_empty')->label('exist_comites_lugar_acc', 'exist comites lugar acc')
                        ->rule('cumb_ob_info_ds40_a21','Utiles::whitespace',array(':value'))
                        ->rule('cumb_ob_info_ds40_a21', 'not_empty')->label('cumb_ob_info_ds40_a21', 'Cump ob info ds40 a21')
                        ->rule('reg_ohys_al_dia','Utiles::whitespace',array(':value'))
                        ->rule('reg_ohys_al_dia', 'not_empty')->label('reg_ohys_al_dia', 'reg ohys al dia')
                        ->rule('depto_pre_rie_teorico','Utiles::whitespace',array(':value'))
                        ->rule('depto_pre_rie_teorico', 'not_empty')->label('depto_pre_rie_teorico', 'depto pre rie teorico')
                        
                        
                        ->rule('programa_pre_rie','Utiles::whitespace',array(':value'))
                        ->rule('programa_pre_rie', 'not_empty')->label('programa_pre_rie', 'programa pre rie')
                        ->rule('trabajador_reg_subcontratacion','Utiles::whitespace',array(':value'))
                        ->rule('trabajador_reg_subcontratacion', 'not_empty')->label('trabajador_reg_subcontratacion', 'trabajador reg subcontratacion')
                        
                        ->rule('comite_par_fae_emp_ppal','Utiles::whitespace',array(':value'))
                        ->rule('comite_par_fae_emp_ppal', 'not_empty')->label('comite_par_fae_emp_ppal', 'comite par fae emp ppal')
                        ->rule('depto_pre_rie_emp_ppal','Utiles::whitespace',array(':value'))
                        ->rule('depto_pre_rie_emp_ppal', 'not_empty')->label('depto_pre_rie_emp_ppal', 'depto pre rie emp ppal')
                        ->rule('imp_sist_gest_sst_emp_ppal','Utiles::whitespace',array(':value'))
                        ->rule('imp_sist_gest_sst_emp_ppal', 'not_empty')->label('imp_sist_gest_sst_emp_ppal', 'imp sist gest sst emp ppal')
                        ->rule('fiscalizacion_con_multas_mat_sst','Utiles::whitespace',array(':value'))
                        ->rule('fiscalizacion_con_multas_mat_sst', 'not_empty')->label('fiscalizacion_con_multas_mat_sst', 'fiscalizacion con multas mat sst')
                        
                        ->rule('desc_acc_invest','Utiles::whitespace',array(':value'))
                        ->rule('desc_acc_invest', 'not_empty')->label('desc_acc_invest', 'desc_acc_invest')

                        ->rule('vehiculo_involucrado','Utiles::whitespace',array(':value'))
                        ->rule('vehiculo_involucrado', 'not_empty')->label('vehiculo_involucrado', 'Vehículo Involucrado')                           
                    
                        ->rule('antecedentes_informacion_acc','Utiles::whitespace',array(':value'))
                        ->rule('antecedentes_informacion_acc', 'not_empty')->label('antecedentes_informacion_acc', 'Antecedentes info. acc')
                        
                        ->rule('investigador_acc_apellido_paterno','Utiles::whitespace',array(':value'))
                        ->rule('investigador_acc_apellido_paterno', 'not_empty')->label('investigador_acc_apellido_paterno', 'Ap. paterno')
                        
                        ->rule('investigador_acc_apellido_materno','Utiles::whitespace',array(':value'))
                        ->rule('investigador_acc_apellido_materno', 'not_empty')->label('investigador_acc_apellido_materno', 'Ap materno')
                        
                        ->rule('investigador_acc_nombres','Utiles::whitespace',array(':value'))
                        ->rule('investigador_acc_nombres', 'not_empty')->label('investigador_acc_nombres', 'Nombres')
                        
                        ->rule('investigador_acc_rut','Utiles::whitespace',array(':value'))
                        ->rule('investigador_acc_rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))
                        ->rule('investigador_acc_rut', 'not_empty')
                        ->rule('investigador_acc_rut','not_empty')->rule('investigador_acc_rut','Utiles::rut',array(':value'))
                        ->label('investigador_acc_rut', 'Rut')
                        ->rule('prof_invest_acc','Utiles::whitespace',array(':value'))
                        ->rule('prof_invest_acc', 'not_empty')->label('prof_invest_acc', 'Prof invest acc')
                        ->rule('invest_es_experto','Utiles::whitespace',array(':value'))
                        ->rule('invest_es_experto', 'not_empty')->label('invest_es_experto', 'Invest es experto')
                        ->rule('origen_comun', 'not_empty')->label('origen_comun', 'Origen Común')        
                        ->rule('justificacion_no_laboral', 'not_empty')->label('justificacion_no_laboral', 'Justificación Origen del Accidente')
                        ->rule('circunstancias_accidente', 'not_empty')->label('circunstancias_accidente', 'Circunstancia o contexto en el cual ocurrió el accidente')
                        ;

                        if($post["vehiculo_involucrado"] == 1) { 
                            $post=$post->rule('codigo_modo_transporte', 'not_empty')->label('codigo_modo_transporte', 'Cod. modo transporte') 
                            ->rule('codigo_papel_lesionado', 'not_empty')->label('codigo_papel_lesionado', 'Cod. papel lesionado') 
                            ->rule('codigo_contraparte', 'not_empty')->label('codigo_contraparte', 'Cod. contraparte') 
                            ->rule('codigo_tipo_evento', 'not_empty')->label('codigo_tipo_evento', 'Cod. tipo evento');
 
                            $hay_vehiculo_involucrado=true;
 
                        }else { 
                            $hay_vehiculo_involucrado=false;
                        }

                        if ($post["jornada_momento_accidente"] == 4) {
                            $post = $post->rule('jornada_momento_accidente_otro', 'not_empty')
                            ->rule('jornada_momento_accidente_otro','Utiles::whitespace',array(':value'))
                            ->label('jornada_momento_accidente_otro', 'Jornada otro');
                        }

                        if ($post["depto_pre_rie_teorico"] == 1) {
                            $post = $post->rule('depto_pre_rie_real','Utiles::whitespace',array(':value'))
                                ->rule('depto_pre_rie_real', 'not_empty')->label('depto_pre_rie_real', 'depto pre rie real');
                        }

                        if ($post["depto_pre_rie_real"] == 1) {
                            $post = $post->rule('exp_pre_em_apellido_paterno','Utiles::whitespace',array(':value'))
                                        ->rule('exp_pre_em_apellido_paterno', 'not_empty')->label('exp_pre_em_apellido_paterno', 'Ap. Paterno')
                                        ->rule('exp_pre_em_apellido_materno','Utiles::whitespace',array(':value'))
                                        ->rule('exp_pre_em_apellido_materno', 'not_empty')->label('exp_pre_em_apellido_materno', 'Ap. Materno')
                                        ->rule('exp_pre_em_nombres','Utiles::whitespace',array(':value'))
                                        ->rule('exp_pre_em_nombres', 'not_empty')->label('exp_pre_em_nombres', 'Nombres')
                                        ->rule('exp_pre_em_rut','Utiles::whitespace',array(':value'))
                                        ->rule('exp_pre_em_rut', 'regex', array(':value', '/^([0-9])+\-([kK0-9])+$/'))     
                                        ->rule('exp_pre_em_rut','not_empty')->rule('exp_pre_em_rut','Utiles::rut',array(':value'))
                                        ->rule('exp_pre_em_rut', 'not_empty')->label('exp_pre_em_rut', 'Rut')
                                        ->rule('tipo_cont_exp_pre_em', 'not_empty')->label('tipo_cont_exp_pre_em', 'tipo cont exp pre em')
                                        ->rule('nro_reg_a_s_exp_pre_em','Utiles::whitespace',array(':value'))
                                        ->rule('nro_reg_a_s_exp_pre_em','Utiles::nonNegativeInteger',array(':value')) 
                                        ->rule('nro_reg_a_s_exp_pre_em', 'not_empty')
                                        ->rule('nro_reg_a_s_exp_pre_em', 'numeric')
                                        ->label('nro_reg_a_s_exp_pre_em', 'nro reg a s exp pre em')
                                        ->rule('cat_exp_pre_em','Utiles::whitespace',array(':value'))
                                        ->rule('cat_exp_pre_em', 'not_empty')->label('cat_exp_pre_em', 'cat exp pre em');
                        }

                        if ($post['tipo_cont_exp_pre_em'] == 7) {
                            $post = $post->rule('tipo_cont_exp_pre_em_otro', 'not_empty')->label('tipo_cont_exp_pre_em_otro', 'Tipo contrato otro');
                        }

                        if (in_array($post['tipo_cont_exp_pre_em'], array(1,3,5))) {
                            $post = $post
                                    ->rule('nro_dias_jor_parcial_cont_exp_pre_emp','Utiles::whitespace',array(':value'))
                                    ->rule('nro_dias_jor_parcial_cont_exp_pre_emp', 'not_empty')->label('nro_dias_jor_parcial_cont_exp_pre_emp', 'Nº días');
                        }

                        if ($post['trabajador_reg_subcontratacion'] == 1) {
                            $post = $post
                            ->rule('registro_ac_antec_a66bis','Utiles::whitespace',array(':value'))
                            ->rule('registro_ac_antec_a66bis', 'not_empty')->label('registro_ac_antec_a66bis', 'Registro ac antec a66bis');
                        }

                        if ($post['fiscalizacion_con_multas_mat_sst'] == 1) {
                            $post = $post
                            ->rule('organismo_multas','Utiles::whitespace',array(':value'))
                            ->rule('organismo_multas', 'not_empty')->label('organismo_multas', 'Organismo multas');
                        }

                        if ($post['invest_es_experto'] == 1) {
                            $post = $post->rule('categoria_experto', 'not_empty')->label('categoria_experto', 'Categoria experto')
                            ->rule('nro_reg_a_s_invest_acc','Utiles::whitespace',array(':value'))
                            ->rule('nro_reg_a_s_invest_acc','Utiles::nonNegativeInteger',array(':value')) 
                            ->rule('nro_reg_a_s_invest_acc', 'not_empty')
                            ->rule('nro_reg_a_s_invest_acc', 'numeric')
                            ->label('nro_reg_a_s_invest_acc', 'Nº reg a s invest acc');
                        }
                
                $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();
                if(count($anexos)==0) {
                    $post=$post->rule('documentos_anexos', 'not_empty')->label('documentos_anexos', 'Documentos Anexos');                                    
                }
                
                /*$anexos_causas=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos_causas')->find_all();
                if(count($anexos_causas)==0) {
                    $post=$post->rule('documentos_anexos_causas', 'not_empty')->label('documentos_anexos_causas', 'Documentos Anexos Causas');
                }*/

                if(!empty($_POST["fecha_inicio_investigacion_acc"])) {
                    if(!($_POST["fecha_inicio_investigacion_acc"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                        $errors = $errors+array("fecha_inicio_investigacion_acc"=>"Fecha debe ser Mayor o igual a fecha de accidente");
                    }
                }

                if(!empty($_POST["fecha_termino_investigacion_acc"])) {
                    if(!empty($_POST["fecha_termino_investigacion_acc"])) {
                        if(!($_POST["fecha_termino_investigacion_acc"]>=$_POST["fecha_inicio_investigacion_acc"])) {
                            $errors = $errors+array("fecha_termino_investigacion_acc"=>"Fecha debe ser Mayor o igual a fecha de inicio");
                        }                    
                    }
                }    

                if(!empty($_POST["causa_medida_plazo_plazo"])) {
                    if(!($_POST["causa_medida_plazo_plazo"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                        $errors = $errors+array("causa_medida_plazo_plazo"=>"Fecha plazo debe ser Mayor o igual a fecha de accidente");
                    }
                }

                /// TO DO 
                //NUEVO CAMPO ORIGEN COMUN
                //Se cargan los datos del documento       
                $xml_documento = simplexml_load_string($documento->xmlstring->XMLSTRING);  
                $caso_id= $xml_documento->ZONA_A->documento->codigo_caso;
                $casoEncontrado = ORM::factory('Caso')->where('CASO_ID', '=', $caso_id)->find();
                $casoEncontrado->ORIGEN_COMUN = $_POST["origen_comun"];
                if ($casoEncontrado->loaded()) {
                    $casoEncontrado->save();
                }
                /*if(!empty($_POST["fecha_notificacion_me_correc"])) {
                    if(!($_POST["fecha_notificacion_me_correc"]>=$ralf->ZONA_P->accidente_fatal->fecha_accidente)) {
                        $errors = $errors+array("fecha_notificacion_me_correc"=>"Fecha plazo debe ser mayor o igual a fecha de accidente");
                    }
                } */      

                //causas y medidas correctivas     
                /*$cmcs=ORM::factory('Causa_Medida_Correctiva')->where('xml_id','=',$xml_id)->find_all();
                if(count($cmcs)==0) {
                    $post=$post->rule('causas_medidas_correctivas', 'not_empty')->label('causas_medidas_correctivas', 'Causas y Medidas Correctivas');                                    
                }*/
                
                if($post->check() && count($errors)==0) {

                    $zona_investigacion= 
                "<ZONA_INVESTIGACION> 
                    <investigacion_acc> 
                        <fecha_inicio_investigacion_acc></fecha_inicio_investigacion_acc> 
                        <fecha_termino_investigacion_acc></fecha_termino_investigacion_acc> 
                        <hora_ingreso></hora_ingreso> 
                        <hora_salida></hora_salida> 
                        <jornada_momento_accidente></jornada_momento_accidente> 
                        <jornada_momento_accidente_otro></jornada_momento_accidente_otro> 
                        <trabajo_habitual_cual></trabajo_habitual_cual> 
                        <trabajo_habitual></trabajo_habitual> 
                        <antiguedad>
                            <annos></annos> 
                            <meses></meses> 
                            <dias></dias> 
                        </antiguedad> 
                        <lugar_trabajo></lugar_trabajo> 
                        <nro_comites_funcio></nro_comites_funcio> 
                        <nro_comites_ds54_a1></nro_comites_ds54_a1> 
                        <exist_comites_lugar_acc></exist_comites_lugar_acc> 
                        <cumb_ob_info_ds40_a21></cumb_ob_info_ds40_a21> 
                        <reg_ohys_al_dia></reg_ohys_al_dia> 
                        <depto_pre_rie_teorico></depto_pre_rie_teorico> 
                        <depto_pre_rie_real></depto_pre_rie_real> 
                        <exp_pre_em> 
                            <apellido_paterno></apellido_paterno> 
                            <apellido_materno></apellido_materno> 
                            <nombres></nombres> 
                            <rut></rut> 
                        </exp_pre_em> 
                        <tipo_cont_exp_pre_em></tipo_cont_exp_pre_em> 
                        <tipo_cont_exp_pre_em_otro></tipo_cont_exp_pre_em_otro> 
                        <nro_dias_jor_parcial_cont_exp_pre_emp></nro_dias_jor_parcial_cont_exp_pre_emp> 
                        <nro_reg_a_s_exp_pre_em></nro_reg_a_s_exp_pre_em> 
                        <cat_exp_pre_em></cat_exp_pre_em> 
                        <programa_pre_rie></programa_pre_rie> 
                        <trabajador_reg_subcontratacion></trabajador_reg_subcontratacion> 
                        <registro_ac_antec_a66bis></registro_ac_antec_a66bis> 
                        <comite_par_fae_emp_ppal></comite_par_fae_emp_ppal> 
                        <depto_pre_rie_emp_ppal></depto_pre_rie_emp_ppal> 
                        <imp_sist_gest_sst_emp_ppal></imp_sist_gest_sst_emp_ppal> 
                        <fiscalizacion_con_multas_mat_sst></fiscalizacion_con_multas_mat_sst> 
                        <organismo_multas></organismo_multas> 
                        <circunstancias_accidente></circunstancias_accidente>
                        <desc_acc_invest></desc_acc_invest> 
                        <justificacion_no_laboral></justificacion_no_laboral>
                        <vehiculo_involucrado></vehiculo_involucrado> 
                        <codificacion_vehiculo_involucrado></codificacion_vehiculo_involucrado> 
                        <antecedentes_informacion_acc></antecedentes_informacion_acc> 
                        <investigador_acc> 
                            <apellido_paterno></apellido_paterno> 
                            <apellido_materno></apellido_materno> 
                            <nombres></nombres> 
                            <rut></rut> 
                        </investigador_acc> 
                        <prof_invest_acc></prof_invest_acc> 
                        <invest_es_experto></invest_es_experto> 
                        <categoria_experto></categoria_experto> 
                        <nro_reg_a_s_invest_acc></nro_reg_a_s_invest_acc> 
                        <documentos_acompanan_investigacion></documentos_acompanan_investigacion>
                    </investigacion_acc> 
                </ZONA_INVESTIGACION></RALF_Investigacion>";

                    //$ralf = simplexml_load_string($zona_investigacion);
                    


                    

                    $ralf->ZONA_INVESTIGACION->investigacion_acc->fecha_inicio_investigacion_acc=$post["fecha_inicio_investigacion_acc"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->fecha_termino_investigacion_acc=$post["fecha_termino_investigacion_acc"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso=$post['hora_ingreso_hr'].":".$post['hora_ingreso_mm'].":".$post['hora_ingreso_ss'];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->hora_salida=$post['hora_salida_hr'].":".$post['hora_salida_mm'].":".$post['hora_salida_ss'];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->jornada_momento_accidente=$post["jornada_momento_accidente"];

                    $ralf->ZONA_INVESTIGACION->investigacion_acc->jornada_momento_accidente_otro=$post["jornada_momento_accidente_otro"];
                    
                    if ($post["jornada_momento_accidente"] != 4) {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->jornada_momento_accidente_otro);
                    }
                    
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->trabajo_habitual_cual=$post["trabajo_habitual_cual"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->trabajo_habitual=$post["trabajo_habitual"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->annos=$post["antiguedad_annos"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->meses=$post["antiguedad_meses"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->dias=$post["antiguedad_dias"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->lugar_trabajo=$post["lugar_trabajo"];

                    
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_comites_funcio=$post["nro_comites_funcio"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_comites_ds54_a1=$post["nro_comites_ds54_a1"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->exist_comites_lugar_acc=$post["exist_comites_lugar_acc"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->cumb_ob_info_ds40_a21=$post["cumb_ob_info_ds40_a21"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->reg_ohys_al_dia=$post["reg_ohys_al_dia"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_teorico=$post["depto_pre_rie_teorico"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_real=$post["depto_pre_rie_real"];
                    
                    if ($post["depto_pre_rie_teorico"] != 1) {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_real);
                    }
                    
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->apellido_paterno=$post["exp_pre_em_apellido_paterno"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->apellido_materno=$post["exp_pre_em_apellido_materno"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->nombres=$post["exp_pre_em_nombres"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->rut=$post["exp_pre_em_rut"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->tipo_cont_exp_pre_em=$post["tipo_cont_exp_pre_em"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_reg_a_s_exp_pre_em=$post["nro_reg_a_s_exp_pre_em"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->cat_exp_pre_em=$post["cat_exp_pre_em"];
                    
                    if ($post["depto_pre_rie_real"] != 1) {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em);
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->tipo_cont_exp_pre_em);
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->nro_reg_a_s_exp_pre_em);
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->cat_exp_pre_em);
                    }
                    
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->tipo_cont_exp_pre_em_otro=$post["tipo_cont_exp_pre_em_otro"];
                    
                    if ($post['tipo_cont_exp_pre_em'] != 7) {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->tipo_cont_exp_pre_em_otro);
                    }
                    
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_dias_jor_parcial_cont_exp_pre_emp=$post["nro_dias_jor_parcial_cont_exp_pre_emp"];
                    
                    if (!in_array($post['tipo_cont_exp_pre_em'], array(1,3,5))) {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->nro_dias_jor_parcial_cont_exp_pre_emp);
                    }

                    $ralf->ZONA_INVESTIGACION->investigacion_acc->programa_pre_rie=$post["programa_pre_rie"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->trabajador_reg_subcontratacion=$post["trabajador_reg_subcontratacion"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->registro_ac_antec_a66bis=$post["registro_ac_antec_a66bis"];
                    if ($post['trabajador_reg_subcontratacion'] != 1) {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->registro_ac_antec_a66bis);
                    }
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->comite_par_fae_emp_ppal=$post["comite_par_fae_emp_ppal"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_emp_ppal=$post["depto_pre_rie_emp_ppal"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->imp_sist_gest_sst_emp_ppal=$post["imp_sist_gest_sst_emp_ppal"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->fiscalizacion_con_multas_mat_sst=$post["fiscalizacion_con_multas_mat_sst"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->organismo_multas=$post["organismo_multas"];
                    if ($post['fiscalizacion_con_multas_mat_sst'] != 1) {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->organismo_multas);
                    }
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->desc_acc_invest=$post["desc_acc_invest"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->vehiculo_involucrado=$post["vehiculo_involucrado"];
                    //$ralf = Controller_RalfInvestigacion::agregarVehiculoInvolucrado($ralf, $post["vehiculo_involucrado"], $hay_vehiculo_involucrado);   
 
                    if($hay_vehiculo_involucrado==true) {                     
                        $ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_modo_transporte=$post["codigo_modo_transporte"];
                        $ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_papel_lesionado=$post["codigo_papel_lesionado"];
                        $ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_contraparte=$post["codigo_contraparte"];
                        $ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_tipo_evento=$post["codigo_tipo_evento"];
                    }else {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado);
                    }
                    


                    $ralf->ZONA_INVESTIGACION->investigacion_acc->antecedentes_informacion_acc=$post["antecedentes_informacion_acc"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->apellido_paterno=$post["investigador_acc_apellido_paterno"]; 
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->apellido_materno=$post["investigador_acc_apellido_materno"]; 
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->nombres=$post["investigador_acc_nombres"]; 
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->rut=strtoupper($post["investigador_acc_rut"]); 
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->prof_invest_acc=$post["prof_invest_acc"]; 
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->invest_es_experto=$post["invest_es_experto"]; 
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->categoria_experto=$post["categoria_experto"]; 
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_reg_a_s_invest_acc=$post["nro_reg_a_s_invest_acc"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->justificacion_no_laboral=$post["justificacion_no_laboral"];
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->circunstancias_accidente=$post["circunstancias_accidente"];

                     if ($post['invest_es_experto'] != 1) {
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->categoria_experto); 
                        unset($ralf->ZONA_INVESTIGACION->investigacion_acc->nro_reg_a_s_invest_acc); 
                    }
 
 
                    $ralf->ZONA_INVESTIGACION->investigacion_acc->documentos_acompanan_investigacion = '';
                    //$ralf = simplexml_load_string($variable);                   
                    if(isset($ralf->ZONA_C->empleado->trabajador->rut)){
                        $ralf = Documento::transformarZonaCNueva($ralf); 
                    }

					$ralf_bd=$ralf->saveXML();
					$ralf=Controller_RalfInvestigacion::documentos_anexos($xml_id,$ralf);
					//$ralf=Controller_Ralf3::documentos_anexos_causas($xml_id,$ralf);

                    #causas y medidas correctivas
                    //$ralf=Controller_Ralf3::causas_medidas_correctivas($xml_id,$ralf);
					
					$ralf_string=$ralf->saveXML();
                    //echo $ralf->saveXML();
					
                    $ralf = Documento::zona_o($ralf_string);                                        
                    $final = Firmar::firmar_xml_ralf(simplexml_load_string($ralf),$documento->TPXML_ID);                                                           
                    $valido=Utiles::valida_xml($final, dirname(__FILE__).'/../../../media/xsd/ralf/SISESAT_RALF_Investigacion.1.0.xsd');                    

                    if($valido['estado']) {                  
                        $documentostring->XMLSTRING=$ralf_bd;
                        $documentostring->save();                        
                        $documento->VALIDO=1;
                        $documento->ESTADO=6;
                        $documento->save();     

                        $ralf3=ORM::factory('Ralf3')->where('xml_id','=',$xml_id)->find();
                        $ralf3->fecha_inicio_investigacion_acc= $post["fecha_inicio_investigacion_acc"];
                        $ralf3->fecha_termino_investigacion_acc=    $post["fecha_termino_investigacion_acc"];
                        $ralf3->hora_ingreso= $post['hora_ingreso_hr'].":".$post['hora_ingreso_mm'].":".$post['hora_ingreso_ss'];
                        $ralf3->hora_salida=$post['hora_salida_hr'].":".$post['hora_salida_mm'].":".$post['hora_salida_ss'];
                        $ralf3->jornada_momento_accidente=  $post["jornada_momento_accidente"];
                        $ralf3->jornada_momento_accidente_otro= $post["jornada_momento_accidente_otro"];
                        $ralf3->trabajo_habitual_cual=  $post["trabajo_habitual_cual"];
                        $ralf3->trabajo_habitual=   $post["trabajo_habitual"];
                        $ralf3->antiguedad_annos=   $post["antiguedad_annos"];
                        $ralf3->antiguedad_meses=   $post["antiguedad_meses"];
                        $ralf3->antiguedad_dias=    $post["antiguedad_dias"];
                        $ralf3->lugar_trabajo=  $post["lugar_trabajo"];
                        $ralf3->nro_comites_funcio= $post["nro_comites_funcio"];
                        $ralf3->nro_comites_ds54_a1=    $post["nro_comites_ds54_a1"];
                        $ralf3->exist_comites_lugar_acc=    $post["exist_comites_lugar_acc"];
                        $ralf3->cumb_ob_info_ds40_a21=  $post["cumb_ob_info_ds40_a21"];
                        $ralf3->reg_ohys_al_dia=    $post["reg_ohys_al_dia"];
                        $ralf3->depto_pre_rie_teorico=  $post["depto_pre_rie_teorico"];
                        $ralf3->depto_pre_rie_real= $post["depto_pre_rie_real"];
                        $ralf3->exp_pre_em_apellido_paterno=    $post["exp_pre_em_apellido_paterno"];
                        $ralf3->exp_pre_em_apellido_materno=    $post["exp_pre_em_apellido_materno"];
                        $ralf3->exp_pre_em_nombres= $post["exp_pre_em_nombres"];
                        $ralf3->exp_pre_em_rut= $post["exp_pre_em_rut"];
                        $ralf3->tipo_cont_exp_pre_em=   $post["tipo_cont_exp_pre_em"];
                        $ralf3->tipo_cont_exp_pre_em_otro=  $post["tipo_cont_exp_pre_em_otro"];
                        $ralf3->nro_dias_jor_parcial_cont_exp_pre_emp=  $post["nro_dias_jor_parcial_cont_exp_pre_emp"];
                        $ralf3->nro_reg_a_s_exp_pre_em= $post["nro_reg_a_s_exp_pre_em"];
                        $ralf3->cat_exp_pre_em= $post["cat_exp_pre_em"];
                        $ralf3->programa_pre_rie=   $post["programa_pre_rie"];
                        $ralf3->trabajador_reg_subcontratacion= $post["trabajador_reg_subcontratacion"];
                        $ralf3->registro_ac_antec_a66bis=   $post["registro_ac_antec_a66bis"];
                        $ralf3->comite_par_fae_emp_ppal=    $post["comite_par_fae_emp_ppal"];
                        $ralf3->depto_pre_rie_emp_ppal= $post["depto_pre_rie_emp_ppal"];
                        $ralf3->imp_sist_gest_sst_emp_ppal= $post["imp_sist_gest_sst_emp_ppal"];
                        $ralf3->fiscalizacion_con_multas_mat_sst=   $post["fiscalizacion_con_multas_mat_sst"];
                        $ralf3->organismo_multas=   $post["organismo_multas"];
                        $ralf3->desc_acc_invest=    $post["desc_acc_invest"];
                        $ralf3->vehiculo_involucrado = $post["vehiculo_involucrado"];
                        $ralf3->codigo_modo_transporte= $post["codigo_modo_transporte"];
                        $ralf3->codigo_papel_lesionado= $post["codigo_papel_lesionado"];
                        $ralf3->codigo_contraparte= $post["codigo_contraparte"];
                        $ralf3->codigo_tipo_evento= $post["codigo_tipo_evento"];
                        $ralf3->antecedentes_informacion_acc=   $post["antecedentes_informacion_acc"];
                        $ralf3->investigador_acc_apellido_paterno=  $post["investigador_acc_apellido_paterno"];
                        $ralf3->investigador_acc_apellido_materno=  $post["investigador_acc_apellido_materno"];
                        $ralf3->investigador_acc_nombres=   $post["investigador_acc_nombres"];
                        $ralf3->investigador_acc_rut=   $post["investigador_acc_rut"];
                        $ralf3->prof_invest_acc=    $post["prof_invest_acc"];
                        $ralf3->invest_es_experto=  $post["invest_es_experto"];
                        $ralf3->categoria_experto=  $post["categoria_experto"];
                        $ralf3->nro_reg_a_s_invest_acc= $post["nro_reg_a_s_invest_acc"];
                        //$ralf3->investigador_apellido_paterno=  $post["investigador_apellido_paterno"];
                        //$ralf3->investigador_apellido_materno=  $post["investigador_apellido_materno"];
                        //$ralf3->investigador_nombres=   $post["investigador_nombres"];
                        //$ralf3->investigador_rut=   $post["investigador_rut"];
                        $ralf3->xml_id=$xml_id;
                        $ralf3->tipo_xml = 143;
                        $ralf3->save();

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
                $ralf->ZONA_INVESTIGACION->investigacion_acc->fecha_inicio_investigacion_acc=$post["fecha_inicio_investigacion_acc"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->fecha_termino_investigacion_acc=$post["fecha_termino_investigacion_acc"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso=$post['hora_ingreso_hr'].":".$post['hora_ingreso_mm'].":".$post['hora_ingreso_ss'];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->hora_salida=$post['hora_salida_hr'].":".$post['hora_salida_mm'].":".$post['hora_salida_ss'];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->jornada_momento_accidente=$post["jornada_momento_accidente"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->jornada_momento_accidente_otro=$post["jornada_momento_accidente_otro"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->trabajo_habitual_cual=$post["trabajo_habitual_cual"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->trabajo_habitual=$post["trabajo_habitual"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->annos=$post["antiguedad_annos"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->meses=$post["antiguedad_meses"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->dias=$post["antiguedad_dias"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->lugar_trabajo=$post["lugar_trabajo"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_comites_funcio=$post["nro_comites_funcio"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_comites_ds54_a1=$post["nro_comites_ds54_a1"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->exist_comites_lugar_acc=$post["exist_comites_lugar_acc"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->cumb_ob_info_ds40_a21=$post["cumb_ob_info_ds40_a21"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->reg_ohys_al_dia=$post["reg_ohys_al_dia"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_teorico=$post["depto_pre_rie_teorico"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_real=$post["depto_pre_rie_real"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->apellido_paterno=$post["exp_pre_em_apellido_paterno"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->apellido_materno=$post["exp_pre_em_apellido_materno"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->nombres=$post["exp_pre_em_nombres"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->rut=$post["exp_pre_em_rut"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->tipo_cont_exp_pre_em=$post["tipo_cont_exp_pre_em"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->tipo_cont_exp_pre_em_otro=$post["tipo_cont_exp_pre_em_otro"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_dias_jor_parcial_cont_exp_pre_emp=$post["nro_dias_jor_parcial_cont_exp_pre_emp"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_reg_a_s_exp_pre_em=$post["nro_reg_a_s_exp_pre_em"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->cat_exp_pre_em=$post["cat_exp_pre_em"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->programa_pre_rie=$post["programa_pre_rie"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->trabajador_reg_subcontratacion=$post["trabajador_reg_subcontratacion"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->registro_ac_antec_a66bis=$post["registro_ac_antec_a66bis"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->comite_par_fae_emp_ppal=$post["comite_par_fae_emp_ppal"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_emp_ppal=$post["depto_pre_rie_emp_ppal"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->imp_sist_gest_sst_emp_ppal=$post["imp_sist_gest_sst_emp_ppal"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->fiscalizacion_con_multas_mat_sst=$post["fiscalizacion_con_multas_mat_sst"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->organismo_multas=$post["organismo_multas"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->desc_acc_invest=$post["desc_acc_invest"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->vehiculo_involucrado=$post["vehiculo_involucrado"];

                $ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_modo_transporte=$post["codigo_modo_transporte"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_papel_lesionado=$post["codigo_papel_lesionado"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_contraparte=$post["codigo_contraparte"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_tipo_evento=$post["codigo_tipo_evento"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->antecedentes_informacion_acc=$post["antecedentes_informacion_acc"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->apellido_paterno=$post["investigador_acc_apellido_paterno"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->apellido_materno=$post["investigador_acc_apellido_materno"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->nombres=$post["investigador_acc_nombres"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->rut=$post["investigador_acc_rut"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->prof_invest_acc=$post["prof_invest_acc"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->invest_es_experto=$post["invest_es_experto"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->categoria_experto=$post["categoria_experto"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->nro_reg_a_s_invest_acc=$post["nro_reg_a_s_invest_acc"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->justificacion_no_laboral=$post["justificacion_no_laboral"];
                $ralf->ZONA_INVESTIGACION->investigacion_acc->circunstancias_accidente=$post["circunstancias_accidente"];
                

                //NUEVO CAMPO ORIGEN COMUN QUE SE GUARDE EN LA TABLA CASO
                $xml_documento = simplexml_load_string($documento->xmlstring->XMLSTRING);  
                $caso_id= $xml_documento->ZONA_A->documento->codigo_caso;
                $casoEncontrado = ORM::factory('Caso')->where('CASO_ID', '=', $caso_id)->find();
                $casoEncontrado->ORIGEN_COMUN = $_POST["origen_comun"];
                if ($casoEncontrado->loaded()) {
                    $casoEncontrado->save();
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
        $this->template->contenido = $this->template->contenido = View::factory('ralfInvestigacion/crear')                 
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
        $caso_id=$this->request->param('id');
        $xml=ORM::factory('XML',$caso_id);
        $casoEncontrado=ORM::factory('Caso',$xml->CASO_ID);

        if(empty($post)) {
            $default["fecha_inicio_investigacion_acc"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->fecha_inicio_investigacion_acc;
            $default["fecha_termino_investigacion_acc"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->fecha_termino_investigacion_acc;
            $default["hora_ingreso"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso;
            $default["hora_salida"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->hora_salida;
            $default["jornada_momento_accidente"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->jornada_momento_accidente;
            $default["jornada_momento_accidente_otro"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->jornada_momento_accidente_otro;
            $default["trabajo_habitual_cual"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->trabajo_habitual_cual;
            $default["trabajo_habitual"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->trabajo_habitual;
            $default["antiguedad_annos"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->annos;
            $default["antiguedad_meses"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->meses;
            $default["antiguedad_dias"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->antiguedad->dias;
            $default["lugar_trabajo"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->lugar_trabajo;
            $default["nro_comites_funcio"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->nro_comites_funcio;
            $default["nro_comites_ds54_a1"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->nro_comites_ds54_a1;
            $default["exist_comites_lugar_acc"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->exist_comites_lugar_acc;
            $default["cumb_ob_info_ds40_a21"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->cumb_ob_info_ds40_a21;
            $default["reg_ohys_al_dia"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->reg_ohys_al_dia;
            $default["depto_pre_rie_teorico"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_teorico;
            $default["depto_pre_rie_real"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_real;
            $default["exp_pre_em_apellido_paterno"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->apellido_paterno;
            $default["exp_pre_em_apellido_materno"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->apellido_materno;
            $default["exp_pre_em_nombres"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->nombres;
            $default["exp_pre_em_rut"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->exp_pre_em->rut;
            $default["tipo_cont_exp_pre_em"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->tipo_cont_exp_pre_em;
            $default["tipo_cont_exp_pre_em_otro"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->tipo_cont_exp_pre_em_otro;
            $default["nro_dias_jor_parcial_cont_exp_pre_emp"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->nro_dias_jor_parcial_cont_exp_pre_emp;
            $default["nro_reg_a_s_exp_pre_em"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->nro_reg_a_s_exp_pre_em;
            $default["cat_exp_pre_em"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->cat_exp_pre_em;
            $default["programa_pre_rie"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->programa_pre_rie;
            $default["trabajador_reg_subcontratacion"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->trabajador_reg_subcontratacion;
            $default["registro_ac_antec_a66bis"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->registro_ac_antec_a66bis;
            $default["comite_par_fae_emp_ppal"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->comite_par_fae_emp_ppal;
            $default["depto_pre_rie_emp_ppal"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->depto_pre_rie_emp_ppal;
            $default["imp_sist_gest_sst_emp_ppal"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->imp_sist_gest_sst_emp_ppal;
            $default["fiscalizacion_con_multas_mat_sst"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->fiscalizacion_con_multas_mat_sst;
            $default["organismo_multas"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->organismo_multas;
            $default["desc_acc_invest"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->desc_acc_invest;
            $default["vehiculo_involucrado"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->vehiculo_involucrado;            

            $default["codigo_modo_transporte"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_modo_transporte;
            $default["codigo_papel_lesionado"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_papel_lesionado;
            $default["codigo_contraparte"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_contraparte;
            $default["codigo_tipo_evento"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->codificacion_vehiculo_involucrado->codigo_tipo_evento;
            $default["antecedentes_informacion_acc"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->antecedentes_informacion_acc;
            $default["investigador_acc_apellido_paterno"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->apellido_paterno;
            $default["investigador_acc_apellido_materno"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->apellido_materno;
            $default["investigador_acc_nombres"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->nombres;
            $default["investigador_acc_rut"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->investigador_acc->rut;
            $default["prof_invest_acc"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->prof_invest_acc;
            $default["invest_es_experto"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->invest_es_experto;
            $default["categoria_experto"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->categoria_experto;
            $default["nro_reg_a_s_invest_acc"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->nro_reg_a_s_invest_acc;
            $default["origen_comun"]=$casoEncontrado->ORIGEN_COMUN;
            $default["justificacion_no_laboral"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->justificacion_no_laboral;
            $default["circunstancias_accidente"]=$ralf->ZONA_INVESTIGACION->investigacion_acc->circunstancias_accidente;
            

        } else {
            $default["fecha_inicio_investigacion_acc"]=$post["fecha_inicio_investigacion_acc"];
            $default["fecha_termino_investigacion_acc"]=$post["fecha_termino_investigacion_acc"];
            $default["hora_ingreso"]=$post['hora_ingreso_hr'].":".$post['hora_ingreso_mm'].":".$post['hora_ingreso_ss'];
            $default["hora_salida"]=$post['hora_salida_hr'].":".$post['hora_salida_mm'].":".$post['hora_salida_ss'];
            $default["jornada_momento_accidente"]=$post["jornada_momento_accidente"];
            $default["jornada_momento_accidente_otro"]=$post["jornada_momento_accidente_otro"];
            $default["trabajo_habitual_cual"]=$post["trabajo_habitual_cual"];
            $default["trabajo_habitual"]=$post["trabajo_habitual"];
            $default["antiguedad_annos"]=$post["antiguedad_annos"];
            $default["antiguedad_meses"]=$post["antiguedad_meses"];
            $default["antiguedad_dias"]=$post["antiguedad_dias"];
            $default["lugar_trabajo"]=$post["lugar_trabajo"];
            $default["nro_comites_funcio"]=$post["nro_comites_funcio"];
            $default["nro_comites_ds54_a1"]=$post["nro_comites_ds54_a1"];
            $default["exist_comites_lugar_acc"]=$post["exist_comites_lugar_acc"];
            $default["cumb_ob_info_ds40_a21"]=$post["cumb_ob_info_ds40_a21"];
            $default["reg_ohys_al_dia"]=$post["reg_ohys_al_dia"];
            $default["depto_pre_rie_teorico"]=$post["depto_pre_rie_teorico"];
            $default["depto_pre_rie_real"]=$post["depto_pre_rie_real"];
            $default["exp_pre_em_apellido_paterno"]=$post["exp_pre_em_apellido_paterno"];
            $default["exp_pre_em_apellido_materno"]=$post["exp_pre_em_apellido_materno"];
            $default["exp_pre_em_nombres"]=$post["exp_pre_em_nombres"];
            $default["exp_pre_em_rut"]=$post["exp_pre_em_rut"];
            $default["tipo_cont_exp_pre_em"]=$post["tipo_cont_exp_pre_em"];
            $default["tipo_cont_exp_pre_em_otro"]=$post["tipo_cont_exp_pre_em_otro"];
            $default["nro_dias_jor_parcial_cont_exp_pre_emp"]=$post["nro_dias_jor_parcial_cont_exp_pre_emp"];
            $default["nro_reg_a_s_exp_pre_em"]=$post["nro_reg_a_s_exp_pre_em"];
            $default["cat_exp_pre_em"]=$post["cat_exp_pre_em"];
            $default["programa_pre_rie"]=$post["programa_pre_rie"];
            $default["trabajador_reg_subcontratacion"]=$post["trabajador_reg_subcontratacion"];
            $default["registro_ac_antec_a66bis"]=$post["registro_ac_antec_a66bis"];
            $default["comite_par_fae_emp_ppal"]=$post["comite_par_fae_emp_ppal"];
            $default["depto_pre_rie_emp_ppal"]=$post["depto_pre_rie_emp_ppal"];
            $default["imp_sist_gest_sst_emp_ppal"]=$post["imp_sist_gest_sst_emp_ppal"];
            $default["fiscalizacion_con_multas_mat_sst"]=$post["fiscalizacion_con_multas_mat_sst"];
            $default["organismo_multas"]=$post["organismo_multas"];
            $default["desc_acc_invest"]=$post["desc_acc_invest"];
            $default["vehiculo_involucrado"] = $post["vehiculo_involucrado"];

            $default["codigo_modo_transporte"]=$post["codigo_modo_transporte"];
            $default["codigo_papel_lesionado"]=$post["codigo_papel_lesionado"];
            $default["codigo_contraparte"]=$post["codigo_contraparte"];
            $default["codigo_tipo_evento"]=$post["codigo_tipo_evento"];
            $default["antecedentes_informacion_acc"]=$post["antecedentes_informacion_acc"];
            $default["investigador_acc_apellido_paterno"]=$post["investigador_acc_apellido_paterno"];
            $default["investigador_acc_apellido_materno"]=$post["investigador_acc_apellido_materno"];
            $default["investigador_acc_nombres"]=$post["investigador_acc_nombres"];
            $default["investigador_acc_rut"]=$post["investigador_acc_rut"];
            $default["prof_invest_acc"]=$post["prof_invest_acc"];
            $default["invest_es_experto"]=$post["invest_es_experto"];
            $default["categoria_experto"]=$post["categoria_experto"];
            $default["nro_reg_a_s_invest_acc"]=$post["nro_reg_a_s_invest_acc"];
            $default["origen_comun"]=$casoEncontrado->ORIGEN_COMUN;
            $default["justificacion_no_laboral"]=$post["justificacion_no_laboral"];
            $default["circunstancias_accidente"]=$post["circunstancias_accidente"];
        }        
        return $default;        
    }

    public static function documentos_anexos($xml_id,$ralf) {        
        $documentos_anexos=$ralf->ZONA_INVESTIGACION->investigacion_acc->documentos_acompanan_investigacion;        
    	$anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();        
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
            View::factory('ralfInvestigacion/borrar_adjunto')->set('borrado',$borrado)->set('xml_id',$xml_id)
            ->set('adjunto_origen',$adjunto_origen)
            ->set('nombre_documento',$nombre_documento)
            );
    }

    public function agregarVehiculoInvolucrado($ralf, $vehiculo_involucrado, $hay_vehiculo_involucrado){        
        $nodoReferencia = 'antecedentes_informacion_acc';
        //error_log($ralf->saveXML(),3,"/var/www/html/ralf/ralf.log");
        if($hay_vehiculo_involucrado){ 
           $nodoReferencia = 'codificacion_vehiculo_involucrado';  
        }
        echo $nodoReferencia;
        $dom = dom_import_simplexml($ralf->ZONA_INVESTIGACION->investigacion_acc); 
        $nodoReferencia = dom_import_simplexml($dom->getElementsByTagName($nodoReferencia)->item(0)); 
 
        $dom->insertBefore($dom->ownerDocument->createElement('vehiculo_involucrado', $vehiculo_involucrado), $nodoReferencia); 
 
        return $ralf; 
 
    }
 

}