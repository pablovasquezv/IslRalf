<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/themes/base/jquery.ui.all.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.6.2.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.core.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.datepicker.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/fechas_conf.js';?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        var w = document.defaultView || document.parentWindow;
        var d = w.parent.document;
        var l = d.getElementById('cscs');
        <?php if(isset($_POST)):?>
        $(l).find('tbody').remove();
        $(l).append(get_medidas());
        <?php endif;?>


        function get_medidas(){
            $t = $("<tbody></tbody>");
            $.get('<?php echo URL::site("ralfPrescripcion/obtener_medidas_correctivas/$xml_id");?>', function(data) {
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

        function get_causasRalf145(){
            $t = $("<div class='detCausas'><tr><th>Cod. Causa</th><th>Glosa Causa</th></tr></div>");
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


        $("input#txt_cod_causa").live("keyup", function( event ){
            if(this.value.length == 4 || this.value.length == 5) {
                buscaGlosaCausaPorCodigo(this.value);
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

    });
</script>

<?php $si_no=array(1=>'Si',2=>'No'); ?>
<?php $tipos_medida=array(1=>'Medida de control Ingeneril',2=>'Medida de control Administrativo', 3=>'Medida de control Protección Personal'); ?>

<div class="popup-container">
    <h2><?php echo __('Agregar Causa'); ?></h2>
    <?php if($mensaje_error):?>          
        <div class="alert alert-success">
            <b><?php echo $mensaje_error; ?></b>
        </div>
    <?php endif; ?>
    <?php echo Form::open()?>
    <div class='form_section_container'>
        <div class='form_section accident'>
            <div class="row">
                <div class="field codigo_causa">
                    <label for=""><?php echo __('Codigo Causa')?></label>        
                    <div class="editable_field codigo_causa">
                        <?php echo Form::input('codigo_causa', $default["codigo_causa"], array('id' => 'txt_cod_causa')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'codigo_causa'); ?></div>
                    </div>
                </div>

                <div class="field glosa">
                    <label for=""><?php echo __('Glosa')?></label>              
                    <div class="editable_field descripcion">
                        <?php echo Form::textarea('glosa_causa', $default['glosa_causa'], array('id' => 'txt_glosa_causa')); ?>
                        <div class="error"><?php echo Arr::get($errors, 'glosa_causa'); ?></div>
                    </div>
                </div>

                <!--<div class="field medida_inmediata">
                    <label for=""><?php //echo __('Medida Inmediata')?></label>            
                    <div class="editable_field medida_inmediata">
                        <?php //echo Form::select("medida_inmediata", $si_no,$default["medida_inmediata"]); ?>
                        <div class="error"><?php //echo Arr::get($errors, 'medida_inmediata'); ?></div>
                    </div>
                </div>

                <div class="field plazo_cumplimiento">
                    <label for=""><?php //echo __('Plazo')?></label>            
                    <div class="editable_field plazo_cumplimiento">
                        <?php //echo Form::input('plazo_cumplimiento', $default['plazo_cumplimiento'],array('class'=>'datepicker')); ?>
                        <div class="error"><?php //echo Arr::get($errors, 'plazo_cumplimiento'); ?></div>
                    </div>
                </div>-->
                
                <div class="clear-both">
                    <br />
                    <?php echo Form::submit('boton_guardar_causa', 'Guardar')?>
                </div>
            </div>
        </div>
    </div>
    <?php echo Form::close()?>
</div>