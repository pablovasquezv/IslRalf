<h3><?php echo __('Representante Legal') ?></h3>

<div class='form_section_container'>
    <div class='form_section accident representante-legal'>                
        <div class="row">
            <div class="field nombres">
                <label for="complaint_diat_attributes_employee_attributes_nombres">Nombre representante legal</label><br>                
                <div class="editable_field nombre_representante_legal">
                    <?php echo Form::input('nombre_representante_legal', $default['nombre_representante_legal']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'nombre_representante_legal'); ?></div>
                </div>
            </div>
            <div class="field rut"><label for="complaint_diat_attributes_employee_attributes_rut">Rut representante (ej. 11111111-1)</label><br>
                <div class="editable_field rut_representante_legal">
                    <?php echo Form::input('rut_representante_legal', $default['rut_representante_legal']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'rut_representante_legal'); ?></div>
                </div>
            </div>
            <div class="field apellido_paterno"><label for="complaint_diat_attributes_employee_attributes_apellido_paterno">Promedio anual trabajadores</label><br>
                <div class="editable_field promedio_anual_trabajadores">
                    <?php echo Form::input('promedio_anual_trabajadores', $default['promedio_anual_trabajadores']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'promedio_anual_trabajadores'); ?></div>
                </div>
            </div>
                 
            <div class="field rut"><label for="complaint_diat_attributes_employee_attributes_rut">Nº sucursales</label><br>
                <div class="editable_field nro_sucursales">
                    <?php echo Form::input('nro_sucursales', $default['nro_sucursales']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'nro_sucursales'); ?></div>
                </div>
            </div>       
            <div class="field apellido_paterno"><label for="complaint_diat_attributes_employee_attributes_apellido_paterno">Tasa ds110</label><br>
                <div class="editable_field tasa_ds110">
                    <?php echo Form::input('tasa_ds110', $default['tasa_ds110']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'tasa_ds110'); ?></div>
                </div>
            </div>       
            <div class="field apellido_materno"><label for="complaint_diat_attributes_employee_attributes_apellido_materno">Tasa ds67</label><br>
                <div class="editable_field tasa_ds67">
                    <?php echo Form::input('tasa_ds67', $default['tasa_ds67']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'tasa_ds67'); ?></div>
                </div>
            </div>  
            <div class="field nombres">
                <?php $STUltimaEvaluacionTasa=array(''=>'Seleccione',1=>'Se mantuvo',2=>'Fue rebajada',3=>'Fue recargada'); ?>
                <label for="complaint_diat_attributes_employee_attributes_nombres">Última evaluación ds67</label><br>                
                <div class="protected_field ultima_eval_ds67">
                    <?php echo Form::select('ultima_eval_ds67', $STUltimaEvaluacionTasa,$default['ultima_eval_ds67']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'ultima_eval_ds67'); ?></div>
                </div>
            </div>
        </div>
        
    </div>
</div>