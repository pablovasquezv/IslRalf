<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Error extends Controller_Website {

    public function action_index() {
        $this->template->mensaje_error = NULL;
        $this->template->contenido = '<div class="mensaje_error">Acceso no permitido</div>';
    }

    public function action_esquema() {
        $this->template->mensaje_error = NULL;
        $this->template->contenido = '<div class="mensaje_error">Documento DIAT no valida contra esquema</div>';
    }

}