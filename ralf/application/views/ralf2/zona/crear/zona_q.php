<h3 class="tit-diag"><?php echo __('Identificación de las medidas inmediatas') ?></h3><br />

<div class='form_section_container'>
    <div class='form_section accident'>        
            
            <h3>Medidas</h3>            
            <?php echo HTML::anchor("medidas/medidas_ralf2/{$xml_id}",'Agregar Medida ',array('class'=>'fancybox-narrow'))?>    
            <?php $medidas=ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_ralf2')->find_all();?>                        
            <div class="error"><?php echo Arr::get($errors, "medidas"); ?></div>            
            <table id="medida1">
                <thead>
                    <tr class="label-row">                        
                        <th>Medida</th>                                                
                        <th>Borrar</th>    
                    </tr>
                </thead>
                <tbody>                    
                    <?php foreach ($medidas as $m):?>    
                        <tr class="data-row">                            
                            <td><?php echo $m->medida?></td>
                            <td>
                            <?php echo HTML::anchor("ralf2/borrar_medida/{$m->id}",'borrar',array('class'=>'fancybox-small'))?>
                            </td>
                        </tr>                    
                    <?php endforeach;?>
                </tbody></table>
                <br />
                <br />
         <div class='row'>                     
            <div class='field fecha_notificacion_medidas_inmediatas'>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Fecha notificación medidas inmediatas</label><br>
                <div class='editable_field fecha_notificacion_medidas_inmediatas'>
                    <?php echo Form::input('fecha_notificacion_medidas_inmediatas', $default['fecha_notificacion_medidas_inmediatas'],array('class'=>'datepicker')); ?>
                    <div class="error"><?php echo Arr::get($errors, 'fecha_notificacion_medidas_inmediatas'); ?></div>
                </div>
            </div>
        </div>

        <h3>Documentación Anexa</h3>        
        <?php echo HTML::anchor("adjuntos/documento_anexo/{$xml_id}",'Agregar Anexos',array('class'=>'fancybox-narrow'))?>    
        <?php $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();?>
       <div class="error"><?php echo Arr::get($errors, "documentos_anexos"); ?></div> 
        <table id="adjuntos">
            <thead>
                <tr class="label-row">                    
                    <th>Nombre Documento</th>
                    <th>Fecha de Documento</th>
                    <th>Autor</th>
                    <th>Documento</th>
                </tr>
            </thead>
            <tbody>                
                <?php foreach ($anexos as $anexo):?>    
                    <tr class="data-row">                        
                        <td><?php echo $anexo->nombre_documento?></td>
                        <td><?php echo $anexo->fecha_documento?></td>
                        <td><?php echo $anexo->autor_documento?></td>                        
                        <?php $ver=Kohana::$config->load('sitio.url_base'). $anexo->ruta;?>
                        <td>
                            <!--<a href="<?php echo $ver; ?>">Ver</a>-->
                            <a onclick="window.open('<?php echo $ver; ?>')">Ver</a>
                            <?php if($documento->ESTADO == 5): ?> | <?php echo HTML::anchor("ralf2/borrar_adjunto/{$anexo->id}",'borrar',array('class'=>'fancybox-small'))?> <?php endif; ?>
                        </td>                        
                    </tr>                
                <?php endforeach;?>
            </tbody>
        </table>
        <br/>
        <div class='clear'></div>       
        <h3>Información Investigador</h3> 
        <div class="row">
            <div class="field nombres">
                <label for="complaint_diat_attributes_employee_attributes_nombres">Nombre</label><br>                
                <div class='editable_field nombres'>
                    <?php echo Form::input('nombres', $default['nombres']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'nombres'); ?></div>
                </div>
            </div>
            <div class="field apellido_paterno">
                <label for="complaint_diat_attributes_employee_attributes_apellido_paterno">Apellido paterno</label><br>
                <div class='editable_field apellido_paterno'>
                    <?php echo form::input('apellido_paterno', $default['apellido_paterno']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'apellido_paterno'); ?></div>
                </div>
            </div>
            <div class="field apellido_materno">
                <label for="complaint_diat_attributes_employee_attributes_apellido_materno">Apellido materno</label><br>
                <div class='editable_field apellido_materno'>
                    <?php echo Form::input('apellido_materno', $default['apellido_materno']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'apellido_materno'); ?></div>
                </div>
            </div>
            <div class="field rut_investigador"><label for="complaint_diat_attributes_employee_attributes_rut">Rut (ej. 11111111-1)</label><br>
                <div class='editable_field rut_investigador'>
                    <?php echo Form::input('rut', $default['rut']); ?>
                    <div class="error"><?php echo Arr::get($errors, 'rut'); ?></div>
                </div>
            </div>
            <div class="clear"></div>
                <div class="field codigo_area">
                    <label for="complaint_diat_attributes_employer_attributes_telephone_attributes_codigo_area">Cód. área</label><br>                    
                    <div class='editable_field codigo_area'>
                        <?php echo Form::input('cod_area', $default['cod_area']); ?>
                        <div class="error"><?php echo Arr::get($errors, 'cod_area'); ?></div>
                    </div>
                </div>
                <div class="field numero_telefono">
                    <label for="complaint_diat_attributes_employer_attributes_telephone_attributes_numero">Nº teléfono</label><br>
                    <div class='editable_field numero_telefono'>
                        <?php echo Form::input('numero', $default['numero']); ?>
                        <div class="error"><?php echo Arr::get($errors, 'numero'); ?></div>
                    </div>
                </div>
            
        </div>
        
    </div>
</div>