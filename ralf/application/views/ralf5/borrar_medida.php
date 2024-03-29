<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.6.2.js';?>"></script>


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
			$.get('<?php echo URL::site("medidas/resultado_medidas_no_implementadas/$xml_id");?>', function(data) {
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
	<h2><?php echo __('Medida'); ?></h2>
	<?php if($borrado):?>          
		<div class="alert alert-success">
			<b>Medida Borrado correctamente</b>
		</div>
	<?php else:?>
		<div class="alert alert-warning">
			<b>¿Seguro quiere borrar la medida <?php echo $medida->medida?>?</b>
		</div>
		<?php echo Form::open(); echo Form::submit('boton_aceptar', 'Aceptar'); echo Form::close()?>
	<?php endif; ?>
</div>

