<?php

  if(isset($xml->ZONA_D->accidente->tipo_accidente_trayecto) && !empty($xml->ZONA_D->accidente->tipo_accidente_trayecto)) {
    $tipo_accidente_trayecto=$accidente_trayecto[(string)$xml->ZONA_D->accidente->tipo_accidente_trayecto] ? : 'n/a';
  }else{
    $tipo_accidente_trayecto='n/a';
  }

  if(isset($xml->ZONA_D->accidente->medio_prueba) && !empty($xml->ZONA_D->accidente->medio_prueba)) {
    $medio_prueba=$prueba[(string)$xml->ZONA_D->accidente->medio_prueba] ? : 'n/a';
  }else{
    $medio_prueba='n/a';
  }




$cod_tipo_calle_acc=(int)$xml->ZONA_D->accidente->direccion_accidente->tipo_calle;

if($cod_tipo_calle_acc==1){
  $tipo_calle_acc="Avenida";

}elseif($cod_tipo_calle_acc==2){
  $tipo_calle_acc="Calle";

}elseif($cod_tipo_calle_acc==3){
  $tipo_calle_acc="Pasaje";
}else {
    $tipo_calle_acc="n/a";
}



?>


<h3><?php echo __('Datos del Accidente'); ?></h3>
<div class='form_section_container'>
  <div class='form_section accident'>
    <div class='row'>
      <div class='field fecha'>
		<label for="complaint_diat_attributes_accident_attributes_fecha"><?php echo __('Fecha y hora del accidente'); ?></label><br />
		<div class='protected_field fecha'>           
            <?php 
                $fecha_accidente = (string)$xml->ZONA_D->accidente->fecha_accidente;
                $hora_incorrecta = substr($fecha_accidente, -1) == 'Z';
                if($hora_incorrecta) {
                    // Las horas de accidentes que terminan en Z son erroneas
                    // porque quienes generaron los documentos no entendian el significado de esta Z.
                    // Por lo tanto, no mostramos la hora para estos documentos.
                    echo Utiles::full_date(strstr($fecha_accidente, 'T', TRUE), FALSE). ' [*]'; 
                } else {
                    echo Utiles::full_date($fecha_accidente, TRUE); 
                }
            ?>
        </div>
        <?php if($hora_incorrecta) { echo '[*] Hora del accidente no fue ingresada en este documento'; } ?>
	  </div>
      <div class='field hora_ingreso_trabajo'>
		<label for="complaint_diat_attributes_accident_attributes_hora_ingreso_trabajo"><?php echo __('Hora ingreso trabajo'); ?></label><br />
		<div class='protected_field hora_ingreso_trabajo'><?php echo $xml->ZONA_D->accidente->hora_ingreso; ?></div>
		</div>
      <div class='field hora_salida_trabajo'>
		<label for="complaint_diat_attributes_accident_attributes_hora_salida_trabajo"><?php echo __('Hora salida trabajo'); ?></label><br />
		<div class='protected_field hora_salida_trabajo'><?php echo $xml->ZONA_D->accidente->hora_salida; ?></div>
		</div>
    </div>
    <div class='row'>
      <!--<div class='address'>-->
        <div class='field codigo_tipo_calle'>
		<label for="complaint_diat_attributes_accident_attributes_address_attributes_codigo_tipo_calle"><?php echo __('Tipo calle'); ?></label><br />
		<div class='protected_field codigo_tipo_calle'><?php echo $tipo_calle_acc; ?></div>
		</div>
        <div class='field nombre_calle'>
		<label for="complaint_diat_attributes_accident_attributes_address_attributes_nombre"><?php echo __('Nombre'); ?></label><br />
		<div class='protected_field nombre_calle'><?php echo $xml->ZONA_D->accidente->direccion_accidente->nombre_calle; ?></div>
		</div>
        <div class='field numero_calle'>
		<label for="complaint_diat_attributes_accident_attributes_address_attributes_numero"><?php echo __('Número'); ?></label><br />
		<div class='protected_field numero_calle'><?php echo $xml->ZONA_D->accidente->direccion_accidente->numero; ?></div>
		</div>
        <div class='field resto_direccion'>
		<label for="complaint_diat_attributes_accident_attributes_address_attributes_resto_direccion"><?php echo __('Villa / población / sector'); ?></label><br />
		<div class='protected_field resto_direccion'><?php echo $xml->ZONA_D->accidente->direccion_accidente->resto_direccion ? : 'n/a'; ?></div>
		</div>
        <div class='field localidad'>
		<label for="complaint_diat_attributes_accident_attributes_address_attributes_localidad"><?php echo __('Localidad'); ?></label><br />
		<div class='protected_field localidad'><?php echo trim($xml->ZONA_D->accidente->direccion_accidente->localidad) ? : 'n/a' ?></div>
		</div>
        <div class='field codigo_comuna'>
		<label for="complaint_diat_attributes_accident_attributes_address_attributes_codigo_comuna"><?php echo __('Comuna'); ?></label><br />
		<div class='protected_field codigo_comuna'><?php echo $comunas[(string)$xml->ZONA_D->accidente->direccion_accidente->comuna]; ?></div>
		</div>
      <!--</div>-->

    </div>
    <div class='row'>
      <div class='field que_estaba_haciendo'>
		<label for="complaint_diat_attributes_accident_attributes_que_estaba_haciendo"><?php echo __('Señale qué estaba haciendo el trabajador al momento o justo antes del accidente'); ?></label><br />
		<div class='protected_field que_estaba_haciendo'><?php echo $xml->ZONA_D->accidente->que; ?></div>
      </div>
      <div class='field lugar_accidente'>
		<label for="complaint_diat_attributes_accident_attributes_lugar_accidente"><?php echo __('Señale el lugar donde ocurrió el accidente (nombre de la sección, edificio,área, etc.)'); ?></label><br />
		<div class='protected_field lugar_accidente'><?php echo $xml->ZONA_D->accidente->lugar_accidente; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field como_ocurrio'>
		<label for="complaint_diat_attributes_accident_attributes_como_ocurrio"><?php echo __('Describa ¿qué pasó o cómo ocurrió el accidente?'); ?></label><br />
		<div class='protected_field como_ocurrio'><?php echo $xml->ZONA_D->accidente->como; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field trabajo_habitual'>
		<label for="complaint_diat_attributes_accident_attributes_trabajo_habitual"><?php echo __('Señale cuál era su trabajo habitual'); ?></label><br />
		<div class='protected_field trabajo_habitual'><?php echo $xml->ZONA_D->accidente->trabajo_habitual_cual; ?></div>
      </div>
      <div class='field desarrollaba_trabajo_habitual'>
		<label for="complaint_diat_attributes_accident_attributes_desarrollaba_trabajo_habitual"><?php echo __('¿al momento del accidente desarrollaba su trabajo habitual?'); ?></label><br />
		<div class='protected_field desarrollaba_trabajo_habitual'><?php echo $si_o_no[(string)$xml->ZONA_D->accidente->trabajo_habitual]; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field accident_clasification_id'>
		<label for="complaint_diat_attributes_accident_attributes_accident_clasification_id"><?php echo __('Clasificación del accidente'); ?></label><br />
		<div class='protected_field accident_clasification_id'><?php echo $gravedad[(string)$xml->ZONA_D->accidente->gravedad]; ?></div>
		</div>
      <div class='field accident_type_id'>
		<label for="complaint_diat_attributes_accident_attributes_accident_type_id"><?php echo __('Tipo de accidente'); ?></label><br />
		<div class='protected_field accident_type_id'><?php echo $accidente_type[(string)$xml->ZONA_D->accidente->tipo_accidente]; ?></div>
		</div>
      <div class='field accident_type_journey_id'>
		<label for="complaint_diat_attributes_accident_attributes_accident_type_journey_id"><?php echo __('Tipo de accidente de trayecto'); ?></label><br />
		<div class='protected_field accident_type_journey_id'><?php echo $tipo_accidente_trayecto; ?></div>
		</div>
    </div>
    <div class='row'>
      <div class='field type_of_proof_id'>
		<label for="complaint_diat_attributes_accident_attributes_type_of_proof_id"><?php echo __('Medio de prueba'); ?></label><br />
		<div class='protected_field type_of_proof_id'><?php echo $medio_prueba; ?></div>
		</div>
      <div class='field proof_detail'>
		<label for="complaint_diat_attributes_accident_attributes_proof_detail"><?php echo __('Detalle del medio de prueba'); ?></label><br />
		<div class='protected_field proof_detail'><?php echo $xml->ZONA_D->accidente->detalle_prueba ? : 'n/a'; ?></div>
		</div>
    </div>
  </div>
</div>