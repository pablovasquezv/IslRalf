<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_ArbolCausas extends Controller_Website {

    public function action_arbol_causas() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $nodos = $this->request->post('arr_nodos');
        $htmlArbol = $this->request->post('htmlArbol');

        $mensaje_error="";
        $guardado_ok = "";
        $errors['antecedente']=null;
        $errors['fecha']=null;
        $errors['autor']=null;
        $errors['nombre']=null;
        $default['fecha']=null;
        $default['autor']=null;
        $default['nombre']=null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_crear_arbol'])) {
                $datos=array_merge($_POST, $_FILES);                
                $post = Validation::factory($datos)->rule('lesion','Utiles::whitespace',array(':value'))
                   ->rule('lesion', 'not_empty')->label('lesion', 'Lesión');

                if ($post->check()) {

                    $lesion = $post["lesion"];                    
                    Controller_ArbolCausas::guardar_cabecera_arbol($xml_id,$lesion,$htmlArbol);
                    Controller_ArbolCausas::guardar_nodos_arbol($nodos,$xml_id);
                    $mensaje_error = "Arbol de Causas Guardado";
                } else {
                    $errors = $post->errors('validate');                        
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
                       
            } 
        }
                        
        $this->response->body (
            View::factory('ralfCausas/arbol_causas')->set('errors',$errors)->set('default',$default)->set('xml_id',$xml_id)->set('mensaje_error',$mensaje_error)
            );
    }

    

    

    public function action_buscar_glosa_causa(){
        $this->auto_render=false;
        $codigo = $this->request->post('codigo');
        $causa = ORM::factory('Causa144')->where('codigo','=',$codigo)->find();

        $this->response->body($causa->glosa);
    }

    public function guardar_cabecera_arbol($xml_id,$lesion,$htmlArbol){

        $arbol_insert=ORM::factory('ArbolCausas');
        $arbol_insert->xml_id = $xml_id;
        $arbol_insert->lesion = $lesion;
        $arbol_insert->arbolstring = $htmlArbol;

        $arbol_insert->save();
    }

    public function guardar_nodos_arbol($nodos,$xml_id){
        $arbol_id = ORM::factory('ArbolCausas')->where('xml_id', '=',$xml_id)->find();

        $arr_nodos = json_decode($nodos);

        foreach ($arr_nodos as $key => $value) {
            $nodo_insert = ORM::factory('ArbolCausasNodo');
            $nodo_insert->vector_nodo = $value->id;
            $nodo_insert->hecho = $value->hecho;
            $nodo_insert->causa_id = $value->codCausa;
            $nodo_insert->glosa_otros = $value->glosaOtros;
            $nodo_insert->arbol_id = $arbol_id;

            $nodo_insert->save();
        }

    }

    public function action_guardar_estructura_arbol(){
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $htmlArbol = $this->request->post('htmlArbol');
        $lesion = $this->request->post('lesion');
        
        $nodos = $this->request->post('arr_nodos');

        Controller_ArbolCausas::guardar_cabecera_arbol($xml_id,$lesion,$htmlArbol);
        Controller_ArbolCausas::guardar_nodos_arbol($nodos,$xml_id);

        $this->response->body("Arbol de Causas Guardado");
    }

    public function action_ver_arbol_causas(){
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $arbol = ORM::factory('ArbolCausas')->where('xml_id', '=',$xml_id)->find();

        $this->response->body (
        View::factory('ralfCausas/ver_arbol_causas')->set('xml_id',$xml_id)->set('arbol',$arbol)
        );
    }

    public function action_buscar_arbol_causas(){
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $arbol = ORM::factory('ArbolCausas')->where('xml_id', '=',$xml_id)->find_all();
        $datos=array(); 
        foreach ($arbol as $a) {
            $ver= HTML::anchor("arbolCausas/ver_arbol_causas/{$xml_id}",'Ver',array('class'=>'fancybox-big'));
            $borrar= HTML::anchor("ralfCausas/borrar_arbol_causas/{$a->arbol_id}",'borrar',array('class'=>'fancybox-small'));
            $link= $ver. " | ".$borrar;
            $datos[] = array( $a->lesion, $link);
        }

        $this->response->body(json_encode($datos));
    }

}