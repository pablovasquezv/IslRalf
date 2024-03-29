<?php defined('SYSPATH') OR die('No direct script access.');

class Model_RalfRecargoTasa extends ORM {
    protected $_table_name = 'ralfRecargoTasa';
    protected $_primary_key = 'id_recargoTasa';
    

    protected $_belongs_to = array(
        'xml' => array(
            'model' => 'Xml',
            'foreign_key' => 'xml_id',
        ),
    );

}