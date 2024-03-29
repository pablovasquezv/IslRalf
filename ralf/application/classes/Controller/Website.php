<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Website extends Controller_Auth {

    public function before() {
        parent::before();
        $this->check_auth();
        $this->config_ralf = Kohana::$config->load('ralf');
    }

    public function after() {

        parent::after();
    }

    protected function get_usuario() {
        return ORM::factory('User')->where("username", "=", Auth::instance()->get_user())->find();
    }

    protected function get_rol() {
        $user = $this->get_usuario();
        $rol = $user->roles->where('id', 'NOT IN', array(1))->find();
        return $rol->name;
    }

    protected function get_user_region() {
        $user = $this->get_usuario();
        $region = ORM::factory('Region', $user->region_id);
        return $region->nombre;
    }

    protected function save_file($file_add) {
        if (!Upload::valid($file_add) OR ! Upload::not_empty($file_add)) {
            return FALSE;
        }

        $directory = DOCROOT . 'uploads/';
        $file = NULL;

        try {
            $file = Upload::save($file_add, NULL, $directory);
            $file_chunks = explode('/', $file);
            return array_pop($file_chunks);
        } catch (Kohana_Exception $ke) {
            var_dump($ke->getMessage());
        }

        return FALSE;
    }

    protected function base64_file($path) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $base64 = base64_encode($data);
        return array(
            'base64' => $base64,
            'type' => $type
        );
    }

}

// End Controller_Website