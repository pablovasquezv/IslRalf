<!DOCTYPE html>
<html>
<head>
	<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/easyTree.css'?>" rel="stylesheet" media="screen" />
	<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/bootstrap.css'?>" rel="stylesheet" media="screen" />
	<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.7.min.js';?>"></script>
	<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/easyTree.js';?>"></script>
	<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/arbol_causas.js';?>"></script>
</head>
<body>	

		<div class="col-md-9" style="margin-top:10px;">
			<h4 class="text-primary">Arbol de Causas</h4>
			<?php if($mensaje_error):?>          
	  			<div class="alert alert-success">
	      			<b><?php echo $mensaje_error; ?></b>
	  			</div>
  			<?php endif; ?>

  			<?php if($mensaje_error != "Arbol de Causas Guardado"):?>
				<div class="easy-tree">
					<ul id="nodos"></ul>
				</div>

	          	<?php echo Form::open();?>
	          		<div class="field nombre">
		            	<label for=""><?php echo __('Lesión')?></label>           
		            	<div class="editable_field lesion">
		                	<?php echo Form::input('lesion','' ,array('id' => 'lesion')); ?>
		                	<div class="text-danger"><?php echo Arr::get($errors, 'lesion'); ?></div>
		            	</div>
	          		</div>
	          		<div class="clear-both">
	          			<br />
	          			
	          				<div>
	          					<?php echo Form::button('boton_crear_arbol', 'Crear Arbol Causas', array('onclick' => "guardarEstructutraArbol(event)", 'id'=>'boton_crear_arbol')); ?>
	          				</div>
	          			
	          			<?php echo Form::input('arr_nodos','' ,array('type' => 'hidden', 'id' => 'arr_nodos'));?>
	          			<?php echo Form::input('htmlArbol','' ,array('type' => 'hidden', 'id' => 'htmlArbol'));?>
		            </div>
		        <?php echo Form::close()?>
	        <?php endif; ?>
        </div>

		
	<script>

    (function ($) {
        function init() {
            $('.easy-tree').EasyTree({
                addable: true,
                editable: false,
                deletable: true
            });

        }

        window.onload = init();
    })(jQuery)


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
			          	$(a).hide();
			       });
		       	$t.append($tr);
	       		});
			});
			return $t;
		}
	});


    function buscaGlosaCausaPorCodigo(codigo){
	    console.log("BUSCANDO DATOS DEL CODIGO ["+codigo+"]");
	    $codigo = $("#txt_cod_causa").val();

	    $.ajax({
	      url: '<?php echo URL::site("/arbolCausas/buscar_glosa_causa/");?>',
	      type: 'post',
	      data: {'codigo': codigo },
	      success: function(result) {
	      	if(result.length > 0){
	      		if(codigo == 7999){
	      			$("#txt_glosa_causa").removeAttr("disabled");
	      		}
	      		$("#txt_glosa_causa").val(result);
	      	}else{
	      		alert("No se encontro el codigo: "+codigo);
	      		$("#txt_cod_causa").val("");
	      		$("#txt_glosa_causa").val("");
	      	}
	        
	      },
	      error: function(xhr, desc, err) {
	        console.log(xhr);
	        console.log("Details: " + desc + "\nError:" + err);
	      }
	    });
	}


	function guardarEstructutraArbol(e){
	    console.log("click");
	    var nodos = [];
	    var nodo = {};

	    $nodosArbol = $('li');

	    if($("#lesion").val().length == 0){
	        console.log("LESION VACIO!");
	        $(".text-danger").html("Debe indicar Lesión");
	        e.preventDefault();
	    }else if($nodosArbol.length == 0){
	    	$(".text-danger").html("Debe crear al menos un nodo");
	    	e.preventDefault();
	    }else{

	    	var htmlArbol = $('ul#nodos').html();

	    	$("#nodos li").each(function(){
		        var glosaOtrosNodo = "";
		        valor = this.innerText;

		        if($("#txt_cod_causa").val() == 7999){
		            glosaOtrosNodo = $(this).find('input').val();
		        }

		        idNodo = this.id;
		        hechoNodo = valor.substring(20, valor.indexOf("{")-1);
		        codCausaNodo = valor.substring(valor.indexOf("{")+1, valor.indexOf("}"));
		        glosaOtrosNodo = $(this).find('input').val();

		        nodo = {
		            id: idNodo,
		            hecho: hechoNodo,
		            codCausa: codCausaNodo,
		            glosaOtros: glosaOtrosNodo
		        }
		        nodos.push(nodo);
	    	});
	    	console.log(nodos);
	    	var JsonStr = JSON.stringify(nodos);
	    	$("#arr_nodos").val(JsonStr);
	    	$("#htmlArbol").val(htmlArbol);   	
	    	$("form").submit();

	    }
	    
	}

	</script>
	
</body>
</html>