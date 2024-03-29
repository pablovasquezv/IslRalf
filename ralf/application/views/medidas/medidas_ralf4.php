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
  		var l = d.getElementById('medida1');
  		<?php if(isset($_POST)):?>
		$(l).find('tbody').remove();
		$(l).append(get_medidas1());
		<?php endif;?>


		function get_medidas1(){
			$t = $("<tbody></tbody>");
			$.get('<?php echo URL::site("medidas/resultado_medidas_ralf4/$xml_id");?>', function(data) {
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



<?php $si_no=array(1=>'Si',2=>'No'); ?>

<div class="popup-container popup-medium">
  <h2><?php echo __('Cumplimiento Medidas'); ?></h2>
  <?php if($mensaje_error):?>          
      <div class="alert alert-success">
          <b><?php echo $mensaje_error; ?></b>
      </div>
  <?php endif; ?>  
  <br />
  <?php echo Form::open()?>
  <div class='form_section_container'>
      <div class='form_section accident'>
        <div class="row">
          <div class="field cumplimiento_medida_id">
            <label for=""><?php echo __('Nº')?></label>           
            <div class="editable_field cumplimiento_medida_id">
                <?php echo Form::input('cumplimiento_medida_id', $default['cumplimiento_medida_id']); ?>
                <div class="error"><?php echo Arr::get($errors, 'cumplimiento_medida_id'); ?></div>
            </div>
          </div>
          <div class="field cumplimiento_medida_medida">
            <label for=""><?php echo __('Medida')?></label>            
            <div class="editable_field cumplimiento_medida_medida">
                <?php echo Form::input('cumplimiento_medida_medida', $default['cumplimiento_medida_medida']); ?>
                <div class="error"><?php echo Arr::get($errors, 'cumplimiento_medida_medida'); ?></div>
            </div>
          </div>
          <div class="field cumplimiento_medida_medida_implementada">
            <label for=""><?php echo __('Medida Implementada')?></label>              
            <div class="editable_field cumplimiento_medida_medida_implementada">
                <?php echo Form::select("cumplimiento_medida_medida_implementada", $si_no,$default["cumplimiento_medida_medida_implementada"]); ?>
                <div class="error"><?php echo Arr::get($errors, 'cumplimiento_medida_medida_implementada'); ?></div>
            </div>
          </div>
          <div class="field cumplimiento_medida_ampliacion_plazo">
            <label for=""><?php echo __('Ampliación Plazo')?></label>              
            <div class="editable_field cumplimiento_medida_ampliacion_plazo">
                <?php echo Form::select("cumplimiento_medida_ampliacion_plazo", $si_no,$default["cumplimiento_medida_ampliacion_plazo"]); ?>
                <div class="error"><?php echo Arr::get($errors, 'cumplimiento_medida_ampliacion_plazo'); ?></div>
            </div>
          </div>
          <div class="field cumplimiento_medida_nueva_fecha_ampliacion_plazo">
            <label for=""><?php echo __('Nueva Fecha de Ampliación Plazo')?></label>           
            <div class="editable_field cumplimiento_medida_nueva_fecha_ampliacion_plazo">
                <?php echo Form::input('cumplimiento_medida_nueva_fecha_ampliacion_plazo', $default['cumplimiento_medida_nueva_fecha_ampliacion_plazo'], array('class'=>'datepicker')); ?>
                <div class="error"><?php echo Arr::get($errors, 'cumplimiento_medida_nueva_fecha_ampliacion_plazo'); ?></div>
            </div>
          </div>
          <div class="field cumplimiento_medida_observaciones">
            <label for=""><?php echo __('Observaciones')?></label>            
            <div class="editable_field cumplimiento_medida_observaciones">
                <?php echo Form::textarea('cumplimiento_medida_observaciones', $default['cumplimiento_medida_observaciones']); ?>
                <div class="error"><?php echo Arr::get($errors, 'cumplimiento_medida_observaciones'); ?></div>
            </div>
          </div>
          <div class="clear-both">
            <br />
            <?php echo Form::submit('boton_aceptar', 'Aceptar')?>
          </div>
        </div>
      </div>
    </div>
    <?php echo Form::close();?>
</div>

