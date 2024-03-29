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
        ->setCellValue('J1', 'descripcion')
        ->setCellValue('K1', 'medida_inmediata')
        ->setCellValue('L1', 'glosa_causa')
        ->setCellValue('M1', 'fecha_verificacion')
        ->setCellValue('N1', 'cumplimiento_medida')
        ->setCellValue('O1', 'fecha_cumplimiento')
        ->setCellValue('P1', 'observacion_verificacion')
        ->setCellValue('Q1', 'verificador_rut')
        ->setCellValue('R1', 'verificador_nombres')
        ->setCellValue('S1', 'verificador_apellido_paterno')
        ->setCellValue('T1', 'verificador_apellido_materno')
        ->setCellValue('V1', 'xml_id');

$i = 2;
foreach ($ralfs as $ralf) {

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $ralf->xml->caso->CASO_CUN)
            ->setCellValue('B' . $i, $ralf->fecha_creacion)
            ->setCellValue('C' . $i, $ralf->region)
            ->setCellValue('D' . $i, $ralf->trabajador_run)
            ->setCellValue('E' . $i, $ralf->trabajador_nombres)
            ->setCellValue('F' . $i, $ralf->trabajador_apellido_paterno)
            ->setCellValue('G' . $i, $ralf->trabajador_apellido_materno)
            ->setCellValue('H' . $i, $ralf->empresa_rut)
            ->setCellValue('I' . $i, $ralf->empresa_razon_social)
            ->setCellValue('J' . $i, $ralf->descripcion)
            ->setCellValue('K1' . $i, ($ralf->medida_inmediata == 1) ? 'SI' : 'NO')
            ->setCellValue('L1' . $i, $ralf->glosa_causa)
            ->setCellValue('M1' . $i, $ralf->fecha_verificacion)
            ->setCellValue('N1' . $i, $ralf->cumplimiento_medida)
            ->setCellValue('O1' . $i, $ralf->fecha_cumplimiento)
            ->setCellValue('P1' . $i, $ralf->observacion_verificacion)
            ->setCellValue('Q1' . $i, $ralf->verificador_rut)
            ->setCellValue('R1' . $i, $ralf->verificador_nombres)
            ->setCellValue('S1' . $i, $ralf->verificador_apellido_paterno)
            ->setCellValue('T1' . $i, $ralf->verificador_apellido_materno)
            ->setCellValue('V1' . $i, $ralf->xml_id);
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