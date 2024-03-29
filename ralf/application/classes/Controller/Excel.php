<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Excel extends Controller_Website {

    public function action_ralf1() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('Ralf1')->where('tipo_xml', 'IS', NULL)->find_all();
        $view = View::factory('excel/ralf1')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralf2() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('Ralf2')->where('tipo_xml', 'IS', NULL)->find_all();
        $view = View::factory('excel/ralf2')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralf3() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('Ralf3')->where('tipo_xml', 'IS', NULL)->find_all();
        $view = View::factory('excel/ralf3')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralf4() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('Ralf4')->find_all();
        $view = View::factory('excel/ralf4')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralf5() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('Ralf5')->find_all();
        $view = View::factory('excel/ralf5')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralfAccidente() {
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('Ralf1')->where('tipo_xml', '=', 141)->find_all();
        
        //XMLS
        /*foreach ($ralfs as $ralf) {
            $xmls[] = ORM::factory('xmlstring')->where('XMLSTRING_ID', '=', $ralf->XMLSTRING_ID)->find();
        }*/
        //print_r($xmls);die();
        $view = View::factory('excel/ralfAccidente')
                ->set('data', $data)
                ->set('ralfs', $ralfs)
                ;
        $this->response->body($view);
    }

    public function action_ralfMedidas() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('Ralf2')->where('tipo_xml', '=', 142)->find_all();
        $view = View::factory('excel/ralfMedidas')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralfInvestigacion() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('Ralf3')->where('tipo_xml', '=', 143)->find_all();
        $view = View::factory('excel/ralfInvestigacion')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralfCausas() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('RalfCausas')->find_all();
        $view = View::factory('excel/ralfCausas')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralfPrescripcion() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('RalfPrescripcion')->find_all();
        $view = View::factory('excel/ralfPrescripcion')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralfVerificacion() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('RalfVerificacion')->find_all();
        $view = View::factory('excel/ralfVerificacion')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

    public function action_ralfNotificacion() {
        if ($this->get_rol() != 'admin') {
            $this->redirect("error");
        }

        $this->auto_render = FALSE;
        $ralfs = ORM::factory('RalfNotificacion')->find_all();
        //console.log("ralfs = "+$ralfs);
        $view = View::factory('excel/ralfNotificacion')->set('ralfs', $ralfs);
        $this->response->body($view);
    }

}
