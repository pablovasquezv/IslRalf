<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/themes/base/jquery.ui.all.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/style_sisesat.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.7.min.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.core.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.datepicker.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/fechas_conf.js';?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        
    });
</script>

<?php 
    $tipos_medida = array(
        1=>'Medida de control Ingeneril',
        2=>'Medida de control Administrativo',
        3=>'Medida de control Protección Personal');
    
    $cumplimiento_medida = array(
        1=>'Cumple medida prescrita por el OA',
        2=>'Cumple implementando medida equivalente o superior, distinta a la prescrita por el OA',
        3=>'No cumple, no implementando o implementando deficientemente medida prescrita por el OA',
        4=>' No cumple, implementando medida deficiente distinta a la prescrita por el OA'); ?>

<div class="popup-container">
    <h2><?php echo __('Verificar Medida').": ".$medida_id; ?></h2>
    <?php if($mensaje_error):?>
        <div class="alert alert-success">
            <b><?php echo $mensaje_error; ?></b>
        </div>
    <?php endif; ?>
    <?php echo Form::open()?>
    <div class='form_section_container'>
        <h3>Medida</h3>
        <table class="info-table">
            <thead>
                <tr class="label-row">
                    <th>Folio Medida</th>
                    <th>Causa</th>                        
                </tr>
            </thead>
            <tbody>
                <tr class="data-row">
                    <td><?php echo $medida_id; ?></td>
                    <td><?php echo $glosa_causa; ?></td>
                </tr>
            </tbody>
        </table>
        <table class="info-table">
            <thead>
                <tr class="label-row">
                    <th>Tipo Medida</th>
                    <th>Descripción Medida</th>                        
                </tr>
            </thead>
            <tbody>
                <tr class="data-row">
                    <td><?php echo $tipos_medida[(int)$tipo]?></td>
                    <td><?php echo $descripcion?></td>
                </tr>
            </tbody>
        </table><br>
        
        <h3>Verificación</h3>
        <table class="info-table">
            <thead>
                <tr class="label-row">
                    <th>Fecha Verificación</th>
                    <th>Cumplimiento Medida</th>
                    <th>Fecha Cumplimiento</th>
                </tr>
            </thead>
            <tbody>
                <tr class="data-row">
                    <td>
                        <?php echo Form::input('fecha_verificacion', $default['fecha_verificacion'], array('class'=>'datepicker','id'=>'fecha_verificacion','data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'fecha_verificacion'); ?></div>
                    </td>
                    <td>
                        <?php echo Form::select("cumplimiento_medida", $cumplimiento_medida, $default['cumplimiento_medida']); ?>
                        <div class="error"><?php echo Arr::get($errors, 'cumplimiento_medida'); ?></div>
                    </td>
                    <td>
                        <?php echo Form::input('fecha_cumplimiento', $default['fecha_cumplimiento'], array('class'=>'datepicker','id'=>'fecha_cumplimiento','data-validation'=>'required')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'fecha_cumplimiento'); ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="info-table">
            <thead>
                <tr class="label-row">
                    <th>Obs. Verificación</th>
                </tr>
            </thead>
            <tbody>
                <tr class="data-row">
                    <td>
                        <?php echo Form::textarea('observacion_verificacion', $default['observacion_verificacion'], array('id'=>'observacion_verificacion', 'cols'=>100, 'rows'=> 5)); ?>
                        <div class="error"><?php echo Arr::get($errors, 'observacion_verificacion'); ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div class="clear-both">
            <br />
            <?php echo Form::submit('boton_verificar_medida', 'Guardar')?>
        </div>
    </div>
    <?php echo Form::close()?>
</div>

