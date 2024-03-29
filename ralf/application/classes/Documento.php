<?php

defined('SYSPATH') or die('No direct script access.');

class Documento {

    // de http://stackoverflow.com/questions/6694956/how-do-you-rename-a-tag-in-simplexml-through-a-dom-object
    public static function clonishNode(DOMNode $oldNode, $newName, $newNS = null) {
        if (isset($newNS)) {
            $newNode = $oldNode->ownerDocument->createElementNS($newNS, $newName);
        } else {
            $newNode = $oldNode->ownerDocument->createElement($newName);
        }
        foreach ($oldNode->attributes as $attr) {
            $newNode->appendChild($attr->cloneNode());
        }
        foreach ($oldNode->childNodes as $child) {
            $newNode->appendChild($child->cloneNode(true));
        }

        $oldNode->parentNode->replaceChild($newNode, $oldNode);
    }

    public static function zona_o($xml) {
        $xml = simplexml_load_string($xml);
        $old_zona_o = $xml->ZONA_O->asXML();
        $new_zona_o = '<ZONA_O>
            <seguridad>
                <Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
                    <SignedInfo>
                        <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
                        <SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
                        <Reference URI="#z_padre">
                            <Transforms>
                                <Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                            </Transforms>
                            <DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>
                            <DigestValue/>
                        </Reference>
                    </SignedInfo>
                    <SignatureValue/>
                    <KeyInfo>
                        <KeyValue>
                        <RSAKeyValue>
                          <Modulus></Modulus>
                          <Exponent></Exponent>
                        </RSAKeyValue>
                      </KeyValue>
                        <X509Data>
                            <X509Certificate/>
                        </X509Data>
                    </KeyInfo>
                </Signature>
            </seguridad>
        </ZONA_O>';
        $xmlstring = $xml->asXML();
        $return = str_replace($old_zona_o, $new_zona_o, $xmlstring);
        return $return;
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
        $tag_documento_identidad->appendChild($dom->ownerDocument->createElement('identificador', strtoupper($xml_ralf->ZONA_C->empleado->trabajador->rut)));
        

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

        $tag_rut = $dom->ownerDocument->createElement('rut', strtoupper($xml_ralf->ZONA_C->empleado->trabajador->documento_identidad->identificador));


        $dom->insertBefore($tag_rut, $nodoReferencia);
        $dom->removeChild($nodoReferencia);

        return $xml_ralf;
    }
}

?>