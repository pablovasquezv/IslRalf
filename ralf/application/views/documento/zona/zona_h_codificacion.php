<?php if(isset ($xml->ZONA_H->resolucion->codificacion_enfermedad->codigo_agente_enfermedad) OR isset ($xml->ZONA_H->resolucion->codificacion_accidente)): ?>
<h3><?php echo __('Codificación'); ?></h3>
<?php endif;?>
<?php if(isset ($xml->ZONA_H->resolucion->codificacion_enfermedad->codigo_agente_enfermedad)):?>
<div class='form_section_container'>
  <div class='form_section disease_agent'>
    <div class='row'>
      <div class='field disease_agent_code'>
        <label for="complaint_reca_attributes_cod_accident_attributes_disease_agent_code"><?php echo __('Agente enfermedad'); ?></label><br />
        <div class='protected_field disease_agent_code'><?php echo $xml->ZONA_H->resolucion->codificacion_enfermedad->codigo_agente_enfermedad; ?></div>
      </div>
    </div>
  </div>
</div>
<?php endif?>

<?php if(isset ($xml->ZONA_H->resolucion->codificacion_accidente)):?>
<div class='form_section_container'>
  <div class='form_section cod_accident'>
    <div class='row'>
      <div class='field codigo_forma'>
        <label for="complaint_reca_attributes_cod_accident_attributes_codigo_forma"><?php echo __('Forma accidente'); ?></label><br />
        <div class='protected_field codigo_forma'><?php echo $codigo_forma[(string)$xml->ZONA_H->resolucion->codificacion_accidente->codigo_forma]; ?></div>
      </div>
      <div class='field codigo_contraparte'>
        <label for="complaint_reca_attributes_cod_accident_attributes_codigo_contraparte"><?php echo __('Contraparte accidente'); ?></label><br />
        <div class='protected_field codigo_contraparte'><?php echo $codigo_contraparte[(string)$xml->ZONA_H->resolucion->codificacion_accidente->codigo_contraparte]; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field codigo_agente_accidente'>
        <label for="complaint_reca_attributes_cod_accident_attributes_codigo_agente_accidente"><?php echo __('Agente accidente'); ?></label><br />
        <div class='protected_field codigo_agente_accidente'><?php echo $codigo_agente_accidente[(string)$xml->ZONA_H->resolucion->codificacion_accidente->codigo_agente_accidente]; ?></div>
      </div>
      <div class='field codigo_modo_transporte'>
        <label for="complaint_reca_attributes_cod_accident_attributes_codigo_modo_transporte"><?php echo __('Modo transporte'); ?></label><br />
        <div class='protected_field codigo_modo_transporte'><?php echo $codigo_modo_transporte[(string)$xml->ZONA_H->resolucion->codificacion_accidente->codigo_modo_transporte]; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field codigo_tipo_evento'>
        <label for="complaint_reca_attributes_cod_accident_attributes_codigo_tipo_evento"><?php echo __('Tipo evento'); ?></label><br />
        <div class='protected_field codigo_tipo_evento'><?php echo $codigo_tipo_evento[(string)$xml->ZONA_H->resolucion->codificacion_accidente->codigo_tipo_evento]; ?></div>
      </div>
      <div class='field codigo_papel_lesionado'>
        <label for="complaint_reca_attributes_cod_accident_attributes_codigo_papel_lesionado"><?php echo __('Papel lesionado'); ?></label><br />
        <div class='protected_field codigo_papel_lesionado'><?php echo $codigo_papel_lesionado[(string)$xml->ZONA_H->resolucion->codificacion_accidente->codigo_papel_lesionado]; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field codigo_intencionalidad'>
        <label for="complaint_reca_attributes_cod_accident_attributes_codigo_intencionalidad"><?php echo __('Intencionalidad accidente'); ?></label><br />
        <div class='protected_field codigo_intencionalidad'><?php echo $codigo_intencionalidad[(string)$xml->ZONA_H->resolucion->codificacion_accidente->codigo_intencionalidad]; ?></div>
      </div>
    </div>
  </div>
</div>
<?php endif?>