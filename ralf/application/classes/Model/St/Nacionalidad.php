<?php defined('SYSPATH') OR die('No direct script access.');

class Model_St_Nacionalidad extends ORM {
    protected $_table_name = 'st_nacionalidades';
    protected $_primary_key = 'id';

	public static function obtener() {
        $lista = array('' => 'seleccionar');
        $all = ORM::factory('St_Nacionalidad')->order_by('nombre','ASC')->find_all();
                
        //echo Database::instance()->last_query;
        //die();
        foreach ($all as $a)
            $lista[$a->codigo] = $a->nombre;
        return $lista;
    }

}
