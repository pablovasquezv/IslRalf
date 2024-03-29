<label>Comentarios:</label><br>
<?php echo Form::textarea('observacion', $default["observacion"]); ?>
<div class="error"><?php echo Arr::get($errors, 'observacion'); ?></div>