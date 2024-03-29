<div id="denuncia-electronica">
    <?php if (!$pdf): ?>
        <h2><?php echo $nombre_documento; ?> <span class="<?php echo $estado_documento->es_anulado() ? 'inactiva' : 'activa' ?>">(<?php echo $estado_documento->DESCRIPCION; ?>)</span></h2>
    <?php endif; ?>
    <?php echo Form::open(); ?>
        <?php foreach ($zonas as $low => $upp): ?>
            <?php $z = $zona . $upp; ?>
            <?php if ($xml->$z): ?>
                <?php echo View::factory($template . $low, $data)->set('xml_id',$xml_id)->render(); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php echo Form::close(); ?>
</div>

<div align="right">
    <?php if($rol_user == "admin"): ?>
        <?php $back_page = URL::site("/caso/ver_caso_admin/{$caso_id}", 'http')?>
    <?php else: ?>
        <?php $caso = ORM::factory('Caso',$caso_id)?>
        <?php if($caso->ESTADO == 'activo'): ?>
            <?php $back_page = URL::site("/caso/ver_caso/{$caso_id}", 'http')?>
        <?php else: ?>
            <?php $back_page = URL::site("/caso/ingresar/{$caso_id}", 'http')?>
        <?php endif; ?>
    <?php endif; ?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>
</div>