<?php defined('SYSPATH') OR die('No direct script access.');

class Model_St_Comuna extends ORM {
    protected $_table_name = 'st_comunas';
    protected $_primary_key = 'id';

    /*
     * Metodo para obtener solo las comunas activas
     */
    public static function obtener() {
        $lista = array('' => 'seleccionar');
        $all = ORM::factory('St_Comuna')->where('estado','=',1)->order_by('nombre','ASC')->find_all();
        //$all = ORM::factory('St_Comuna')->order_by('nombre','ASC')->find_all();
                
        //echo Database::instance()->last_query;
        //die();
        foreach ($all as $a)
            $lista[$a->codigo] = $a->nombre;
        return $lista;
    }

    public static function obtenerSinFiltro() {
        $lista = array('' => 'seleccionar');
        $all = ORM::factory('St_Comuna')->order_by('nombre','ASC')->find_all();
                
        //echo Database::instance()->last_query;
        //die();
        foreach ($all as $a)
            $lista[$a->codigo] = $a->nombre;
        return $lista;
    }

    /*
     * metodo usado en ralfPrescripcion para mapear valor de comunas
     * usando la glosa como llave
     */
    public static function obtenerSinFiltroLlaveGlosa() {
        //$lista = array('' => 'seleccionar');
        $all = ORM::factory('St_Comuna')->order_by('nombre','ASC')->find_all();
                
        //echo Database::instance()->last_query;
        //die();
        foreach ($all as $a)
            $lista[$a->nombre] = $a->codigo;
        return $lista;
    }

}
