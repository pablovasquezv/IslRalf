<?php
defined('SYSPATH') or die('No direct script access.');

class Firmar {

    public static function firmar_xml_ralf($xml, $tipo_doc) {
        $tipo_ralf = Firmar::tipo_ralf($tipo_doc);
        $folio = (string) $xml->ZONA_A->documento->folio;
        $ruta_cert = dirname(__FILE__) . '/../certificados';
        $ruta_tmp = dirname(__FILE__) . '/../../tmp';
        $xml->asXML("$ruta_tmp/parafirmar_$folio.xml");
        $parafirmar = $ruta_tmp . '/parafirmar_' . $folio . '.xml';
        $firmado = $ruta_tmp . '/firmado_' . $folio . '.xml';
        chmod($parafirmar, 0777);

        $e = 'xmlsec1 --sign --id-attr ' . $tipo_ralf . ' --privkey-pem ' . $ruta_cert . '/ca-privkey.pem,' . $ruta_cert . '/ca-cert.pem --output ' . $firmado . '  --pwd 123isl ' . $parafirmar;
        exec($e);
        $consolidado = dirname(__FILE__) . '/../../consolidados/' . $folio . '.xml';
        if (is_file($consolidado)) {
            unlink($consolidado);
        }
        //echo "<br>dirname(__FILE__) : ".dirname(__FILE__);
        //echo "<br>parafirmar : ".$parafirmar;
        //echo "<br>consolidado : ".$consolidado;
        //echo "<br>firmado : ".$firmado;
        //echo $e;
        //die();

        rename($firmado, $consolidado);
        unlink($parafirmar);
        $final = simplexml_load_file($consolidado)->asXML();
        return $final;
    }

    public static function tipo_ralf($tipo_doc) {

        switch ($tipo_doc) {
            case 12:
            case 141:
                $r = 'RALF_Accidente';
                break;
            case 13:
            case 142:
                $r = 'RALF_Medidas';
                break;
            case 143:
                $r = 'RALF_Investigacion';
                break;
            case 144:
                $r = 'RALF_Causas';
                break;
            case 145:
                $r = 'RALF_Prescripcion';
                break;
            case 146:
                $r = 'RALF_Verificacion';
                break;
            case 147:
                $r = 'RALF_Notificacion';
                break;
            case 148:
                $r = 'RALF_Recargo_tasa';
                break;
            case 14:
                $r = 'ralf3';
                break;
            case 15:
                $r = 'ralf4';
                break;
            case 16:
                $r = 'ralf5';
                break;
            default:
                break;
        }

        return $r;
    }

}
?>
