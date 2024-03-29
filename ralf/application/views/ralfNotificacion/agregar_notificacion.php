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
        var l = d.getElementById('notificaciones');
        
        <?php if (isset($_POST)): ?>
            $(l).find('tbody').remove();
            $(l).append(get_notificaciones());
        <?php endif; ?>

        function get_notificaciones() {
            $tb = $("<tbody></tbody>");
            $.get('<?php echo URL::site("ralfNotificacion/agregar_notificacion_anexo/$xml_id"); ?>', function (data) {
                $.each(JSON.parse(data), function (k, d) {
                    $tr = $('<tr></tr>').addClass('data-row');
                    $.each(d, function (i, j) {
                        $value = j;
                        $tr.append($("<td></td>").html($value));
                    });
                    $tb.append($tr);
                });
            });
            return $tb;
        }
    });
</script>


<div class="popup-container">
    <h2><?php echo __('Notificación'); ?></h2>
    <?php if ($mensaje_error): ?>          
        <div class="alert alert-success">
            <b><?php echo $mensaje_error; ?></b>
        </div>
    <?php endif; ?>
    <?php echo Form::open(NULL, array('enctype' => 'multipart/form-data')) ?>
    <div class="tabla-general-wrap">
        <table class="tabla-form">
            <tbody>
                <tr>
                    <td><label><?php echo __('Fecha') ?>:</label></td>
                    <td>
                        <?php echo Form::input("fecha_notificacion_autoridad", $default["fecha_notificacion_autoridad"], array('id'=>'fecha_notificacion_autoridad', 'class'=>'datepicker')); ?>
                        <div class="error"><?php echo Arr::get($errors, "fecha_notificacion_autoridad"); ?></div>
                    </td>
                    <td><label><?php echo __('Autoridad receptora') ?>:</label></td>
                    <td>
                        <?php echo Form::select("autoridad_receptora", $config_ralf['147']['autoridad_receptora'], $default["autoridad_receptora"], array('id'=>'autoridad_receptora', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, "autoridad_receptora"); ?></div>
                    </td>
                    <td><label><?php echo __('Región autoridad') ?>:</label></td>
                    <td>
                        <?php echo Form::select("region_autoridad_receptora", $regiones, $default["region_autoridad_receptora"], array('id'=>'region_autoridad_receptora', 'data-validation'=>'required', 'style="width: 250px;"')); ?>
                        <div class="error"><?php echo Arr::get($errors, "region_autoridad_receptora"); ?></div>
                    </td>
                </tr>
                <tr>
                    <th colspan="8"><h4>Autoridad Receptora</h4></th>
                </tr>
                <tr>
                    <td><label><?php echo __('Paterno') ?>:</label></td>
                    <td>
                        <?php echo Form::input("apellido_paterno_autoridad", $default["apellido_paterno_autoridad"], array('id'=>'apellido_paterno_autoridad', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, "apellido_paterno_autoridad"); ?></div>
                    </td>
                    <td><label><?php echo __('Materno') ?>:</label></td>
                    <td>
                        <?php echo Form::input("apellido_materno_autoridad", $default["apellido_materno_autoridad"], array('id'=>'apellido_materno_autoridad', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, "apellido_materno_autoridad"); ?></div>
                    </td>
                    <td><label><?php echo __('Nombres') ?>:</label></td>
                    <td>
                        <?php echo Form::input("nombres_autoridad", $default["nombres_autoridad"], array('id'=>'nombres_autoridad', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, "nombres_autoridad"); ?></div>
                    </td>
                </tr>
                <tr>
                    <td><label><?php echo __('Rut') ?>:</label></td>
                    <td>
                        <?php echo Form::input("rut_profesional_autoridad", $default["rut_profesional_autoridad"], array('id'=>'rut_profesional_autoridad', 'data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, "rut_profesional_autoridad"); ?></div>
                    </td>
                    <td><label><?php echo __('Email') ?>:</label></td>
                    <td colspan="3">
                        <?php echo Form::input("correo_elect_resp_autoridad", $default["correo_elect_resp_autoridad"], array('id'=>'correo_elect_resp_Autoridad', 'data-validation'=>'required', 'style="width: 250px;"')); ?>
                        <div class="error"><?php echo Arr::get($errors, "correo_elect_resp_autoridad"); ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="clear-both">
        <br />
        <?php echo Form::submit('boton_agegar_notificacion', 'Agregar') ?>
    </div>
    <?php echo Form::close(); ?>
</div>