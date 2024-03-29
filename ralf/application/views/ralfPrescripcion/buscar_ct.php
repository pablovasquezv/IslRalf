<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/themes/base/jquery.ui.all.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.6.2.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.core.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.datepicker.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/fechas_conf.js';?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        console.log("buscando CT");
        
    });
</script>

<?php $si_no=array(1=>'Si',2=>'No'); ?>
<?php $tipos_medida=array(1=>'Medida de control Ingeneril',2=>'Medida de control Administrativo', 3=>'Medida de control Protección Personal'); ?>

<div class="popup-container">
    <h2><?php echo __('Centro de Trabajo'); ?></h2>
    <?php if($mensaje_error):?>          
        <div class="alert alert-success">
            <b><?php echo $mensaje_error; ?></b>
        </div>
    <?php endif; ?>
    <?php echo Form::open()?>
    <div class='form_section_container'>
        <div class='form_section accident'>
            <div class="row">
                <div class="field tipo">
                    <label for=""><?php echo __('Tipo')?></label>        
                    <div class="editable_field tipo">
                        <?php echo Form::select("tipo", $tipos_medida,$default["tipo"]); ?>
                        <div class="error"><?php echo Arr::get($errors, 'tipo'); ?></div>
                    </div>
                </div>

                <div class="field descripcion">
                    <label for=""><?php echo __('Medida')?></label>              
                    <div class="editable_field descripcion">
                        <?php echo Form::input('descripcion', $default['descripcion']); ?>
                        <div class="error"><?php echo Arr::get($errors, 'descripcion'); ?></div>
                    </div>
                </div>

                <div class="field medida_inmediata">
                    <label for=""><?php echo __('Medida Inmediata')?></label>            
                    <div class="editable_field medida_inmediata">
                        <?php echo Form::select("medida_inmediata", $si_no,$default["medida_inmediata"]); ?>
                        <div class="error"><?php echo Arr::get($errors, 'medida_inmediata'); ?></div>
                    </div>
                </div>

                <div class="field plazo_cumplimiento">
                    <label for=""><?php echo __('Plazo')?></label>            
                    <div class="editable_field plazo_cumplimiento">
                        <?php echo Form::input('plazo_cumplimiento', $default['plazo_cumplimiento'],array('class'=>'datepicker')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'plazo_cumplimiento'); ?></div>
                    </div>
                </div>
                
                <div class="clear-both">
                    <br />
                    <?php echo Form::submit('boton_crear_medida', 'Guardar')?>
                </div>
            </div>
        </div>
    </div>
    <?php echo Form::close()?>
</div>