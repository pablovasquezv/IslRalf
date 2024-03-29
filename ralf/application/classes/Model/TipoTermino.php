<?php defined('SYSPATH') OR die('No direct script access.');

class Model_TipoTermino extends ORM {
    protected $_table_name = 'tipo_termino';
    protected $_primary_key = 'ID_TIPO_TERMINO';    
    public static $id_tipo_termino = 'ID_TIPO_TERMINO';
    public static $descripcion = 'DESCRIPCION';
    public static $vigencia = 'VIGENCIA';

    protected $column_alias = array(        
        'ID_TIPO_TERMINO' => 'id_tipo_termino',
        'DESCRIPCION' => 'descripcion',  
        'VIGENCIA' => 'vigencia'      
    );           

    
}
