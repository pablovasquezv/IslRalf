<?php
	/**
	 * 
	 * SIAP
	 * BinaryBag 2011
	 * 
	 **/
?>
<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Tipo_Evento extends ORM {
    protected $_table_name = 'tipo_evento';
    protected $_primary_key = "TIPO_EVENTO_ID";

    public static $tipo_evento_id = 'TIPO_EVENTO_ID';
    public static $descripcion = 'DESCRIPCION';
    
    protected $column_alias = array(
        'tipo_evento_id' => 'TIPO_EVENTO_ID',
        'descripcion' => 'DESCRIPCION'
    );
    
    
    const TIPO_ENFERMEDAD = 1;
    const TIPO_ACCIDENTE_LABORAL = 2;
    const TIPO_ACCIDENTE_TRAYECTO = 3;

}
