<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Envios extends Controller_Website {
    public function action_index() {
        if($this->get_rol()!='admin') {
            $this->redirect("error");
        }
        $this->template->mensaje_error = NULL;
        $envios=ORM::factory('Intento_Envios')->find_all();
        $this->template->contenido = View::factory('envios/index')->set('envios',$envios);
    }
}

// End 
