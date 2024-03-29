<?php

defined('SYSPATH') or die('No direct script access.');

class Utiles {

    /**
     * Checks if a rut number is valid.
     * @param   string rut number to check
     * @return  boolean
     * @link    http://juque.cl/weblog/2004/06/16/validador-de-rut-en-php.html
     */
    public static function rut($rut) {
        //echo $rut."<br />";
        if (!preg_match("/[0-9]{1,8}-[0-9Kk]/", $rut)) {
            return FALSE;
        }

        $r = strtoupper(preg_replace("/\.|,|-/", "", $rut));
        $sub_rut = substr($r, 0, strlen($r) - 1);
        $sub_dv = substr($r, -1);

        $x = 2;
        $s = 0;
        for ($i = strlen($sub_rut) - 1; $i >= 0; $i--) {
            if ($x > 7)
                $x = 2;
            $s += $sub_rut[$i] * $x;
            $x++;
        }
        $dv = 11 - ($s % 11);

        if ($dv == 10)
            $dv = 'K';
        if ($dv == 11)
            $dv = '0';

        return $dv == $sub_dv;
    }

    /**
     * Valida XML contra Esquema
     * @param string $xmlString,$xmlSchema
     * @return  array
     * @author jperez
     */
    public static function valida_xml($xmlString, $xmlSchema) {
        libxml_use_internal_errors(true);
        libxml_clear_errors();

        $objDom = new DomDocument();
        $objDom->loadXML($xmlString);

        /* Validamos datos contra esquema */
        if ($objDom->schemaValidate($xmlSchema)) {
            $retorno['estado'] = TRUE;
            $retorno['mensaje'] = '';
        } else {
            /* si algo sale mal se puede obtener todos los errores a la vez */
            $retorno['estado'] = FALSE;
            $retorno['mensaje'] = '';
            $allErrors = libxml_get_errors();
            
            Log::instance()->add(Log::ERROR, 'xmlString: ' . print_r($xmlString, TRUE));
            Log::instance()->add(Log::ERROR, 'Valida_xml: ' . print_r($allErrors, TRUE));
            
            $retorno['mensaje'] = self::xml_error_parser($allErrors);
        }
        return (array) $retorno;
    }

    /**
     * Recorre un directorio recibido como parámetro, devolviendo su contenido en un array,
     * Opcionalmente el segundo parámetro permite setear el primer elemento del array.
     * 
     * @param String $path
     * @param String $extension
     * @param String $first
     * @param Boolean $down_level
     * @return Array 
     */
    public static function search_media($path, $extension, $first = NULL, $down_level = TRUE) {
        $media_array = array();
        if (is_dir($path)) {
            $media = opendir($path);
            if ($media) {
                while (($file = readdir($media)) !== false) {
                    $path_info = pathinfo($path . $file);
                    if (preg_match('/^([a-zA-Z0-9]+\-?\.?)/', $file) AND $path_info['extension'] == $extension AND ! is_dir($path . $file)) {
                        if ($first == $file)
                            array_unshift($media_array, $path . $file);
                        else
                            $media_array[] = $path . $file;
                    }
                    /* Si nos encontramos con un directorio, buscará archivos compatible en él */
                    else if ($down_level AND ! preg_match('/[\.]+$/', $path . $file)) {
                        $media_array = array_merge($media_array, Utiles::search_media($path . $file . '/', $extension, $first, $down_level));
                    }
                }
                closedir($media);
            }
        }
        return $media_array;
    }

    /**
     * Configura el plugin jquery impromptu y genera codigo javascript para insertar.
     * ToDo: cambiar para que acepte mensajes como array y cambiar tipo de prompt.
     * 
     * @param Mixed $msg Mensaje a mostrar
     * @param String $btn Nombre del boton
     * @param String $rd Url para redirigir después de aceptar
     * @param Integer $w ancho de la ventana
     * @param Integer $fs tamaño de la fuente del mensaje
     * @param String $ta alineación del texto
     * @param String $ba alineación de los botones
     * 
     * @return Javascript Code
     */
    public static function prompt($msg, $btn = 'Aceptar', $rd = NULL, $w = 400, $fs = 11, $ta = 'center', $ba = 'center') {
        $rd = ($rd) ? "submit:function(){window.location.href='$rd'}" : "";
        $buttons = "buttons:{" . $btn . ":true}";
        $prompt = "$.prompt('$msg',{" . $buttons . "," . $rd . "});";
        $style = "$('.jqimessage').css('font-size','{$fs}px');
                $('div.jqi').css('width','{$w}px');
                margin_left = -(($w/2)+8)+'px';
                margin_top  = ($(window).height()/4)+'px';
                $('div.jqi').css({'margin-left' : margin_left, 'margin-top' : margin_top,'text-align' : '$ta'});
                $('.jqibuttons').css({'text-align' : '$ba'})";
        return $prompt . $style;
    }

    /**
     *
     * @param <type> $php_date_format
     * @return <type>
     */
    public static function cdate($php_date_format = 'l d F Y') {
        $date_string = NULL;
        $date_array = explode(" ", str_replace("  ", " ", $php_date_format));
        foreach ($date_array as $date) {
            $date_string .= Utiles::tdate(date($date)) . ' ';
        }
        return $date_string;
    }

    /**
     * Custom date
     * @param <type> $date
     * @return <type> 
     */
    public static function tdate($date) {
        $tdates = array(
            'Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mi&eacute;', 'Thu' => 'Jue', 'Fri' => 'Vie',
            'Sat' => 'S&acute;b', 'Sun' => 'Dom', 'Monday' => 'Lunes', 'Tuesday' => 'Martes',
            'Wednesday' => 'Mi&eacute;rcoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes',
            'Saturday' => 'S&aacute;bado', 'Sunday' => 'Domingo', 'Jan' => 'Ene', 'Feb' => 'Feb',
            'Mar' => 'Mar', 'Apr' => 'Abr', 'May' => 'May', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Ago',
            'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Dic', 'January' => 'Enero',
            'February' => 'Febrero', 'March' => 'Marzo', 'April' => 'Abril', 'May' => 'Mayo',
            'June' => 'Junio', 'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
            'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre',
        );
        return isset($tdates[$date]) ? $tdates[$date] : $date;
    }

    /**
     *
     * @param type $array 
     */
    public static function js_response($array) {
        echo json_encode($array);
        exit();
    }

    /**
     *
     * @param type $replace
     * @param type $text
     * @return type 
     */
    public static function text_replace($replace, $text) {
        $search_keys = array();
        $replace_values = array();
        foreach ($replace as $k => $v) {
            $search_keys[] = $k;
            $replace_values[] = $v;
        }
        return str_replace($search_keys, $replace_values, $text);
    }

    /**
     *
     * @param Array $destinatarios
     * @param String $mensaje
     * @return Integer 
     */
    public static function enviar_email($destinatarios, $mensaje, $asunto) {
        $mailer = new Mailer($destinatarios, $mensaje, $asunto);
        return $mailer->enviar();
    }

    /**
     * Retorna la edad, recibiendo como parametro la fecha de nacimiento.
     * 
     * @param type $fecha_nacimiento
     * @return Integer 
     */
    public static function edad($fecha_nacimiento) {
        $ano_dif = 0;
        if (preg_match("/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/", $fecha_nacimiento)) {
            list($ano_nacimiento, $mes_nacimiento, $dia_nacimiento) = explode("-", $fecha_nacimiento);
            //busca la diferencia
            $ano_dif = date("Y") - $ano_nacimiento;
            $mes_dif = date("m") - $mes_nacimiento;
            $dia_dif = date("d") - $dia_nacimiento;
            //por si el cumpleaños no es este año
            if ($dia_dif < 0 || $mes_dif < 0)
                $ano_dif--;
        }
        return $ano_dif;
    }

    /**
     *
     * @param type $email
     * @return type 
     */
    public static function check_mail($email) {
        return preg_match('/^\w+\@[a-zA-Z_]+?\.[a-zA-Z_]{2,}$/', $email);
    }

    /**
     *
     * @param type $fields
     * @param type $array
     * @param type $glue
     * @return type 
     */
    public static function set_prompt($fields, $array, $glue) {
        $prompt = array();
        foreach ($fields as $field) {
            if (isset($array[$field])) {
                $prompt[$field] = ucfirst($array[$field]);
            }
        }
        return implode($glue, $prompt);
    }

    /**
     *
     * @param type $response
     * @param type $show_message
     * @param type $fields
     * @param type $redirect
     * @param type $set_fields 
     */
    public static function form_response($response, $show_message = FALSE, $fields = array(), $redirect = NULL, $set_fields = array()) {
        if (is_array($response) AND count($response)) {//<< Ajax Response
            $js_response = array(
                'errors' => TRUE,
                'show_fields' => $response,
                'css_rule' => 'background-image',
                'css_value' => "url('" . URL::site('/media/images/medium-input-bg-color.png', 'http') . "')",
                'message' => 'Por favor revise los campos marcados'
            );
            if ($show_message)
                $js_response['prompt'] = Utiles::set_prompt($fields, $response, "<br/>");
            if ($set_fields)
                $js_response['set_fields'] = $set_fields;
        }
        else {
            $js_response = array('response' => 'Cambios realizados correctamente', "redirect" => $redirect);
        }
        Utiles::js_response($js_response); //<< Ajax Response
    }

    /**
     *
     * @param type $date
     * @param type $time
     * @param type $short
     * @return type 
     */
    public static function full_date($date, $time = FALSE, $short = FALSE) {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date; // no se puede formatear, retornar fecha
        }

        //20 de Marzo del 2012 a las 17:11:30
        //if(TRUE){
        $meses = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $dia = date('d', $timestamp);
        $mes = $meses[intval(date('n', $timestamp))];
        $anio = date('Y', $timestamp);
        $hora = date('H:i:s', $timestamp);
        $fecha = ($short) ? "$dia de $mes $anio" : "$dia de $mes del $anio";

        if ($time AND $hora AND ! $short)
            $fecha .= " a las $hora";

        return $fecha; //}else return $date;
    }

    public static function full_date_slash($date, $time = FALSE, $short = FALSE) {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date; // no se puede formatear, retornar fecha
        }

        //20/03/2012
        //if(TRUE){
        $meses = array("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
        $dia = date('d', $timestamp);
        $mes = $meses[intval(date('n', $timestamp))];
        $anio = date('Y', $timestamp);
        //$hora  = date('H:i:s', strtotime($date));
        $fecha = ($short) ? "$dia / $mes $anio" : "$dia / $mes / $anio";

        if ($time AND $hora AND ! $short)
            $fecha .= " a las $hora";

        return $fecha; //}else return $date;
    }

    /**
     *
     * @param type $string
     * @return type 
     */
    public static function capitalize($string) {
        $search = array('ÿ', 'ñ', 'ç', 'æ', 'œ', '.', '-', 'á', 'é', 'í', 'ó', 'ú', '"', 'à', 'è', 'ì', 'ò',
            'ù', 'ä', 'ë', 'ï', 'ö', 'ü', 'â', 'ê', 'î', 'ô', 'û', 'å', 'e', 'i', 'ø', 'u', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');

        $replace = array('y', 'n', 'c', 'ae', 'oe', '', '', 'a', 'e', 'i', 'o', 'u', '', 'a', 'e', 'i', 'o',
            'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'á', 'é', 'í', 'ó', 'ú', 'ñ');

        return ucwords(str_replace($search, $replace, strtolower($string)));
    }

    /**
     *
     * @return type 
     */
    public static function dominios_comunes() {
        $ciius = Kohana::$config->load('dominios.STCIIU');
        $ciuo = Kohana::$config->load('dominios.STCIUO');
        $comunas = Kohana::$config->load('dominios.STCodigo_comuna');
        $nacionalidades = Kohana::$config->load('dominios.STPais_nacionalidad');
        $tipo_calle = Kohana::$config->load('dominios.STTipoCalle');
        $sexo = Kohana::$config->load('dominios.STSexo');
        $etnia = Kohana::$config->load('dominios.STCodigo_etnia');
        $ocupacion = Kohana::$config->load('dominios.STCategoria_ocupacion');
        $contrato = Kohana::$config->load('dominios.STDuracion_contrato');
        $dependencia = Kohana::$config->load('dominios.STDependencia');
        $remuneracion = Kohana::$config->load('dominios.STRemuneracion');
        $clas_trabajador = Kohana::$config->load('dominios.STClasificacion_trabajador');
        $sistema = Kohana::$config->load('dominios.STSistema_comun');
        $si_o_no = Kohana::$config->load('dominios.STSiNo');
        $alta_medica_alme = Kohana::$config->load('dominios.STTipo_alta_medica');
        $gravedad = Kohana::$config->load('dominios.STCriterio_gravedad');
        $accidente_type = Kohana::$config->load('dominios.STTipo_accidente');
        $accidente_trayecto = Kohana::$config->load('dominios.STTipo_accidente_trayecto');
        $prueba = Kohana::$config->load('dominios.STMedio_prueba_accidente');
        $denunciante_class = Kohana::$config->load('dominios.STClasificacion_denunciante');
        $tipo_acc_enf = Kohana::$config->load('dominios.STTipo_accidente_enfermedad');
        $prop_empresa = Kohana::$config->load('dominios.STPropiedad_empresa');
        $tipo_empresa = Kohana::$config->load('dominios.STTipo_empresa');
        $organismo = Kohana::$config->load('dominios.STOrganismo');

        $dominios = array(
            'ciius' => $ciius, 'comunas' => $comunas, 'nacionalidades' => $nacionalidades, 'tipo_calle' => $tipo_calle,
            'sexo' => $sexo, 'etnia' => $etnia, 'ocupacion' => $ocupacion, 'contrato' => $contrato, 'dependencia' => $dependencia,
            'remuneracion' => $remuneracion, 'clas_trabajador' => $clas_trabajador, 'sistema' => $sistema, 'si_o_no' => $si_o_no, 'alta_medica_alme' => $alta_medica_alme,
            'gravedad' => $gravedad, 'accidente_type' => $accidente_type, 'accidente_trayecto' => $accidente_trayecto,
            'prueba' => $prueba, 'denunciante_class' => $denunciante_class, 'tipo_acc_enf' => $tipo_acc_enf,
            'prop_empresa' => $prop_empresa, 'tipo_empresa' => $tipo_empresa, 'ciuo' => $ciuo, 'organismo' => $organismo
        );

        //Borro los "seleccionar" que hay en algunos arrays
        foreach ($dominios as $k => $d)
            unset($dominios[$k]['']);

        return $dominios;
    }

    /**
     *
     * @return type 
     */
    public static function dominios_codificacion() {
        //var_dump(Kohana::$config->load('dominios')->as_array());

        $codigo_forma = Kohana::$config->load('dominios.STCodigo_forma');
        $codigo_contraparte = Kohana::$config->load('dominios.STCodigo_contraparte');
        $codigo_agente_accidente = Kohana::$config->load('dominios.STCodigo_agente_accidente');
        $codigo_modo_transporte = Kohana::$config->load('dominios.STCodigo_modo_transporte');
        $codigo_tipo_evento = Kohana::$config->load('dominios.STCodigo_Tipo_evento');
        $codigo_papel_lesionado = Kohana::$config->load('dominios.STCodigo_papel_lesionado');
        $codigo_intencionalidad = Kohana::$config->load('dominios.STCodigo_intencionalidad');

        $dominios = array(
            'codigo_forma' => $codigo_forma, 'codigo_contraparte' => $codigo_contraparte,
            'codigo_agente_accidente' => $codigo_agente_accidente, 'codigo_modo_transporte' => $codigo_modo_transporte,
            'codigo_tipo_evento' => $codigo_tipo_evento, 'codigo_papel_lesionado' => $codigo_papel_lesionado,
            'codigo_intencionalidad' => $codigo_intencionalidad);

        //Borro los "seleccionar" que hay en algunos arrays
        foreach ($dominios as $k => $d)
            unset($dominios[$k]['']);

        return $dominios;
    }

    public static function zonas_xml($no_incluir = array()) {
        $zonas = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F', 'g' => 'G', 'h' => 'H');
        foreach ($no_incluir as $zona)
            unset($zonas[$zona]);
        return $zonas;
    }

    public static function formato_fecha($fecha) {
        $dNewDate = strtotime($fecha);
        return date('Y-m-d', $dNewDate);
    }

    public static function formato_fecha2($fecha) {
        $dNewDate = strtotime($fecha);
        return date('d-m-Y', $dNewDate);
    }

    public static function formato_fecha3($fecha) {
        $dNewDate = strtotime($fecha);
        $meses = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $mes = $meses[intval(date('n', strtotime($fecha)))];

        $arreglo['dia'] = date('d', $dNewDate);
        $arreglo['mes'] = $mes;
        $arreglo['año'] = date('Y', $dNewDate);
        $arreglo['nmes'] = date('m', $dNewDate);
        return $arreglo;
    }

    public static function fecha_hora($date, $time = FALSE, $short = FALSE) {
        //20 de Marzo del 2012 a las 17:11:30
        //if(TRUE){
        $meses = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $dia = date('d', strtotime($date));
        $mes = $meses[intval(date('n', strtotime($date)))];
        $anio = date('Y', strtotime($date));
        $hora = date('H', strtotime($date));
        $minuto = date('i', strtotime($date));

        $arreglo['dia'] = $dia;
        $arreglo['mes'] = $mes;
        $arreglo['año'] = $anio;
        $arreglo['hora'] = $hora;
        $arreglo['minuto'] = $minuto;
        $arreglo['nmes'] = date('m', strtotime($date));

        //if($time AND $hora AND !$short) $fecha .= " a las $hora";

        return $arreglo; //}else return $date;
    }

    public static function hora($hora) {
        //20 de Marzo del 2012 a las 17:11:30
        //if(TRUE){
        $hora = date('H', strtotime($hora));
        $minuto = date('i', strtotime($hora));
        $seg = date('s', strtotime($hora));

        $arreglo['hora'] = $hora;
        $arreglo['minuto'] = $minuto;
        $arreglo['seg'] = $seg;

        //if($time AND $hora AND !$short) $fecha .= " a las $hora";

        return $arreglo; //}else return $date;
    }

    public static function n_dias($fecha_desde, $fecha_hasta) {
        $dias = (strtotime($fecha_desde) - strtotime($fecha_hasta)) / 86400;
        $dias = abs($dias);
        $dias = floor($dias);
        return ($dias + 1);
    }

    public static function show_array($array) {
        return "<pre>" . print_r($array, TRUE) . "</pre>";
    }

    public static function ws_anulacion_message($codigo) {
        $codigos = array(
            '-14' => 'Token inválido',
            '-315' => 'Tipo de documento no válido',
            '-40' => 'Anulación exitosa',
            '-44' => 'El documento ya estaba anteriormente anulado.',
            '-48' => 'No se encuentra el documento a anular.',
            '-412' => 'Error de comunicación con la base de datos',
            '-53' => 'Usuario sin privilegios',
            '-55' => 'Password vencida',
            '-57' => 'Error al llamar al webservice de privilegios',
            '-58' => 'Error en llamada al Webservice Token'
        );
        return $codigos[$codigo];
    }

    public static function random_string($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count - 1)];
        }
        return $str;
    }

    public static function xml_error_parser($errores) {
        $campos = array();
        foreach ($errores as $error) {
            $pool = $error->message;
            $var1 = "'";
            $var2 = "'";
            $temp1 = strpos($pool, $var1) + strlen($var1);
            $result = substr($pool, $temp1, strlen($pool));
            $dd = strpos($result, $var2);
            if ($dd == 0) {
                $dd = strlen($result);
            }
            $message = strtoupper(str_replace('_', ' ', substr($result, 0, $dd)));
            $campos[] = $message;
        }
        return $campos;
    }

    public static function whitespace($value) {
        if (trim($value) == "") {
            return false;
        } else {
            return true;
        }
    }

    public static function size_min($value) {

        $count = strlen(base64_encode(file_get_contents($value["tmp_name"])));
        if ($count > 20) {
            return true;
        } else {
            return false;
        }
    }

    public static function is_float($value) {
        if ($value == '0' || filter_var($value, FILTER_VALIDATE_FLOAT)) {
            return true;
        } else {
            return false;
        }
    }

    public static function nonNegativeInteger($value) {

        if ($value >= 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function fecha_minima($fecha) {
        if ($fecha >= '1900-01-01') {
            return true;
        } else {
            return false;
        }
    }

    public static function mayorqueuno($value) {
        if ($value >= '1') {
            return true;
        } else {
            return false;
        }
    }

    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function validaFechaMenorHoy($fecha) {
        $fechaFormat = strtotime($fecha);
        return $fechaFormat <= time();
    }

    public static function validaCodigoCausa($codigo) {
        $causas = Model_Causa144::obtenerCodCausas();
        return in_array($codigo, $causas);
    }
    
    public static function regiones() {
        $res = array();
        $regiones = ORM::factory('Region')->find_all();
        foreach ($regiones as $r) {
            $res[$r->id] = $r->nombre;
        }
        return $res;
    }

}
