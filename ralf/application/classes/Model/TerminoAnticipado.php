<?php

defined('SYSPATH') OR die('No direct script access.');

class Model_TerminoAnticipado extends ORM {    
    
    protected $_table_name = 'termino_anticipado';
    protected $_primary_key = 'TERMINO_ANTICIPADO_ID';    
    public static $id_termino_anticipado = 'ID_TERMINO_ANTICIPADO';
    public static $caso_id = 'CASO_ID';
    public static $id_tipo_termino = 'ID_TIPO_TERMINO';


    protected $column_alias = array(        
        'id_termino_anticipado' => 'ID_TERMINO_ANTICIPADO',
        'caso_id' => 'CASO_ID'       ,
        'id_tipo_termino' => 'ID_TIPO_TERMINO'
    );

    public function __toString ()
    {
        return $this->title;
    }
}
