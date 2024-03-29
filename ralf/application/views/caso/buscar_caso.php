<h2><?php echo __('Buscar caso por Cun')?></h2>

<?php echo Form::open('',  array('class'=>'buscador')); ?>
<table>            
  <tr>
    <td><?php echo Form::input('cun', 'Ingrese cun', array('onclick'=>"this.value='';this.style.color='#333'; this.style.fontStyle='normal'"));?>
      <div class="error"><?php echo  Arr::get($errors, 'cun'); ?></div>
    </td>
    <td><?php echo Form::submit('boton_buscar', 'Buscar')?></td>
  </tr>
</table>
<?php echo Form::close()?>