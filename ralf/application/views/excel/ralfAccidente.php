<?php

require_once(Kohana::find_file('vendor/phpexcel/', 'PHPExcel'));

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'CUN')
        ->setCellValue('B1', 'Run Accidentado')
        ->setCellValue('C1', 'Fecha de Notificación')
        ->setCellValue('D1', 'Dirección Regional')
        ->setCellValue('E1', 'Prevencionista')
        ->setCellValue('F1', 'Nombre Accidentado')
        ->setCellValue('G1', 'Paterno Accidentado')
        ->setCellValue('H1', 'Materno Accidentado')
        ->setCellValue('I1', 'Edad')
        ->setCellValue('J1', 'Sexo')
        
        ->setCellValue('K1', 'Rut Empresa')
        ->setCellValue('L1', 'Razón Social')
        ->setCellValue('M1', 'Dirección')
        ->setCellValue('N1', 'Comuna')
        ->setCellValue('O1', 'Región')
        ->setCellValue('P1', 'CIIU')
        
        ->setCellValue('Q1', 'Fecha Accidente')
        ->setCellValue('R1', 'Hora Accidente')
        ->setCellValue('S1', 'Criterio Gravedad')
        ->setCellValue('T1', 'Dirección Accidente')
        ->setCellValue('U1', 'Comuna')
        ->setCellValue('V1', 'Región')
        ->setCellValue('W1', 'Descripcción Accidente')
        
        /*->setCellValue('Y1', 'cod_area')
        ->setCellValue('Z1', 'telefono_informante_oa')
        ->setCellValue('AA1', 'correo_electronico_informante_oa')
        ->setCellValue('AB1', 'xml_id')*/
        ;

$i = 2;
foreach ($ralfs as $ralf) {
    
    $documento = ORM::factory('Xml', $ralf->xml_id);
    $caso = ORM::factory('Caso')->where('CASO_CUN', '=', $ralf->xml->caso->CASO_CUN)->find();
    $xmlstring = $documento->xmlstring->XMLSTRING;
    $xml = simplexml_load_string($xmlstring);
    
    $sexos = $data['sexo'];
    
    #criterios de gravedad
    $criterio_gravedad_final = "";
    $criterios_gravedad_bd = explode("-", $ralf->criterio_gravedad);
    $criterios_gravedad_dominio = Controller_Dominios::STCriterio_gravedad_RALF();
    $criterios_gravedad_temporal = array();
    if (count($criterios_gravedad_bd) > 0) {
        foreach ($criterios_gravedad_bd as $cg_id) {
            $id = (int) $cg_id;
            if ($id > 0)
                $criterios_gravedad_temporal[$id] = $criterios_gravedad_dominio[$id];
        }
    }
    if (count($criterios_gravedad_temporal) > 0)
        $criterio_gravedad_final = implode(", ", $criterios_gravedad_temporal);

    #lugar de defuncion
    $lugar_defuncion_bd = (int) $ralf->lugar_defuncion;
    $lugar_defuncion_array = Controller_Dominios::STLugarDefuncion();
    $lugar_defuncion = (strlen($lugar_defuncion_bd) > 0 AND $lugar_defuncion_bd > 0) ? $lugar_defuncion_array[$lugar_defuncion_bd] : "";

    #tipo de calle
    $tipo_calle_bd = (int) $ralf->tipo_calle;
    $tipo_calle_array = Controller_Dominios::STTipoCalle();
    $tipo_calle = (strlen($tipo_calle_bd) > 0 AND $tipo_calle_bd > 0) ? $tipo_calle_array[$tipo_calle_bd] : "";

    //COMUNA
    $comuna_bd = (int) $ralf->comuna;
    $comuna = (strlen($comuna_bd) > 0 AND $comuna_bd > 0) ? ORM::factory('Comuna', $comuna_bd)->nombre : "";
    
    //REGION
    $regiones = Utiles::regiones();
    $comunas = Model_St_Comuna::obtener();
    $comuna_emp = $xml->ZONA_B->empleador->direccion_empleador->comuna;
    $nom_comuna_emp = isset($comunas[ (string) $comuna_emp]) ? $comunas[ (string) $comuna_emp] : "S/I";
    
    $comuna_emp = substr($comuna_emp, 0, strlen($comuna_emp) - 3);
    $comuna_acc = substr($comuna_bd, 0, strlen($comuna_bd) - 3);
    $region_emp = isset($regiones[ (int) $comuna_emp]) ? $regiones[ (int) $comuna_emp] : "S/I";
    $region_acc = isset($regiones[ (int) $comuna_acc]) ? $regiones[ (int) $comuna_acc] : "S/I";
    
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $ralf->xml->caso->CASO_CUN)
            ->setCellValue('B' . $i, $xml->ZONA_C->empleado->trabajador->documento_identidad->identificador)
            ->setCellValue('C' . $i, $documento->FECHA_CREACION)
            ->setCellValue('D' . $i, $regiones[$caso->REGION_ID])
            ->setCellValue('E' . $i, $ralf->nombres . " " . $ralf->apellido_paterno)
            ->setCellValue('F' . $i, $xml->ZONA_C->empleado->trabajador->nombres)
            ->setCellValue('G' . $i, $xml->ZONA_C->empleado->trabajador->apellido_paterno)
            ->setCellValue('H' . $i, $xml->ZONA_C->empleado->trabajador->apellido_materno)
            ->setCellValue('I' . $i, $xml->ZONA_C->empleado->trabajador->edad)
            ->setCellValue('J' . $i, $sexos[ (int) $xml->ZONA_C->empleado->trabajador->sexo])
            
            
            ->setCellValue('K' . $i, $xml->ZONA_B->empleador->rut_empleador)
            ->setCellValue('L' . $i, $xml->ZONA_B->empleador->nombre_empleador)
            ->setCellValue('M' . $i, $xml->ZONA_B->empleador->direccion_empleador->nombre_calle." ".$xml->ZONA_B->empleador->direccion_empleador->numero)
            ->setCellValue('N' . $i, $nom_comuna_emp)
            ->setCellValue('O' . $i, $region_emp)
            ->setCellValue('P' . $i, $xml->ZONA_B->empleador->ciiu_empleador)
            
            ->setCellValue('Q' . $i, $ralf->fecha_accidente)
            ->setCellValue('R' . $i, $ralf->hora_accidente)
            ->setCellValue('S' . $i, $criterio_gravedad_final)
            ->setCellValue('T' . $i, $ralf->nombre_calle ." ".$ralf->numero)
            ->setCellValue('U' . $i, $comuna)
            ->setCellValue('V' . $i, $region_acc)
            ->setCellValue('w' . $i, $ralf->descripcion_accidente_ini)
            ;
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle('Hoja');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ralfAccidente.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>