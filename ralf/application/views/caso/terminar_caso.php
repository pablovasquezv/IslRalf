<h2><?php echo __('Termino Anticipado de Caso Nº ').$caso->CASO_ID; ?></h2>
<h2><?php echo __('Motivo de Termino :  ').$descripcion ?></h2>
<div class="detalle-caso">   
    <h3>&nbsp;&nbsp;&nbsp;El caso fue terminado correctamente</h3>
    <div align="right">
        <?php $back_page=URL::site("caso/ver_caso/".$caso->CASO_ID, 'http'); ?>
        <?php 
            echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')"));         
        ?>
    </div>
</div>  