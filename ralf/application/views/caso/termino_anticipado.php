<h2><?php echo __('Termino Anticipado de Caso Nº ').$caso->CASO_ID; ?></h2>
<div class="detalle-caso">
    <div class="tabla-general-wrap">
            <h3>&nbsp;&nbsp;&nbsp;Motivo de Termino </h3>
            &nbsp;&nbsp;&nbsp;<select name="select" id="select_tipo_termino">
                <option value="0" selected disabled>Seleccione</option>
                <?php 
                    foreach ($lista_tipo_termino as $r) {
                ?>
                    <option value="<?php echo $r->ID_TIPO_TERMINO ?>" ><?php echo $r->DESCRIPCION ?></option>
                <?php
                }
                ?>
            </select>
        </div>    
    <div align="left">
        <br>
        &nbsp;&nbsp;&nbsp;<?php echo Form::submit('btn_termino', 'Terminar Caso', array('type' => 'button', 'onclick' => "guardarTipoTermino(event)")); ?>
    </div>
    <div align="right">
        <?php $back_page=URL::site("caso/ver_caso/".$caso->CASO_ID, 'http'); ?>        
        <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>
    </div>
</div>
<script>
    function guardarTipoTermino(e){
        var select_tipo = $('#select_tipo_termino').val();
        var url ="<?php echo URL::site("/caso/terminar_caso/{$caso->CASO_ID}", 'http')?>";

        if(select_tipo == 0){
            alert("Favor Ingresar el tipo de termino para este caso.");
            return false;
        }else{
            var url_completa = url+"/"+select_tipo;
            //alert(url_completa);
            window.location.replace(url_completa);
        }
    }
</script>
