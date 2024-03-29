<h3><?php echo __('Doctor') ?></h3>
<div class='form_section_container'>
    <div class='form_section doctor'>
        <div class='row'>
            <div class='field nombres'>
                <label for="alla_doctor_attributes_nombres"><?php echo __('Nombres'); ?></label><br />
                <div class='protected_field nombres'><?php echo $xml->ZONA_L->doctor->medico->nombres; ?></div>
            </div>
            <div class='field apellido_paterno'>
                <label for="alla_doctor_attributes_apellido_paterno"><?php echo __('Apellido paterno'); ?></label><br />
                <div class='protected_field apellido_paterno'><?php echo $xml->ZONA_L->doctor->medico->apellido_paterno; ?></div>
            </div>
            <div class='field apellido_materno'>
                <label for="alla_doctor_attributes_apellido_materno"><?php echo __('Apellido materno'); ?></label><br />
                <div class='protected_field apellido_materno'><?php echo $xml->ZONA_L->doctor->medico->apellido_materno; ?></div>
            </div>
            <div class='field rut'>
                <label for="alla_doctor_attributes_rut"><?php echo __('Rut'); ?></label><br />
                <div class='protected_field rut'><?php echo $xml->ZONA_L->doctor->medico->rut; ?></div>
            </div>
        </div>
    </div>
</div>