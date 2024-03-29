<h3><?php echo __('Resolución')?></h3>

<div class='form_section_container'>
  <div class='form_section resolution'>
    <div class='row'>
      <div class='field numero'>
		<label for="complaint_reca_attributes_resolution_attributes_numero"><?php echo __('Nº Resolución'); ?></label><br />
		<div class='protected_field numero'><?php echo $xml->ZONA_H->resolucion->num_resol; ?></div>
      </div>
      <div class='field derivacion'>
		<label for="complaint_reca_attributes_resolution_attributes_derivacion"><?php echo __('Derivación 77'); ?></label><br />
		<div class='protected_field derivacion'><?php echo $si_o_no[(string)$xml->ZONA_H->resolucion->derivacion77] ? : 'n/a';?></div>
      </div>
      <div class='field tipo_accidente_enfermedad'>
		<label for="complaint_reca_attributes_resolution_attributes_tipo_accidente_enfermedad"><?php echo __('Tipo accidente o enfermedad'); ?></label><br />
		<div class='protected_field tipo_accidente_enfermedad'><?php echo $tipo_acc_enf[(string)$xml->ZONA_H->resolucion->tipo_acc_enf] ? : 'n/a'?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field indicacion'>
		<label for="complaint_reca_attributes_resolution_attributes_indicaciones"><?php echo __('Indicaciones'); ?></label><br />
        <div class='protected_field indicacion'><?php echo ucfirst(trim($xml->ZONA_H->resolucion->indicaciones)) ? : 'n/a'; ?></div>
      </div>
    </div>
    <div class='qualifier'>
      <div class='row'>
        <div class='field nombres'>
          <label for="complaint_reca_attributes_resolution_attributes_qualifier_attributes_nombres"><?php echo __('Nombres'); ?></label><br />
          <div class='protected_field nombres'><?php echo $xml->ZONA_H->resolucion->calificador->nombres; ?></div>
        </div>
        <div class='field apellido_paterno'>
          <label for="complaint_reca_attributes_resolution_attributes_qualifier_attributes_apellido_paterno"><?php echo __('Apellido Paterno'); ?></label><br />
          <div class='protected_field apellido_paterno'><?php echo $xml->ZONA_H->resolucion->calificador->apellido_paterno; ?></div>
        </div>
        <div class='field apellido_materno'>
          <label for="complaint_reca_attributes_resolution_attributes_qualifier_attributes_apellido_materno"><?php echo __('Apellido Materno'); ?></label><br />
          <div class='protected_field apellido_materno'><?php echo $xml->ZONA_H->resolucion->calificador->apellido_materno; ?></div>
        </div>
      </div>
      <div class='row'>
        <div class='field rut'>
          <label for="complaint_reca_attributes_resolution_attributes_qualifier_attributes_rut"><?php echo __('Rut'); ?></label><br />
          <div class='protected_field rut'><?php echo $xml->ZONA_H->resolucion->calificador->rut; ?></div>
        </div>
        <div class='field codigo_sexo'>
          <label for="complaint_reca_attributes_resolution_attributes_qualifier_attributes_codigo_sexo"><?php echo __('Sexo'); ?></label><br />
          <div class='protected_field codigo_sexo'><?php echo $sexo[(string)$xml->ZONA_H->resolucion->calificador->sexo] ? : 'n/a'; ?></div>
        </div>
        <div class='field fecha_nacimiento'>
          <label for="complaint_reca_attributes_resolution_attributes_qualifier_attributes_fecha_nacimiento"><?php echo __('Fecha Nacimiento'); ?></label><br />
          <div class='protected_field fecha_nacimiento'><?php echo $xml->ZONA_H->resolucion->calificador->fecha_nacimiento ? : 'n/a'; ?></div>
        </div>
        <div class='field codigo_nacionalidad'>
          <label for="complaint_reca_attributes_resolution_attributes_qualifier_attributes_codigo_nacionalidad"><?php echo __('Nacionalidad'); ?></label><br />
          <div class='protected_field codigo_nacionalidad'><?php echo $nacionalidades[(string)$xml->ZONA_H->resolucion->calificador->pais_nacionalidad]; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
