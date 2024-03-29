<?php

defined('SYSPATH') or die('No direct script access.');

class Task_Envio extends Minion_Task {

    protected $_options = array(
        'nombre_parametro' => NULL
    );

    /**
     * This is a demo task
     * php index.php --task=envio --nombre_parametro=roles
     * @return null
     */
    protected function _execute(array $params) {
        $nombre_parametro = $params['nombre_parametro'];

        $documentos = ORM::factory('Xml')
                ->where('ESTADO', '=', 2)
                ->where('VALIDO', '=', 1)
                ->and_where_open()
                ->where('codigo_retorno', '<>', '-40')
                ->or_where('codigo_retorno', 'IS', NULL)
                ->and_where_close()
                ->find_all();
        
        //echo Database::instance()->last_query."\n";
        echo "INICIO ".date('Y-m-d H:i:s')."\n";
        echo "Documentos para enviar: ".count($documentos)."\n";
        foreach ($documentos as $documento) {
            if (TRUE || $documento->intento_envios->count_all() < 5) {
                try {
                    $prefijo = "[DOCUMENTO_XML_ID = {$documento->XML_ID}]";
                    $xml = simplexml_load_string($documento->xmlstring->XMLSTRING);

                    if($documento->TPXML_ID == 12) {
                        $tipodoc = 141;
                    } else if($documento->TPXML_ID == 13) {
                        $tipodoc = 142;    
                    } else {
                        $tipodoc = $documento->TPXML_ID;
                    }
                    
                    $cun = $documento->caso->CASO_CUN;
                    $folio = (string)$xml->ZONA_A->documento->folio;
                    $consolidado = dirname(__FILE__).'/../../../consolidados/'.$folio.'.xml';
                    if(file_exists($consolidado)) {
                        echo "$prefijo Por enviar Documento {$documento->XML_ID}\n";
                        $final = simplexml_load_file($consolidado)->asXML();
                        $value = Suseso::ingreso_ws($final, $tipodoc, $cun);

                        if ($value['return'] == '-40') {
                            $documento->ESTADO = 1;
                            $documento->codigo_retorno = $value['return'];
                            $documento->retorno_completo = "transaction ".$value['transaction']." ".$value['error_message'];
                            $documento->save();
                        } else {
                            $comentario = ORM::factory('Comentario');
                            $comentario->observacion = $value['error_message'];
                            $comentario->xml_id = $documento->XML_ID;

                            $user = ORM::factory('User')->find();
                            $comentario->user_id = $user->id;
                            $comentario->tipo = 'error_suseso';
                            $comentario->save();
                            $documento->ESTADO = 6;
                            $documento->save();
                        }

                        $intento_envio = ORM::factory('Intento_Envios');
                        $intento_envio->XML_ID = $documento->XML_ID;
                        $intento_envio->codigo_retorno = $value['return'];
                        $intento_envio->retorno_completo = "transaction ".$value['transaction']." ".$value['error_message'];
                        $intento_envio->save();
                    } else {
                        echo "$prefijo No existe Xml {$documento->XML_ID}\n";
                        Kohana::$log->add(Log::ERROR, 'Archivo: ' . $consolidado . ' - No existe');
                    }
                
                } catch (Exception $e) {
                    error_log($e, 3, "/var/www/html/ralf/application/logs/errores.log");
                }
            }
        }
        echo "FIN ".date('Y-m-d H:i:s')."\n";
    }

}