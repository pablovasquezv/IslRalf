<script type="text/javascript">
function select_validar(id)
{        
    $.post('http://'+location.host+site+'validar/select_ralf/'+id,
    function(data) {
        $('#select_validar').html(data);        
    });
}
</script>


<div id="denuncia-electronica">    
    <h2><?php echo $nombre_documento; ?> <span class="<?php echo $estado_documento->es_anulado() ? 'inactiva' : 'activa' ?>">(<?php echo $estado_documento->DESCRIPCION ?>)</span></h2>    
    <?php echo Form::open(); ?>
    <?php foreach ($zonas as $low => $upp): ?>
        <?php $z = $zona . $upp; ?>
        <?php if ($xml->$z):?>
            <?php echo View::factory($template . $low, $data)->render(); ?>            
        <?php endif; ?>
    <?php endforeach; ?>
    <br>
    <?php $comentarios=ORM::factory("Comentario")->where('xml_id','=',$xml_id)->where('tipo','=','comentario_admin')->find_all();?>
    <?php if(count($comentarios)>0):?>
        <div class='error'><b>Comentarios anteriores del Admin al Documento</b></div>
        <div class="tabla-general-wrap">
        <table class="tabla-general">
            <thead>
                <tr>
                    <th><?php echo __('ID')?></th>
                    <th><?php echo __('Comentarios de Admin')?></th>                    
                </tr>
            </thead>
            <tbody>          
            <?php foreach($comentarios as $comentario):?>
                <tr>
                    <td><?php echo $comentario->id ?></td>
                    <td><?php echo $comentario->observacion ?></td>
            <?php endforeach;?>
            </tbody>
        </table>
        </div>    
    <?php endif?>
    <br>
    <?php $comentarios=ORM::factory("Comentario")->where('xml_id','=',$xml_id)->where('tipo','=','error_suseso')->find_all();?>
    <?php if(count($comentarios)>0):?>
        <div class='error'><b>Errores de envío a suseso</b></div>
        <div class="tabla-general-wrap">
        <table class="tabla-general">
            <thead>
                <tr>
                    <th><?php echo __('ID')?></th>
                    <th><?php echo __('Error')?></th>                    
                </tr>
            </thead>
            <tbody>          
            <?php foreach($comentarios as $comentario):?>
                <tr>
                    <td><?php echo $comentario->id ?></td>
                    <td><?php echo $comentario->observacion ?></td>
            <?php endforeach;?>
            </tbody>
        </table>
        </div>    
    <?php endif?>
    <br>
    <div class='form_section_container'>
        <div class='form_section accident'>                
            <div class="row">
                <div>
                    <label>Documento Valido?</label><br>                
                    <?php echo Form::select('valido',array(""=>"Seleccione",1=>'No',2=>'Si'), $default["valido"], array("onchange"=>"select_validar(this.value);"))?>
                    <div class="error"><?php echo Arr::get($errors, 'valido'); ?></div>
                </div>                                                 
                <div id="select_validar">
                    <?php if($default["valido"]==1):?>
                        <label>Comentarios:</label><br>                
                        <?php echo Form::textarea('observacion', $default["observacion"])?>
                        <div class="error"><?php echo Arr::get($errors, 'observacion'); ?></div>                    
                    <?php endif?>
                </div>
            </div>
            
        </div>
    </div>          
</div>

<div align="right">   
    <?php echo Form::submit('boton_validar', 'Aceptar')?>
    <?php $back_page=URL::site("/caso/ver_caso_admin/{$caso_id}", 'http')?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>    
</div>
<?php echo Form::close(); ?>