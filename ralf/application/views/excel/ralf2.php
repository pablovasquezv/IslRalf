<?php

require_once(Kohana::find_file('vendor/phpexcel/', 'PHPExcel'));


$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'cun')
			->setCellValue('B1', 'medida')
			->setCellValue('C1', 'fecha_notificacion_medidas_inmediatas')
			->setCellValue('D1', 'apellido_paterno')
			->setCellValue('E1', 'apellido_materno')
			->setCellValue('F1', 'nombres')
			->setCellValue('G1', 'rut')
			->setCellValue('H1', 'cod_area')
			->setCellValue('I1', 'numero')
			->setCellValue('J1', 'xml_id');

$i=2;
foreach($ralfs as $ralf){

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $ralf->xml->caso->CASO_CUN)
			->setCellValue('B'.$i, $ralf->medida)
			->setCellValue('C'.$i, $ralf->fecha_notificacion_medidas_inmediatas)
			->setCellValue('D'.$i, $ralf->apellido_paterno)
			->setCellValue('E'.$i, $ralf->apellido_materno)
			->setCellValue('F'.$i, $ralf->nombres)
			->setCellValue('G'.$i, $ralf->rut)
			->setCellValue('H'.$i, "'".$ralf->cod_area)
			->setCellValue('I'.$i, "'".$ralf->numero)
			->setCellValue('J'.$i, $ralf->xml_id);
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle('Hoja');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ralf2.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>