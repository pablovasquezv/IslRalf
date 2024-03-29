<?php

defined('SYSPATH') or die('No direct script access.');

class Suseso {

    public static function token_ws() {
        static $curr_token = NULL;
        static $last_request_time = NULL;
        if ($last_request_time !== NULL && time() - $last_request_time <= 30 * 60 && $curr_token !== NULL && strlen($curr_token) > 0) {
            return $curr_token;
        }

        $usuario = Kohana::$config->load('ws_suseso.usuario');
        $clave = Kohana::$config->load('ws_suseso.clave');
        $wsdl_token = Kohana::$config->load('ws_suseso.wsdl_token_prod');

        ini_set("soap.wsdl_cache_enabled", "0");
        $client = new SoapClient($wsdl_token);

        $parametros = array(
            'CtaUsr' => $usuario,
            'ClaveUsr' => $clave,
            'Funcion' => 'PT',
            'Token' => NULL
        );
        $token = $client->WSToken($parametros);
        $curr_token = $token->return;
        $last_request_time = time();
        return $curr_token;
    }

    public static function ingreso_ws($Xml, $TipoDoc, $CUN) {

        $wsdl_ingreso = Kohana::$config->load('ws_suseso.wsdl_ingreso_prod');

        $Token = self::token_ws();

        $CtaUsr = Kohana::$config->load('ws_suseso.usuario');
        $PswUsr = Kohana::$config->load('ws_suseso.clave');
        $Funcion = 'I';

        ini_set("soap.wsdl_cache_enabled", "0");
        $client = new SoapClient($wsdl_ingreso);

        $value = $client->IngresoDoc((string) $Token, (string) $CtaUsr, (string) $PswUsr, (string) $Funcion, (string) $TipoDoc, (string) $Xml, (string) $CUN);
        return $value;
    }

    public static function anular_ws($TipoDoc, $CUN, $Folio) {

        $wsdl_ingreso = Kohana::$config->load('ws_suseso.wsdl_ingreso_prod');

        $Token = self::token_ws();
        $CtaUsr = Kohana::$config->load('ws_suseso.usuario');
        $PswUsr = Kohana::$config->load('ws_suseso.clave');

        ini_set("soap.wsdl_cache_enabled", "0");
        $client = new SoapClient($wsdl_ingreso);

        $value = $client->AnulacionDoc((string) $Token, (string) $CtaUsr, (string) $PswUsr, (string) $TipoDoc, (string) $CUN, $Folio);
        return $value;
    }

}
