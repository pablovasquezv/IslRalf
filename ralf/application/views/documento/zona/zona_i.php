<h3><?php echo __('Incapacidad Temporal') ?></h3>
<div class='form_section_container'>
    <div class='form_section incapacidad_temporal'>
        <div class='row'>
            <div class='field fecha_inicio_incapacidad'>
                <label for="rela_incapacidad_temporal_attributes_fecha_inicio_incapacidad"><?php echo __('Fecha Inicio'); ?></label><br />
                <div class='protected_field fecha_alta_medica'><?php echo $xml->ZONA_I->incapacidad_temporal->fecha_inicio_incap_temp; ?></div>
            </div>
            <div class='field fecha_termino_incapacidad'>
                <label for="rela_incapacidad_temporal_attributes_fecha_termino_incapacidad"><?php echo __('Fecha Termino'); ?></label><br />
                <div class='protected_field tipo_alta_medica'><?php echo $xml->ZONA_I->incapacidad_temporal->fecha_termino_incap_temp; ?></div>
            </div>
            <div class='field cantidad_dias_incapacidad'>
                <label for="rela_incapacidad_temporal_attributes_fecha_termino_incapacidad"><?php echo __('Número de Días'); ?></label><br />
                <div class='protected_field cantidad_dias_incapacidad'><?php echo $xml->ZONA_I->incapacidad_temporal->n_dias_incap_temp; ?></div>
            </div>
        </div>
    </div>
</div>