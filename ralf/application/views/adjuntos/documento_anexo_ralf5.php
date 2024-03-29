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
  		var l = d.getElementById('adjuntos');
  		<?php if(isset($_POST)):?>
		$(l).find('tbody').remove();
		$(l).append(get_adjuntos());
		<?php endif;?>


		function get_adjuntos(){
			$t = $("<tbody></tbody>");
			$.get('<?php echo URL::site("adjuntos/adjuntos_ralf5/$xml_id");?>', function(data) {
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
  <h2><?php echo __('Documento Anexo'); ?></h2>
  <?php if($mensaje_error):?>          
	  <div class="alert alert-success">
	      <b><?php echo $mensaje_error; ?></b>
	  </div>
  <?php endif; ?>
  <?php echo Form::open(NULL, array('enctype' => 'multipart/form-data'))?>
  <div class='form_section_container'>
      <div class='form_section accident'>
        <div class="row">
          <div class="field nombre">
            <label for=""><?php echo __('Nombre')?></label>           
            <div class="editable_field nombre">
                <?php echo Form::input('nombre', $default['nombre']); ?>
                <div class="error"><?php echo Arr::get($errors, 'nombre'); ?></div>
            </div>
          </div>
          <div class="field fecha">
            <label for=""><?php echo __('Fecha')?></label>            
            <div class="editable_field fecha">
                <?php echo Form::input('fecha', $default['fecha'],array('class'=>'datepicker')); ?>
                <div class="error"><?php echo Arr::get($errors, 'fecha'); ?></div>
            </div>
          </div>
          <div class="field autor">
            <label for=""><?php echo __('Autor')?></label>              
            <div class="editable_field autor">
                <?php echo Form::input('autor', $default['autor']); ?>
                <div class="error"><?php echo Arr::get($errors, 'autor'); ?></div>
            </div>
          </div>
          <div class="field causa_medida_plazo_plazo">
            <label for=""><?php echo __('Seleccione archivo')?></label>            
            <div class="editable_field antecedente">
                <?php echo Form::file('antecedente'); ?>
                <div class="error"><?php echo Arr::get($errors, 'antecedente'); ?></div>
            </div>
          </div>
          <div class="clear-both">
          	<br />
          	<?php echo Form::submit('boton_subir_documento', 'Subir')?>
          </div>
        </div>
      </div>
    </div>
	<?php echo Form::close();?>
</div>