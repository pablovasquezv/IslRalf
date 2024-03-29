<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.6.2.js';?>"></script>

<script type="text/javascript">
	$(document).ready(function() {

		var w = document.defaultView || document.parentWindow;
  		var d = w.parent.document;
  		var l = d.getElementById('arbol');
  		var a = d.getElementById('lnk_arbol');
  		<?php if(isset($_POST)):?>
		$(l).find('tbody').remove();
		$(l).append(obtener_arbol());
		<?php endif;?>


		function obtener_arbol(){
			$t = $("<tbody></tbody>");
			$.get('<?php echo URL::site("arbolCausas/buscar_arbol_causas/$xml_id");?>', function(data) {
				$.each(JSON.parse(data),function(k,d) {
					$tr = $('<tr></tr>').addClass('data-row');
			       	$.each(d,function(i,j) {			        	
		        		$value = j;			        	
			          	$tr.append($("<td></td>").html($value));
			          	$(a).show();
			       });
		       	$t.append($tr);
	       		});
			});
			return $t;
		}
	});
</script>

<div class="popup-container">
	<h2><?php echo __('Borrar Arbol Causas'); ?></h2>
	<?php if($borrado):?>          
		<div class="alert alert-success">
			<b>Arbol Borrado Correctamente</b>
		</div>
	<?php else:?>
		<div class="alert alert-warning">
			<b>¿Seguro quiere borrar el Arbol de Causas?</b>
		</div>
		<?php echo Form::open(); echo Form::submit('boton_aceptar', 'Aceptar',array('id' =>'btn_borrar_arbol' )); echo Form::close()?>
	<?php endif; ?>
</div>