<script type="text/javascript">
    
$(document).ready(function() {
        $("#datatable").dataTable({
            "oLanguage": {
                "sUrl": "<?php echo Kohana::$config->load("sitio.media");?>vendor/datatable/language/spanish.txt"
            },
            "aaSorting": [[ 0, "desc" ]]  
        });
         /*** esconde el id de las filas para poder ordenar desc o asc ***/
        $(".dataTable_hide").hide();
        
    });
    
</script>

<h2 class="tit-bandeja"><?php echo __('Intentos de Envíos'); ?></h2>
<br>    
<?php if(count($envios)>0):?>
<table id="datatable" class="tabla-general">
    <thead>
        <th class="dataTable_hide"><?php echo __("INTENTO_ID"); ?></th>                  
        <th>XML_ID</th>        
        <th>Fecha</th>        
        <th>codigo_retorno</th>        
        <th>retorno_completo</th>        
        <th>Ver Doc</th>        
        
</thead>
<tbody>
    <?php foreach ($envios as $envio): ?>
        <tr>
            <td class="dataTable_hide"><?php echo $envio->INTENTO_ID; ?></td>            
            <td><?php echo $envio->XML_ID?></td>            
            <td><?php echo $envio->FECHA?></td>            
            <td><?php echo $envio->codigo_retorno?></td>            
            <td><?php echo $envio->retorno_completo?></td> 
            <td><?php echo $envio->xml->ver()?></td>
        </tr>
    <?php endforeach; ?>
</tbody>
<?php else: ?>
    <h3 class="information"><?php echo __("No hay información disponible");?></h3>
<?php endif;?>
</table>