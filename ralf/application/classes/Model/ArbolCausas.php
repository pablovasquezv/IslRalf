<?php defined('SYSPATH') OR die('No direct script access.');

class Model_ArbolCausas extends ORM {
    protected $_table_name = 'arbol_causas';
    protected $_primary_key = 'arbol_id';

    

    protected $_belongs_to = array(
        'xml' => array(
            'model' => 'Xml',
            'foreign_key' => 'XML_ID',
        ),
    );
    
    protected $_has_many = array(        
        'arbol_causas_nodo' => array(
            'model' => 'ArbolCausasNodo',
            'foreign_key' => 'arbol_id',
        )
    );
}
