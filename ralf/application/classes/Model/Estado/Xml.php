<?php
	/**
	 * 
	 * SIAP
	 * BinaryBag 2011
	 * 
	 **/
?>
<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Estado_Xml extends ORM {
    protected $_table_name = 'estado_xml';
    protected $_primary_key = "ESTADO_ID";

    public static $estado_oda_id = 'ESTADO_ID';
    public static $descripcion = 'DESCRIPCION';
    
    const ENVIADO = 1;
    const PENDIENTE_ENVIO = 2;
    const ANULADO = 3;
    const RECA_EN_CONSTRUCCION = 4;
    const INCOMPLETO = 5;

    protected $column_alias = array(
        'estado_id' => 'ESTADO_ID',
        'descripcion' => 'DESCRIPCION'
    );

    public function es_anulado() {
        return $this->ESTADO_ID == Model_Estado_Xml::ANULADO;
    }
}
