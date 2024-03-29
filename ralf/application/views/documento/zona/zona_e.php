<h3><?php echo __('Datos de la Enfermedad') ?></h3>

<div class='form_section_container'>
  <div class='form_section disease'>
    <div class='row'>
      <div class='field sintomas'>
		<label for="complaint_diep_attributes_disease_attributes_sintomas"><?php echo __('Describa las molestias o síntomas que actualmente tiene el trabajador/a'); ?></label><br />
		<div class='protected_field sintomas'><?php echo $xml->ZONA_E->enfermedad->sintoma; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field fecha_sintoma'>
		<label for="complaint_diep_attributes_disease_attributes_fecha_sintoma"><?php echo __('¿desde cuando tiempo tiene estas molestias o síntomas?'); ?></label><br />
		<div class='protected_field fecha_sintoma'><?php echo Utiles::full_date((string)$xml->ZONA_E->enfermedad->fecha_sintoma); ?></div>
      </div>
      <div class='field antecedentes_previos'>
		<label for="complaint_diep_attributes_disease_attributes_antecedentes_previos"><?php echo __('¿había tenido estas molestias en el puesto de trabajo actual, anteriormente?'); ?></label><br />
		<div class='protected_field antecedentes_previos'><?php echo $si_o_no[(string)$xml->ZONA_E->enfermedad->antecedente_previo]; ?></div>
      </div>
      <div class='field parte_del_cuerpo'>
		<label for="complaint_diep_attributes_disease_attributes_parte_del_cuerpo"><?php echo __('Parte del cuerpo afectada'); ?></label><br />
		<div class='protected_field parte_del_cuerpo'><?php echo $xml->ZONA_E->enfermedad->parte_cuerpo; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field descripcion_trabajo'>
		<label for="complaint_diep_attributes_disease_attributes_descripcion_trabajo"><?php echo __('Describa el trabajo o actividad que realizaba cuando comenzaron las molestias'); ?></label><br />
		<div class='protected_field descripcion_trabajo'><?php echo $xml->ZONA_E->enfermedad->direccion_trabajo; ?></div>
      </div>
    <div class='row'>
      <div class='field puesto_trabajado'>
		<label for="complaint_diep_attributes_disease_attributes_puesto_trabajado"><?php echo __('Nombre del puesto de trabajo o actividad que realizaba cuando comenzaron las molestias'); ?></label><br />
		<div class='protected_field puesto_trabajado'><?php echo $xml->ZONA_E->enfermedad->puesto_trabajo; ?></div>
      </div>
      <div class='field antecedentes_companero'>
		<label for="complaint_diep_attributes_disease_attributes_antecedentes_companero"><?php echo __('¿existen compañeros de trabajo con las mismas molestias?'); ?></label><br />
		<div class='protected_field antecedentes_companero'><?php echo $si_o_no[(string)$xml->ZONA_E->enfermedad->antecedente_companero]; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field agente_sospechoso'>
		<label for="complaint_diep_attributes_disease_attributes_agente_sospechoso"><?php echo __('¿qué cosas o agentes del trabajo cree ud. que le causan estas molestias?'); ?></label><br />
		<div class='protected_field agente_sospechoso'><?php echo $xml->ZONA_E->enfermedad->agente_sospechoso; ?></div>
      </div>
      <div class='field fecha_agente'>
		<label for="complaint_diep_attributes_disease_attributes_fecha_agente"><?php echo __('¿desde cuando ha estado expuesto o trabajando con estas cosas o agentes del trabajo?'); ?></label><br />
		<div class='protected_field fecha_agente'><?php echo Utiles::full_date((string)$xml->ZONA_E->enfermedad->fecha_agente); ?></div>
      </div>
    </div>
  </div>
</div>
</div>