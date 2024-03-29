<?php defined('SYSPATH') OR die('No direct script access.');

class Model_St_Ciuo extends ORM {
    protected $_table_name = 'st_ciuos';
    protected $_primary_key = 'id';

    public static function obtener() {
        $lista = array('' => 'seleccionar');
        $all = ORM::factory('St_Ciuo')->order_by('nombre','ASC')->find_all();
                
        //echo Database::instance()->last_query;
        //die();
        foreach ($all as $a)
            $lista[$a->codigo] = $a->nombre;
        return $lista;
    }

}
