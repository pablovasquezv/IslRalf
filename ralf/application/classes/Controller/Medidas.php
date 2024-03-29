<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Medidas extends Controller_Website {

    public function action_medidas_no_implementadas() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $mensaje_error="";

        $errors = array();
        $default['medida']=null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_aceptar'])) {
                $post = Validation::factory($_POST)
                ->rule('medida','Utiles::whitespace',array(':value'))
                ->rule('medida', 'not_empty')->label('medida', 'Medida');
                if ($post->check()) {
                    $med=ORM::factory('Medida');
                    $med->medida=$post["medida"];
                    $med->xml_id=$xml_id;
                    $med->origen="medidas_no_implementadas";
                    $med->save();
                    $mensaje_error = "Medida cargada exitosamente";
                } else {
                    $default['medida']=$post['medida'];
                    $errors = $post->errors('validate');
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }

        $this->response->body (
            View::factory('medidas/medidas_no_implementadas')->set('errors',$errors)->set('default',$default)->set('xml_id',$xml_id)->set('mensaje_error',$mensaje_error)
            );
    }

    public function action_resultado_medidas_no_implementadas() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $medidas=array();
        foreach(ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_no_implementadas')->find_all() as $m)  {
            $link= HTML::anchor("ralf5/borrar_medida/{$m->id}",'borrar',array('class'=>'fancybox-small'));
            $medidas[] = array( $m->medida,$link);
        }
        $this->response->body(json_encode($medidas));
    }

    public function action_medidas_no_implementadas_plazo_ampliado() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $mensaje_error="";

        $errors = array();
        $default['medida']=null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_aceptar'])) {
                $post = Validation::factory($_POST)
                ->rule('medida','Utiles::whitespace',array(':value'))
                ->rule('medida', 'not_empty')->label('medida', 'Medida');
                if ($post->check()) {
                    $med=ORM::factory('Medida');
                    $med->medida=$post["medida"];
                    $med->xml_id=$xml_id;
                    $med->origen="medidas_no_implementadas_plazo_ampliado";
                    $med->save();
                    $mensaje_error = "Medida cargada exitosamente";
                } else {
                    $default['medida']=$post['medida'];
                    $errors = $post->errors('validate');
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }

        $this->response->body (
            View::factory('medidas/medidas_no_implementadas_plazo_ampliado')->set('errors',$errors)->set('default',$default)->set('xml_id',$xml_id)->set('mensaje_error',$mensaje_error)
            );
    }

    public function action_resultado_medidas_no_implementadas_plazo_ampliado() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $medidas=array();
        foreach(ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_no_implementadas_plazo_ampliado')->find_all() as $m)  {
            $link= HTML::anchor("ralf5/borrar_medida2/{$m->id}",'borrar',array('class'=>'fancybox-small'));
            $medidas[] = array($m->medida,$link);
        }
        $this->response->body(json_encode($medidas));
    }

    public function action_medidas_ralf2() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $mensaje_error="";

        $errors = array();
        $default['medida']=null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_aceptar'])) {
                $post = Validation::factory($_POST)->rule('medida','Utiles::whitespace',array(':value'))->rule('medida', 'not_empty')->label('medida', 'Medida');
                if ($post->check()) {
                    $med=ORM::factory('Medida');
                    $med->medida=$post["medida"];
                    $med->xml_id=$xml_id;
                    $med->origen="medidas_ralf2";
                    $med->save();
                    $mensaje_error = "Medida cargada exitosamente";
                } else {
                    $default['medida']=$post['medida'];
                    $errors = $post->errors('validate');
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }

        $this->response->body (
            View::factory('medidas/medidas_ralf2')->set('errors',$errors)->set('default',$default)->set('xml_id',$xml_id)->set('mensaje_error',$mensaje_error)
            );
    }

    public function action_resultado_medidas_ralf2() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $medidas=array();
        foreach(ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_ralf2')->find_all() as $m)  {
            $link= HTML::anchor("ralf2/borrar_medida/{$m->id}",'borrar',array('class'=>'fancybox-small'));
            $medidas[] = array($m->medida,$link);
        }
        $this->response->body(json_encode($medidas));
    }

    public function action_medidas_ralf4() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $fecha_accidente = $this->request->param('id2');
        $fecha_accidente;
        $mensaje_error="";

        $errors = array();
        $default["cumplimiento_medida_id"]=null;
        $default["cumplimiento_medida_medida"]=null;
        $default["cumplimiento_medida_medida_implementada"]=null;
        $default["cumplimiento_medida_ampliacion_plazo"]=null;
        $default["cumplimiento_medida_nueva_fecha_ampliacion_plazo"]=null;
        $default["cumplimiento_medida_observaciones"]=null;
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset ($_POST['boton_aceptar'])) {
                $post = Validation::factory($_POST)->rule('cumplimiento_medida_id', 'regex', array(':value', '/^[m|M]([0-9]+)$/'))
                        ->rule('cumplimiento_medida_id','Utiles::whitespace',array(':value'))
                        ->rule('cumplimiento_medida_id', 'not_empty')->label('cumplimiento_medida_id', 'Nº')
                        ->rule('cumplimiento_medida_medida','Utiles::whitespace',array(':value'))
                        ->rule('cumplimiento_medida_medida', 'not_empty')->label('cumplimiento_medida_medida', 'Medida')
                        ->rule('cumplimiento_medida_medida_implementada', 'not_empty')->label('cumplimiento_medida_medida_implementada', 'medida implementada')
                        ->rule('cumplimiento_medida_ampliacion_plazo','Utiles::whitespace',array(':value'))
                        ->rule('cumplimiento_medida_ampliacion_plazo', 'not_empty')->label('cumplimiento_medida_ampliacion_plazo', 'ampliacion plazo')


                        ->rule('cumplimiento_medida_observaciones','Utiles::whitespace',array(':value'))
                        //->rule('cumplimiento_medida_observaciones', 'not_empty')
                        ->label('cumplimiento_medida_observaciones', 'Observaciones');

                if($post["cumplimiento_medida_ampliacion_plazo"]==1) {
                    $post=$post->rule('cumplimiento_medida_nueva_fecha_ampliacion_plazo', 'not_empty')
                        ->rule('cumplimiento_medida_nueva_fecha_ampliacion_plazo','Utiles::validateDate',array(':value'))
                        ->rule('cumplimiento_medida_nueva_fecha_ampliacion_plazo', 'date')
                        ->label('cumplimiento_medida_nueva_fecha_ampliacion_plazo', 'Nueva Fecha');

                }

                if(!empty($_POST["cumplimiento_medida_nueva_fecha_ampliacion_plazo"])) {
                    if(!($_POST["cumplimiento_medida_nueva_fecha_ampliacion_plazo"]>=$fecha_accidente)) {
                        $errors = $errors+array("cumplimiento_medida_nueva_fecha_ampliacion_plazo"=>"Fecha Mayor o igual a fecha de accidente");
                    }
                }

                if($post->check() && count($errors)==0) {
                    $med=ORM::factory('Cumplimiento_Medida');
                    $med->medida_id=$post["cumplimiento_medida_id"];
                    $med->medida=$post["cumplimiento_medida_medida"];
                    $med->medida_implementada=$post["cumplimiento_medida_medida_implementada"];
                    $med->ampliacion_plazo=$post["cumplimiento_medida_ampliacion_plazo"];
                    $med->nueva_fecha_ampliacion_plazo=$post["cumplimiento_medida_nueva_fecha_ampliacion_plazo"];
                    $med->observaciones=$post["cumplimiento_medida_observaciones"];
                    $med->xml_id=$xml_id;
                    $med->save();
                    $mensaje_error = "Medida cargada exitosamente";
                } else {
                    $default["cumplimiento_medida_id"]=$post["cumplimiento_medida_id"];
                    $default["cumplimiento_medida_medida"]=$post["cumplimiento_medida_medida"];
                    $default["cumplimiento_medida_medida_implementada"]=$post["cumplimiento_medida_medida_implementada"];
                    $default["cumplimiento_medida_ampliacion_plazo"]=$post["cumplimiento_medida_ampliacion_plazo"];
                    $default["cumplimiento_medida_nueva_fecha_ampliacion_plazo"]=$post["cumplimiento_medida_nueva_fecha_ampliacion_plazo"];
                    $default["cumplimiento_medida_observaciones"]=$post["cumplimiento_medida_observaciones"];
                    $errors = $post->errors('validate')+$errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }

        $this->response->body (
            View::factory('medidas/medidas_ralf4')->set('errors',$errors)->set('default',$default)->set('xml_id',$xml_id)->set('mensaje_error',$mensaje_error)
            );
    }

    public function action_resultado_medidas_ralf4() {
        $this->auto_render=false;
        $xml_id = $this->request->param('id');
        $medidas=array();
        $si_no=array(1=>'Si',2=>'No');
        foreach(ORM::factory('Cumplimiento_Medida')->where('xml_id','=',$xml_id)->find_all() as $med)  {
            $link= HTML::anchor("ralf4/borrar_medida/{$med->id}",'borrar',array('class'=>'fancybox-small'));
            $medidas[] = array($med->medida_id,$med->medida,$si_no[$med->medida_implementada],$si_no[$med->ampliacion_plazo],$med->nueva_fecha_ampliacion_plazo,$med->observaciones,$link);
        }

        $this->response->body(json_encode($medidas));
    }
}