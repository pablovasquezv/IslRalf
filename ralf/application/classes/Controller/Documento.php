<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Documento extends Controller_Website {

    public function action_ver() {
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'Error, Falta id de documento';
            $this->template->contenido = '';
            return;
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar documento';
            $this->template->contenido = '';
            return;
        }
        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        $zonas = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F', 'g' => 'G', 'h' => 'H', 'i' => 'I', 'j' => 'J', 'k' => 'K', 'l' => 'L', 'p' => 'P', 'q' => 'Q');

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $xml;

        $this->template->contenido = View::factory('documento/ver')
                ->set('pdf', false)
                ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
                ->set('estado_documento', $documento->estado_xml)
                ->set('xml', $xml)
                ->set('data', $data)
                ->set('zona', 'ZONA_')
                ->set('zonas', $zonas)
                ->set('tipo_doc', $documento->TPXML_ID)
                ->set('template', '/documento/zona/zona_')
                ->set('estado', $documento->ESTADO)
                ->set('caso_id',$documento->CASO_ID)
                ->set('rol_user',$this->get_rol())
                ->set('xml_id',$xml_id);
    }

    public function action_ralf3() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('documento/ralf3')
                ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
                ->set('estado_documento', $documento->estado_xml)
                ->set('xml', $xml)
                ->set('data', $data)            
                ->set('tipo_doc', $documento->TPXML_ID)                                    
                ->set('estado', $documento->ESTADO)
                ->set('caso_id',$documento->CASO_ID)            
                ->set('xml_id',$xml_id)
                ->set('rol_user',$this->get_rol());
    }

    public function action_ralf4() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('documento/ralf4')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)            
            ->set('xml_id',$xml_id)
            ->set('rol_user',$this->get_rol());    
    }
    public function action_ralf5() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('documento/ralf5')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)            
            ->set('xml_id',$xml_id)
            ->set('rol_user',$this->get_rol());
    }

    /* 
     * faillons 
     * Metodo para generar XML de ZONA_C 
     * Desde la estrucutra antigua a la Nueva 
     */    
 
    public static function transformarZonaCNueva($xml_ralf){
        $dom = dom_import_simplexml($xml_ralf->ZONA_C->empleado->trabajador); 
        $nodoReferencia = dom_import_simplexml($dom->getElementsByTagName('rut')->item(0));
 
        $tag_documento_identidad = $dom->ownerDocument->createElement('documento_identidad', '');
        $tag_documento_identidad->appendChild($dom->ownerDocument->createElement('origen_doc_identidad', 1));
        $tag_documento_identidad->appendChild($dom->ownerDocument->createElement('identificador', $xml_ralf->ZONA_C->empleado->trabajador->rut));
 
        $dom->insertBefore($tag_documento_identidad, $nodoReferencia);
        $dom->removeChild($nodoReferencia);
 
        return $xml_ralf;
    }

    /* 
     * faillons 
     * Metodo para generar XML de ZONA_C 
     * Desde la estructura nueva a la antigua 
     */    
 
    public static function transformarZonaCAntigua($xml_ralf){
        $dom = dom_import_simplexml($xml_ralf->ZONA_C->empleado->trabajador); 
        $nodoReferencia = dom_import_simplexml($dom->getElementsByTagName('documento_identidad')->item(0)); 
 
        $tag_rut = $dom->ownerDocument->createElement('rut', $xml_ralf->ZONA_C->empleado->trabajador->documento_identidad->identificador);
  
        $dom->insertBefore($tag_rut, $nodoReferencia); 
        $dom->removeChild($nodoReferencia); 
 
        return $xml_ralf; 
    }
 

    public function action_ralfInvestigacion() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('documento/ralfInvestigacion')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)            
            ->set('xml_id',$xml_id)
            ->set('rol_user',$this->get_rol());
    }

    public function action_ralfCausas() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('documento/ralfCausas')            
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)            
            ->set('xml_id',$xml_id)
            ->set('rol_user',$this->get_rol());
    }

    public function action_ralfPrescripcion() {        
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();                
        $data['xml'] = $xml;        
        $this->template->contenido = View::factory('documento/ralfPrescripcion')         
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)            
            ->set('tipo_doc', $documento->TPXML_ID)                                    
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)            
            ->set('xml_id',$xml_id)
            ->set('rol_user',$this->get_rol());
    }
 
    public function action_ralfVerificacion() {
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error='Error, Falta id de documento';
            $this->template->contenido='';
            return;
        }
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error='Error, Error al cargar documento';
            $this->template->contenido='';
            return;
        }
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        

        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $data['xml'] = $xml;
        $this->template->contenido = View::factory('documento/ralfVerificacion')
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)
            ->set('tipo_doc', $documento->TPXML_ID)
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('xml_id',$xml_id)
            ->set('xml_id_origen',$documento->XML_ID_ORIGEN)
            ->set('documento', $documento)
            ->set('rol_user',$this->get_rol());
    }
    
    public function action_ralfNotificacion() {
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'Error, Falta id de documento';
            $this->template->contenido = '';
            return;
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar documento';
            $this->template->contenido = '';
            return;
        }
        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $regiones = Utiles::regiones();
        $data['xml'] = $xml;
        $this->template->contenido = View::factory('documento/ralfNotificacion')
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)
            ->set('tipo_doc', $documento->TPXML_ID)
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('xml_id',$xml_id)
            ->set('xml_id_origen',$documento->XML_ID_ORIGEN)
            ->set('documento', $documento)
            ->set('rol_user',$this->get_rol())
            ->set('config_ralf', $this->config_ralf)
            ->set('regiones', $regiones)
            ;
    }

    public function action_ralfRecargoTasa() {
        $xml_id = $this->request->param('id');
        if (empty($xml_id) || !is_numeric($xml_id)) {
            $this->template->mensaje_error = 'Error, Falta id de documento';
            $this->template->contenido = '';
            return;
        }
        
        $documento = ORM::factory('Xml', $xml_id);
        if (!$documento->loaded()) {
            $this->template->mensaje_error = 'Error, Error al cargar documento';
            $this->template->contenido = '';
            return;
        }
        
        $xmlstring = $documento->xmlstring->XMLSTRING;
        $xml = simplexml_load_string($xmlstring);
        $this->template->titulo = __('Documento');
        
        $data = Utiles::dominios_comunes() + Utiles::dominios_codificacion();
        $regiones = Utiles::regiones();
        $data['xml'] = $xml;
        $this->template->contenido = View::factory('documento/ralfRecargoTasa')
            ->set('nombre_documento', $documento->tipo_xml->DESCRIPCION)
            ->set('estado_documento', $documento->estado_xml)
            ->set('xml', $xml)
            ->set('data', $data)
            ->set('tipo_doc', $documento->TPXML_ID)
            ->set('estado', $documento->ESTADO)
            ->set('caso_id',$documento->CASO_ID)
            ->set('xml_id',$xml_id)
            ->set('xml_id_origen',$documento->XML_ID_ORIGEN)
            ->set('documento', $documento)
            ->set('rol_user',$this->get_rol())
            ->set('config_ralf', $this->config_ralf)
            ->set('regiones', $regiones)
            ;
    }
}

// End Documento
