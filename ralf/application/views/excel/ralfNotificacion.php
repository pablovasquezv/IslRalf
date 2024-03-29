<?php

require_once(Kohana::find_file('vendor/phpexcel/', 'PHPExcel'));

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'cun')
        ->setCellValue('B1', 'causa_notificacion')
        ->setCellValue('C1', 'fecha_notificacion_autoridad')
        ->setCellValue('D1', 'autoridad_receptora')
        ->setCellValue('E1', 'region_autoridad_receptora')
        ->setCellValue('F1', 'rut_profesional_autoridad')
        ->setCellValue('G1', 'apellido_paterno_autoridad')
        ->setCellValue('H1', 'apellido_materno_autoridad')
        ->setCellValue('I1', 'nombres_autoridad')
        ->setCellValue('J1', 'correo_elect_resp_autoridad')
        ->setCellValue('K1', 'tipo_multa')
        ->setCellValue('L1', 'fecha_inicio_multa')
        ->setCellValue('M1', 'fecha_fin_multa')
        ->setCellValue('N1', 'monto_multa')
        ->setCellValue('O1', 'recargo')
        ->setCellValue('P1', 'rut')
        ->setCellValue('Q1', 'nombres')
        ->setCellValue('R1', 'apellido_paterno')
        ->setCellValue('S1', 'apellido_materno')
        ->setCellValue('T1', 'xml_id');

$i=2;
foreach($ralfs as $ralf){

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$i, $ralf->xml->caso->CASO_CUN)
        ->setCellValue('B'.$i, $ralf->causa_notificacion)
        ->setCellValue('C'.$i, $ralf->fecha_notificacion_autoridad)
        ->setCellValue('D'.$i, $ralf->autoridad_receptora)
        ->setCellValue('E'.$i, $ralf->region_autoridad_receptora)
        ->setCellValue('F'.$i, $ralf->rut_profesional_autoridad)
        ->setCellValue('G'.$i, $ralf->apellido_paterno_autoridad)
        ->setCellValue('H'.$i, $ralf->apellido_materno_autoridad)
        ->setCellValue('I'.$i, $ralf->nombres_autoridad)
        ->setCellValue('J'.$i, $ralf->correo_elect_resp_autoridad)
        ->setCellValue('K1'.$i, $ralf->tipo_multa)
        ->setCellValue('L1'.$i, $ralf->fecha_inicio_multa)
        ->setCellValue('M1'.$i, $ralf->fecha_fin_multa)
        ->setCellValue('N1'.$i, $ralf->monto_multa)
        ->setCellValue('O1'.$i, $ralf->recargo)
        ->setCellValue('P1'.$i, $ralf->rut)
        ->setCellValue('Q1'.$i, $ralf->nombres)
        ->setCellValue('R1'.$i, $ralf->apellido_paterno)
        ->setCellValue('S1'.$i, $ralf->apellido_materno)
        ->setCellValue('T1'.$i, $ralf->xml_id);
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle('Hoja');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ralfVerificacion.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>