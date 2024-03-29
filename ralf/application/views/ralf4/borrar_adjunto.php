<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.6.2.js';?>"></script>

<?php
if($adjunto_origen=="documentos_anexos"){
	$origen="adjuntos_documento_anexo";
	$origen2="adjuntos";
}


?>
<script type="text/javascript">
	$(document).ready(function() {

		var w = document.defaultView || document.parentWindow;
  		var d = w.parent.document;
  		var l = d.getElementById(<?php echo "'$origen2'"?>);
  		<?php if(isset($_POST)):?>
		$(l).find('tbody').remove();
		$(l).append(get_adjuntos());
		<?php endif;?>


		function get_adjuntos(){
			$t = $("<tbody></tbody>");
			$.get('<?php echo URL::site("adjuntos/$origen/$xml_id");?>', function(data) {
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
	<h2><?php echo __('Adjunto'); ?></h2>
	<?php if($borrado):?>          
		<div class="alert alert-success">
			<b>Adjunto Borrado correctamente</b>
		</div>
	<?php else:?>
		<div class="alert alert-warning">
			<b>¿Seguro quiere borrar el adjunto <?php echo $nombre_documento?>?</b>
		</div>
		<?php echo Form::open(); echo Form::submit('boton_aceptar', 'Aceptar'); echo Form::close()?>
	<?php endif; ?>
</div>