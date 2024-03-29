<?php
    /**
     * 
     * SIAP
     * BinaryBag 2011
     * 
     **/

defined('SYSPATH') OR die('No direct script access.');

class Model_Tipo_Notificacion extends ORM {
    protected $_table_name = 'tipo_notificacion';
    protected $_primary_key = "TIPO_NOT_ID";

    public static function tipos_array() {
        $a = array();
        $orm = ORM::factory('tipo_notificacion');

        foreach($orm->find_all() as $tipo) {
            $a[$tipo->TIPO_NOT_ID] = $tipo->TIPO_NOT_DESCRIPCION;
        }

        return $a;
    }
}