<?php

require_once(Kohana::find_file('vendor/phpexcel/', 'PHPExcel'));


$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'cun')
			->setCellValue('B1', 'fecha_creacion')
			->setCellValue('C1', 'region')
			->setCellValue('D1', 'trabajador_rut')
			->setCellValue('E1', 'trabajador_nombres')
			->setCellValue('F1', 'trabajador_apellido_paterno')
			->setCellValue('G1', 'trabajador_apellido_materno')
			->setCellValue('H1', 'empleador_rut')
			->setCellValue('I1', 'empleador_nombre')
			->setCellValue('J1', 'investigador_rut')
			->setCellValue('K1', 'investigador_nombres')
			->setCellValue('L1', 'investigador_apellido_paterno')
			->setCellValue('M1', 'investigador_apellido_materno')
			->setCellValue('N1', 'xml_id');

$i=2;
foreach($ralfs as $ralf){

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $ralf->xml->caso->CASO_CUN)
			->setCellValue('B'.$i, $ralf->fecha_creacion)
			->setCellValue('C'.$i, $ralf->region)
			->setCellValue('D'.$i, $ralf->trabajador_run)
			->setCellValue('E'.$i, $ralf->trabajador_nombres)
			->setCellValue('F'.$i, $ralf->trabajador_apellido_paterno)
			->setCellValue('G'.$i, $ralf->trabajador_apellido_materno)
			->setCellValue('H'.$i, $ralf->empresa_rut)
			->setCellValue('I'.$i, $ralf->empresa_razon_social)
			->setCellValue('J'.$i, $ralf->investigador_rut)
			->setCellValue('K'.$i, $ralf->investigador_nombres)
			->setCellValue('L'.$i, $ralf->investigador_apellido_paterno)
			->setCellValue('M'.$i, $ralf->investigador_apellido_materno)
			->setCellValue('N'.$i, $ralf->xml_id);
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle('Hoja');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ralfCausas.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>