<h2><?php echo __('Ingresar Caso Nº ').$caso->CASO_ID;?></h2>
<br>
<div class="detalle-caso">
  <table id="form-vista">
    <tbody>
      <tr class="label-row">
        <th><?php echo __('ID Caso');?></th>
        <th><?php echo __('Fecha Creación');?></th>
        <th><?php echo __('Tipo Evento');?></th>
        <th><?php echo __('Cun');?></th>
        <th><?php echo __('Región');?></th>
      </tr>
    </tbody>
      <tr class="data-row">
        <td><?php echo $caso->CASO_ID; ?></td>
        <td><?php echo Utiles::full_date($caso->CASO_DTT_CREAC); ?></td>
        <td><?php echo $caso->tipo_evento->DESCRIPCION; ?></td>
        <td><?php echo $caso->CASO_CUN; ?></td>
        <td><?php echo $caso->region->nombre; ?></td>
      </tr>
  </table>
  
  <table id="form-vista">
    <tbody>
      <tr class="label-row">
        <th><?php echo __('RUT Trabajador');?></th>
        <th><?php echo __('Nombre Trabajador');?></th>
        <th><?php echo __('RUT Empleador');?></th>
        <th><?php echo __('Razón Social');?></th>
      </tr>
      <tr class="data-row">
        <td><?php echo $caso->trabajador->rut; ?></td>
        <td><?php echo $caso->trabajador->nombre_completo(); ?></td>
        <td><?php echo $caso->empleador->rut_empleador; ?></td>
        <td><?php echo $caso->empleador->nombre_empleador ; ?></td>
      </tr>
    </tbody>
  </table>  
</div>
<h3>Documentos</h3>
<?php $documentos=$caso->xmls->find_all()?>
 </div>
    <?php if(count($documentos)>0):?>
    <div class="tabla-general-wrap">
      <table class="tabla-general">
        <thead>
          <tr>
            <th class="tipo-doc"><?php echo __('Tipo Documento')?></th>
            <th><?php echo __('Estado')?></th>            
            <th class="last-right"><?php echo __('Fecha de Creación')?></th>
            <th class="last-right"><?php echo __('valido?')?></th>
            <th class="last-right"><?php echo __('Documento')?></th>
          </tr>
        </thead>
        <tbody>          
          <?php foreach($documentos as $documento):?>
          <tr>
            <td><?php echo $documento->tipo_xml->NOMBRE ?></td>
            <td><?php echo $documento->estado_xml->DESCRIPCION ?></td>
            <td><?php echo $documento->FECHA_CREACION ?></td>            
            <td><?php echo ($documento->VALIDO==1)?'Valido':'No valido';?></td>
            <td><?php echo $documento->ver();?></td>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <?php else:?>
      <h3><?php echo __('No existen documentos asociados')?></h3>
    <?php endif?>


<div align="right">        
    <?php echo Form::open()?>      
    <?php $back_page=URL::site("/", 'http')?>
    <?php echo Form::submit('boton_ingresar', 'Ingresar'); ?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>
    <?php echo Form::close()?>      
</div>