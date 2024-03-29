<h3><?php echo __('Alta Medica') ?></h3>
<div class='form_section_container'>
    <div class='form_section alta_medica'>
        <div class='row'>
            <div class='field fecha_alta_medica'>
                <label for="alme_alta_medica_attributes_fecha_alta_medica"><?php echo __('Fecha'); ?></label><br />
                <div class='protected_field fecha_alta_medica'><?php echo $xml->ZONA_K->indicaciones_alta_medica->fecha_alta_medica; ?></div>
            </div>
            <div class='field tipo_alta_medica'>
                <label for="alme_alta_medica_attributes_tipo_alta_medica"><?php echo __('Tipo Alta Medica'); ?></label><br />
                <div class='protected_field tipo_alta_medica'><?php echo $alta_medica_alme[(string)$xml->ZONA_K->indicaciones_alta_medica->tipo_alta_medica]; ?></div>
            </div>
            <div class='field otra_alta_medica'>
                <label for="alme_alta_medica_attributes_otro_motivo_alta"><?php echo __('Otro Motivo Alta'); ?></label><br />
                <div class='protected_field otra_alta_medica'><?php echo $xml->ZONA_K->indicaciones_alta_medica->otro_motivo_alta; ?></div>
            </div>
            <div class='field evaluacion_incapacidad'>
                <label for="alme_alta_medica_attributes_evaluacion_incapacidad"><?php echo __('Evaluacion Incapacidad'); ?></label><br />
                <div class='protected_field evaluacion_incapacidad'><?php echo $si_o_no[(string)$xml->ZONA_K->indicaciones_alta_medica->evaluacion_incapacidad]; ?></div>
            </div>
        </div>
    </div>
</div>

