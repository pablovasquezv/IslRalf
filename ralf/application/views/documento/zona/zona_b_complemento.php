<h3 class="tit-diag"><?php echo __('Representante Legal') ?></h3><br />

<div class='form_section_container'>
    <div class='form_section accident'>                
        <div class="row">
            <div class="field nombre_representante_legal">
                <label for="complaint_diat_attributes_employee_attributes_nombres">Nombre representante legal</label><br>
                <div class='protected_field nombre_representante_legal'><?php echo $xml->ZONA_B->empleador->nombre_representante_legal; ?></div>                
            </div>
            <div class="field rut_representante_legal"><label for="complaint_diat_attributes_employee_attributes_rut">Rut representante (ej. 11111111-1)</label><br>
                <div class='protected_field rut_representante_legal'><?php echo $xml->ZONA_B->empleador->rut_representante_legal; ?></div>
            </div>
            <div class="field promedio_anual_trabajadores"><label for="complaint_diat_attributes_employee_attributes_apellido_paterno">Promedio anual trabajadores</label><br>
            	<div class='protected_field promedio_anual_trabajadores'><?php echo $xml->ZONA_B->empleador->promedio_anual_trabajadores; ?></div>                
            </div>
                 
            <div class="field rut"><label for="complaint_diat_attributes_employee_attributes_rut">Nº sucursales</label><br>
            <div class='protected_field nro_sucursales'><?php echo $xml->ZONA_B->empleador->nro_sucursales; ?></div>                                
            </div>
            <div class="clear"></div>            
            <div class="field apellido_paterno"><label for="complaint_diat_attributes_employee_attributes_apellido_paterno">Tasa ds110</label><br>
            <div class='protected_field tasa_ds110'><?php echo $xml->ZONA_B->empleador->tasa_ds110; ?></div>                
            </div>       
            <div class="field apellido_materno"><label for="complaint_diat_attributes_employee_attributes_apellido_materno">Tasa ds67</label><br>
            <div class='protected_field tasa_ds67'><?php echo $xml->ZONA_B->empleador->tasa_ds67; ?></div>                
            </div>  
            <div class="field nombres">
                <?php $STUltimaEvaluacionTasa=array(1=>'Se mantuvo',2=>'Fue rebajada',3=>'Fue recargada'); ?>
                <?php $ultima_eval_ds67 =(int)$xml->ZONA_B->empleador->ultima_eval_ds67?>
                <label for="complaint_diat_attributes_employee_attributes_nombres">Última evaluación ds67</label><br>                
                <div class='protected_field ultima_eval_ds67'><?php echo $STUltimaEvaluacionTasa[$ultima_eval_ds67]; ?></div>                
            </div>
        </div>
        
    </div>
</div>