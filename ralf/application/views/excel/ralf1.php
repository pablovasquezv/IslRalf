<?php

require_once(Kohana::find_file('vendor/phpexcel/', 'PHPExcel'));


$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'cun')
			->setCellValue('B1', 'rut_representante_legal')
			->setCellValue('C1', 'nombre_representante_legal')
			->setCellValue('D1', 'tasa_ds110')
			->setCellValue('E1', 'tasa_ds67')
			->setCellValue('F1', 'ultima_eval_ds67')
			->setCellValue('G1', 'nro_sucursales')
			->setCellValue('H1', 'promedio_anual_trabajadores')
			->setCellValue('I1', 'fecha_accidente')
			->setCellValue('J1', 'hora_accidente')
			->setCellValue('K1', 'tipo_calle')
			->setCellValue('L1', 'nombre_calle')
			->setCellValue('M1', 'numero')
			->setCellValue('N1', 'resto_direccion')
			->setCellValue('O1', 'localidad')
			->setCellValue('P1', 'comuna')
			->setCellValue('Q1', 'criterio_gravedad')
			->setCellValue('R1', 'fecha_defuncion')
			->setCellValue('S1', 'lugar_defuncion')
			->setCellValue('T1', 'descripcion_accidente_ini')
			->setCellValue('U1', 'apellido_paterno')
			->setCellValue('V1', 'apellido_materno')
			->setCellValue('W1', 'nombres')
			->setCellValue('X1', 'rut')
			->setCellValue('Y1', 'cod_area')
			->setCellValue('Z1', 'telefono_informante_oa')
			->setCellValue('AA1', 'correo_electronico_informante_oa')
			->setCellValue('AB1', 'xml_id');

$i=2;
foreach($ralfs as $ralf){
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

	#comuna
	$comuna_bd = (int) $ralf->comuna;
	$comuna = (strlen($comuna_bd) > 0 AND $comuna_bd > 0) ? ORM::factory('Comuna', $comuna_bd)->nombre : "";
	$STUltimaEvaluacionTasa=array(1=>'Se mantuvo',2=>'Fue rebajada',3=>'Fue recargada'); 
	$ultima_eval_ds67 = (isset($STUltimaEvaluacionTasa[$ralf->ultima_eval_ds67]))?$STUltimaEvaluacionTasa[$ralf->ultima_eval_ds67]:"";
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $ralf->xml->caso->CASO_CUN)
			->setCellValue('B'.$i, $ralf->rut_representante_legal)
			->setCellValue('C'.$i, $ralf->nombre_representante_legal)
			->setCellValue('D'.$i, $ralf->tasa_ds110)
			->setCellValue('E'.$i, $ralf->tasa_ds67)
			->setCellValue('F'.$i, $ultima_eval_ds67)
			->setCellValue('G'.$i, $ralf->nro_sucursales)
			->setCellValue('H'.$i, $ralf->promedio_anual_trabajadores)
			->setCellValue('I'.$i, $ralf->fecha_accidente)
			->setCellValue('J'.$i, $ralf->hora_accidente)
			->setCellValue('K'.$i, $tipo_calle)
			->setCellValue('L'.$i, $ralf->nombre_calle)
			->setCellValue('M'.$i, "'".$ralf->numero)
			->setCellValue('N'.$i, $ralf->resto_direccion)
			->setCellValue('O'.$i, $ralf->localidad)
			->setCellValue('P'.$i, $comuna)
			->setCellValue('Q'.$i, $criterio_gravedad_final)
			->setCellValue('R'.$i, $ralf->fecha_defuncion)
			->setCellValue('S'.$i, $lugar_defuncion)
			->setCellValue('T'.$i, $ralf->descripcion_accidente_ini)
			->setCellValue('U'.$i, $ralf->apellido_paterno)
			->setCellValue('V'.$i, $ralf->apellido_materno)
			->setCellValue('W'.$i, $ralf->nombres)
			->setCellValue('X'.$i, $ralf->rut)
			->setCellValue('Y'.$i, "'".$ralf->cod_area)
			->setCellValue('Z'.$i, "'".$ralf->telefono_informante_oa)
			->setCellValue('AA'.$i, $ralf->correo_electronico_informante_oa)
			->setCellValue('AB'.$i, $ralf->xml_id);
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle('Hoja');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ralf1.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>