<!DOCTYPE html>
<html>
<head>
	<link type="text/css" href="../../../media/css/easyTree.css" rel="stylesheet" />
	<link type="text/css" href="../../../media/css/bootstrap.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../media/js/jquery-1.7.min.js"></script>	
	<script type="text/javascript" src="../../../media/js/easyTree.js"></script>
	<script type="text/javascript" src="../../../media/js/arbol_causas.js"></script>
</head>
<body>

	<div class="col-md-9" style="margin-top:10px;">
		<h4 class="text-primary">Arbol de Causas</h4>
		<div class="easy-tree">
			<ul id="nodos_ver"><?php echo $arbol->arbolstring;?></ul>
		</div>
        <?php echo Form::open();?>
          	<div class="clear-both">
          		<br />
	        </div>
	    <?php echo Form::close()?>
    </div>
    
</body>
</html>