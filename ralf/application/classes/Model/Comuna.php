<?php

defined('SYSPATH') OR die('No direct script access.');

class Model_Comuna extends ORM {

    protected $_table_name = 'comuna';
    protected $_primary_key = "id";
    protected $_belongs_to = array(
        'region' => array(
            'model' => 'Region',
            'foreign_key' => 'region_id',
        ),
    );

    const EXTRANJERO = '99999';

    public static function obtener() {
        $lista = array('' => 'seleccionar');
        $all = ORM::factory('Comuna')->find_all();
                
        //echo Database::instance()->last_query;
        //die();
        foreach ($all as $a)
            $lista[$a->id] = $a->nombre;
        return $lista;
    }

}
