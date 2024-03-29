<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base') . 'media/css/sitio.css' ?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/jquery-1.6.2.js'; ?>"></script>

<div class="popup-container">
    <h2><?php echo __('Documento'); ?></h2>
    <?php if ($borrado): ?>
        <div class="alert alert-success">
            <b>Documento anulado correctamente</b><br>
            <b>SUSESO: </b><?php echo $res['return']; ?><br>
            <b>Mensaje: </b><?php echo $res['error_message']; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <b>¿Seguro quiere anular documento: <?php echo $documento->tipo_xml->NOMBRE . " (" . $documento->XML_ID . ")"; ?>?</b>
        </div>
        <?php
        echo Form::open();
        echo Form::submit('boton_aceptar', 'Aceptar');
        echo Form::close()
        ?>
<?php endif; ?>
</div>