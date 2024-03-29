<?php
$comuna_acc=ORM::factory('St_Comuna')->where('codigo','=',$xml->ZONA_P->accidente_fatal->direccion_accidente->comuna)->find();

?>

<h3 class="tit-diag"><?php echo __('Información Accidente Fatal o Grave') ?></h3><br />

<div class='form_section_container'>
    <div class='form_section accident fatal-accident'>
        <div class='row'>
            <div class='field codigo_area'>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Fecha del accidente</label><br>
                <div class='protected_field fecha_accidente'><?php echo $xml->ZONA_P->accidente_fatal->fecha_accidente; ?></div>                
            </div>            
            <div class='field codigo_area'>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Hora del accidente</label><br>                
                <div class='protected_field hora_accidente'><?php echo $xml->ZONA_P->accidente_fatal->hora_accidente; ?></div>
            </div>
        </div>
        <div class='row'>
            <div class=''>                
                <div class='field codigo_tipo_calle'> 
                    <label for="complaint_diat_attributes_employer_attributes_address_attributes_codigo_tipo_calle"><?php echo __('Tipo calle'); ?></label><br />
                    <?php $tipos=array(1=>'Avenida',2=>'Calle',3=>'Pasaje')?>
                    <div class='protected_field codigo_tipo_calle'><?php echo $tipos[(int)$xml->ZONA_P->accidente_fatal->direccion_accidente->tipo_calle]; ?></div>
                </div>
                <div class='field nombre_calle'>
                    <label for="complaint_diat_attributes_employer_attributes_address_attributes_nombre"><?php echo __('Nombre'); ?></label><br />
                    <div class='protected_field nombre_calle'><?php echo $xml->ZONA_P->accidente_fatal->direccion_accidente->nombre_calle; ?></div>
                </div>
                <div class='field numero_calle'>
                    <label for="complaint_diat_attributes_employer_attributes_address_attributes_numero"><?php echo __('Número'); ?></label><br />
                    <div class='protected_field numero_calle'><?php echo $xml->ZONA_P->accidente_fatal->direccion_accidente->numero; ?></div>                    
                </div>
                <?php if(isset($xml->ZONA_P->accidente_fatal->direccion_accidente->resto_direccion)):?>
                <div class='field resto_direccion'>
                    <label for="complaint_diat_attributes_employer_attributes_address_attributes_resto_direccion"><?php echo __('Villa / población / sector'); ?></label><br />
                    <div class='protected_field resto_direccion'><?php echo $xml->ZONA_P->accidente_fatal->direccion_accidente->resto_direccion; ?></div>
                </div>
                <?php endif?>
                <?php if(isset($xml->ZONA_P->accidente_fatal->direccion_accidente->localidad)):?>
                <div class='field localidad'>
                    <label for="complaint_diat_attributes_employer_attributes_address_attributes_localidad"><?php echo __('Localidad'); ?></label><br />
                    <div class='protected_field localidad'><?php echo $xml->ZONA_P->accidente_fatal->direccion_accidente->localidad; ?></div>
                </div>
                <?php endif?>
                <div class='field codigo_comuna'>
                    <label for="complaint_diat_attributes_employer_attributes_address_attributes_codigo_comuna"><?php echo __('Comuna'); ?></label><br />
                    <div class='protected_field codigo_comuna'><?php echo $comuna_acc->nombre; ?></div>
                </div>                
            </div>
            <div class='clear'></div>
        </div><div class='clear'></div>
        <div class='row'>            
            <h3>Criterios de Gravedad</h3>
            
            <table class= "tabla-general">                        
                <?php $criterio_gravedad_ralf=array(1=>'Muerte del trabajador',2=>'Desaparecido producto del accidente',3=>'Maniobras de reanimación',4=>'Maniobras de rescate',5=>'Caída de altura de más de 1,8 m.',6=>'Amputación traumática',7=>'Número de trabajadores afecta el desarrollo normal de la faena',8=>'Accidente en condición hiperbárica')?>
                <?php foreach($xml->ZONA_P->accidente_fatal->gravedad->criterio_gravedad as $g):?>
                    <tr><td>
                        <?php 
                            echo Form::checkbox("criterio_{$g}",$g,true,array('disabled'=>'disabled'));
                            echo $criterio_gravedad_ralf[(int)$g];
                        ?>
                    </td></tr>
                <?php endforeach?>
            </table>
            <?php if(isset($xml->ZONA_P->accidente_fatal->fecha_defuncion)):?>
            <div class='field fecha_defuncion'>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Fecha defunción</label><br>
                <div class='protected_field fecha_defuncion'><?php echo $xml->ZONA_P->accidente_fatal->fecha_defuncion; ?></div>
            </div>
            <?php endif?>
            <?php if(isset($xml->ZONA_P->accidente_fatal->lugar_defuncion)):?>
            <div class='field lugar_defuncion'>
                <?php $STLugarDefuncion=array(1=>'Mismo lugar del Accidente',2=>'Traslado al Centro Asistencial',3=>'Centro Asistencial',4=>'Otro (indicar lugar)'); ?>                
                <?php $lugar=(int)$xml->ZONA_P->accidente_fatal->lugar_defuncion ?>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Lugar defunción</label><br>
                <div class='protected_field lugar_defuncion'><?php echo (isset($STLugarDefuncion[$lugar])) ? $STLugarDefuncion[$lugar] : ''; ?></div>
            </div>
            <?php endif?>
        </div>
        <div class='row no-overflow'>
            <?php if(isset($xml->ZONA_P->accidente_fatal->lugar_defuncion_otro)):?>
            <div class="field como_ocurrio"><label for="complaint_diat_attributes_accident_attributes_como_ocurrio">Lugar defunción otro</label><br>
                <div class='protected_field_auto_height'><?php echo $xml->ZONA_P->accidente_fatal->lugar_defuncion_otro; ?></div>
            </div>
            <?php endif?>
        </div>
        <div class='row no-overflow'>
            <div class="field como_ocurrio"><label for="complaint_diat_attributes_accident_attributes_como_ocurrio">Describa ¿qué pasó o cómo ocurrió el accidente?</label><br>
            <div class='protected_field_auto_height'><?php echo $xml->ZONA_P->accidente_fatal->descripcion_accidente_ini; ?></div>
            </div>
        </div>
        <h3>Datos de Informante</h3>
        <div class="row">
            <div class="field nombres">
                <label for="complaint_diat_attributes_employee_attributes_nombres">Nombres</label><br>
                <div class='protected_field nombres'><?php echo $xml->ZONA_P->accidente_fatal->informante_oa->nombres; ?></div>                
            </div>
            <div class="field apellido_paterno"><label for="complaint_diat_attributes_employee_attributes_apellido_paterno">Apellido paterno</label><br>
            <div class='protected_field apellido_paterno'><?php echo $xml->ZONA_P->accidente_fatal->informante_oa->apellido_paterno; ?></div>                
            </div>
            <div class="field apellido_materno"><label for="complaint_diat_attributes_employee_attributes_apellido_materno">Apellido materno</label><br>
            <div class='protected_field apellido_materno'><?php echo $xml->ZONA_P->accidente_fatal->informante_oa->apellido_materno; ?></div>                
            </div>
            <div class="field rut"><label for="complaint_diat_attributes_employee_attributes_rut">Rut (ej. 11111111-1)</label><br>
            <div class='protected_field rut'><?php echo $xml->ZONA_P->accidente_fatal->informante_oa->rut; ?></div>                
            </div>
            <div class="clear"></div>
            <?php if(isset($xml->ZONA_P->accidente_fatal->telefono_informante_oa)):?>
            <div class="telephone">
                <div class="field codigo_area"><label for="complaint_diat_attributes_employer_attributes_telephone_attributes_codigo_area">Código Área</label><br>                    
                <div class='protected_field cod_area'><?php echo $xml->ZONA_P->accidente_fatal->telefono_informante_oa->cod_area; ?></div>                    
                </div>
                <div class="field numero"><label for="complaint_diat_attributes_employer_attributes_telephone_attributes_numero">Número de Teléfono</label><br>
                <div class='protected_field numer'><?php echo $xml->ZONA_P->accidente_fatal->telefono_informante_oa->numero; ?></div>                    
                </div>
            </div>
            <?php endif?>
            <?php if(isset($xml->ZONA_P->accidente_fatal->correo_electronico_informante_oa)):?>
            <div class="field proof_detail"><label for="complaint_diat_attributes_accident_attributes_proof_detail">Correo Electrónico Informante OA</label><br>
            <div class='protected_field correo_electronico_informante_oa'><?php echo $xml->ZONA_P->accidente_fatal->correo_electronico_informante_oa; ?></div>                                    
            </div>
            <?php endif?>
        </div>
        
    </div>
</div>