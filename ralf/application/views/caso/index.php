<script type="text/javascript">
    var filtro = "";
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            oLanguage: {
                "sUrl": "<?php echo Kohana::$config->load("sitio.media");?>vendor/datatable/language/spanish.txt",
                "sPaginationType": "full_numbers",
            },
            aaSorting: [[ 0, "desc" ]],
            dom: 'lr<"table-filter-container">tip',
            initComplete: function(settings){
                var api = new $.fn.dataTable.Api( settings );
                $('.table-filter-container', api.table().container()).append(
                    $('#table-filter').detach().show()
                );
                
                $('#table-filter select').on('change', function(){
                    table.search(this.value).draw(); 
                    filtro = this.value;
                });       
            }
        });
        /*** esconde el id de las filas para poder ordenar desc o asc ***/
        $(".dataTable_hide").hide();

        
    });
    function onChangeFiltro(selectObject) {
        var value = selectObject.value;  
        filtro = value;
        console.log(value);
    }
</script>
<?php 
    $listSelect = array("Todos", "SI", "NO");
    $filtro_comun_seleccionado = Request::current()->post('filtro_origen_comun');

    if($filtro_comun_seleccionado == ""){
        $filtro_comun_seleccionado = 0;
    }
?>
<h2 class="tit-bandeja"><?php echo __('Casos recepcionados'); ?></h2>
<h3>Región <?php echo $usuario_region?></h3>
<?php echo Form::submit('boton_buscar', 'Buscar Caso', array('onclick' => 'location.href="' . Url::site('caso/buscar_caso') . '"')) ?>
<br><br>
<div style="float: right;">
    <?php echo Form::open('caso/filtrado/', array('method' => 'post')); ?>
        <p>
            Origen Común:
            <?php echo Form::select('filtro_origen_comun', $listSelect, $filtro_comun_seleccionado );?>
            
            <?php echo Form::submit(NULL, 'Filtrar') ?>
        </p>
    <br>
</div>

<?php if(count($casos)>0):?>
<table id="datatable" class="tabla-general">
    <thead>
        <th class="dataTable_hide"><?php echo __("Código Caso"); ?></th>
        <th><h4>CUN</h4></th>
        <th><h4>Origen Común</h4></th>
        <th><h4>RUT Trabajador</h4></th>
        <th><h4>RUT Empleador</h4></th>
        <th><h4>Razón Social Empleador</h4></th>
        <th><h4>Estado Ultimo Documento</h4></th>
        <th><h4>Detalle</h4></th>
</thead>
<tbody>
    <?php foreach ($casos as $caso): ?>
        <tr>
            <td class="dataTable_hide"><?php echo $caso->CASO_ID; ?></td>
            <td><?php echo $caso->CASO_CUN; ?></td>
            <td><?php echo $caso->ORIGEN_COMUN; ?></td>
            <td><?php echo $caso->trabajador->rut; ?></td>
            <td><?php echo $caso->empleador->rut_empleador; ?></td>
            <td><?php echo $caso->empleador->nombre_empleador; ?></td>
            <td><?php echo $caso->estado_ultimo_documento(); ?></td>
            <td><?php echo Html::anchor("caso/ver_caso/{$caso->CASO_ID}", 'Detalle'); ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>
<?php else: ?>
    <h3 class="information"><?php echo __("No hay información disponible");?></h3>
<?php endif;?>
</table>