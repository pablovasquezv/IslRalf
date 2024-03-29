<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Causa144 extends ORM {
    protected $_table_name = 'causa144';
    protected $_primary_key = 'id';

    public static function obtenerCodCausas(){
    	$causas=ORM::factory('Causa144')->find_all();
        $datos_causas=array();

        foreach ($causas as $causa)
            $lista[] = $causa->codigo;
        return $lista;
    }

}