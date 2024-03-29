<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Xml extends ORM {
    protected $_table_name = 'xml';
    protected $_primary_key = 'XML_ID';

    public static $xml_id = 'XML_ID';
    public static $xmlstring_id = 'XMLSTRING_ID';
    public static $fecha_creacion = 'FECHA_CREACION';
    public static $estado = 'ESTADO';
    public static $caso_id = 'CASO_ID';
    public static $tpxml_id='TPXML_ID';

    protected $column_alias = array(
        'xml_id' => 'XML_ID',        
        'xmlstring_id' => 'XMLSTRING_ID',
        'fecha_creacion' => 'FECHA_CREACION',
        'estado' => 'ESTADO',
        'caso_id' => 'CASO_ID',
        'tpxml_id'=>'TPXML_ID',
        'valido' => 'VALIDO'
    );


    protected $_belongs_to = array(
        'tipo_xml' => array(
            'model' => 'Tipo_Xml',
            'foreign_key' => 'TPXML_ID',
        ),
        'caso' => array(
            'model' => 'Caso',
            'foreign_key' => 'CASO_ID',
        ),
        'estado_xml' => array(
            'model' => 'Estado_Xml',
            'foreign_key' => 'ESTADO',
        ),
        'xmlstring' => array(
            'model' => 'Xmlstring',
            'foreign_key' => 'XMLSTRING_ID',
        ),
    );
    
    protected $_has_many = array(        
        'intento_envios' => array(
            'model' => 'Intento_Envios',
            'foreign_key' => 'XML_ID',
        )
    );

    public function codigo_retorno_mas_reciente() {
        $orm =  $this->intento_envios->order_by('FECHA', 'DESC')->limit(1);
        $intento = $orm->find();
        //echo $orm->last_query();

        return $intento->codigo_retorno;
    }

    public function fecha_ultimo_intento_envio() {
        $orm =  $this->intento_envios->order_by('FECHA', 'DESC')->limit(1);
        $intento = $orm->find();
        //echo $orm->last_query();

        return $intento->FECHA;
    }    

    public function validar_contra_schema() {
        return Utiles::valida_xml($this->xmlstring->xmlstring, dirname(__FILE__) . '/../../../media/xsd/' . $this->tipo_xml->XSD);
    }
    public static function sacar_cun_a_xmldom($xml){
        $simplexml = simplexml_load_string($xml);
        $dom = dom_import_simplexml($simplexml);
        $elem_documento = $dom->getElementsByTagName('ZONA_A')->item(0)->getElementsByTagName('documento')->item(0);
        $cun = $dom->getElementsByTagName('ZONA_A')->item(0)->getElementsByTagName('documento')->item(0)->getElementsByTagName('cun')->item(0);
        if($cun){
            $log = Log::instance();
            $log->add(Log::INFO, "Sacando el cun".var_dump($cun->textContent));
            $cun->parentNode->removeChild($cun);
        }
        return $dom->ownerDocument->saveXML();
    }

    public static function poner_cun_a_xmldom(&$dom, $cun) {
        $elem_documento = $dom->getElementsByTagName('ZONA_A')->item(0)->getElementsByTagName('documento')->item(0);
        $elem_documento->insertBefore($dom->ownerDocument->createElement('cun', $cun), $elem_documento->firstChild);
    }

    public static function poner_cun_a_xml($xml, $cun) {
        $simplexml = simplexml_load_string($xml);
        $dom = dom_import_simplexml($simplexml);
        self::poner_cun_a_xmldom($dom, $cun);
        return $dom->ownerDocument->saveXML();
    }

    public function obtener_folio() {
        $simplexml = simplexml_load_string($this->xmlstring->xmlstring);
        return (string)$simplexml->ZONA_A->documento->folio;
    }
    
    public function ver() {
        
        if($this->ESTADO == 1 || $this->ESTADO == 2 || $this->ESTADO == 6 || $this->ESTADO == 3) {
            if($this->TPXML_ID == 14) {
                $retorno=Html::anchor("documento/ralf3/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 15) {
                $retorno=Html::anchor("documento/ralf4/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 16) {
                $retorno=Html::anchor("documento/ralf5/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 143) {
                $retorno=Html::anchor("documento/ralfInvestigacion/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 144) {
                $retorno=Html::anchor("documento/ralfCausas/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 145) {
                $retorno=Html::anchor("documento/ralfPrescripcion/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 146) {
                $retorno=Html::anchor("documento/ralfVerificacion/{$this->XML_ID}", 'Ver');   
            } elseif($this->TPXML_ID == 147) {
                $retorno=Html::anchor("documento/ralfNotificacion/{$this->XML_ID}", 'Ver');   
            }elseif($this->TPXML_ID == 148) {
                $retorno=Html::anchor("documento/ralfRecargoTasa/{$this->XML_ID}", 'Ver');   
            } else {
                $retorno=Html::anchor("documento/ver/{$this->XML_ID}", 'Ver');    
            }            
        } else {
            if($this->TPXML_ID == 12) {
                $retorno=Html::anchor("ralf/crear/{$this->XML_ID}", 'Completar Ralf1');
            } elseif($this->TPXML_ID == 13) {
                $retorno=Html::anchor("ralf2/crear/{$this->XML_ID}", 'Completar Ralf2');
            } elseif($this->TPXML_ID == 14) {
                $retorno=Html::anchor("ralf3/crear/{$this->XML_ID}", 'Completar Ralf3');
            } elseif($this->TPXML_ID == 15) {
                $retorno=Html::anchor("ralf4/crear/{$this->XML_ID}", 'Completar Ralf4');
            } elseif($this->TPXML_ID == 16) {
                $retorno=Html::anchor("ralf5/crear/{$this->XML_ID}", 'Completar Ralf5');
            } elseif ($this->TPXML_ID == 141) {
                $retorno=Html::anchor("ralfAccidente/crear/{$this->XML_ID}", 'Completar Ralf Accidente');
            } elseif ($this->TPXML_ID == 142) {
                $retorno=Html::anchor("ralfMedidas/crear/{$this->XML_ID}", 'Completar Ralf Medidas');
            } elseif ($this->TPXML_ID == 143) {
                $retorno=Html::anchor("ralfInvestigacion/crear/{$this->XML_ID}", 'Completar Ralf Investigación');
            } elseif ($this->TPXML_ID == 144) {
                $retorno=Html::anchor("ralfCausas/crear/{$this->XML_ID}", 'Completar Ralf Causas');
            } elseif ($this->TPXML_ID == 145) {
                $retorno=Html::anchor("ralfPrescripcion/crear/{$this->XML_ID}", 'Completar Ralf Prescripción');
            } elseif ($this->TPXML_ID == 146) {
                $retorno=Html::anchor("ralfVerificacion/verificar/{$this->XML_ID}", 'Completar Ralf Verificación');
            } elseif ($this->TPXML_ID == 147) {
                $retorno=Html::anchor("ralfNotificacion/crear/{$this->XML_ID}", 'Completar Ralf Notificación');
            } elseif ($this->TPXML_ID == 148) {
                $retorno=Html::anchor("ralfRecargoTasa/crear/{$this->XML_ID}", 'Completar Ralf Recargo Tasa');
            }          
        }
        return $retorno;
    }

    public function ver_admin() {
        if($this->ESTADO == 6) {
            if($this->TPXML_ID == 12) {
                $retorno=Html::anchor("validar/ralf1/{$this->XML_ID}", 'Ir a validar Ralf1');
            } elseif($this->TPXML_ID == 13) {
                $retorno=Html::anchor("validar/ralf2/{$this->XML_ID}", 'Ir a validar Ralf2');
            } elseif($this->TPXML_ID == 14) {
                $retorno=Html::anchor("validar/ralf3/{$this->XML_ID}", 'Ir a validar Ralf3');
            } elseif($this->TPXML_ID == 15) {
                $retorno=Html::anchor("validar/ralf4/{$this->XML_ID}", 'Ir a validar Ralf4');
            } elseif($this->TPXML_ID == 16) {
                $retorno=Html::anchor("validar/ralf5/{$this->XML_ID}", 'Ir a validar Ralf5');
            } elseif ($this->TPXML_ID == 141) {
                $retorno=Html::anchor("validar/ralfAccidente/{$this->XML_ID}", 'Ir a validar Ralf Accidente');
            } elseif ($this->TPXML_ID == 142) {
                $retorno=Html::anchor("validar/ralfMedidas/{$this->XML_ID}", 'Ir a validar Ralf Medidas');
            } elseif ($this->TPXML_ID == 143) {
                $retorno=Html::anchor("validar/ralfInvestigacion/{$this->XML_ID}", 'Ir a validar Ralf Investigación');
            } elseif ($this->TPXML_ID == 144) {
                $retorno=Html::anchor("validar/ralfCausas/{$this->XML_ID}", 'Ir a validar Ralf Causas');
            } elseif ($this->TPXML_ID == 145) {
                $retorno=Html::anchor("validar/ralfPrescripcion/{$this->XML_ID}", 'Ir a validar Ralf Prescripción');
            } elseif ($this->TPXML_ID == 146) {
                $retorno=Html::anchor("validar/ralfVerificacion/{$this->XML_ID}", 'Ir a validar Ralf Verificación');
            } elseif ($this->TPXML_ID == 147) {
                $retorno=Html::anchor("validar/ralfNotificacion/{$this->XML_ID}", 'Ir a validar Ralf Notificación');
            } elseif ($this->TPXML_ID == 148) {
                $retorno=Html::anchor("validar/ralfRecargoTasa/{$this->XML_ID}", 'Ir a validar Ralf Recargo Tasa');
            }
        } elseif($this->ESTADO == 5) {
            $retorno = "No disponible";
        } else {
            if($this->TPXML_ID == 14) {
                $retorno=Html::anchor("documento/ralf3/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 15) {
                $retorno=Html::anchor("documento/ralf4/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 16) {
                $retorno=Html::anchor("documento/ralf5/{$this->XML_ID}", 'Ver');
            } elseif($this->TPXML_ID == 143){
                $retorno=Html::anchor("documento/ralfInvestigacion/{$this->XML_ID}", 'Ver');
            } elseif ($this->TPXML_ID == 144) {
                $retorno=Html::anchor("documento/ralfCausas/{$this->XML_ID}", 'Ver');
            } elseif ($this->TPXML_ID == 145) {
                $retorno=Html::anchor("documento/ralfPrescripcion/{$this->XML_ID}", 'Ver');
            } elseif ($this->TPXML_ID == 146) {
                $retorno=Html::anchor("documento/ralfVerificacion/{$this->XML_ID}", 'Ver');
            } elseif ($this->TPXML_ID == 147) {
                $retorno=Html::anchor("documento/ralfNotificacion/{$this->XML_ID}", 'Ver');
            } elseif ($this->TPXML_ID == 148) {
                $retorno=Html::anchor("documento/ralfRecargoTasa/{$this->XML_ID}", 'Ver');
            } else {
                $retorno=Html::anchor("documento/ver/{$this->XML_ID}", 'Ver');
            }
        }
        return $retorno;
    }
}
