<?php if(isset($xml->ZONA_G)):?>
<h3 class="tit-diag"><?php echo __('Identificación de la evaluación (diagnóstico).') ?></h3>
<?php if(count($xml->ZONA_G->evaluacion)>1):?>
<h4 class="sub-diag"><?php echo __('Se entiende que la primera evaluación es la principal')?></h4>
<?php else: ?><br />
<?php endif;?>
<?php $i=1; ?>
<?php foreach($xml->ZONA_G->evaluacion as $evaluacion):?>
<?php if(count($xml->ZONA_G->evaluacion)>1):?>
<h4><?php echo __("Diagnóstico")." ".$i++;?></h4>
<?php endif;?>
<div class='form_section_container'>
  <div class='form_section evaluation'>
    <div class='row'>
      <div class='field diagnostico'>
        <label for="complaint_reca_attributes_evaluation_attributes_diagnostico"><?php echo __('Diagnóstico'); ?></label><br />
        <div class='protected_field diagnostico'><?php echo $evaluacion->diagnostico; ?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field codigo_diagnostico'>
        <label for="complaint_reca_attributes_evaluation_attributes_codigo_diagnostico"><?php echo __('Código Diagnóstico'); ?></label><br />
        <div class='protected_field codigo_diagnostico'><?php echo $evaluacion->codigo_diagnostico; ?></div>
      </div>
      <div class='field ubicacion_lesion'>
        <label for="complaint_reca_attributes_evaluation_attributes_ubicacion_lesion"><?php echo __('Ubicación Lesión'); ?></label><br />
        <div class='protected_field ubicacion_lesion'><?php echo $evaluacion->ubicacion; ?></div>
      </div>
      <div class='field fecha_diagnostico'>
        <label for="complaint_reca_attributes_evaluation_attributes_fecha_diagnostico"><?php echo __('Fecha Diagnóstico'); ?></label><br />
        <div class='protected_field fecha_diagnostico'><?php echo Utiles::full_date((string)$evaluacion->fecha_diagnostico); ?></div>
      </div>
    </div>
  </div>
</div>
<?php endforeach;?>
<?php endif; ?>
