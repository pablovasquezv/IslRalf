<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Validar extends Controller_Website {

    public function action_ralf1() {
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }

        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        $zonas = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F', 'g' => 'G', 'h' => 'H', 'i' => 'I', 'j' => 'J', 'k' => 'K', 'l' => 'L', 'p' => 'P', 'q' => 'Q');

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;

        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }



        $this->template->contenido = View::factory('validar/ralf1')                    
                    ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
                    ->set('estado_documento', $documento->estado_xml)
                    ->set('xml', $xml)
                    ->set('data', $data)
                    ->set('zona', 'ZONA_')
                    ->set('zonas', $zonas)
                    ->set('tipo_doc', $documento->TPXML_ID)                    
                    ->set('template', '/documento/zona/zona_')                    
                    ->set('estado', $documento->ESTADO)
                    ->set('caso_id',$documento->CASO_ID)
                    ->set('errors',$errors)->set('default',$default)
                    ->set('xml_id',$xml_id)
                    ;
    }

    public function action_select_ralf() {
        $id=$this->request->param('id');
        $this->auto_render = FALSE;
        if($id==1) {
            $errors['observacion'] = null;
            $default['observacion'] = null;
            $view=View::factory('validar/select_ralf')->set('errors',$errors)->set('default',$default);
        } else {
            $view= "";
        }
        
        $this->response->body($view);
    }

    public function action_ralf2() {
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }

        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        $zonas = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F', 'g' => 'G', 'h' => 'H', 'i' => 'I', 'j' => 'J', 'k' => 'K', 'l' => 'L', 'p' => 'P', 'q' => 'Q');

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;

        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }



        $this->template->contenido = View::factory('validar/ralf2')                    
                    ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
                    ->set('estado_documento', $documento->estado_xml)
                    ->set('xml', $xml)
                    ->set('data', $data)
                    ->set('zona', 'ZONA_')
                    ->set('zonas', $zonas)
                    ->set('tipo_doc', $documento->TPXML_ID)                    
                    ->set('template', '/documento/zona/zona_')                    
                    ->set('estado', $documento->ESTADO)
                    ->set('caso_id',$documento->CASO_ID)
                    ->set('errors',$errors)->set('default',$default)
                    ->set('xml_id',$xml_id)
                    ;
    } 

    public function action_ralf3() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        

        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('validar/ralf3')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('errors',$errors)->set('default',$default)            
            ;         
    }

    public function action_ralf4() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        
        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }


        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('validar/ralf4')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('errors',$errors)->set('default',$default);             
    }

    public function action_ralf5() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');

        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }        

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('validar/ralf5')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('errors',$errors)->set('default',$default);        
    }


    public function action_ralfAccidente() {
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }

        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        $zonas = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F', 'g' => 'G', 'h' => 'H', 'i' => 'I', 'j' => 'J', 'k' => 'K', 'l' => 'L', 'p' => 'P', 'q' => 'Q');

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;

        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }


        $this->template->contenido = View::factory('validar/ralfAccidente')                    
                    ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
                    ->set('estado_documento', $documento->estado_xml)
                    ->set('xml', $xml)
                    ->set('data', $data)
                    ->set('zona', 'ZONA_')
                    ->set('zonas', $zonas)
                    ->set('tipo_doc', $documento->TPXML_ID)                    
                    ->set('template', '/documento/zona/zona_')                    
                    ->set('estado', $documento->ESTADO)
                    ->set('caso_id',$documento->CASO_ID)
                    ->set('errors',$errors)->set('default',$default)
                    ->set('xml_id',$xml_id)
                    ;
    }


    public function action_ralfMedidas() {
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }

        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        $zonas = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F', 'g' => 'G', 'h' => 'H', 'i' => 'I', 'j' => 'J', 'k' => 'K', 'l' => 'L', 'p' => 'P', 'inmediatas' => 'INMEDIATAS');

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;

        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }



        $this->template->contenido = View::factory('validar/ralfMedidas')                    
                    ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
                    ->set('estado_documento', $documento->estado_xml)
                    ->set('xml', $xml)
                    ->set('data', $data)
                    ->set('zona', 'ZONA_')
                    ->set('zonas', $zonas)
                    ->set('tipo_doc', $documento->TPXML_ID)                    
                    ->set('template', '/documento/zona/zona_')                    
                    ->set('estado', $documento->ESTADO)
                    ->set('caso_id',$documento->CASO_ID)
                    ->set('errors',$errors)->set('default',$default)
                    ->set('xml_id',$xml_id)
                    ;
    }

    public function action_ralfInvestigacion() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        

        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('validar/ralfInvestigacion')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('errors',$errors)->set('default',$default)          
            ;         
    }

    public function action_ralfCausas() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        
        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {                
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                if($_POST['valido']==1){
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                if ($post->check()) {
                    if($post['valido']==1){
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    }else {
                        $documento->ESTADO=2;
                        $documento->save();

                    }                    
                   $this->redirect("/");                                 
                } else {
                    $default['valido']=$post['valido']; 
                    if(isset($post['observacion'])){
                        $default['observacion']=$post['observacion'];                                            
                    }    
                    
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }


        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('validar/ralfCausas')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('errors',$errors)->set('default',$default);             
    }


    public function action_ralfPrescripcion() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        if($this->get_rol()!='admin') {              
            $this->redirect("error");
        }        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        
        $errors['valido']=null;
        $default['valido']=null;
        $errors['observacion']=null;
        $default['observacion']=null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                
                if($_POST['valido']==1) {
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                
                if ($post->check()) {
                    if($post['valido']==1) {
                        $comentario=ORM::factory('Comentario');
                        $comentario->observacion=$post["observacion"];
                        $comentario->xml_id=$xml_id;
                        $user=$this->get_usuario();
                        $comentario->user_id=$user->id;
                        $comentario->tipo='comentario_admin';
                        $comentario->save();
                        $documento->ESTADO=5;
                        $documento->save();
                    } else {
                        $documento->ESTADO=2;
                        $documento->save();
                    }
                    
                   $this->redirect("/");                  
                } else {
                    $default['valido']=$post['valido'];
                    if(isset($post['observacion'])) {
                        $default['observacion']=$post['observacion'];
                    }
                    
                    $errors = $post->errors('validate');
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            } 
        }

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $xml;
        $this->template->contenido = View::factory('validar/ralfPrescripcion')
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)
            ->set('tipo_doc', $documento->TPXML_ID)
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('errors',$errors)->set('default',$default);
    }

    public function action_ralfVerificacion() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'Error, Falta id de documento';
            $this->template->contenido = '';
            return;
        }
        
        if($this->get_rol() != 'admin') {
            $this->redirect("error");
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        
        $errors['valido'] = null;
        $default['valido'] = null;
        $errors['observacion'] = null;
        $default['observacion'] = null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                
                if($_POST['valido'] == 1) {
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                
                if ($post->check()) {
                    if($post['valido'] == 1) {
                        $comentario = ORM::factory('Comentario');
                        $comentario->observacion = $post["observacion"];
                        $comentario->xml_id = $xml_id;
                        $user = $this->get_usuario();
                        $comentario->user_id = $user->id;
                        $comentario->tipo = 'comentario_admin';
                        $comentario->save();
                        
                        $documento->ESTADO = 5;
                        $documento->save();
                    } else {
                        $documento->ESTADO = 2;
                        $documento->save();

                    }
                   $this->redirect("/");
                } else {
                    $default['valido'] = $post['valido'];
                    if(isset($post['observacion'])) {
                        $default['observacion'] = $post['observacion'];
                    }
                    
                    $errors = $post->errors('validate');
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }
        
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $xml;
        $this->template->contenido = View::factory('validar/ralfVerificacion')
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)
            ->set('tipo_doc', $documento->TPXML_ID)
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('xml_id_origen',$documento->XML_ID_ORIGEN)
            ->set('documento',$documento)
            ->set('errors',$errors)->set('default',$default);
    }
    
    public function action_ralfNotificacion() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'Error, Falta id de documento';
            $this->template->contenido = '';
            return;
        }
        
        if($this->get_rol() != 'admin') {
            $this->redirect("error");
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        
        $errors['valido'] = null;
        $default['valido'] = null;
        $errors['observacion'] = null;
        $default['observacion'] = null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                
                if($_POST['valido'] == 1) {
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                
                if ($post->check()) {
                    if($post['valido'] == 1) {
                        $comentario = ORM::factory('Comentario');
                        $comentario->observacion = $post["observacion"];
                        $comentario->xml_id = $xml_id;
                        $user = $this->get_usuario();
                        $comentario->user_id = $user->id;
                        $comentario->tipo = 'comentario_admin';
                        $comentario->save();
                        
                        $documento->ESTADO = 5;
                        $documento->save();
                    } else {
                        $documento->ESTADO = 2;
                        $documento->save();

                    }
                   $this->redirect("/");
                } else {
                    $default['valido'] = $post['valido'];
                    if(isset($post['observacion'])) {
                        $default['observacion'] = $post['observacion'];
                    }
                    
                    $errors = $post->errors('validate');
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }
        
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $regiones = Utiles::regiones();
        $data['xml'] = $xml;
        $this->template->contenido = View::factory('validar/ralfNotificacion')
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)
            ->set('tipo_doc', $documento->TPXML_ID)
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('xml_id_origen',$documento->XML_ID_ORIGEN)
            ->set('documento',$documento)
            ->set('config_ralf', $this->config_ralf)
            ->set('regiones', $regiones)
            ->set('errors',$errors)->set('default',$default)
            ;
    }


    public function action_ralfRecargoTasa() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'Error, Falta id de documento';
            $this->template->contenido = '';
            return;
        }
        
        if($this->get_rol() != 'admin') {
            $this->redirect("error");
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        
        $errors['valido'] = null;
        $default['valido'] = null;
        $errors['observacion'] = null;
        $default['observacion'] = null;

        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_validar'])) {
                $post = Validation::factory($_POST)->rule('valido', 'not_empty')->label('valido', 'valido');
                
                if($_POST['valido'] == 1) {
                    $post = $post->rule('observacion', 'not_empty')->label('observacion', 'Observaciones');
                }
                
                if ($post->check()) {
                    if($post['valido'] == 1) {
                        $comentario = ORM::factory('Comentario');
                        $comentario->observacion = $post["observacion"];
                        $comentario->xml_id = $xml_id;
                        $user = $this->get_usuario();
                        $comentario->user_id = $user->id;
                        $comentario->tipo = 'comentario_admin';
                        $comentario->save();
                        
                        $documento->ESTADO = 5;
                        $documento->save();
                    } else {
                        $documento->ESTADO = 2;
                        $documento->save();

                    }
                   $this->redirect("/");
                } else {
                    $default['valido'] = $post['valido'];
                    if(isset($post['observacion'])) {
                        $default['observacion'] = $post['observacion'];
                    }
                    
                    $errors = $post->errors('validate');
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }
        
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $regiones = Utiles::regiones();
        $data['xml'] = $xml;
        $this->template->contenido = View::factory('validar/ralfRecargoTasa')
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)
            ->set('tipo_doc', $documento->TPXML_ID)
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('back_page', URL::site("caso/ver_caso_admin/{$documento->CASO_ID}", 'http'))
            ->set('xml_id',$xml_id)
            ->set('xml_id_origen',$documento->XML_ID_ORIGEN)
            ->set('documento',$documento)
            ->set('config_ralf', $this->config_ralf)
            ->set('regiones', $regiones)
            ->set('errors',$errors)->set('default',$default)
            ;
    }

}

// End Documento
