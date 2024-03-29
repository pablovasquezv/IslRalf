<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base') . 'media/css/themes/base/jquery.ui.all.css' ?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base') . 'media/css/sitio.css' ?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/jquery-1.6.2.js'; ?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/jquery-ui/jquery.ui.core.js'; ?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/jquery-ui/jquery.ui.datepicker.js'; ?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/fechas_conf.js'; ?>"></script>


<script type="text/javascript">
    $(document).ready(function () {

        var w = document.defaultView || document.parentWindow;
        var d = w.parent.document;
        var l = d.getElementById('multas');
        
        <?php if (isset($_POST)): ?>
            $(l).find('tbody').remove();
            $(l).append(get_multas());
        <?php endif; ?>

        function get_multas() {
            $t = $("<tbody></tbody>");
            $.get('<?php echo URL::site("ralfNotificacion/agregar_multa_anexo/$xml_id"); ?>', function (data) {
                $.each(JSON.parse(data), function (k, d) {
                    $tr = $('<tr></tr>').addClass('data-row');
                    $.each(d, function (i, j) {
                        $value = j;
                        $tr.append($("<td></td>").html($value));
                    });
                    $t.append($tr);
                });
            });
            return $t;
        }
    });
</script>


<div class="popup-container">
    <h2><?php echo __('Aplicación Multa'); ?></h2>
    <?php if ($mensaje_error): ?>          
        <div class="alert alert-success">
            <b><?php echo $mensaje_error; ?></b>
        </div>
    <?php endif; ?>
    <?php echo Form::open(NULL, array('enctype' => 'multipart/form-data')) ?>
    <div class='form_section_container'>
        <div class='form_section accident'>
            <div class="row">
                <div class="field tipo_multa">
                    <label for=""><?php echo __('Tipo de multa') ?></label>           
                    <div class="editable_field nombre">
                        <?php echo Form::select("tipo_multa", $config_ralf['147']['tipo_multa'], $default["tipo_multa"], array('id'=>'tipo_multa', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'tipo_multa'); ?></div>
                    </div>
                </div>
                <div class="field fecha_inicio_multa">
                    <label for=""><?php echo __('Fecha inicio multa') ?></label>            
                    <div class="editable_field fecha">
                        <?php echo Form::input("fecha_inicio_multa", $default["fecha_inicio_multa"], array('id'=>'fecha_inicio_multa', 'class'=>'datepicker', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'fecha_inicio_multa'); ?></div>
                    </div>
                </div>
                <div class="field fecha_fin_multa">
                    <label for=""><?php echo __('Fecha fin multa') ?></label>              
                    <div class="editable_field autor">
                        <?php echo Form::input("fecha_fin_multa", $default["fecha_fin_multa"], array('id'=>'fecha_fin_multa', 'class'=>'datepicker', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'fecha_fin_multa'); ?></div>
                    </div>
                </div>
                <div class="field causa_medida_plazo_plazo">
                    <label for=""><?php echo __('Monto') ?></label>            
                    <div class="editable_field antecedente">
                        <?php echo Form::input("monto_multa", $default["monto_multa"], array('id'=>'monto_multa', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'monto_multa'); ?></div>
                    </div>
                </div>
                <div class="field causa_medida_plazo_plazo">
                    <label for=""><?php echo __('Recargo') ?></label>            
                    <div class="editable_field antecedente">
                        <?php echo Form::input("recargo", $default["recargo"], array('id'=>'recargo', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'recargo'); ?></div>
                    </div>
                </div>
                <div class="clear-both">
                    <br />
                    <?php echo Form::submit('boton_agegar_multa', 'Agregar') ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo Form::close(); ?>
</div>