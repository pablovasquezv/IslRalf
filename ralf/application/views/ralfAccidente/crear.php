<!--<link href="/ralf/media/denuncias/application-2413e909b83a5b20b06b8e89c1c074a5.css" media="screen" rel="stylesheet" type="text/css" />-->
<div id="denuncia-electronica">    
    <h2><?php echo __('(RALF Accidente) Notificación provisoria inmediata accidente laboral fatal'); ?></h2>

    <?php $comentarios=ORM::factory("Comentario")->where('xml_id','=',$xml_id)->find_all();?>
    <?php if(count($comentarios)>0):?>
        <div class='error'><b>El documento no fue validado por el Admin</b></div>
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
    <?php if($errores_esquema):?>            
    <div class='errores_esquema'>
        <h4><?php echo __('Existen errores en los siguientes campos:'); ?></h4>
            <ul>
                <?php foreach ($errores_esquema as $error):?>
                    <li><?php echo $error; ?></li>
                <?php endforeach;?>
            </ul>
        </div>  
    <?php endif; ?>     
    <?php echo Form::open(); ?>
    <?php echo View::factory('ralfAccidente/zona/zona_a', $data)->render(); ?>
    <?php echo View::factory('ralfAccidente/zona/zona_b', $data)->set('errors',$errors)->set('default',$default)->render(); ?>
    <?php echo View::factory('ralfAccidente/zona/crear/zona_b_complemento', $data)->set('errors',$errors)->set('default',$default)->render(); ?>     
    <?php echo View::factory('ralfAccidente/zona/zona_c', $data)->set('errors',$errors)->set('default',$default)->render(); ?>   
    <?php echo View::factory('ralfAccidente/zona/crear/zona_p')->set('errors',$errors)->set('default',$default)->set('data',$data)->render(); ?>     
</div>

<div align="right">
    <?php $llamar_servicio=URL::site("caso/ingreso_visita/".$caso->CASO_ID."/".$caso->ultimo_documento(), 'http'); ?>        
    <?php echo Form::input('boton_llamar_servicio', 'Crear Visita Prevención en Terreno', array('type' => 'button', 'onclick' => "send_page('$llamar_servicio')")); ?>
    
    <?php echo Form::submit('boton_incompleta', 'Guardar Incompleta')?>
    <?php echo Form::submit('boton_finalizar', 'Finalizar')?>
    <?php echo Form::close(); ?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>    
</div>