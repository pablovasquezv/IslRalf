<?php

require_once(Kohana::find_file('vendor/phpexcel/', 'PHPExcel'));


$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'cun')
			->setCellValue('B1', 'fecha_inicio_investigacion_acc')
			->setCellValue('C1', 'fecha_termino_investigacion_acc')
			->setCellValue('D1', 'hora_ingreso')
			->setCellValue('E1', 'hora_salida')
			->setCellValue('F1', 'jornada_momento_accidente')
			->setCellValue('G1', 'jornada_momento_accidente_otro')
			->setCellValue('H1', 'trabajo_habitual_cual')
			->setCellValue('I1', 'trabajo_habitual')
			->setCellValue('J1', 'antiguedad_annos')
			->setCellValue('K1', 'antiguedad_meses')
			->setCellValue('L1', 'antiguedad_dias')
			->setCellValue('M1', 'lugar_trabajo')
			->setCellValue('N1', 'direccion_sucursal_tipo_calle')
			->setCellValue('O1', 'direccion_sucursal_nombre_calle')
			->setCellValue('P1', 'direccion_sucursal_numero')
			->setCellValue('Q1', 'direccion_sucursal_resto_direccion')
			->setCellValue('R1', 'direccion_sucursal_localidad')
			->setCellValue('S1', 'direccion_sucursal_comuna')
			->setCellValue('T1', 'nro_comites_funcio')
			->setCellValue('U1', 'nro_comites_ds54_a1')
			->setCellValue('V1', 'exist_comites_lugar_acc')
			->setCellValue('W1', 'cump_ob_info_ds40_a21')
			->setCellValue('X1', 'reg_ohys_al_dia')
			->setCellValue('Y1', 'depto_pre_rie_teorico')
			->setCellValue('Z1', 'depto_pre_rie_real')
			->setCellValue('AA1', 'exp_pre_em_apellido_paterno')
			->setCellValue('AB1', 'exp_pre_em_apellido_materno')
			->setCellValue('AC1', 'exp_pre_em_nombres')
			->setCellValue('AD1', 'exp_pre_em_rut')
			->setCellValue('AE1', 'tipo_cont_exp_pre_em')
			->setCellValue('AF1', 'tipo_cont_exp_pre_em_otro')
			->setCellValue('AG1', 'nro_dias_jor_parcial_cont_exp_pre_emp')
			->setCellValue('AH1', 'nro_reg_a_s_exp_pre_em')
			->setCellValue('AI1', 'cat_exp_pre_em')
			->setCellValue('AJ1', 'programa_pre_rie')
			->setCellValue('AK1', 'trabajador_reg_subcontratacion')
			->setCellValue('AL1', 'registro_ac_antec_a66bis')
			->setCellValue('AM1', 'comite_par_fae_emp_ppal')
			->setCellValue('AN1', 'depto_pre_rie_emp_ppal')
			->setCellValue('AO1', 'imp_sist_gest_sst_emp_ppal')
			->setCellValue('AP1', 'fiscalizacion_con_multas_mat_sst')
			->setCellValue('AQ1', 'organismo_multas')
			->setCellValue('AR1', 'desc_acc_invest')
			->setCellValue('AS1', 'codigo_forma')
			->setCellValue('AT1', 'codigo_agente_accidente')
			->setCellValue('AU1', 'codigo_intencionalidad')
			->setCellValue('AV1', 'codigo_modo_transporte')
			->setCellValue('AW1', 'codigo_papel_lesionado')
			->setCellValue('AX1', 'codigo_contraparte')
			->setCellValue('AY1', 'codigo_tipo_evento')
			->setCellValue('AZ1', 'antecedentes_informacion_acc')
			->setCellValue('BA1', 'investigador_acc_apellido_paterno')
			->setCellValue('BB1', 'investigador_acc_apellido_materno')
			->setCellValue('BC1', 'investigador_acc_nombres')
			->setCellValue('BD1', 'investigador_acc_rut')
			->setCellValue('BE1', 'prof_invest_acc')
			->setCellValue('BF1', 'invest_es_experto')
			->setCellValue('BG1', 'categoria_experto')
			->setCellValue('BH1', 'nro_reg_a_s_invest_acc')
			->setCellValue('BI1', 'fecha_notificacion_me_correc')
			->setCellValue('BJ1', 'investigador_apellido_paterno')
			->setCellValue('BK1', 'investigador_apellido_materno')
			->setCellValue('BL1', 'investigador_nombres')
			->setCellValue('BM1', 'investigador_rut')
			/*->setCellValue('BN1', 'causa_medida_plazo_id')
			->setCellValue('BO1', 'causa_medida_plazo_causa')
			->setCellValue('BP1', 'causa_medida_plazo_medida')
			->setCellValue('BQ1', 'causa_medida_plazo_plazo')*/
			->setCellValue('BN1', 'xml_id');

$i=2;
$atributos = array(
	'categoria_experto' => 'STCategoriaExperto', 
	'codigo_tipo_evento' => 'STCodigo_Tipo_evento', 
	'codigo_contraparte' => 'STCodigo_contraparte',
	'codigo_papel_lesionado' => 'STCodigo_papel_lesionado',
	'codigo_modo_transporte' => 'STCodigo_modo_transporte',
	'codigo_intencionalidad' => 'STCodigo_intencionalidad',
	'codigo_agente_accidente' => 'STCodigo_agente_accidente',
	'codigo_forma' => 'STCodigo_forma',
	'organismo_multas' => 'STOrg_multas',
	'cat_exp_pre_em' => 'STCategoriaExperto',
	'tipo_cont_exp_pre_em' => 'STTipoContratoExperto',
	'jornada_momento_accidente' => 'STTipoJornada',

	'exist_comites_lugar_acc' => 'si_no',
	'cumb_ob_info_ds40_a21' => 'si_no',
	'reg_ohys_al_dia' => 'si_no',
	'depto_pre_rie_teorico' => 'si_no',
	'depto_pre_rie_real' => 'si_no',
	'programa_pre_rie' => 'si_no',
	'trabajador_reg_subcontratacion' => 'si_no',
	'registro_ac_antec_a66bis' => 'si_no',
	'comite_par_fae_emp_ppal' => 'si_no',
	'depto_pre_rie_emp_ppal' => 'si_no',
	'imp_sist_gest_sst_emp_ppal' => 'si_no',
	'fiscalizacion_con_multas_mat_sst' => 'si_no',
	'invest_es_experto' => 'si_no',
	'trabajo_habitual' => 'si_no'

);

foreach($ralfs as $ralf){
	$atributos_valores = array();
	foreach ($atributos as $atributo => $dominio) {
		$atributo_bd = (string) $ralf->$atributo;
		$atributo_array = Controller_Dominios::$dominio();
		//var_dump("atributo_id: ",$atributo_bd);
		if (strlen($atributo_bd) > 0 AND $atributo_bd > 0) {
			if (isset($atributo_array[$atributo_bd])) {
				$atributos_valores[$atributo] = $atributo_array[$atributo_bd];
			} else {
				$atributos_valores[$atributo] = "";
			}
		}else {
			$atributos_valores[$atributo] = "";
		} 
	}
	//var_dump($atributos_valores);
	//die();
	/*traductor de ids*/
	$lugar_trabajo_array=array(1=>'Casa Matriz',2=>'Sucursal Empresa');
	$lugar_trabajo = (isset($lugar_trabajo_array[$ralf->lugar_trabajo]))?$lugar_trabajo_array[$ralf->lugar_trabajo]:"";
	$direccion_sucursal_comuna_array = Model_St_Comuna::obtenerSinFiltro();
	$direccion_sucursal_comuna = (isset($direccion_sucursal_comuna_array[$ralf->direccion_sucursal_comuna]) && $ralf->direccion_sucursal_comuna != "" )?$direccion_sucursal_comuna_array[$ralf->direccion_sucursal_comuna]:"";
	$STDiasJornadaParcial=array(''=>'Seleccione',1=>'1 día',2=>'1,5 días',3=>'2 días',4=>'2,5 días',5=>'3 días',6=>'3,5 días',7=>'4 días');
	$nro_dias_jor_parcial_cont_exp_pre_emp = (isset($STDiasJornadaParcial[$ralf->nro_dias_jor_parcial_cont_exp_pre_emp]) && $ralf->nro_dias_jor_parcial_cont_exp_pre_emp != "")?$STDiasJornadaParcial[$ralf->nro_dias_jor_parcial_cont_exp_pre_emp]:"";
	/*****/
    $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $ralf->xml->caso->CASO_CUN)
			->setCellValue('B'.$i, $ralf->fecha_inicio_investigacion_acc)
			->setCellValue('C'.$i, $ralf->fecha_termino_investigacion_acc)
			->setCellValue('D'.$i, $ralf->hora_ingreso)
			->setCellValue('E'.$i, $ralf->hora_salida)
			->setCellValue('F'.$i, $atributos_valores['jornada_momento_accidente'])
			->setCellValue('G'.$i, $ralf->jornada_momento_accidente_otro)
			->setCellValue('H'.$i, $ralf->trabajo_habitual_cual)
			->setCellValue('I'.$i, $atributos_valores['trabajo_habitual'])
			->setCellValue('J'.$i, $ralf->antiguedad_annos)
			->setCellValue('K'.$i, $ralf->antiguedad_meses)
			->setCellValue('L'.$i, $ralf->antiguedad_dias)
			->setCellValue('M'.$i, $lugar_trabajo)
			->setCellValue('N'.$i, $ralf->direccion_sucursal_tipo_calle)
			->setCellValue('O'.$i, $ralf->direccion_sucursal_nombre_calle)
			->setCellValue('P'.$i, "'".$ralf->direccion_sucursal_numero)
			->setCellValue('Q'.$i, $ralf->direccion_sucursal_resto_direccion)
			->setCellValue('R'.$i, $ralf->direccion_sucursal_localidad)
			->setCellValue('S'.$i, $direccion_sucursal_comuna)
			->setCellValue('T'.$i, $ralf->nro_comites_funcio)
			->setCellValue('U'.$i, $ralf->nro_comites_ds54_a1)
			->setCellValue('V'.$i, $atributos_valores['exist_comites_lugar_acc'])
			->setCellValue('W'.$i, $atributos_valores['cumb_ob_info_ds40_a21'])
			->setCellValue('X'.$i, $atributos_valores['reg_ohys_al_dia'])
			->setCellValue('Y'.$i, $atributos_valores['depto_pre_rie_teorico'])
			->setCellValue('Z'.$i, $atributos_valores['depto_pre_rie_real'])
			->setCellValue('AA'.$i, $ralf->exp_pre_em_apellido_paterno)
			->setCellValue('AB'.$i, $ralf->exp_pre_em_apellido_materno)
			->setCellValue('AC'.$i, $ralf->exp_pre_em_nombres)
			->setCellValue('AD'.$i, $ralf->exp_pre_em_rut)
			->setCellValue('AE'.$i, $atributos_valores['tipo_cont_exp_pre_em'])
			->setCellValue('AF'.$i, $ralf->tipo_cont_exp_pre_em_otro)
			->setCellValue('AG'.$i, $nro_dias_jor_parcial_cont_exp_pre_emp)
			->setCellValue('AH'.$i, $ralf->nro_reg_a_s_exp_pre_em)
			->setCellValue('AI'.$i, $atributos_valores['cat_exp_pre_em'])
			->setCellValue('AJ'.$i, $atributos_valores['programa_pre_rie'])
			->setCellValue('AK'.$i, $atributos_valores['trabajador_reg_subcontratacion'])
			->setCellValue('AL'.$i, $atributos_valores['registro_ac_antec_a66bis'])
			->setCellValue('AM'.$i, $atributos_valores['comite_par_fae_emp_ppal'])
			->setCellValue('AN'.$i, $atributos_valores['depto_pre_rie_emp_ppal'])
			->setCellValue('AO'.$i, $atributos_valores['imp_sist_gest_sst_emp_ppal'])
			->setCellValue('AP'.$i, $atributos_valores['fiscalizacion_con_multas_mat_sst'])
			->setCellValue('AQ'.$i, $atributos_valores['organismo_multas'])
			->setCellValue('AR'.$i, $ralf->desc_acc_invest)
			->setCellValue('AS'.$i, $atributos_valores['codigo_forma'])
			->setCellValue('AT'.$i, $atributos_valores['codigo_agente_accidente'])
			->setCellValue('AU'.$i, $atributos_valores['codigo_intencionalidad'])
			->setCellValue('AV'.$i, $atributos_valores['codigo_modo_transporte'])
			->setCellValue('AW'.$i, $atributos_valores['codigo_papel_lesionado'])
			->setCellValue('AX'.$i, $atributos_valores['codigo_contraparte'])
			->setCellValue('AY'.$i, $atributos_valores['codigo_tipo_evento'])
			->setCellValue('AZ'.$i, $ralf->antecedentes_informacion_acc)
			->setCellValue('BA'.$i, $ralf->investigador_acc_apellido_paterno)
			->setCellValue('BB'.$i, $ralf->investigador_acc_apellido_materno)
			->setCellValue('BC'.$i, $ralf->investigador_acc_nombres)
			->setCellValue('BD'.$i, $ralf->investigador_acc_rut)
			->setCellValue('BE'.$i, $ralf->prof_invest_acc)
			->setCellValue('BF'.$i, $atributos_valores['invest_es_experto'])
			->setCellValue('BG'.$i, $atributos_valores['categoria_experto'])
			->setCellValue('BH'.$i, $ralf->nro_reg_a_s_invest_acc)
			->setCellValue('BI'.$i, $ralf->fecha_notificacion_me_correc)
			->setCellValue('BJ'.$i, $ralf->investigador_apellido_paterno)
			->setCellValue('BK'.$i, $ralf->investigador_apellido_materno)
			->setCellValue('BL'.$i, $ralf->investigador_nombres)
			->setCellValue('BM'.$i, $ralf->investigador_rut)
			/*->setCellValue('BN'.$i, $ralf->causa_medida_plazo_id)
			->setCellValue('BO'.$i, $ralf->causa_medida_plazo_causa)
			->setCellValue('BP'.$i, $ralf->causa_medida_plazo_medida)
			->setCellValue('BQ'.$i, $ralf->causa_medida_plazo_plazo)*/
			->setCellValue('BN'.$i, $ralf->xml_id)
            ;
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle('Hoja');

#causa_medida
$objPHPExcel->createSheet(1);
$objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A1', 'ralf_id')
            ->setCellValue('B1', 'causa_medida_plazo_id')
			->setCellValue('C1', 'causa_medida_plazo_causa')
			->setCellValue('D1', 'causa_medida_plazo_medida')
			->setCellValue('E1', 'causa_medida_plazo_plazo');
#body
$i=2;
foreach($ralfs as $ralf){
	$causas=ORM::factory('Causa_Medida_Correctiva')->where('xml_id','=',$ralf->xml_id)->find_all();
	if (count($causas) > 0) {
		foreach ($causas as $causa) {
			$objPHPExcel->setActiveSheetIndex(1)
						->setCellValue('A'.$i, $ralf->id)
						->setCellValue('B'.$i, $causa->causa_medida_plazo_id)
						->setCellValue('C'.$i, $causa->causa_medida_plazo_causa)
						->setCellValue('D'.$i, $causa->causa_medida_plazo_medida)
						->setCellValue('E'.$i, $causa->causa_medida_plazo_plazo);
			$i++;
		}
		
	}
}
$objPHPExcel->getActiveSheet()->setTitle('Medidas');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ralf3.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>
