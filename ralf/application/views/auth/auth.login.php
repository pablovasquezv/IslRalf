<div class="login-wrap">
  <div class="login">
    <?php echo Form::open() ?>
    <table class="tabla-form">
      <tr>
        <th><label><?php echo __('Usuario') ?>:</label></th>
        <td><?php echo Form::input('username'); ?></td>
      </tr>
      <tr>
        <th><label><?php echo __('Contraseña'); ?>:</label></th>
        <td><?php echo Form::password('password'); ?></td>
      </tr>
      <tr>
        <td colspan="2" class="link-button">          
          <?php echo Form::submit('sign_in', 'Ingresar', array('class'=>'boton')); ?>
        </td>
      </tr>
    </table>
    <?php echo form::close(); ?>
  </div>
</div>