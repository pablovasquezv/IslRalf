<?php

defined('SYSPATH') or die('No direct script access.');

class Ws_Spm {

    public static function ingreso_denuncia($cun) {
        $wsdl_ingreso = Kohana::$config->load('ws_spm.wsdl_ingreso');
        ini_set("soap.wsdl_cache_enabled", "0");
        $params = array("arg0" => $cun);
        $client = new SoapClient($wsdl_ingreso);
        $response = $client->__soapCall("getXmlDiatOA", array($params));
        //var_dump($response);
        return $response;
    }


    public static function busca_caso($cun) {
        $result = Ws_Spm::ingreso_denuncia($cun);
        $result = $result->return;
        $find='{"return":"0","comment":"OK","xml":"';

        if ((strpos($result, $find))=== false) {
            $return = array(
                "xml"=> NULL,
            );
        } else {
            $xml = str_replace($find, "", $result);
            $xml = str_replace('"}', "", $xml);
            $return = array(
                "xml"=> $xml,
            );
        }
        return $return;
    }

    public static function ingresa_caso($response) {
        $denuncia = simplexml_load_string($response['xml']);
        $tipo_doc = $denuncia->getName();

        if ($tipo_doc == "DIAT") {
            $caso_id = (int) $denuncia->ZONA_A->documento->codigo_caso;
            $caso = ORM::factory('Caso', $caso_id);
            $tipo_denuncia = self::tipo_denuncia($tipo_doc, $clasificacion_denunciante = $denuncia->ZONA_F->denunciante->clasificacion);
            $tipo_evento = self::tipo_evento($tipo_denuncia, $denuncia);
            $region_id = self::region_id($denuncia);

            $cun = $response['cun'];
            if(isset ($denuncia->ZONA_A->documento->cun)) {
                if($cun != $denuncia->ZONA_A->documento->cun) {
                    return array('r'=>false,'mje'=>'CUN distinto al que viene en XML');
                }
            }

            if(in_array($tipo_denuncia, array(1))) {
                if(in_array($tipo_evento, array(1,2,3))) {
                    if (!$caso->loaded()) {
                        $empleador = self::empleador($denuncia);
                        $trabajador = self::trabajador($denuncia);
                        try {
                            $caso->CASO_ID = $caso_id;
                            $caso->CASO_DTT_CREAC_EDENUNCIA = date('Y-m-d H:i:s');
                            $caso->CASO_CUN = $cun;
                            $caso->CASO_TIPO_EVENTO = $tipo_evento;
                            $caso->REGION_ID = $region_id;
                            $caso->TRA_ID = $trabajador->tra_id;
                            $caso->EMP_ID = $empleador->emp_id;
                            $caso->ESTADO = 'inactivo';
                            $caso->save();

                            $xmlstring = ORM::factory('Xmlstring');
                            $xmlstring->XMLSTRING = $response['xml'];
                            $xmlstring->save();

                            $xml_insert = ORM::factory('Xml');
                            $xml_insert->XMLSTRING_ID = $xmlstring->XMLSTRING_ID;
                            $xml_insert->ESTADO = 1;
                            $xml_insert->CASO_ID = $caso_id;
                            $xml_insert->TPXML_ID = $tipo_denuncia;
                            $xml_insert->VALIDO = 1;
                            $xml_insert->codigo_retorno = '-40';
                            $xml_insert->retorno_completo = '-40';
                            $xml_insert->save();
                        } catch (Database_Exception $e) {
                            /*
                              $error_ingreso = ORM::factory('error_ingreso');
                              $error_ingreso->caso_id = $caso_id;
                              $error_ingreso->descripcion = $e->getMessage();
                              $error_ingreso->save();

                              $retorno = array(
                              "return" => 10,
                              "comment" => 'Error de Ingreso',
                              );
                              echo json_encode($retorno);
                              $log->add(Log::INFO, 'Resultado ingreso: ' . print_r($retorno, 1));
                             *
                             */
                            return;
                        }
                        return array('r'=>true,'mje'=>'ok');
                    } else {
                        return array('r'=>false,'mje'=>'Caso anteriormente cargado pero con errores');
                    }
                } else {
                    return array('r'=>false,'mje'=>'Denuncia debe ser tipo Enfermedad Profesional o Accidente Laboral');
                }
            } else {
                return array('r'=>false,'mje'=>'Denuncia debe ser tipo DIAT OA');
            }
        } elseif ($tipo_doc == "DIEP") {
            return array('r'=>false,'mje'=>'Denuncia debe ser tipo DIAT OA, no se permiten DIEP');
        } else {
            return array('r'=>false,'mje'=>'no viene documento');
        }
    }

    public static function region_id( $denuncia) {
        $com_id = $denuncia->ZONA_D->accidente->direccion_accidente->comuna;
        $comuna = ORM::factory('Comuna', $com_id);
        $region_id = $comuna->region_id;
        return $region_id;
    }

    public static function tipo_denuncia($tipo_doc, $clasificacion_denunciante) {
        /*
          1 => 'Empleador OE',
          2 => 'Trabajador OT',
          3 => 'Familiar OT',
          4 => 'Comité Paritario de Higiene y Seguridad OT',
          5 => 'Medico tratante OT',
          6 => 'Empresa usuaria OT',
          7 => 'Organismo administrador OA',
          8 => 'Otro OT',
         */
        if ($clasificacion_denunciante == 1) {
            $tipo_denuncia = ($tipo_doc == 'DIAT') ? Model_Tipo_Xml::DIAT_EM : Model_Tipo_Xml::DIEP_EM;
        } elseif ($clasificacion_denunciante == 7) {
            $tipo_denuncia = ($tipo_doc == 'DIAT') ? Model_Tipo_Xml::DIAT_OA : Model_Tipo_Xml::DIEP_OA;
        } else {
            $tipo_denuncia = ($tipo_doc == 'DIAT') ? Model_Tipo_Xml::DIAT_OT : Model_Tipo_Xml::DIEP_OT;
        }
        //$clasificacion_denunciante_tipos=Kohana::$config->load('dominios.STClasificacion_denunciante');
        return (int) $tipo_denuncia;
    }

    public static function tipo_evento($tipo_denuncia, $denuncia) {
        /*
          1=>Enfermedad Profesional
          2=>Accidente Laboral
          3=>Accidente Trayecto
         */
        if ($tipo_denuncia == 4 || $tipo_denuncia == 2 || $tipo_denuncia == 6) {
            $tipo_evento = Model_Tipo_Evento::TIPO_ENFERMEDAD;
        } else {
            if (isset($denuncia->ZONA_D->accidente->tipo_accidente) && $denuncia->ZONA_D->accidente->tipo_accidente == 2) {
                $tipo_evento = Model_Tipo_Evento::TIPO_ACCIDENTE_TRAYECTO;
            } else {
                $tipo_evento = Model_Tipo_Evento::TIPO_ACCIDENTE_LABORAL;
            }
        }
        return (int) $tipo_evento;
    }

    public static function empleador($denuncia, $id_empleador = NULL) {
        $empleador = ORM::factory('Empleador', $id_empleador);
        $empleador->rut_empleador = $denuncia->ZONA_B->empleador->rut_empleador;
        $empleador->nombre_empleador = $denuncia->ZONA_B->empleador->nombre_empleador;
        if (!empty($denuncia->ZONA_B->empleador->direccion_empleador->tipo_calle)) {
            $empleador->tipo_calle = $denuncia->ZONA_B->empleador->direccion_empleador->tipo_calle;
        }
        if (!empty($denuncia->ZONA_B->empleador->direccion_empleador->nombre_calle)) {
            $empleador->nombre_calle = $denuncia->ZONA_B->empleador->direccion_empleador->nombre_calle;
        }

        if (!empty($denuncia->ZONA_B->empleador->direccion_empleador->numero)) {
            $empleador->numero = $denuncia->ZONA_B->empleador->direccion_empleador->numero;
        } else {
            $empleador->numero = "SN";
        }

        if (!empty($denuncia->ZONA_B->empleador->direccion_empleador->resto_direccion)) {
            $empleador->resto_direccion = $denuncia->ZONA_B->empleador->direccion_empleador->resto_direccion;
        }

        if (!empty($denuncia->ZONA_B->empleador->direccion_empleador->localidad)) {
            $empleador->localidad = $denuncia->ZONA_B->empleador->direccion_empleador->localidad;
        }

        if (!empty($denuncia->ZONA_B->empleador->direccion_empleador->comuna)) {
            $empleador->comuna = $denuncia->ZONA_B->empleador->direccion_empleador->comuna;
        }

        if (($denuncia->ZONA_B->empleador->ciiu_empleador)) {
            $empleador->ciiu_empleador = $denuncia->ZONA_B->empleador->ciiu_empleador;
        }
        if (($denuncia->ZONA_B->empleador->ciiu_texto)) {
            $empleador->ciiu_texto = $denuncia->ZONA_B->empleador->ciiu_texto;
        }

        if (($denuncia->ZONA_B->empleador->n_trabajadores)) {
            $empleador->n_trabajadores = $denuncia->ZONA_B->empleador->n_trabajadores;
        }

        if (($denuncia->ZONA_B->empleador->n_trabajadores_hombre)) {
            $empleador->n_trabajadores_hombre = $denuncia->ZONA_B->empleador->n_trabajadores_hombre;
        } else {
            $empleador->n_trabajadores_hombre = 0;
        }

        if (!empty($denuncia->ZONA_B->empleador->n_trabajadores_mujer)) {
            $empleador->n_trabajadores_mujer = $denuncia->ZONA_B->empleador->n_trabajadores_mujer;
        } else {
            $empleador->n_trabajadores_mujer = 0;
        }

        if (!empty($denuncia->ZONA_B->empleador->tipo_empresa)) {
            $empleador->tipo_empresa = $denuncia->ZONA_B->empleador->tipo_empresa;
        }

        if (!empty($denuncia->ZONA_B->empleador->ciiu2_empleador)) {
            $empleador->ciiu2_empleador = $denuncia->ZONA_B->empleador->ciiu2_empleador;
        }

        if (!empty($denuncia->ZONA_B->empleador->ciiu2_texto)) {
            $empleador->ciiu2_texto = $denuncia->ZONA_B->empleador->ciiu2_texto;
        }

        if (!empty($denuncia->ZONA_B->empleador->propiedad_empresa)) {
            $empleador->propiedad_empresa = $denuncia->ZONA_B->empleador->propiedad_empresa;
        }

        if (!empty($denuncia->ZONA_B->empleador->telefono_empleador->cod_pais)) {
            $empleador->cod_pais = $denuncia->ZONA_B->empleador->telefono_empleador->cod_pais;
        }

        if (!empty($denuncia->ZONA_B->empleador->telefono_empleador->cod_area)) {
            $empleador->cod_area = $denuncia->ZONA_B->empleador->telefono_empleador->cod_area;
        }

        if (!empty($denuncia->ZONA_B->empleador->telefono_empleador->numero)) {
            $empleador->numero_telefono = $denuncia->ZONA_B->empleador->telefono_empleador->numero;
        }

        try {
            $empleador->save();
        } catch (Database_Exception $e) {
            /*
              $error_ingreso = ORM::factory('error_ingreso');
              $error_ingreso->caso_id = $denuncia->ZONA_A->documento->codigo_caso;
              $error_ingreso->descripcion = $e->getMessage();
              $error_ingreso->save();
              $retorno = array(
              "return" => 13,
              "comment" => 'Error de Ingreso',
              );
              echo json_encode($retorno);
             *
             */
            return;
        }
        return (object) $empleador;
    }

    public static function trabajador($denuncia, $id_trabajador = NULL) {
        $trabajador = ORM::factory('Trabajador', $id_trabajador);
        if (!empty($denuncia->ZONA_C->empleado->trabajador->apellido_paterno)) {
            $trabajador->apellido_paterno = $denuncia->ZONA_C->empleado->trabajador->apellido_paterno;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->apellido_materno)) {
            $trabajador->apellido_materno = $denuncia->ZONA_C->empleado->trabajador->apellido_materno;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->nombres)) {
            $trabajador->nombres = $denuncia->ZONA_C->empleado->trabajador->nombres;
        }

        //faillons cambio por nueva estructura zona C
        if(isset($denuncia->ZONA_C->empleado->trabajador->rut)){
            $trabajador->rut = $denuncia->ZONA_C->empleado->trabajador->rut;
        }else{
            if (isset($denuncia->ZONA_C->empleado->trabajador->documento_identidad)) {
                $trabajador->rut = $denuncia->ZONA_C->empleado->trabajador->documento_identidad->identificador;
            }
        }

        /*if (!empty($denuncia->ZONA_C->empleado->trabajador->rut)) {
            $trabajador->rut = $denuncia->ZONA_C->empleado->trabajador->rut;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->documento_identidad)) {
            $trabajador->rut = $denuncia->ZONA_C->empleado->trabajador->documento_identidad->identificador;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->fecha_nacimiento)) {
            $trabajador->fecha_nacimiento = $denuncia->ZONA_C->empleado->trabajador->fecha_nacimiento;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->edad)) {
            $trabajador->edad = $denuncia->ZONA_C->empleado->trabajador->edad;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->sexo)) {
            $trabajador->sexo = $denuncia->ZONA_C->empleado->trabajador->sexo;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->pais_nacionalidad)) {
            $trabajador->pais_nacionalidad = $denuncia->ZONA_C->empleado->trabajador->pais_nacionalidad;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->codigo_etnia)) {
            $trabajador->codigo_etnia = $denuncia->ZONA_C->empleado->codigo_etnia;
        }
        if (!empty($denuncia->ZONA_C->empleado->trabajador->etnia_otro)) {
            $trabajador->etnia_otro = $denuncia->ZONA_C->empleado->etnia_otro;
        }
        if (!empty($denuncia->ZONA_C->empleado->direccion_trabajador->tipo_calle)) {
            $trabajador->tipo_calle = $denuncia->ZONA_C->empleado->direccion_trabajador->tipo_calle;
        }
        if (!empty($denuncia->ZONA_C->empleado->direccion_trabajador->nombre_calle)) {
            $trabajador->nombre_calle = $denuncia->ZONA_C->empleado->direccion_trabajador->nombre_calle;
        }
        if (!empty($denuncia->ZONA_C->empleado->direccion_trabajador->numero)) {
            $trabajador->numero = $denuncia->ZONA_C->empleado->direccion_trabajador->numero;
        }

        if (!empty($denuncia->ZONA_C->empleado->direccion_trabajador->localidad)) {
            $trabajador->localidad = $denuncia->ZONA_C->empleado->direccion_trabajador->localidad;
        }
        if (!empty($denuncia->ZONA_C->empleado->direccion_trabajador->comuna)) {
            $trabajador->comuna = $denuncia->ZONA_C->empleado->direccion_trabajador->comuna;
        }
        if (!empty($denuncia->ZONA_C->empleado->direccion_trabajador->resto_direccion)) {
            $trabajador->resto_direccion = $denuncia->ZONA_C->empleado->direccion_trabajador->resto_direccion;
        }
        if (!empty($denuncia->ZONA_C->empleado->profesion_trabajador)) {
            $trabajador->profesion_trabajador = $denuncia->ZONA_C->empleado->profesion_trabajador;
        }
        if (!empty($denuncia->ZONA_C->empleado->ciuo_trabajador)) {
            $trabajador->ciuo_trabajador = $denuncia->ZONA_C->empleado->ciuo_trabajador;
        }
        if (!empty($denuncia->ZONA_C->empleado->categoria_ocupacion)) {
            $trabajador->categoria_ocupacion = $denuncia->ZONA_C->empleado->categoria_ocupacion;
        }
        if (!empty($denuncia->ZONA_C->empleado->duracion_contrato)) {
            $trabajador->duracion_contrato = $denuncia->ZONA_C->empleado->duracion_contrato;
        }
        if (!empty($denuncia->ZONA_C->empleado->tipo_dependencia)) {
            $trabajador->tipo_dependencia = $denuncia->ZONA_C->empleado->tipo_dependencia;
        }
        if (!empty($denuncia->ZONA_C->empleado->tipo_remuneracion)) {
            $trabajador->tipo_remuneracion = $denuncia->ZONA_C->empleado->tipo_remuneracion;
        }
        if (!empty($denuncia->ZONA_C->empleado->fecha_ingreso)) {
            $trabajador->fecha_ingreso = $denuncia->ZONA_C->empleado->fecha_ingreso;
        }
        if (!empty($denuncia->ZONA_C->empleado->clasificacion_trabajador)) {
            $trabajador->clasificacion_trabajador = $denuncia->ZONA_C->empleado->clasificacion_trabajador;
        }
        if (!empty($denuncia->ZONA_C->empleado->sistema_comun)) {
            $trabajador->sistema_comun = $denuncia->ZONA_C->empleado->sistema_comun;
        }
        if (!empty($denuncia->ZONA_C->empleado->telefono_trabajador->numero_telefono)) {
            $trabajador->numero_telefono = $denuncia->ZONA_C->empleado->telefono_trabajador->numero_telefono;
        }
        if (!empty($denuncia->ZONA_C->empleado->telefono_trabajador->cod_area)) {
            $trabajador->cod_area = $denuncia->ZONA_C->empleado->telefono_trabajador->cod_area;
        }
        if (!empty($denuncia->ZONA_C->empleado->telefono_trabajador->cod_pais)) {
            $trabajador->cod_pais = $denuncia->ZONA_C->empleado->telefono_trabajador->cod_pais;
        }
        */
        try {
            $trabajador->save();
        } catch (Database_Exception $e) {
              $error_ingreso = ORM::factory('error_ingreso');
              $error_ingreso->caso_id = $denuncia->ZONA_A->documento->codigo_caso;
              $error_ingreso->descripcion = $e->getMessage();
              $error_ingreso->save();
              $retorno = array(
              "return" => 14,
              "comment" => 'Error de Ingreso',
              );
              echo json_encode($retorno);
            return;
            
        }
        return (object) $trabajador;
    }

}
