<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Ws extends Controller {

    public function action_index() {
        /*
        echo "TEST WS TOKEN ";
        $token=Suseso::token_ws();    
        var_dump($token);
        die();
        */
        /*
        $wsdl_ingreso = Kohana::$config->load('ws_suseso.wsdl_ingreso_prod');                
        
        ini_set("soap.wsdl_cache_enabled", "0");
        $client = new SoapClient($wsdl_ingreso);
        var_dump($client->__getFunctions());
        */
    }
}
