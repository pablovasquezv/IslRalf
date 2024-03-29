<h2><?php echo __('Detalle de Caso Nº ').$caso->CASO_ID; ?></h2>
<?php if($usuario_region!=$caso->region->nombre): ?>
    <b>No se puede trabajar en este caso: este caso pertenece a la Región: <?php echo $caso->region->nombre; ?></b>
<?php else: ?>
    <?php if($caso->ultimo_documento()->ESTADO != 6): ?>
        <?php if($caso->ultimo_tipo_documento()<12 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF Accidente', array('onclick' => 'location.href="'.Url::site('ralfAccidente/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==12 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF2', array('onclick' => 'location.href="'.Url::site('ralf2/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==141 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF Medidas', array('onclick' => 'location.href="'.Url::site('ralfMedidas/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==13 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF3', array('onclick' => 'location.href="'.Url::site('ralf3/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==142 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF Investigación', array('onclick' => 'location.href="'.Url::site('ralfInvestigacion/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==14 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF4', array('onclick' => 'location.href="'.Url::site('ralf4/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==143 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF Causas', array('onclick' => 'location.href="'.Url::site('ralfCausas/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==15 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF5', array('onclick' => 'location.href="'.Url::site('ralf5/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==144 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF Prescripción', array('onclick' => 'location.href="'.Url::site('ralfPrescripcion/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==145 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF Verificación', array('onclick' => 'location.href="'.Url::site('ralfVerificacion/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php elseif($caso->ultimo_tipo_documento()==146 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF Notificación', array('onclick' => 'location.href="'.Url::site('ralfNotificacion/insertar/' . $caso->CASO_ID).'"')); ?>
            <?php elseif($caso->ultimo_tipo_documento()==147 && in_array($caso->ultimo_documento()->ESTADO, array(1)) && $caso->ESTADO == 'activo'): ?>
            <?php echo Form::submit('boton_buscar', 'Crear RALF Recargo de Tasa', array('onclick' => 'location.href="'.Url::site('ralfRecargoTasa/insertar/' . $caso->CASO_ID).'"')); ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<?php if($caso->ultimo_documento()->ESTADO != null): ?>
    <?php if($caso->ultimo_tipo_documento()==141 || $caso->ultimo_tipo_documento()==142 || $caso->ultimo_tipo_documento()==143 || $caso->ultimo_tipo_documento()==144): ?>
        <?php if($caso->ESTADO == 'activo' && in_array($caso->ultimo_documento()->ESTADO, array(1))): ?>        
            <?php echo Form::submit('boton_termino', 'Termino Anticipado', array('onclick' => 'location.href="'.Url::site('caso/termino_anticipado/'.$caso->CASO_ID).'"')); ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<br><br>
<?php //echo "<b> eDoc : ".$caso->ultimo_tipo_documento()." | Estado : ".$caso->ultimo_documento()->ESTADO." | Estado Array: ".in_array($caso->ultimo_documento()->ESTADO, array(1))." | ESTADO CASO: ".$caso->ESTADO." | ".$caso->ultimo_documento()."</b>"?>
<div class="detalle-caso">
    <table id="form-vista">
        <thead>
            <tr class="label-row">
                <th><?php echo __('ID Caso'); ?></th>
                <th><?php echo __('Fecha Creación'); ?></th>
                <th><?php echo __('Tipo Evento'); ?></th>
                <th><?php echo __('Cun'); ?></th>
                <th><?php echo __('Región'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="data-row">
                <td><?php echo $caso->CASO_ID; ?></td>
                <td><?php echo Utiles::full_date($caso->CASO_DTT_CREAC); ?></td>
                <td><?php echo $caso->tipo_evento->DESCRIPCION; ?></td>
                <td><?php echo $caso->CASO_CUN; ?></td>
                <td><?php echo $caso->region->nombre; ?></td>
            </tr>
        </tbody>
    </table>
  
    <table id="form-vista">
        <tbody>
            <tr class="label-row">
              <th><?php echo __('RUT Trabajador');?></th>
              <th><?php echo __('Nombre Trabajador');?></th>
              <th><?php echo __('RUT Empleador');?></th>
              <th><?php echo __('Razón Social');?></th>
              <th><?php echo __('Origen Común');?></th>
            </tr>
            <tr class="data-row">
              <td><?php echo $caso->trabajador->rut; ?></td>
              <td><?php echo $caso->trabajador->nombre_completo(); ?></td>
              <td><?php echo $caso->empleador->rut_empleador; ?></td>
              <td><?php echo $caso->empleador->nombre_empleador; ?></td>
              <td><?php echo $caso->ORIGEN_COMUN; ?></td>
            </tr>
        </tbody>
    </table>  
</div>

<h3>Documentos</h3>
<?php $documentos = $caso->xmls->find_all(); ?>
<?php if(count($documentos) > 0): ?>
    <div class="tabla-general-wrap">
        <table class="tabla-general">
            <thead>
                <tr>
                    <th class="tipo-doc"><?php echo __('Tipo Documento'); ?></th>
                    <th><?php echo __('Estado'); ?></th>
                    <th class="last-right"><?php echo __('Fecha de Creación'); ?></th>
                    <th class="last-right"><?php echo __('Documento'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($documentos as $documento): ?>
                <tr>
                    <td><?php echo $documento->tipo_xml->NOMBRE; ?></td>
                    <td><?php echo $documento->estado_xml->DESCRIPCION; ?></td>
                    <td><?php echo $documento->FECHA_CREACION; ?></td>
                    <td><?php echo $documento->ver(); ?></td>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <h3><?php echo __('No existen documentos asociados');?></h3>
<?php endif; ?>

<div align="right">
    <?php $back_page=URL::site("/", 'http'); ?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>
</div>
