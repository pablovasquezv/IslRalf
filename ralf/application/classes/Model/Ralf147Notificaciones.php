<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Ralf147Notificaciones extends ORM {
    protected $_table_name = 'ralf147_notificaciones';
    protected $_primary_key = 'id';
    
    protected $_belongs_to = array(
        'xml' => array(
            'model' => 'Xml',
            'foreign_key' => 'XML_ID',
        ),
    );
}