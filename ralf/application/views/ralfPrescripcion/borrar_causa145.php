<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.6.2.js';?>"></script>
<script type="text/javascript">
    $(document).ready(function() {

        var w = document.defaultView || document.parentWindow;
        var d = w.parent.document;
        var l = d.getElementById('cscs');
        var c = d.getElementsByClassName('causas145');
        <?php if(isset($_POST)):?>
        $(l).find('tbody').remove();
        $(l).append(get_medidas());
        //$(c).remove();
        $(c).append(get_causasRalf145());
        <?php endif;?>


        function get_medidas(){
            var si_no = {1:"Si", 2:"No"};
            var tipos_medida = {1:"Medida de control Ingeneril", 2:"Medida de control Administrativo", 3:"Medida de control Protección Personal"};

            $t = $("<tbody></tbody>");
            $.get('<?php echo URL::site("ralfPrescripcion/obtener_medidas_correctivas/$xml_id");?>', function(data) {
                $.each(JSON.parse(data),function(k,d) {
                    $tr = $('<tr></tr>').addClass('data-row');
                    $.each(d,function(i,j) {                        
                        $value = j;
                        if(i == 1){
                            $tr.append($("<td></td>").html(tipos_medida[$value]));
                        }else if(i == 3){
                            $tr.append($("<td></td>").html(si_no[$value]));
                        }
                        else{
                            $tr.append($("<td></td>").html($value));    
                        }                    
                        //$tr.append($("<td></td>").html($value));
                   });
                $t.append($tr);
                });
            });
            return $t;
        }

        function get_causasRalf145(){
            $t = $("<tr><th>Cod. Causa</th><th>Glosa Causa</th></tr>");
            $.get('<?php echo URL::site("ralfPrescripcion/obtener_causas_ralf145/$xml_id");?>', function(data) {
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
    <h2><?php echo __('Causas'); ?></h2>
    <?php if($borrado):?>          
        <div class="alert alert-success">
            <b>Causa Borrada correctamente</b>
        </div>
    <?php else:?>
        <div class="alert alert-warning">
            <b>¿Seguro quiere borrar la causa <?php echo $medida->id?>?</b>
        </div>
        <?php echo Form::open(); echo Form::submit('boton_aceptar', 'Aceptar'); echo Form::close()?>
    <?php endif; ?>
</div>

