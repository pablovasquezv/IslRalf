<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Reporte extends Controller_Website {
    public function action_index() {        
        if($this->get_rol()=='admin') {           
            $this->template->mensaje_error = NULL;
            $this->template->contenido = $this->template->contenido = View::factory('reporte/index');            
        }else{
            $this->redirect("error");            
        }
    }

}