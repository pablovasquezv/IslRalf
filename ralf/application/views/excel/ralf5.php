<?php

require_once(Kohana::find_file('vendor/phpexcel/', 'PHPExcel'));


$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'cun')
			->setCellValue('B1', 'fecha_informe_acciones_adoptadas')
			->setCellValue('C1', 'aplicacion_multa_art_80_ley')
			->setCellValue('D1', 'monto_multa')
			->setCellValue('E1', 'fecha_multa')
			->setCellValue('F1', 'recargo_ds67_a15')
			->setCellValue('G1', 'recargo_ds67_a5')
			->setCellValue('H1', 'fecha_inicio_recargo_a15')
			->setCellValue('I1', 'fecha_termino_recargo_a15')
			->setCellValue('J1', 'comunicacion_dir_trabajo')
			->setCellValue('K1', 'nro_comunic_dir_trabajo')
			->setCellValue('L1', 'fecha_comunic_dir_trabajo')
			->setCellValue('M1', 'comunicacion_seremi')
			->setCellValue('N1', 'identificacion_seremi')
			->setCellValue('O1', 'nro_comunic_seremi')
			->setCellValue('P1', 'fecha_comunic_seremi')
			->setCellValue('Q1', 'plan_esp_trabajo_empresa')
			->setCellValue('R1', 'fecha_ini_plan_trabajo_empresa')
			->setCellValue('S1', 'resumen_plan_trabajo')
			->setCellValue('T1', 'representante_oa_apellido_paterno')
			->setCellValue('U1', 'representante_oa_apellido_materno')
			->setCellValue('V1', 'representante_oa_nombres')
			->setCellValue('W1', 'representante_oa_rut')
			->setCellValue('X1', 'medidas_no_implementadas_fecha_verificacion')
			->setCellValue('Y1', 'medidas_no_implementadas_plazo_ampliado_fecha_verificacion')
			->setCellValue('Z1', 'xml_id');

$atributos = array(
	'plan_esp_trabajo_empresa' => 'si_no',
	'identificacion_seremi' => 'STNumSEREMI',
	'comunicacion_seremi' => 'si_no',
	'recargo_ds67_a5' => 'si_no',
	'recargo_ds67_a15' => 'si_no',
	'aplicacion_multa_art_80_ley' => 'si_no'
);

$i=2;
foreach($ralfs as $ralf){

	$atributos_valores = array();
	foreach ($atributos as $atributo => $dominio) {
		$atributo_bd = (int) $ralf->$atributo;
		$atributo_array = Controller_Dominios::$dominio();
		if (strlen($atributo_bd) > 0 AND $atributo_bd > 0) {
			if (isset($atributo_array[$atributo_bd])) {
				$atributos_valores[$atributo] = $atributo_array[$atributo_bd];
			} else {
				$atributos_valores[$atributo] = "";
			}
		} else {
			$atributos_valores[$atributo] = "";
		} 
	}

	$si_no=array(1=>'Si',2=>'No');
	$comunicacion_dir_trabajo = (isset($si_no[$ralf->comunicacion_dir_trabajo]))?$si_no[$ralf->comunicacion_dir_trabajo]:"";
    $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $ralf->xml->caso->CASO_CUN)
			->setCellValue('B'.$i, $ralf->fecha_informe_acciones_adoptadas)
			->setCellValue('C'.$i, $atributos_valores['aplicacion_multa_art_80_ley'])
			->setCellValue('D'.$i, $ralf->monto_multa)
			->setCellValue('E'.$i, $ralf->fecha_multa)
			->setCellValue('F'.$i, $atributos_valores['recargo_ds67_a15'])
			->setCellValue('G'.$i, $atributos_valores['recargo_ds67_a5'])
			->setCellValue('H'.$i, $ralf->fecha_inicio_recargo_a15)
			->setCellValue('I'.$i, $ralf->fecha_termino_recargo_a15)
			->setCellValue('J'.$i, $comunicacion_dir_trabajo)
			->setCellValue('K'.$i, $ralf->nro_comunic_dir_trabajo)
			->setCellValue('L'.$i, $ralf->fecha_comunic_dir_trabajo)
			->setCellValue('M'.$i, $atributos_valores['comunicacion_seremi'])
			->setCellValue('N'.$i, $atributos_valores['identificacion_seremi'])
			->setCellValue('O'.$i, $ralf->nro_comunic_seremi)
			->setCellValue('P'.$i, $ralf->fecha_comunic_seremi)
			->setCellValue('Q'.$i, $atributos_valores['plan_esp_trabajo_empresa'])
			->setCellValue('R'.$i, $ralf->fecha_ini_plan_trabajo_empresa)
			->setCellValue('S'.$i, $ralf->resumen_plan_trabajo)
			->setCellValue('T'.$i, $ralf->representante_oa_apellido_paterno)
			->setCellValue('U'.$i, $ralf->representante_oa_apellido_materno)
			->setCellValue('V'.$i, $ralf->representante_oa_nombres)
			->setCellValue('W'.$i, $ralf->representante_oa_rut)
			->setCellValue('X'.$i, $ralf->medidas_no_implementadas_fecha_verificacion)
			->setCellValue('Y'.$i, $ralf->medidas_no_implementadas_plazo_ampliado_fecha_verificacion)
			->setCellValue('Z'.$i, $ralf->xml_id)
            ;
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle('Hoja');
#causa_medida
$objPHPExcel->createSheet(1);
$objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A1', 'ralf_id')
            ->setCellValue('B1', 'ID')
			->setCellValue('C1', 'Medida');
#body
$i=2;
foreach($ralfs as $ralf){
	$medidas=ORM::factory('Medida')->where('xml_id','=',$ralf->xml_id)->where('origen','IN',array('medidas_no_implementadas','medidas_no_implementadas_plazo_ampliado'))->find_all();
	if (count($medidas) > 0) {
		foreach ($medidas as $medida) {
			$objPHPExcel->setActiveSheetIndex(1)
						->setCellValue('A'.$i, $ralf->id)
						->setCellValue('B'.$i, $medida->id)
						->setCellValue('C'.$i, $medida->medida);
			$i++;
		}
		
	}
}
$objPHPExcel->getActiveSheet()->setTitle('Medidas');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ralf5.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>