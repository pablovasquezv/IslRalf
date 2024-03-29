<h3><?php echo __('Datos del Documento')?></h3>
<div class='form_section_container'>
  <div class='form_section document_data'>
    <div class="row">
      <div class='field folio'>
        <label for="complaint_diat_attributes_document_datum_attributes_folio"><?php echo __('Folio');?></label><br />
        <div class='protected_field codigo_caso'><?php echo $xml->ZONA_A->documento->folio ? : 'N/A'; ?></div>
      </div>
      <div class='field codigo_caso'>
        <label for="complaint_diat_attributes_document_datum_attributes_codigo_caso"><?php echo __('Código caso');?></label><br />
        <div class='protected_field folio'><?php echo $xml->ZONA_A->documento->codigo_caso; ?></div>
      </div>
      <div class='field cun'>
        <label for="complaint_diat_attributes_document_datum_attributes_cun"><?php echo __('Cun');?></label><br />
        <div class='protected_field cun'><?php echo $xml->ZONA_A->documento->cun ? : 'n/a'; ?></div>
      </div>
      <div class='field fecha_emision'>
        <label for="complaint_diat_attributes_document_datum_attributes_fecha_emision"><?php echo __('Fecha emisión');?></label><br />
        <div class='protected_field fecha_emision'><?php echo Utiles::full_date((string)$xml->ZONA_A->documento->fecha_emision, TRUE); ?></div>
      </div>
    </div>
  </div>
</div>