<?php

require_once(Kohana::find_file('vendor/phpexcel/', 'PHPExcel'));


$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
            //->setCellValue('A1', 'id')
            ->setCellValue('A1', 'cun')
			->setCellValue('B1', 'fecha_verificacion')
			->setCellValue('C1', 'verificador_apellido_paterno')
			->setCellValue('D1', 'verificador_apellido_materno')
			->setCellValue('E1', 'verificador_nombres')
			->setCellValue('F1', 'verificador_rut')
			->setCellValue('G1', 'xml_id');

$i=2;
foreach($ralfs as $ralf){

    $objPHPExcel->setActiveSheetIndex(0)
			//->setCellValue('A'.$i, $ralf->id)
			/*->setCellValue('B'.$i, $ralf->cumplimiento_medida_id)
			->setCellValue('C'.$i, $ralf->cumplimiento_medida_medida)
			->setCellValue('D'.$i, $ralf->cumplimiento_medida_medida_implementada)
			->setCellValue('E'.$i, $ralf->cumplimiento_medida_ampliacion_plazo)
			->setCellValue('F'.$i, $ralf->cumplimiento_medida_nueva_fecha_ampliacion_plazo)
			->setCellValue('G'.$i, $ralf->cumplimiento_medida_observaciones)*/
			->setCellValue('A'.$i, $ralf->xml->caso->CASO_CUN)
			->setCellValue('B'.$i, $ralf->fecha_verificacion)
			->setCellValue('C'.$i, $ralf->verificador_apellido_paterno)
			->setCellValue('D'.$i, $ralf->verificador_apellido_materno)
			->setCellValue('E'.$i, $ralf->verificador_nombres)
			->setCellValue('F'.$i, $ralf->verificador_rut)
			->setCellValue('G'.$i, $ralf->xml_id)
            ;
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle('Hoja');


#medidas
$objPHPExcel->createSheet(1);
$objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A1', 'ralf_id')
            ->setCellValue('B1', 'cumplimiento_medida_id')
			->setCellValue('C1', 'cumplimiento_medida_medida')
			->setCellValue('D1', 'cumplimiento_medida_medida_implementada')
			->setCellValue('E1', 'cumplimiento_medida_ampliacion_plazo')
			->setCellValue('F1', 'cumplimiento_medida_nueva_fecha_ampliacion_plazo')
			->setCellValue('G1', 'cumplimiento_medida_observaciones');

#body
$i=2;
foreach($ralfs as $ralf){
	$cumplimientos = ORM::factory('Cumplimiento_Medida')->where('xml_id', '=', $ralf->xml_id)->find_all();
	if (count($cumplimientos) > 0) {
		foreach ($cumplimientos as $cumplimiento) {
			$objPHPExcel->setActiveSheetIndex(1)
						->setCellValue('A'.$i, $ralf->id)
						->setCellValue('B'.$i, $cumplimiento->medida_id)
						->setCellValue('C'.$i, $cumplimiento->medida)
						->setCellValue('D'.$i, $cumplimiento->medida_implementada)
						->setCellValue('E'.$i, $cumplimiento->ampliacion_plazo)
						->setCellValue('F'.$i, $cumplimiento->nueva_fecha_ampliacion_plazo)
						->setCellValue('G'.$i, $cumplimiento->observaciones);
						$i++;
		}
		
	}
}

$objPHPExcel->getActiveSheet()->setTitle('Medidas');

$objPHPExcel->setActiveSheetIndex(0);


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ralf4.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>