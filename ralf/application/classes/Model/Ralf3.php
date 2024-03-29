<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Ralf3 extends ORM {
    protected $_table_name = 'ralf3';
    protected $_primary_key = 'id';

    protected $_belongs_to = array(
        'xml' => array(
            'model' => 'Xml',
            'foreign_key' => 'xml_id',
        ),
    );

}
