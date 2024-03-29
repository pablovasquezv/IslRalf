<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base') . 'media/css/themes/base/jquery.ui.all.css' ?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base') . 'media/css/sitio.css' ?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/style_sisesat.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/jquery-1.6.2.js'; ?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/jquery-ui/jquery.ui.core.js'; ?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/jquery-ui/jquery.ui.datepicker.js'; ?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base') . 'media/js/fechas_conf.js'; ?>"></script>

<script type="text/javascript">
    $(document).ready(function () {

    });
</script>

<div class="popup-container">
    <h2><?php echo __('Agregar Usuario'); ?></h2>
    <?php if ($mensaje_error): ?>
        <div class="alert alert-success">
            <b><?php echo $mensaje_error; ?></b>
        </div>
    <?php endif; ?>
    <?php echo Form::open(NULL, array('enctype' => 'multipart/form-data')) ?>
    <div class='form_section_container'>
        <div class='form_section accident'>
            <div class="row field">
                <div class="field name">
                    <label><?php echo __('Nombre'); ?></label>
                    <?php echo Form::input("name", $default['name'], array('id'=>'name')); ?>
                    <div class="error"><?php echo Arr::get($errors, 'name'); ?></div>
                </div>
                <div class="field email">
                    <label><?php echo __('Email') ?></label>
                    <div class="editable_field email">
                        <?php echo Form::input("email", $default['email'], array('id'=>'email')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'email'); ?></div>
                    </div>
                </div>
            </div>
            <div class="row field roles">
                <label for=""><?php echo __('Roles') ?></label>
                <div class="protected_field roles">
                    <table>
                        <tbody>
                            <tr class="data-row">
                            <?php foreach ($roles as $rol) {
                                echo '<td><input type="checkbox" name="roles['.$rol->id.']" value="'.$rol->name.'" >'.$rol->name.'</td>';
                            } ?>
                            </tr>
                        </tbody>
                    </table>
                    <div class="error"><?php echo Arr::get($errors, 'roles'); ?></div>
                </div>
            </div>
            <div class="row field">
                <div class="field name">
                    <label><?php echo __('Región'); ?></label>
                    <?php echo Form::select("region_id", $regiones, $default['region_id'], array('id'=>'region_id', 'data-validation'=>'required', 'style="width: 250px;"')); ?>
                    <div class="error"><?php echo Arr::get($errors, 'region_id'); ?></div>
                </div>
            </div>
            <div class="clear-both">
                <br />
                <?php echo Form::submit('boton_add_usuario', 'Agregar') ?>
            </div>
            </div>
        </div>
    </div>
    <?php echo Form::close(); ?>
</div>