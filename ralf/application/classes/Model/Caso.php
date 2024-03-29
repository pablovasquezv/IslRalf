<?php

defined('SYSPATH') OR die('No direct script access.');

class Model_Caso extends ORM {

    protected $_table_name = 'caso';
    protected $_primary_key = "CASO_ID";

    public static $caso_id = 'CASO_ID';
    public static $tra_id= 'TRA_ID';
    public static $emp_id = 'EMP_ID';
    public static $caso_dtt_creac = 'CASO_DTT_CREAC';
    public static $caso_cun = 'CASO_CUN';
    public static $caso_tipo_evento = 'CASO_TIPO_EVENTO';
    public static $scrl_id = 'SCRL_ID';
    public static $region_id = 'REGION_ID';
    public static $estado_caso = 'ESTADO';

    protected $column_alias = array(
        'caso_id' => 'CASO_ID',
        'tra_id' => 'TRA_ID',
        'emp_id' => 'EMP_ID',
        'caso_dtt_creac' => 'CASO_DTT_CREAC',
        'caso_dtt_creac_edenuncia' => 'CASO_DTT_CREAC_EDENUNCIA',
        'caso_cun' => 'CASO_CUN',
        'caso_tipo_evento' => 'CASO_TIPO_EVENTO',
        'scrl_id' => 'SCRL_ID',
        'region_id' => 'REGION_ID',
        'estado_caso' => 'ESTADO'
    );

    protected $_has_many = array(
        'xmls' => array(
            'model' => 'Xml',
            'foreign_key' => 'CASO_ID',
        )
    );

    protected $_belongs_to = array(
        'trabajador' => array(
            'model' => 'Trabajador',
            'foreign_key' => 'TRA_ID',
        ),
        'empleador' => array(
            'model' => 'Empleador',
            'foreign_key' => 'EMP_ID',
        ),
        'tipo_evento' => array(
            'model' => 'Tipo_Evento',
            'foreign_key' => 'CASO_TIPO_EVENTO',
        ),
        'region' => array(
            'model' => 'Region',
            'foreign_key' => 'REGION_ID',
        ),
    );




    public function ultimo_tipo_documento() {
        $r=$this->xmls->where('estado', 'NOT IN', array(3))->order_by('XML_ID', 'desc')->limit(1)->find();     
        return $r->TPXML_ID;
    }

    public function estado_ultimo_documento() {
        $r=$this->xmls->order_by('XML_ID', 'desc')->limit(1)->find();        
        return "{$r->tipo_xml->NOMBRE} ({$r->estado_xml->DESCRIPCION})";
    } 

    public function ultimo_documento() {        
        return $this->xmls->where('estado', 'NOT IN', array(3))->order_by('XML_ID', 'desc')->limit(1)->find();
    }
}
