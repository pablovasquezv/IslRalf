<h3><?php echo __('Alta Laboral') ?></h3>
<div class='form_section_container'>
    <div class='form_section alta_laboral'>
        <div class='row'>
            <div class='field fecha_alta_laboral'>
                <label for="alla_alta_laboral_attributes_fecha_alta_laboral"><?php echo __('Fecha'); ?></label><br />
                <div class='protected_field fecha_alta_laboral'><?php echo $xml->ZONA_J->indicaciones_alta_laboral->fecha_alta_laboral; ?></div>
            </div>
            <div class='field alta_inmediata'>
                <label for="alla_alta_laboral_attributes_alta_inmediata"><?php echo __('Alta Inmediata'); ?></label><br />
                <div class='protected_field alta_inmediata'><?php echo $si_o_no[(string)$xml->ZONA_J->indicaciones_alta_laboral->alta_inmediata]; ?></div>
            </div>
            <div class='field condiciones_alta'>
                <label for="alla_alta_laboral_attributes_condiciones_alta"><?php echo __('Condiciones'); ?></label><br />
                <div class='protected_field condiciones_alta'><?php echo $si_o_no[(string)$xml->ZONA_J->indicaciones_alta_laboral->condiciones]; ?></div>
            </div>
            <div class='field tipo_condicion_alta'>
                <label for="alla_alta_laboral_attributes_tipo_condicion_alta"><?php echo __('Tipo Condicion'); ?></label><br />
                <div class='protected_field tipo_condicion_alta'><?php echo $xml->ZONA_J->indicaciones_alta_laboral->tipo_condicion; ?></div>
            </div>
            <div class='field dias_periodo_condicion'>
                <label for="alla_alta_laboral_attributes_dias_periodo_condicion"><?php echo __('N de Dias Periodo Condicion'); ?></label><br />
                <div class='protected_field dias_periodo_condicion'><?php echo $xml->ZONA_J->indicaciones_alta_laboral->n_dias_periodo_condicion; ?></div>
            </div>
            <div class='field continua_tratamiento_laboral'>
                <label for="alla_alta_laboral_attributes_continua_tratamiento_laboral"><?php echo __('Continua Tratamiento'); ?></label><br />
                <div class='protected_field continua_tratamiento_laboral'><?php echo $si_o_no[(string)$xml->ZONA_J->indicaciones_alta_laboral->continua_tratamiento]; ?></div>
            </div>
            <div class='field tipo_tratamiento_laboral'>
                <label for="alla_alta_laboral_attributes_tipo_tratamiento_laboral"><?php echo __('Tipo Tratamiento'); ?></label><br />
                <div class='protected_field tipo_tratamiento_laboral'><?php echo $xml->ZONA_J->indicaciones_alta_laboral->tipo_tratamiento; ?></div>
            </div>
        </div>
    </div>
</div>