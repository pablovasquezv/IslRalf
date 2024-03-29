<h3><?php echo __('Identificación del Denunciante'); ?></h3>
<div class='form_section_container'>
  <div class='form_section complainant'>
    <div class='row'>
      <div class='field nombre_completo'>
        <label for="complaint_diat_attributes_complainant_attributes_nombre_completo"><?php echo __('Nombre completo'); ?></label><br />
        <div class='protected_field nombre_completo'><?php echo $xml->ZONA_F->denunciante->nombre_denunciante; ?></div>
      </div>
      <div class='field run'>
		<label for="complaint_diat_attributes_complainant_attributes_run"><?php echo __('Run'); ?></label><br />
        <div class='protected_field run'><?php echo $xml->ZONA_F->denunciante->rut_denunciante; ?></div>
      </div>
    <!--</div>
    <div class='row'>
      <div class='telephone'>-->
        <div class='field codigo_area'>
          <label for="complaint_diat_attributes_complainant_attributes_telephone_attributes_codigo_area"><?php echo __('Cód. área'); ?></label><br />
          <div class='protected_field codigo_area'>
            <?php echo $xml->ZONA_F->denunciante->telefono_denunciante->cod_pais ? "({$xml->ZONA_F->denunciante->telefono_denunciante->cod_pais})" : ''; ?>
            <?php echo $xml->ZONA_F->denunciante->telefono_denunciante->cod_area ? : 'n/a'; ?>
          </div>
        </div>
        <div class='field numero_telefono'>
          <label for="complaint_diat_attributes_complainant_attributes_telephone_attributes_numero"><?php echo __('Nº teléfono'); ?></label><br />
          <div class='protected_field numero_telefono'><?php echo $xml->ZONA_F->denunciante->telefono_denunciante->numero ? : 'n/a'; ?></div>
        </div><!--
      </div>
      -->
      <div class='field complainant_classification_id'>
        <label for="complaint_diat_attributes_complainant_attributes_complainant_classification_id"><?php echo __('Clasificación del denunciante'); ?></label><br />
        <div class='protected_field complainant_classification_id'><?php echo $denunciante_class[(string)$xml->ZONA_F->denunciante->clasificacion]; ?></div>
      </div>
    </div>
  </div>
</div>
    
