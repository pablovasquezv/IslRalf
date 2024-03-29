<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Region extends ORM {
    protected $_table_name = 'region';
    protected $_primary_key = 'id';
    
    public static $id = 'id';
    public static $nombre = 'nombre';


    protected $column_alias = array(        
        'id' => 'id',
        'nombre' => 'nombre'        
    );           
}
