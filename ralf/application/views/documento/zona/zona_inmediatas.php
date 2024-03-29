<h3 class="tit-diag"><?php echo __('Identificación de las medidas inmediatas') ?></h3><br />

<div class='form_section_container'>
    <div class='form_section accident'>
        <h3>Medidas</h3>            
            <?php $medidas=ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_ralf2')->find_all();?>            
            <table class= "tabla-general">                        
                <thead>
                    <tr class="label-row">                        
                        <th>Medida</th>                                                
                    </tr>
                </thead>
                <tbody>                    
                    <?php foreach ($medidas as $m):?>    
                        <tr class="data-row">                            
                            <td><?php echo $m->medida?></td>
                        </tr>                    
                    <?php endforeach;?>
                </tbody></table>
                <br />
                <br />
         <div class='clear'></div>                  
        <div class='row'>            
            <div class='field codigo_area'>
                <label for="complaint_diat_attributes_accident_attributes_fecha">Fecha notificación medidas inmediatas</label><br>
                <div class='protected_field'><?php echo $xml->ZONA_INMEDIATAS->fecha_notificacion_medidas_inmediatas; ?></div>                
            </div>
        </div>

      <!--  <div class='clear'></div> -->       
            <div class="row">
                 <h3>Documentación Anexa</h3>
                  <?php $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();?>
        <table>
            <tbody><tr class="label-row">                    
                    <th>Nombre del Documento</th>
                    <th>Fecha</th>
                    <th>Autor</th>
                    <th>Documento</th>
                </tr>     
                <?php foreach ($anexos as $anexo):?>    
                    <tr class="data-row">
                        <td><?php echo $anexo->nombre_documento?></td>
                        <td><?php echo $anexo->fecha_documento?></td>
                        <td><?php echo $anexo->autor_documento?></td>                        
                        <?php $ver=str_replace('index.php/', '', URL::site($anexo->ruta, 'http'))?>
                        <td><a href="<?php echo $ver; ?>">Ver</a></td>                        
                    </tr>
                <?php endforeach;?>
                
            </tbody></table>            

           <!-- </div> -->
        </div>


        <div class='clear'></div>        
        <div class="row">
            <h3>Información Investigador</h3>
            <div class="field nombres">
                <label for="complaint_diat_attributes_employee_attributes_nombres">Nombres</label><br>
                <div class='protected_field nombres'><?php echo $xml->ZONA_INMEDIATAS->investigador->nombres; ?></div>                
            </div>
            <div class="field apellido_paterno"><label for="complaint_diat_attributes_employee_attributes_apellido_paterno">Apellido paterno</label><br>
            <div class='protected_field apellido_paterno'><?php echo $xml->ZONA_INMEDIATAS->investigador->apellido_paterno; ?></div>                                
            </div>
            <div class="field apellido_materno"><label for="complaint_diat_attributes_employee_attributes_apellido_materno">Apellido materno</label><br>
            <div class='protected_field apellido_materno'><?php echo $xml->ZONA_INMEDIATAS->investigador->apellido_materno; ?></div>                                
            </div>
            <div class="field rut"><label for="complaint_diat_attributes_employee_attributes_rut">Rut (ej. 11111111-1)</label><br>
            <div class='protected_field rut'><?php echo $xml->ZONA_INMEDIATAS->investigador->rut; ?></div>
            </div>                            
            <div class="clear"></div>
            <?php if(isset($xml->ZONA_INMEDIATAS->telefono_investigador)):?>
            <div class="telephone">
                <div class="field codigo_area"><label for="complaint_diat_attributes_employer_attributes_telephone_attributes_codigo_area">Código area</label><br>                    
                    <div class='protected_field'><?php echo $xml->ZONA_INMEDIATAS->telefono_investigador->cod_area; ?></div>                    
                </div>
                <div class="field numero"><label for="complaint_diat_attributes_employer_attributes_telephone_attributes_numero">Número de Teléfono</label><br>
                    <div class='protected_field'><?php echo $xml->ZONA_INMEDIATAS->telefono_investigador->numero; ?></div>                                        
                </div>
            </div>         
            <?php endif?>
        </div>
        
    </div>
</div>