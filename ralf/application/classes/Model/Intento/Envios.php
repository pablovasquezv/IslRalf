<?php
	/**
	 * 
	 * SIAP
	 * BinaryBag 2011
	 * 
	 **/
?>
<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Intento_Envios extends ORM {
    protected $_table_name = 'intento_envios';
    protected $_primary_key = 'INTENTO_ID';

    protected $_belongs_to = array(
        'xml' => array(
            'model' => 'Xml',
            'foreign_key' => 'XML_ID',
        )
    );

    public function numero_intentos()
    {
        return ORM::factory('intento_envios')->where('XML_ID','=',$this->XML_ID)->count_all();
    }
}
