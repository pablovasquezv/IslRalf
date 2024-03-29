<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Adjuntos extends Controller_Website {

    public function action_documento_anexo() {   
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $mensaje_error="";
        $errors['antecedente']=null;
        $errors['fecha']=null;
        $errors['autor']=null;
        $errors['nombre']=null;
        $default['fecha']=null;
        $default['autor']=null;
        $default['nombre']=null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_subir_documento'])) {

                $datos=array_merge($_POST, $_FILES);                
                $post = Validation::factory($datos)->rule('fecha', 'not_empty')
                	->rule('fecha', 'date')
                        ->rule('fecha','Utiles::validateDate',array(':value')) 
                        ->rule('fecha','Utiles::fecha_minima',array(':value'))
                	->label('fecha', 'Fecha')
                        ->rule('autor','Utiles::whitespace',array(':value'))
                        ->rule('autor', 'not_empty')->label('autor', 'Autor')
                        ->rule('nombre','Utiles::whitespace',array(':value'))
                        ->rule('nombre', 'not_empty')->label('nombre', 'Nombre Doc')
                    
                        ->rules( 'antecedente', array(
                            array('Upload::not_empty', NULL),
                            array('Upload::valid' , NULL),
                            //array('Upload::type' , array(':value',array('jpg','gif','tiff','png','pdf','txt'))),
                            array('Upload::size',array(':value','5M')),                        
                            ))
                        ->rule('antecedente','Utiles::size_min',array(':value')) 
                        ->label('antecedente', 'Documento');
                if ($post->check()) {
                    
                    $result=$this->save_file($_FILES['antecedente']);
                    if($result) {                        
                        $d=$this->base64_file("uploads/{$result}");                        
                        $doc=ORM::factory('Adjunto');
                        $doc->nombre_documento=$post['nombre'];
                        $doc->autor_documento=$post['autor'];
                        $doc->fecha_documento=$post['fecha'];                        
                        $doc->base64=NULL;
                        $doc->extension=$d['type'];                        
                        $doc->xml_id=$xml_id;
                        $doc->ruta="uploads/{$result}";
                        $doc->origen="documentos_anexos";
                        $doc->save();                        
                        $mensaje_error = "Archivo cargado exitosamente";
                    }else {
                        $mensaje_error = "Error carga";
                    }                                    
                } else {
                    $default['nombre']=$post['nombre'];
                    $default['fecha']=$post['fecha'];
                    $default['autor']=$post['autor'];
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }
                        
        $this->response->body (
            View::factory('adjuntos/documento_anexo')->set('errors',$errors)->set('default',$default)->set('xml_id',$xml_id)->set('mensaje_error',$mensaje_error)
        );
    }

    public function action_documento_anexo_causas() {   
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $mensaje_error="";
        $errors['antecedente']=null;
        $errors['fecha']=null;
        $errors['autor']=null;
        $errors['nombre']=null;
        $default['fecha']=null;
        $default['autor']=null;
        $default['nombre']=null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_subir_documento'])) {

                $datos=array_merge($_POST, $_FILES);                
                $post = Validation::factory($datos)->rule('fecha', 'not_empty')
                	->rule('fecha', 'date')
                    ->rule('fecha','Utiles::validateDate',array(':value')) 
                    ->rule('fecha','Utiles::fecha_minima',array(':value'))
                	->label('fecha', 'Fecha')
                    ->rule('autor','Utiles::whitespace',array(':value'))
                    ->rule('autor', 'not_empty')->label('autor', 'Autor')
                    ->rule('nombre','Utiles::whitespace',array(':value'))
                    ->rule('nombre', 'not_empty')->label('nombre', 'Nombre Doc')                    
                    ->rules( 'antecedente', array(
                        array('Upload::not_empty', NULL),
                        array('Upload::valid' , NULL),
                        //array('Upload::type' , array(':value',array('jpg','gif','tiff','png','pdf','txt'))),
                        array('Upload::size',array(':value','5M')),                        
                        ))
                    ->rule('antecedente','Utiles::size_min',array(':value')) 
                    ->label('antecedente', 'Documento');
                if ($post->check()) {
                    
                    $result=$this->save_file($_FILES['antecedente']);
                    if($result) {                        
                        $d=$this->base64_file("uploads/{$result}");                        
                        $doc=ORM::factory('Adjunto');
                        $doc->nombre_documento=$post['nombre'];
                        $doc->autor_documento=$post['autor'];
                        $doc->fecha_documento=$post['fecha'];                        
                        $doc->base64=NULL;
                        $doc->extension=$d['type'];                        
                        $doc->xml_id=$xml_id;
                        $doc->ruta="uploads/{$result}";
                        $doc->origen="documentos_anexos_causas";
                        $doc->save();                        
                        $mensaje_error = "Archivo cargado exitosamente";
                    }else {
                        $mensaje_error = "Error carga";
                    }                                    
                } else {
                    $default['nombre']=$post['nombre'];
                    $default['fecha']=$post['fecha'];
                    $default['autor']=$post['autor'];
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }
                        
        $this->response->body (
            View::factory('adjuntos/documento_anexo_causas')->set('errors',$errors)->set('default',$default)->set('xml_id',$xml_id)->set('mensaje_error',$mensaje_error)
            );
    }

    public function action_documento_anexo_ralf5() {   

        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $mensaje_error="";
        $errors['antecedente']=null;
        $errors['fecha']=null;
        $errors['autor']=null;
        $errors['nombre']=null;
        $default['fecha']=null;
        $default['autor']=null;
        $default['nombre']=null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_subir_documento'])) {

                $datos=array_merge($_POST, $_FILES);                
                $post = Validation::factory($datos)->rule('fecha', 'not_empty')
                	->rule('fecha', 'date')
                    ->rule('fecha','Utiles::validateDate',array(':value')) 
                    ->rule('fecha','Utiles::fecha_minima',array(':value'))
                	->label('fecha', 'Fecha')
                    ->rule('autor','Utiles::whitespace',array(':value'))
                    ->rule('autor', 'not_empty')->label('autor', 'Autor')
                    ->rule('nombre','Utiles::whitespace',array(':value'))
                    ->rule('nombre', 'not_empty')->label('nombre', 'Nombre Doc')                    
                    ->rules( 'antecedente', array(
                        array('Upload::not_empty', NULL),
                        array('Upload::valid' , NULL),
                        //array('Upload::type' , array(':value',array('jpg','gif','tiff','png','pdf','txt'))),
                        array('Upload::size',array(':value','5M')),                        
                        ))
                    ->rule('antecedente','Utiles::size_min',array(':value')) 
                    ->label('antecedente', 'Documento');
                if ($post->check()) {
                    
                    $result=$this->save_file($_FILES['antecedente']);
                    if($result) {                        
                        $d=$this->base64_file("uploads/{$result}");                        
                        $doc=ORM::factory('Adjunto');
                        $doc->nombre_documento=$post['nombre'];
                        $doc->autor_documento=$post['autor'];
                        $doc->fecha_documento=$post['fecha'];                        
                        $doc->base64=NULL;
                        $doc->extension=$d['type'];                        
                        $doc->xml_id=$xml_id;
                        $doc->ruta="uploads/{$result}";
                        $doc->origen="documentos_anexos_ralf5";
                        $doc->save();                        
                        $mensaje_error = "Archivo cargado exitosamente";
                    }else {
                        $mensaje_error = "Error carga";
                    }                                    
                } else {
                    $default['nombre']=$post['nombre'];
                    $default['fecha']=$post['fecha'];
                    $default['autor']=$post['autor'];
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }   
            } 
        }
                        
        $this->response->body (
            View::factory('adjuntos/documento_anexo_ralf5')->set('errors',$errors)->set('default',$default)->set('xml_id',$xml_id)->set('mensaje_error',$mensaje_error)
            );
    }


    public function action_adjuntos_documento_anexo() {   
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $adjuntos=array();        
        foreach(ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all() as $a)  {
            $ver= HTML::anchor(Kohana::$config->load('sitio.url_base').$a->ruta,'Ver');
            $borrar= HTML::anchor("ralf3/borrar_adjunto/{$a->id}",'borrar',array('class'=>'fancybox-small'));
            $link= $ver. " | ".$borrar;
            $adjuntos[] = array($a->nombre_documento, $a->fecha_documento, $a->autor_documento, $link);            
        }
        $this->response->body(json_encode($adjuntos));
    }

    public function action_adjuntos_documento_anexo_causas() {   
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $adjuntos=array();        
        foreach(ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos_causas')->find_all() as $a)  {
            $ver= HTML::anchor(Kohana::$config->load('sitio.url_base').$a->ruta,'Ver');
            $borrar= HTML::anchor("ralf3/borrar_adjunto/{$a->id}",'borrar',array('class'=>'fancybox-small'));
            $link= $ver. " | ".$borrar;
            $adjuntos[] = array( $a->nombre_documento, $a->fecha_documento, $a->autor_documento, $link);            
        }
        $this->response->body(json_encode($adjuntos));
    }


     public function action_adjuntos_ralf5() {   
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $adjuntos=array();        
        foreach(ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos_ralf5')->find_all() as $a)  {
            $ver= HTML::anchor(Kohana::$config->load('sitio.url_base').$a->ruta,'Ver');
            $borrar= HTML::anchor("ralf5/borrar_adjunto/{$a->id}",'borrar',array('class'=>'fancybox-small'));
            $link= $ver. " | ".$borrar;
            $adjuntos[] = array( $a->nombre_documento, $a->fecha_documento, $a->autor_documento, $link);            
        }
        $this->response->body(json_encode($adjuntos));
    }



}