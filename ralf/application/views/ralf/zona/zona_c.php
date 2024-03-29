<?php

$cod_ciuo_trab=(string)$xml->ZONA_C->empleado->ciuo_trabajador;
$ciuo_trabajador=Tipos::codigo($cod_ciuo_trab,'STCIUO');

if(isset($xml->ZONA_C->empleado->codigo_etnia) && !empty($xml->ZONA_C->empleado->codigo_etnia)) {
    $cod_et=(string)$xml->ZONA_C->empleado->codigo_etnia;
    $codigo_etnia=$etnia[$cod_et];
} else {
    $codigo_etnia='n/a';
}

if(isset($xml->ZONA_C->empleado->clasificacion_trabajador) && !empty($xml->ZONA_C->empleado->clasificacion_trabajador)) {
    $cla_tra=(string)$xml->ZONA_C->empleado->clasificacion_trabajador;
    $codigo_clasificacion_trabajador=$clas_trabajador[$cla_tra];
} else {
    $codigo_clasificacion_trabajador='n/a';
}

if(isset($xml->ZONA_C->empleado->sistema_comun) && !empty($xml->ZONA_C->empleado->sistema_comun)) {
    $sist=(string)$xml->ZONA_C->empleado->sistema_comun;
    $codigo_sistema_salud_comun=$sistema[$sist];
} else {
    $codigo_sistema_salud_comun='n/a';
}

$cod_pais_trab=(string)$xml->ZONA_C->empleado->trabajador->pais_nacionalidad;
$cod_pais=Tipos::codigo($cod_pais_trab,'STPais_nacionalidad');

?>

<h3><?php echo __('Identificación del Trabajador/a')?></h3>
<div class='form_section_container'>
    <div class='form_section employee'>
        <div class='row'>
            <div class='field nombres'>
                <label for="complaint_diat_attributes_employee_attributes_nombres"><?php echo __('Nombres'); ?></label><br />
        	<div class='protected_field nombres'><?php echo $xml->ZONA_C->empleado->trabajador->nombres; ?></div>
            </div>
            <div class='field apellido_paterno'>
              <label for="complaint_diat_attributes_employee_attributes_apellido_paterno"><?php echo __('Apellido paterno'); ?></label><br />
              <div class='protected_field apellido_paterno'><?php echo $xml->ZONA_C->empleado->trabajador->apellido_paterno; ?></div>
            </div>
            <div class='field apellido_materno'>
                <label for="complaint_diat_attributes_employee_attributes_apellido_materno"><?php echo __('Apellido materno'); ?></label><br />
                <div class='protected_field apellido_materno'><?php echo $xml->ZONA_C->empleado->trabajador->apellido_materno; ?></div>
            </div>
            <div class='field rut'>
                <label for="complaint_diat_attributes_employee_attributes_rut"><?php echo __('Rut'); ?></label><br />
                <div class='protected_field rut'>
                        <?php 
                            if(isset($xml->ZONA_C->empleado->trabajador->rut)){
                                echo $xml->ZONA_C->empleado->trabajador->rut; 
                            } else {
                                if(isset($xml->ZONA_C->empleado->trabajador->documento_identidad)){
                                    echo $xml->ZONA_C->empleado->trabajador->documento_identidad->identificador;
                                }
                            }
                        ?>
                </div>
            </div>
        </div>
    
        <div class='row'>
            <div class='field codigo_tipo_calle'>
                <label for="complaint_diat_attributes_employee_attributes_address_attributes_codigo_tipo_calle"><?php echo __('Tipo calle'); ?></label><br />
                <div class='protected_field codigo_tipo_calle'><?php echo $tipo_calle[(string)$xml->ZONA_C->empleado->direccion_trabajador->tipo_calle];?></div>
            </div>
            <div class='field nombre_calle'>
                <label for="complaint_diat_attributes_employee_attributes_address_attributes_nombre"><?php echo __('Nombre'); ?></label><br />
                <div class='protected_field nombre_calle'><?php echo $xml->ZONA_C->empleado->direccion_trabajador->nombre_calle; ?></div>
            </div>
            <div class='field numero_calle'>
                <label for="complaint_diat_attributes_employee_attributes_address_attributes_numero"><?php echo __('Número'); ?></label><br />
                <div class='protected_field numero_calle'><?php echo $xml->ZONA_C->empleado->direccion_trabajador->numero; ?></div>
            </div>
            <div class='field resto_direccion'>
                <label for="complaint_diat_attributes_employee_attributes_address_attributes_resto_direccion"><?php echo __('Villa / población / sector'); ?></label><br />
                <div class='protected_field resto_direccion'><?php echo $xml->ZONA_C->empleado->direccion_trabajador->resto_direccion ? : 'n/a'; ?></div>
            </div>
            <div class='field localidad'>
                <label for="complaint_diat_attributes_employee_attributes_address_attributes_localidad"><?php echo __('Localidad'); ?></label><br />
                <div class='protected_field localidad'><?php echo trim($xml->ZONA_C->empleado->direccion_trabajador->localidad) ? : 'n/a'; ?></div>
            </div>
            <div class='field codigo_comuna'>
                <label for="complaint_diat_attributes_employee_attributes_address_attributes_codigo_comuna"><?php echo __('Comuna'); ?></label><br />
                <div class='protected_field codigo_comuna'><?php echo $comunas[(string)$xml->ZONA_C->empleado->direccion_trabajador->comuna]; ?></div>
            </div>
            <div class='field codigo_area'>
                <label for="complaint_diat_attributes_employee_attributes_telephone_attributes_codigo_area"><?php echo __('Cód. área'); ?></label><br />
                <div class='protected_field codigo_area'>
                    <?php echo $xml->ZONA_C->empleado->telefono_trabajador->cod_pais ? "({$xml->ZONA_C->empleado->telefono_trabajador->cod_pais})" : ''; ?>
                    <?php echo $xml->ZONA_C->empleado->telefono_trabajador->cod_area ? : 'n/a'; ?>
                </div>
            </div>
            <div class='field numero_telefono'>
                <label for="complaint_diat_attributes_employee_attributes_telephone_attributes_numero"><?php echo __('Nº teléfono'); ?></label><br />
                <div class='protected_field numero_telefono'>
                    <?php echo $xml->ZONA_C->empleado->telefono_trabajador->numero ? : 'n/a'; ?>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='field codigo_sexo'>
                <label for="complaint_diat_attributes_employee_attributes_codigo_sexo"><?php echo __('Sexo'); ?></label><br />
                <div class='protected_field codigo_sexo'><?php echo $sexo[(string)$xml->ZONA_C->empleado->trabajador->sexo]; ?></div>
            </div>
            <div class='field edad'>
                <label for="complaint_diat_attributes_employee_attributes_edad"><?php echo __('Edad'); ?></label><br />
                <div class='protected_field edad'><?php echo $xml->ZONA_C->empleado->trabajador->edad/*Utiles::edad($xml->ZONA_C->empleado->trabajador->fecha_nacimiento)*/; ?></div>
            </div>
            <div class='field fecha_nacimiento'>
                <label for="complaint_diat_attributes_employee_attributes_fecha_nacimiento"><?php echo __('Fecha nacimiento'); ?></label><br />
                <div class='protected_field fecha_nacimiento'><?php echo Utiles::full_date((string)$xml->ZONA_C->empleado->trabajador->fecha_nacimiento); ?></div>
            </div>
            <div class='field codigo_nacionalidad'>
                <label for="complaint_diat_attributes_employee_attributes_codigo_nacionalidad"><?php echo __('Nacionalidad'); ?></label><br />
                <div class='protected_field codigo_nacionalidad'>
                    <?php echo form::select('pais_nacionalidad', Model_St_Nacionalidad::obtener(), $default['pais_nacionalidad'], array('class' => 'pais_nacionalidad_select')); ?>
                    <div class="error"><?php echo Arr::get($errors, 'pais_nacionalidad'); ?></div>
                </div>
            </div>
            <div class='field codigo_etnia'>
                <label for="complaint_diat_attributes_employee_attributes_codigo_etnia"><?php echo __('Etnia'); ?></label><br />
                <div class='protected_field codigo_etnia'><?php echo $codigo_etnia?></div>
            </div>
            <div class='field otra_etnia'>
                <label for="complaint_diat_attributes_employee_attributes_otra_etnia"><?php echo __('Otra etnia'); ?></label><br />
                <div class='protected_field otra_etnia'><?php echo $xml->ZONA_C->empleado->etnia_otro ? : 'n/a';?></div>
            </div>
        </div>
    
        <div class='row'>
            <div class='field profesion'>
                <label for="complaint_diat_attributes_employee_attributes_profesion"><?php echo __('Profesión'); ?></label><br />
		<div class='protected_field profesion'><?php echo $xml->ZONA_C->empleado->profesion_trabajador; ?></div>
            </div>
            <div class='field codigo_ciuo'>
		<label for="complaint_diat_attributes_employee_attributes_codigo_ciuo"><?php echo __('Ciuo'); ?></label><br />
		<div class='protected_field codigo_ciuo'><?php echo form::select('ciuo_trabajador', Model_St_Ciuo::obtener(), $default['ciuo_trabajador'], array('class' => 'select_ciiu')); ?></div>
		<div class="error"><?php echo Arr::get($errors, 'ciuo_trabajador'); ?></div>
            </div>
            <div class='field codigo_categoria_ocupacion'>
		<label for="complaint_diat_attributes_employee_attributes_codigo_categoria_ocupacion"><?php echo __('Categoría ocupación'); ?></label><br />
		<div class='protected_field codigo_categoria_ocupacion'><?php echo $ocupacion[(string)$xml->ZONA_C->empleado->categoria_ocupacion]; ?></div>
            </div>
        </div>
        <div class='row'>
            <div class='field codigo_duracion_contrato'>
		<label for="complaint_diat_attributes_employee_attributes_codigo_duracion_contrato"><?php echo __('Duración contrato'); ?></label><br />
		<div class='protected_field codigo_duracion_contrato'><?php echo $contrato[(string)$xml->ZONA_C->empleado->duracion_contrato]; ?></div>
            </div>
            <div class='field codigo_tipo_dependencia'>
		<label for="complaint_diat_attributes_employee_attributes_codigo_tipo_dependencia"><?php echo __('Dependencia'); ?></label><br />
		<div class='protected_field codigo_tipo_dependencia'><?php echo $dependencia[(string)$xml->ZONA_C->empleado->tipo_dependencia]; ?></div>
            </div>
            <div class='field codigo_tipo_remuneracion'>
		<label for="complaint_diat_attributes_employee_attributes_codigo_tipo_remuneracion"><?php echo __('Remuneración'); ?></label><br />
		<div class='protected_field codigo_tipo_remuneracion'><?php echo $remuneracion[(string) $xml->ZONA_C->empleado->tipo_remuneracion]; ?></div>
            </div>
            <div class='field fecha_ingreso'>
                <label for="complaint_diat_attributes_employee_attributes_fecha_ingreso"><?php echo __('Fecha ingreso'); ?></label><br />
                <div class='protected_field fecha_ingreso'><?php echo Utiles::full_date((string)$xml->ZONA_C->empleado->fecha_ingreso); ?></div>
            </div>
            <div class='field codigo_clasificacion_trabajador'>
		<label for="complaint_diat_attributes_employee_attributes_codigo_clasificacion_trabajador"><?php echo __('Clasificación trabajador'); ?></label><br />
		<div class='protected_field codigo_clasificacion_trabajador'><?php echo $codigo_clasificacion_trabajador?></div>
            </div>
            <div class='field codigo_sistema_salud_comun'>
		<label for="complaint_diat_attributes_employee_attributes_codigo_sistema_salud_comun"><?php echo __('Sistema salud'); ?></label><br />
		<div class='protected_field codigo_sistema_salud_comun'><?php echo $codigo_sistema_salud_comun; ?></div>
            </div>
        </div>
    </div>
</div>