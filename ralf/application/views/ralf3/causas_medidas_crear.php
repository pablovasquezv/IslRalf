<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/themes/base/jquery.ui.all.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.6.2.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.core.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.datepicker.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/fechas_conf.js';?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        var w = document.defaultView || document.parentWindow;
        var d = w.parent.document;
        var l = d.getElementById('cscs');
        <?php if(isset($_POST)):?>
        $(l).find('tbody').remove();
        $(l).append(get_adjuntos());
        <?php endif;?>


        function get_adjuntos(){
            $t = $("<tbody></tbody>");
            $.get('<?php echo URL::site("ralf3/obtener_causas_medidas_correctivas/$xml_id");?>', function(data) {
                $.each(JSON.parse(data),function(k,d) {
                    $tr = $('<tr></tr>').addClass('data-row');
                    $.each(d,function(i,j) {                        
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
    <h2><?php echo __('Causas y Medidas Correctivas'); ?></h2>
    <?php if($mensaje_error):?>          
        <div class="alert alert-success">
            <b><?php echo $mensaje_error; ?></b>
        </div>
    <?php endif; ?>
    <?php echo Form::open()?>
    <div class='form_section_container'>
        <div class='form_section accident'>
            <div class="row">
                <div class="field causa_medida_plazo_id">
                    <label for=""><?php echo __('ID')?></label>           
                    <div class="editable_field causa_medida_plazo_id">
                        <?php echo Form::input('causa_medida_plazo_id', $default['causa_medida_plazo_id']); ?>
                        <div class="error"><?php echo Arr::get($errors, 'causa_medida_plazo_id'); ?></div>
                    </div>
                </div>

                <div class="field causa_medida_plazo_plazo">
                    <label for=""><?php echo __('Plazo')?></label>            
                    <div class="editable_field causa_medida_plazo_plazo">
                        <?php echo Form::input('causa_medida_plazo_plazo', $default['causa_medida_plazo_plazo'],array('class'=>'datepicker')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'causa_medida_plazo_plazo'); ?></div>
                    </div>
                </div>

                <div class="field causa_medida_plazo_causa">
                    <label for=""><?php echo __('Causa')?></label>            
                    <div class="editable_field causa_medida_plazo_causa">
                        <?php echo Form::input('causa_medida_plazo_causa', $default['causa_medida_plazo_causa']); ?>
                        <div class="error"><?php echo Arr::get($errors, 'causa_medida_plazo_causa'); ?></div>
                    </div>
                </div>

                <div class="field causa_medida_plazo_medida">
                    <label for=""><?php echo __('Medida')?></label>              
                    <div class="editable_field causa_medida_plazo_medida">
                        <?php echo Form::input('causa_medida_plazo_medida', $default['causa_medida_plazo_medida']); ?>
                        <div class="error"><?php echo Arr::get($errors, 'causa_medida_plazo_medida'); ?></div>
                    </div>
                </div>
                <div class="clear-both">
                    <br />
                    <?php echo Form::submit('boton_crear_causa_medida', 'Guardar')?>
                </div>
            </div>
        </div>
    </div>
<!--
    <table>
        <tr >
            <td><?php echo __('ID')?></td>
            <td><?php echo Form::input("causa_medida_plazo_id", $default["causa_medida_plazo_id"]); ?><div class="error"><?php echo Arr::get($errors, "causa_medida_plazo_id"); ?></div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Causa')?></td>
            <td><?php echo Form::input("causa_medida_plazo_causa", $default["causa_medida_plazo_causa"]); ?><div class="error"><?php echo Arr::get($errors, "causa_medida_plazo_causa"); ?></div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Medida')?></td>
            <td><?php echo Form::input("causa_medida_plazo_medida", $default["causa_medida_plazo_medida"]); ?><div class="error"><?php echo Arr::get($errors, "causa_medida_plazo_medida"); ?></div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Plazo')?></td>
            <td><?php echo Form::input("causa_medida_plazo_plazo", $default["causa_medida_plazo_plazo"],array('class'=>'datepicker')); ?><div class="error"><?php echo Arr::get($errors, "causa_medida_plazo_plazo"); ?></div>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php echo Form::submit('boton_crear_causa_medida', 'Guardar')?></td>
        </tr>
    </table>-->
    <?php echo Form::close()?>
</div>