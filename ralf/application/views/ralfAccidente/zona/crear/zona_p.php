
<?php
$xml=$data['xml'];
$criterios_array=array();
foreach($xml->ZONA_P->accidente_fatal->gravedad->criterio_gravedad as $g) {
	$val=(int)$g;
    $criterios_array[]=$val;
}

if($_POST) {
	$default["criterio_1"]=(isset($_POST["criterio_1"]))?true:false;
	$default["criterio_2"]=(isset($_POST["criterio_2"]))?true:false;
	$default["criterio_3"]=(isset($_POST["criterio_3"]))?true:false;
	$default["criterio_4"]=(isset($_POST["criterio_4"]))?true:false;
	$default["criterio_5"]=(isset($_POST["criterio_5"]))?true:false;
	$default["criterio_6"]=(isset($_POST["criterio_6"]))?true:false;
	$default["criterio_7"]=(isset($_POST["criterio_7"]))?true:false;
    $default["criterio_8"]=(isset($_POST["criterio_8"]))?true:false;

}else {	
	$default["criterio_1"]=(in_array(1, $criterios_array))?true:false;
	$default["criterio_2"]=(in_array(2, $criterios_array))?true:false;
	$default["criterio_3"]=(in_array(3, $criterios_array))?true:false;
	$default["criterio_4"]=(in_array(4, $criterios_array))?true:false;
	$default["criterio_5"]=(in_array(5, $criterios_array))?true:false;
	$default["criterio_6"]=(in_array(6, $criterios_array))?true:false;
	$default["criterio_7"]=(in_array(7, $criterios_array))?true:false;
    $default["criterio_8"]=(in_array(8, $criterios_array))?true:false;

}

$comunas=$data['comunas'];
$criterio_gravedad=$data['criterio_gravedad'];

$tipo_calle=$data['tipo_calle'];

if(isset($_POST["hora_accidente_hr"])) {
    $default["hora_accidente_hr"]=$_POST["hora_accidente_hr"];
}else {
    if(isset($xml->ZONA_P->accidente_fatal->hora_accidente)) {
        $default["hora_accidente_hr"]= substr($xml->ZONA_P->accidente_fatal->hora_accidente,0,2);
    }else {
        $default["hora_accidente_hr"]="00";
    }
}

if(isset($_POST["hora_accidente_mm"])) {
    $default["hora_accidente_mm"]=$_POST["hora_accidente_mm"];
}else {
    if(isset($xml->ZONA_P->accidente_fatal->hora_accidente)) {
        $default["hora_accidente_mm"]= substr($xml->ZONA_P->accidente_fatal->hora_accidente,-5,-3);
    }else {
        $default["hora_accidente_mm"]="00";
    }
}

if(isset($_POST["hora_accidente_ss"])) {
    $default["hora_accidente_ss"]=$_POST["hora_accidente_ss"];
}else {
    if(isset($xml->ZONA_P->accidente_fatal->hora_accidente)) {
        $default["hora_accidente_ss"]= substr($xml->ZONA_P->accidente_fatal->hora_accidente,6,7);
    }else {
        $default["hora_accidente_ss"]="00";
    }
}

$hh_array["00"]="00";
$hh_array["01"]="01";
$hh_array["02"]="02";
$hh_array["03"]="03";
$hh_array["04"]="04";
$hh_array["05"]="05";
$hh_array["06"]="06";
$hh_array["07"]="07";
$hh_array["08"]="08";
$hh_array["09"]="09";
for ($i=10;$i<=23;$i++) {
    $hh_array[$i]=$i;
}


$mm_ss_array["00"]="00";
$mm_ss_array["01"]="01";
$mm_ss_array["02"]="02";
$mm_ss_array["03"]="03";
$mm_ss_array["04"]="04";
$mm_ss_array["05"]="05";
$mm_ss_array["06"]="06";
$mm_ss_array["07"]="07";
$mm_ss_array["08"]="08";
$mm_ss_array["09"]="09";
for ($i=10;$i<=59;$i++) {
    $mm_ss_array[$i]=$i;
}

?>
<h3 class="tit-diag"><?php echo __('Información Accidente Fatal o Grave') ?></h3><br />

<div class='form_section_container'>    
    <div class='form_section accident fatal-accident'>  
        <div class='row no-overflow'>
            <div class='field fecha_accidente'>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Fecha del accidente</label><br>
                <div class="editable_field fecha_accidente">
                    <?php echo Form::input('fecha_accidente', $default['fecha_accidente'],array('class'=>'datepicker')); ?>
                    <div class="error"><?php echo Arr::get($errors, 'fecha_accidente'); ?></div>
                </div>
            </div>            
            <div class='field hora_accidente'>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Hora del accidente</label><br>
                <div class="protected_field hora_accidente">
                    <?php echo Form::select('hora_accidente_hr', $hh_array,$default["hora_accidente_hr"]); ?>
                    <?php echo Form::select('hora_accidente_mm', $mm_ss_array,$default["hora_accidente_mm"]); ?>
                    <?php echo Form::select('hora_accidente_ss', $mm_ss_array,$default["hora_accidente_ss"]); ?>
                    <div class="error"><?php echo Arr::get($errors, 'hora_accidente'); ?></div>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='field codigo_tipo_calle'> 
                <label for="complaint_diat_attributes_employer_attributes_address_attributes_codigo_tipo_calle"><?php echo __('Tipo calle'); ?></label><br />
                <div class="protected_field tipo_calle">
                    <?php echo Form::select('tipo_calle', $tipo_calle,$default['tipo_calle']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'tipo_calle'); ?></div>
                </div>
            </div>
            <div class='field nombre_calle'>
                <label for="complaint_diat_attributes_employer_attributes_address_attributes_nombre"><?php echo __('Nombre'); ?></label><br />
                <div class="editable_field nombre_calle">
                    <?php echo Form::input('nombre_calle', $default['nombre_calle']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'nombre_calle'); ?></div>
                </div>
            </div>
            <div class='field numero_calle'>
                <label for="complaint_diat_attributes_employer_attributes_address_attributes_numero"><?php echo __('Número'); ?></label><br />
                <div class="editable_field numero_calle">
                    <?php echo Form::input('numero', $default['numero']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'numero'); ?></div>
                </div>
            </div>
            <div class='field resto_direccion'>
                <label for="complaint_diat_attributes_employer_attributes_address_attributes_resto_direccion"><?php echo __('Villa / población / sector'); ?></label><br />
                <div class="editable_field resto_direccion">
                    <?php echo Form::input('resto_direccion', $default['resto_direccion']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'resto_direccion'); ?></div>
                </div>
            </div>
            <div class='field localidad'>
                <label for="complaint_diat_attributes_employer_attributes_address_attributes_localidad"><?php echo __('Localidad'); ?></label><br />
                <div class="editable_field localidad">
                    <?php echo Form::input('localidad', $default['localidad']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'localidad'); ?></div>
                </div>
            </div>
            <div class='field codigo_comuna'>
                <label for="complaint_diat_attributes_employer_attributes_address_attributes_codigo_comuna"><?php echo __('Comuna'); ?></label><br />
                <div class="protected_field codigo_comuna">
                    <?php echo Form::select('comuna',Model_St_Comuna::obtener(), $default['comuna']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'comuna'); ?></div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='field criterio'>
                <label for="complaint_diat_attributes_accident_attributes_criterios_de_gravedad">Criterios de Gravedad</label><br>
                <div class="protected_field criterio">
                    <ul class="left">
                        <li><?php echo Form::checkbox('criterio_1',1,$default["criterio_1"])?> Muerte del trabajador</li>
                        <li><?php echo Form::checkbox('criterio_4',4,$default["criterio_4"])?> Maniobras de rescate</li>
                    </ul>
                    <ul class="center">
                        <li><?php echo Form::checkbox('criterio_6',6,$default["criterio_6"])?> Amputación traumática</li>
                        <li><?php echo Form::checkbox('criterio_3',3,$default["criterio_3"])?> Maniobras de reanimación</li>
                    </ul>
                    <ul class="center">
                        <li><?php echo Form::checkbox('criterio_5',5,$default["criterio_5"])?> Caída de altura de más de 2 m.</li>
                        <li><?php echo Form::checkbox('criterio_2',2,$default["criterio_2"])?> Desaparecido producto del accidente</li>
                    </ul>
                    <ul class="right">
                        <li><?php echo Form::checkbox('criterio_7',7,$default["criterio_7"])?> Número de trabajadores afecta el desarrollo normal de la faena</li>
                        <li><?php echo Form::checkbox('criterio_8',8,$default["criterio_8"])?> Accidente en condición hiperbárica</li>
                    </ul>

                </div>
                <div class="clear-both"></div>
                <div class="error"><?php echo Arr::get($errors, 'criterio'); ?></div>
            </div>

        </div>

        <div class='row'>

            <div class='field fecha_defuncion'>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Fecha Defunción</label><br>
                <div class="editable_field fecha_defuncion">
                    <?php echo Form::input('fecha_defuncion', $default['fecha_defuncion'],array('class'=>'datepicker')); ?>
                    <div class="error"><?php echo Arr::get($errors, 'fecha_defuncion'); ?></div>
                </div>
            </div>
            <div class='field codigo_area'>
                <?php $STLugarDefuncion=array(''=>'Seleccione',1=>'Mismo lugar del Accidente',2=>'Traslado al Centro Asistencial',3=>'Centro Asistencial',4=>'Otro (indicar lugar)'); ?>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Lugar Defunción</label><br>
                <div class="protected_field lugar_defuncion">
                    <?php echo Form::select('lugar_defuncion',$STLugarDefuncion, $default['lugar_defuncion']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'lugar_defuncion'); ?></div>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class="field lugar_defuncion_otro como_ocurrio">
                <label for="complaint_diat_attributes_accident_attributes_como_ocurrio">Lugar Defunción Otro</label><br>                
                <div class="editable_field lugar_defuncion_otro">
                    <?php echo Form::textarea('lugar_defuncion_otro', $default['lugar_defuncion_otro']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'lugar_defuncion_otro'); ?></div>
                </div>
            </div>
            <div class="field descripcion_accidente_ini como_ocurrio">
                <label for="complaint_diat_attributes_accident_attributes_como_ocurrio">Describa ¿qué pasó o cómo ocurrió el accidente?</label><br>                
                <div class="editable_field descripcion_accidente_ini">
                    <?php echo Form::textarea('descripcion_accidente_ini', $default['descripcion_accidente_ini']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'descripcion_accidente_ini'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<h3>Datos de Informante</h3>
<div class='form_section_container'>
    <div class='form_section accident'>
        <div class="row">
            <div class="field nombres">
                <label for="complaint_diat_attributes_employee_attributes_nombres">Nombres</label><br>                
                <div class="editable_field nombres">
                    <?php echo Form::input('nombres', $default['nombres']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'nombres'); ?></div>
                </div>
            </div>
            <div class="field apellido_paterno"><label for="complaint_diat_attributes_employee_attributes_apellido_paterno">Apellido Paterno</label><br>
                <div class="editable_field apellido_paterno">
                    <?php echo Form::input('apellido_paterno', $default['apellido_paterno']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'apellido_paterno'); ?></div>
                </div>
            </div>
            <div class="field apellido_materno"><label for="complaint_diat_attributes_employee_attributes_apellido_materno">Apellido Materno</label><br>
                <div class="editable_field apellido_materno">
                    <?php echo Form::input('apellido_materno', $default['apellido_materno']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'apellido_materno'); ?></div>
                </div>
            </div>
            <div class="field rut"><label for="complaint_diat_attributes_employee_attributes_rut">Rut (ej. 11111111-1)</label><br>
                <div class="editable_field rut">
                    <?php echo Form::input('rut', $default['rut']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'rut'); ?></div>
                </div>
            </div>
            <div class="field cod_area"><label for="complaint_diat_attributes_employer_attributes_telephone_attributes_codigo_area">Código Área</label><br>                    
                <div class="editable_field cod_area">
                    <?php echo Form::input('cod_area', $default['cod_area']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'cod_area'); ?></div>
                </div>
            </div>
            <div class="field telefono_informante_oa"><label for="complaint_diat_attributes_employer_attributes_telephone_attributes_numero">Número de Teléfono</label><br>
                <div class="editable_field telefono_informante_oa">
                    <?php echo Form::input('telefono_informante_oa', $default['telefono_informante_oa']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'telefono_informante_oa'); ?></div>
                </div>
            </div>
            <div class="field correo_electronico_informante_oa"><label for="complaint_diat_attributes_accident_attributes_proof_detail">Correo Electrónico Informante OA</label><br>
                <div class="editable_field correo_electronico_informante_oa">
                    <?php echo Form::input('correo_electronico_informante_oa', $default['correo_electronico_informante_oa']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'correo_electronico_informante_oa'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>