<h2 class="tit-bandeja"><?php echo __('CODIGO INTENCIONALIDAD'); ?></h2>
<table id="datatable" class="tabla-general">
    <thead>        
        <th>Código</th>        
        <th>Descripcionn</th>
        
	</thead>
	<tbody>
	<?php foreach ($codigos as $key=>$descripcion):?>
		<tr>
			<td><?php echo $key?></td>        
        	<td><?php echo $descripcion?></td>        
		</tr>
	<?php endforeach;?>
	</tbody>
</table>