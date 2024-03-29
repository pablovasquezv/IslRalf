<?php defined('SYSPATH') OR die('No direct script access.');

class Model_ArbolCausasNodo extends ORM {
    protected $_table_name = 'arbol_causas_nodo';
    protected $_primary_key = 'id';

    

    protected $_belongs_to = array(
        'arbol_causas' => array(
            'model' => 'ArbolCausas',
            'foreign_key' => 'arbol_id',
        ),
        'causa144' => array(
            'model' => 'Causa144',
            'foreign_key' => 'causa_id',
        ),
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
        
        if($this->ESTADO==1 || $this->ESTADO==2 || $this->ESTADO==6) {
            if($this->TPXML_ID==14) {
                $retorno=Html::anchor("documento/ralf3/{$this->XML_ID}", 'Ver');    
            }elseif($this->TPXML_ID==15) {
                $retorno=Html::anchor("documento/ralf4/{$this->XML_ID}", 'Ver');    
            }elseif($this->TPXML_ID==16) {
                $retorno=Html::anchor("documento/ralf5/{$this->XML_ID}", 'Ver');    
            }elseif($this->TPXML_ID == 143){
                $retorno=Html::anchor("documento/ralfInvestigacion/{$this->XML_ID}", 'Ver');    
            }
            else {
                $retorno=Html::anchor("documento/ver/{$this->XML_ID}", 'Ver');    
            }            
        } else {
            if($this->TPXML_ID==12) {
                $retorno=Html::anchor("ralf/crear/{$this->XML_ID}", 'Completar Ralf1');
            }elseif($this->TPXML_ID==13) {
                $retorno=Html::anchor("ralf2/crear/{$this->XML_ID}", 'Completar Ralf2');
            }elseif($this->TPXML_ID==14) {
                $retorno=Html::anchor("ralf3/crear/{$this->XML_ID}", 'Completar Ralf3');
            }elseif($this->TPXML_ID==15) {
                $retorno=Html::anchor("ralf4/crear/{$this->XML_ID}", 'Completar Ralf4');
            }elseif($this->TPXML_ID==16) {
                $retorno=Html::anchor("ralf5/crear/{$this->XML_ID}", 'Completar Ralf5');
            }elseif ($this->TPXML_ID==141) {
                $retorno=Html::anchor("ralfAccidente/crear/{$this->XML_ID}", 'Completar Ralf Accidente');
            }elseif ($this->TPXML_ID==142) {
                $retorno=Html::anchor("ralfMedidas/crear/{$this->XML_ID}", 'Completar Ralf Medidas');
            }elseif ($this->TPXML_ID==143) {
                $retorno=Html::anchor("ralfInvestigacion/crear/{$this->XML_ID}", 'Completar Ralf Investigacion');
            } elseif ($this->TPXML_ID==144) {
                $retorno=Html::anchor("ralfCausas/crear/{$this->XML_ID}", 'Completar Ralf Causas');
            }           
        }
        return $retorno;
    }

    public function ver_admin() {
        
        if($this->ESTADO==6) {
            if($this->TPXML_ID==12) {
                $retorno=Html::anchor("validar/ralf1/{$this->XML_ID}", 'Ir a validar Ralf1');
            }elseif($this->TPXML_ID==13) {
                $retorno=Html::anchor("validar/ralf2/{$this->XML_ID}", 'Ir a validar Ralf2');
            }elseif($this->TPXML_ID==14) {
                $retorno=Html::anchor("validar/ralf3/{$this->XML_ID}", 'Ir a validar Ralf3');
            }elseif($this->TPXML_ID==15) {
                $retorno=Html::anchor("validar/ralf4/{$this->XML_ID}", 'Ir a validar Ralf4');
            }elseif($this->TPXML_ID==16) {
                $retorno=Html::anchor("validar/ralf5/{$this->XML_ID}", 'Ir a validar Ralf5');
            }elseif ($this->TPXML_ID==141) {
                $retorno=Html::anchor("validar/ralfAccidente/{$this->XML_ID}", 'Ir a validar Ralf Accidente');
            }elseif ($this->TPXML_ID==142) {
                $retorno=Html::anchor("validar/ralfMedidas/{$this->XML_ID}", 'Ir a validar Ralf Medidas');
            }elseif ($this->TPXML_ID==143) {
                $retorno=Html::anchor("validar/ralfInvestigacion/{$this->XML_ID}", 'Ir a validar Ralf Investigacion');
            } 

        }elseif($this->ESTADO==5) {
            $retorno="No disponible";
        }else {
            if($this->TPXML_ID==14) {
                $retorno=Html::anchor("documento/ralf3/{$this->XML_ID}", 'Ver');    
            }elseif($this->TPXML_ID==15) {
                $retorno=Html::anchor("documento/ralf4/{$this->XML_ID}", 'Ver');    
            }elseif($this->TPXML_ID==16) {
                $retorno=Html::anchor("documento/ralf5/{$this->XML_ID}", 'Ver');    
            }elseif($this->TPXML_ID==143){
                $retorno=Html::anchor("documento/ralfInvestigacion/{$this->XML_ID}", 'Ver');
            }else {
                $retorno=Html::anchor("documento/ver/{$this->XML_ID}", 'Ver');    
            }
        }
        return $retorno;
    }
}
