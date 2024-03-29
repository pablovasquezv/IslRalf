<?php
	/**
	 * 
	 * SIAP
	 * BinaryBag 2011
	 * 
	 **/

defined('SYSPATH') OR die('No direct script access.');

class Model_Xmlstring extends ORM {
    protected $_table_name = 'xmlstring';
    protected $_primary_key = 'XMLSTRING_ID';

    public static $xmlstring_id = 'XMLSTRING_ID';
    public static $xmlstring = 'XMLSTRING';

    protected $column_alias = array(
        'xmlstring_id' => 'XMLSTRING_ID',
        'xmlstring' => 'XMLSTRING',
        'zona_g' => 'ZONA_G'
    );

    protected $_has_one = array(        
        'xml' => array(
            'model' => 'xml',
            'foreign_key' => 'XMLSTRING_ID',
        ),
    );
    
}
