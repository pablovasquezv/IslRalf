<?php
	/**
	 * 
	 * SIAP
	 * BinaryBag 2011
	 * 
	 **/
?>
<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Tipo_Xml extends ORM {
    protected $_table_name = 'tipo_xml';
    protected $_primary_key = "TPXML_ID";

    public static $tpxml_id = 'TPXML_ID';
    public static $nombre = 'NOMBRE';
    public static $xmlstring = 'XMLSTRING';
    public static $descripcion = 'DESCRIPCION';

    protected $column_alias = array(
        'tpxml_id' => 'TPXML_ID',
        'nombre' => 'NOMBRE',
        'xmlstring' => 'XMLSTRING',
        'descripcion'=>'DESCRIPCION'
    );
    
    const DIAT_OA  = 1;
    const DIEP_OA  = 2;
    const DIAT_EM  = 3;
    const DIEP_EM  = 4;
    const DIAT_OT  = 5;
    const DIEP_OT  = 6;
    const RECA     = 7;
    const RELA     = 8;
    const ALLA     = 9;
    const ALME     = 10;

    protected $_has_many = array(
        'xmls' => array(
            'model' => 'Xml',
            'foreign_key' => 'TPXML_ID',
        )
    );

    public static function arreglo_codigos_denuncia() {
        return array(
            self::DIAT_OA,
            self::DIEP_OA,
            self::DIAT_EM,
            self::DIEP_EM,
            self::DIAT_OT,
            self::DIEP_OT,
        );
    }
}
