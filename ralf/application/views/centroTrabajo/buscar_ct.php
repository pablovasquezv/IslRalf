<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/themes/base/jquery.ui.all.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/style_sisesat.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.7.min.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.core.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.datepicker.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/fechas_conf.js';?>"></script>
<!--script type="text/javascript" src="<?php //echo Kohana::$config->load('sitio.url_base').'media/js/centro_trabajo.js';?>"></script-->
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/validacion/jquery.form-validator-2.3.79.min.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/validacion/lang/es.js';?>"></script>

<div class="popup-container">
    <h2><?php echo __('Centros de Trabajo'); ?></h2>
    <?php if($mensaje_error):?>
        <div class="alert alert-success">
            <b><?php echo $mensaje_error; ?></b>
        </div>
    <?php endif; ?>

    <?php echo Form::open(NULL, array('id'=>'nuevo_ct_form'))?>
    <div class='form_section_container'>
        <div class='form_section accident'>
            <div class="row">
                <div class="field tipo">
                    <label for=""><?php echo __('Rut Empleador')?></label>
                    <div class="editable_field tipo">
                        <?php echo Form::label("rut_empleador",$rut_empleador, array('id' => 'rut_empleador')); ?>
                        <?php echo Form::input("urlRestObtenerListaCompletaCT", $urlRestObtenerListaCompletaCT, array('id' => 'urlRestObtenerListaCompletaCT','type'=>'hidden')); ?>

                        <?php echo Form::input("urlCrearCentroTrabajo", $urlCrearCentroTrabajo, array('id' => 'urlCrearCentroTrabajo','type'=>'hidden')); ?>
                        <?php echo Form::input("urlObtenerListasCTPorEmpresaRutCT", $urlObtenerListasCTPorEmpresaRutCT, array('id' => 'urlObtenerListasCTPorEmpresaRutCT','type'=>'hidden')); ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="conResultados" style="display: none;"> <br><br>
            <div style="height:200px; overflow:auto;">
                <table class="info-table" >
                    <thead>
                        <tr class="label-row">
                            <th>Elegir</th>
                            <th>Rut Empleador</th>
                            <th>Nombre CT</th>
                            <th>Estado CT</th>
                            <th>Calle</th>
                            <th>Numero</th>
                            <th>Latitud</th>
                            <th>Longitud</th>
                        </tr>
                    </thead>
                    <tbody class="ct"></tbody>
                </table>
            </div>

            <div class="clear-both">
                <br />
                <?php echo Form::submit('boton_seleccionar_ct', 'Seleccionar CT', array('id' => 'btnSeleccionar'))?>
                <?php echo Form::button('boton_nuevo_ct', 'Crear Nuevo CT', array('id' => 'btnNuevoCT'))?>
            </div>
        </div>

        <?php
            $propiedades    = array(1=>'Privada',2=>'Publica');
            $tipos_empresa  = array(1=>'Principal',2=>'Contratista',3=>'Subcontratista',4=>'De servicios transitorios');
            $tipos_calle    = array(1=>'Avenida',2=>'Calle',3=>'Pasaje');
            $si_no          = array(1=>'Si',2=>'No');
        ?>

        <div id="sinResultados" style="display: none;">
            <div class='form_section accident'>
                <div class="row">
                    <div class="field tipo">
                        <div class="editable_field tipo">
                            <?php echo Form::input("caso_id",$caso_id, array('id' => 'caso_id','type'=>'hidden')); ?>
                            <?php echo Form::input("xml_id",$xml_id, array('id' => 'xml_id','type'=>'hidden')); ?>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
            <div id="mensaje_error_ct" class="mensaje_error_ct" style="display: none;">
                <p>No se ha encontrado Centro de Trabajo para el empleador. Por favor complete los datos y presione el botón <strong>Guardar Centro Trabajo</strong></p>
            </div>
            <br>
            <?php if($correo_electronico_informante_oa == "") { ?>
                <table class="info-table">
                    <thead>
                        <tr class="label-row">
                            <th>Correo Electrónico Informante OA </th>
                        </tr>
                    </thead>
                    <tr class="data-row">
                        <td>
                            <div class="editable_field nombre_ct">
                                <?php echo Form::input('correo_electronico_informante_oa','', array('id' => 'correo_electronico_informante_oa','width'=>'500','data-validation'=>'required, email')); ?>
                                <label class="error">Obligatorio</label>
                                <div class="error"><?php //echo Arr::get($errors, 'nuevo_nombre_ct'); ?></div>
                            </div>
                        </td>
                    </tr>
                </table>
            <?php } ?>
            <table class="info-table">
                <thead>
                    <tr class="label-row">
                        <th>Nombre CT</th>
                        <th>Tipo Empresa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <div class="editable_field nombre_ct">
                                <?php echo Form::input('nuevo_nombre_ct','', array('id' => 'nuevo_nombre_ct','width'=>'100','data-validation'=>'required')); ?>
                                <div class="error"><?php //echo Arr::get($errors, 'nuevo_nombre_ct'); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="editable_field tipo_empresa_ct">
                                <?php echo Form::select("nuevo_tipo_empresa_ct",$tipos_empresa, $default["nuevo_tipo_empresa_ct"],array('id'=>'nuevo_tipo_empresa_ct','data-validation'=>'required')); ?>
                                <div class="error"><?php echo Arr::get($errors, 'nuevo_tipo_empresa_ct'); ?></div>
                            </div>
                        </td>

                    </tr>
                </tbody>
            </table>

            <table class="info-table">
                <thead>
                    <tr class="label-row">
                        <th>Tipo Calle</th>
                        <th>Calle</th>
                        <th>Numero</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <div>
                                <?php echo Form::select("nuevo_tipo_calle_ct",$tipos_calle, $default["nuevo_tipo_calle_ct"],array('id'=>'nuevo_tipo_calle_ct','data-validation'=>'required')); ?>
                                <div class="error"><?php echo Arr::get($errors, 'nuevo_tipo_calle_ct'); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="editable_field nombre_ct">
                                <?php echo Form::input("nuevo_calle_ct",'', array('id' => 'nuevo_calle_ct','data-validation'=>'required')); ?>
                                <div class="error"><?php echo Arr::get($errors, 'nuevo_calle_ct'); ?></div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <?php echo Form::input("nuevo_numero_ct",'', array('id' => 'nuevo_numero_ct','data-validation'=>'required')); ?>
                                <div class="error"><?php echo Arr::get($errors, 'nuevo_numero_ct'); ?></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table>
                <thead>
                    <tr class="label-row">
                        <th>Resto Direccion</th>
                        <th>Localidad</th>
                        <th>Comuna</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <div class="editable_field resto">
                                <?php echo Form::input("nuevo_resto_direccion_ct",'', array('id' => 'nuevo_resto_direccion_ct', 'width'=>'200')); ?>
                                <div class="error"><?php echo Arr::get($errors, 'nuevo_resto_direccion_ct'); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="editable_field resto">
                                <?php echo Form::input("nuevo_localidad_ct",'', array('id' => 'nuevo_localidad_ct', 'width'=>'200')); ?>
                                <div class="error"><?php echo Arr::get($errors, 'nuevo_localidad_ct'); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="editable_field tipo_empresa_ct">
                                <?php echo Form::select("nuevo_comuna_ct",$comunas, $default["nuevo_comuna_ct"],array('id'=>'nuevo_comuna_ct','data-validation'=>'required', 'style'=>'width: 200px !important;')); ?>
                                <div class="error"><?php echo Arr::get($errors, 'nuevo_comuna_ct'); ?></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="info-table">
                <thead>
                    <tr class="label-row">
                        <th>Latitud</th>
                        <th>Longitud</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <?php echo Form::input("nuevo_latitud_ct",'', array('id' => 'nuevo_latitud_ct','data-validation'=>'required,custom','data-validation-regexp'=>'^(-?[0-9]{1,2}\.[0-9]{7})$','placeholder'=>'Ej: -20.2423678')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'latitud'); ?></div>
                        </td>
                        <td>
                            <?php echo Form::input("nuevo_longitud_ct",'', array('id' => 'nuevo_longitud_ct','data-validation'=>'required,custom','data-validation-regexp'=>'^(-?[0-9]{1,3}\.[0-9]{7})$','placeholder'=>'Ej: -70.1331602')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'nuevo_longitud_ct'); ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table>
                <thead>
                    <tr class="label-row">
                        <th>Descripción Actividad Trabajadores</th>
                        <th>Num. Trabajadores Hombre</th>
                        <th>Num. Trabajadores Mujer</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <div class="editable_field nombre_ct">
                                <?php echo Form::input("nuevo_desc_act_trab",'', array('id' => 'nuevo_desc_act_trab', 'data-validation'=>'required')); ?>
                                <div class="error"><?php echo Arr::get($errors, 'nuevo_desc_act_trab'); ?></div>
                            </div>
                        </td>

                        <td>
                            <?php echo Form::input("nuevo_num_trab_hombre",'', array('id' => 'nuevo_num_trab_hombre','data-validation'=>'required, number')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'nuevo_num_trab_hombre'); ?></div>
                        </td>

                        <td>
                            <?php echo Form::input("nuevo_num_trab_mujer",'', array('id' => 'nuevo_num_trab_mujer','data-validation'=>'required, num_total_trab')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'nuevo_num_trab_mujer'); ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="info-table">
                <thead>
                    <tr class="label-row">
                        <th>¿Tiene Experto?</th>
                        <th>Horas Dedica CT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <?php echo Form::select("experto_prevencion_riesgos",$si_no, $default['experto_prevencion_riesgos'], array('id' => 'experto_prevencion_riesgos','data-validation'=>'required')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'experto_prevencion_riesgos'); ?></div>
                        </td>
                        <td>
                            <?php echo Form::input("horas_semana_dedica_ct",'', array('id' => 'horas_semana_dedica_ct','disabled'=>'disabled','data-validation'=>'required,number')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'horas_semana_dedica_ct'); ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="info-table">
                <thead>
                    <tr class="label-row">
                        <th>Fecha Inicio CT</th>
                        <th>Tiene Fecha Termino</th>
                        <th>Fecha Termino CT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <?php echo Form::input('fecha_inicio_nuevo_ct', $default['fecha_inicio_nuevo_ct'],array('class'=>'datepicker','id'=>'fecha_inicio_nuevo_ct','data-validation'=>'required')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'fecha_inicio_nuevo_ct'); ?></div>
                        </td>
                        <td>
                            <?php echo Form::select('tiene_fecha_termino_nuevo_ct',$si_no, $default['tiene_fecha_termino_nuevo_ct'],array('class'=>'datepicker','id'=>'tiene_fecha_termino_nuevo_ct','data-validation'=>'required')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'tiene_fecha_termino_nuevo_ct'); ?></div>
                        </td>
                        <td>
                            <?php echo Form::input('fecha_termino_nuevo_ct', $default['fecha_termino_nuevo_ct'],array('class'=>'datepicker','id'=>'fecha_termino_nuevo_ct','disabled'=>'disabled','data-validation'=>'required,ini_mayor_fin')); ?>
                            <div class="error"><?php echo Arr::get($errors, 'fecha_termino_nuevo_ct'); ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="clear-both">
                <br />
                <?php echo Form::input("cargaCT",true, array('id' => 'cargaCT','type'=>'hidden')); ?>
                <?php echo Form::submit('boton_ingresar_ct', 'Guardar Centro Trabajo', array('id' => 'btnIngresar'))?>
            </div>
        </div>
    </div>
    <?php echo Form::close()?>
</div>

<script type="text/javascript">
    var urlObtenerListasCTPorEmpresaRutCT = $("#urlObtenerListasCTPorEmpresaRutCT").val();
    var respuestaListaCT;
    var $w = document.defaultView || document.parentWindow;
    var $d = $w.parent.document;
    var estadosCentros = {true:"Activo", false:"Caduco"};
    
    var rutObj = $("#rut_empleador").text().split("-");
    var rut = rutObj[0];
    var dv = rutObj[1];
    
    //documentReady(1);
    
    $(document).ready(function() {
        cargaCT(rut, dv);
        $("#tiene_fecha_termino_nuevo_ct").change(function(){
            if($(this).val() == 1) {
                $("#fecha_termino_nuevo_ct").removeAttr("disabled");
            } else if($(this).val() != 1) {
                $("#fecha_termino_nuevo_ct").attr("disabled","disabled");
                $("#fecha_termino_nuevo_ct").val("");
                $("#fecha_termino_nuevo_ct").removeAttr('style');
            }
        });

        $("#experto_prevencion_riesgos").change(function(){
            if($(this).val() == 1) {
                $("#horas_semana_dedica_ct").removeAttr("disabled");
            } else if($(this).val() != 1) {
                $("#horas_semana_dedica_ct").attr("disabled","disabled");
                $("#horas_semana_dedica_ct").val("");
                $("#horas_semana_dedica_ct").removeAttr('style');
                $("span.form-error").text("");
            }
        });

        $("#btnNuevoCT").click(function(){
            $("#conResultados").hide();
            $("#mensaje_error_ct").hide();
            $("#sinResultados").show();
            return false;
        });
        
        $(document).on("click", ":checkbox", function () {
            if($(this).attr('checked')){
                $(':checkbox').not(this).attr('checked', false).attr('disabled','disabled');
            }else{
                $(':checkbox').not(this).removeAttr('disabled');
            }
        });

    });
    
    $('#btnSeleccionar').click(function(e){
        var idCheckbox;

        idCheckbox = $('input:checkbox:checked').prop('id');
        if (typeof idCheckbox != "undefined") {
            var indiceCtSeleccionado = idCheckbox.substring(idCheckbox.indexOf('_')+1 , idCheckbox.length);
            $centroTrabajo = respuestaListaCT.centros[indiceCtSeleccionado];

            llenarFormularioCT($w, $d, $centroTrabajo);
            //e.preventDefault();
            parent.$.fancybox.close();
            return true;
        } else {
            alert('Debes seleccionar un Centro de Trabajo');
            return false;
        }

    });
        
    $("#btnIngresar").click(function(){

        $.formUtils.addValidator({
            name : 'num_total_trab',
            validatorFunction : function(value, $el, config, language, $form) {
                var total = parseInt($("#nuevo_num_trab_hombre").val()) + parseInt($("#nuevo_num_trab_mujer").val());
                return total > 0;
            },
            errorMessage : 'TOTAL DE TRABAJADORES DEBE SER MAYOR A 0',
            errorMessageKey: 'numTotalTrabCero'
        });

        $.formUtils.addValidator({
            name : 'ini_mayor_fin',
            validatorFunction : function(value, $el, config, language, $form) {
                return fechaToTimestamp($("#fecha_inicio_nuevo_ct").val()) <= fechaToTimestamp($("#fecha_termino_nuevo_ct").val());
            },
            errorMessage : 'FECHA INICIO MAYOR A FECHA TERMINO',
            errorMessageKey: 'fechaIniMayorFin'
        });

        $.validate({
            lang: 'es',
            onSuccess: function(form) {
                $.ajax({
                    url  : '../../../centroTrabajo/obtener_datos_ingreso_ct/',
                    type : 'post',
                    dataType : "json",
                    async: false,
                    data : {'rut_empleador': $("#rut_empleador").text() , 'caso_id': $("#caso_id").val(), 'xml_id': $("#xml_id").val() },
                    success : function(respuesta) {
                        console.log("BOTON INGRESO DE NUEVO CT");

                        var msg = ingresatCT(respuesta);
                        console.log("msg: " + msg);
                        if(msg.length > 0){
                            $("#mensaje_error_ct").text(msg);
                            $("#mensaje_error_ct").show();

                            if(msg == 'OPERACION EJECUTADA CON EXITO') {
                                //cargaCT = true;
                                return true;
                            } else {
                                console.log(mensaje);
                                return false;
                            }
                        }
                        return false;
                    }
                });
            }
        });
        //return false;
    });
    
    function cargaCT(rut, dv) {
        $.ajax({
            url  : urlObtenerListasCTPorEmpresaRutCT +"/rut/"+rut+"/dv/"+dv+"/",
            type : "GET",
            dataType : "json",
            success : function(respuesta) {
                console.log("Busqueda de CT Completa");
                //console.log(respuesta);
                if(respuesta.centros.length > 0) {
                    respuestaListaCT = respuesta;
                    $tbody = $("#conResultados").find('tbody');
                    $.each(respuesta.centros,function(indice,valor) {
                        $tr = $('<tr></tr>').addClass('data-row');
                        $tr.append($("<td></td>").html('<input id="ct_'+indice+'" type="checkbox" name="ct_'+indice+'" value="" class="chkbox" />'));
                        $tr.append($("<td></td>").html(valor.rutEmpPrincipal));
                        $tr.append($("<td></td>").html(valor.nombreCentroTrabajo));
                        $tr.append($("<td></td>").html(estadosCentros[valor.centroActivo]));
                        $tr.append($("<td></td>").html(valor.nombreCalle));
                        $tr.append($("<td></td>").html(valor.numero));
                        $tr.append($("<td></td>").html(valor.latitud));
                        $tr.append($("<td></td>").html(valor.longitud));
                        $tbody.append($tr);
                    });

                    $("#conResultados").show();

                    //documentReady();

                } else {
                    $("#sinResultados").show();
                    $("#mensaje_error_ct").show();

                    //documentReady();
                }
            }
        });
    }
    
    function ingresatCT(parametros) {
        var msg_error = "";
        
        $.ajax({
            url  : '../../../centroTrabajo/ws_crear_ct/',
            type: "POST",
            dataType: "json",
            data: {'json_ct': dataNuevoCT_JSON(parametros)},
            async: false,
            success : function(respuesta) {
                console.log("INGRESANDO NUEVO CT");
                if(respuesta.operacionResponse != null) {
                    if(respuesta.operacionResponse.transaccionSUSESO.codigoResponseSuseso == '-40') {
                        msg_error = respuesta.descripcion;
                    } else {
                        msg_error = $(respuesta.operacionResponse.transaccionSUSESO.responseSuseso).find('error_message').text();
                    }
                } else {
                    msg_error = respuesta.descripcion;
                }

                if(msg_error.length > 0){
                    $("#mensaje_error_ct").text(msg_error);
                    $("#mensaje_error_ct").show();
                }
            }
        });
        //console.log("msg_error: " + msg_error);
        return msg_error;
    }
    
    function dataNuevoCT_JSON(parametrosNuevoCt) {
        var correo_electronico_informante_oa;

        if(parametrosNuevoCt.correoProfesionalOa == "") {
            correo_electronico_informante_oa = $("#correo_electronico_informante_oa").val()
        } else {
            correo_electronico_informante_oa = parametrosNuevoCt.correoProfesionalOa;
        }

        var resp =  JSON.stringify({
            "idSistema": parametrosNuevoCt.idSistema,
            "documento":{

                "zid":{
                    "organismo": 21,
                    "idtipoDocumento":51,
                    "folio": parametrosNuevoCt.folio,
                    "rutProfesionalOa":parametrosNuevoCt.rutProfesionalOa,
                    "apellidopatProfesionalOa": parametrosNuevoCt.apellidopatProfesionalOa,
                    "apellidomatProfesionalOa": parametrosNuevoCt.apellidomatProfesionalOa,
                    "nombresProfesionalOa": parametrosNuevoCt.nombresProfesionalOa,
                    "correoProfesionalOa": correo_electronico_informante_oa
                },

                "zem":{
                    "rutEmpleador": parametrosNuevoCt.rutEmpleador,
                    "razonSocial": parametrosNuevoCt.razonSocial,
                    "tipoCalle": parametrosNuevoCt.tipoCalle,
                    "nombreCalle": parametrosNuevoCt.nombreCalle,
                    "numero": parametrosNuevoCt.numero,
                    "restoDireccion": parametrosNuevoCt.restoDireccion,
                    "localidad": parametrosNuevoCt.localidad,
                    "comuna": parametrosNuevoCt.comuna,
                    "ciiuEmpleadorEvaluado": parametrosNuevoCt.ciiuEmpleadorEvaluado,
                    "ciiuGiroEmpleadorEvaluado": parametrosNuevoCt.ciiuGiroEmpleadorEvaluado,
                    "caracterOrganizacion": parametrosNuevoCt.caracterOrganizacion,
                    "nTrabajadoresPropios": parametrosNuevoCt.nTrabajadoresPropios,
                    "nTrabajadoresHombre": parametrosNuevoCt.nTrabajadoresHombre,
                    "nTrabajadoresMujer": parametrosNuevoCt.nTrabajadoresMujer,
                    "reglamHigSeg": parametrosNuevoCt.reglamHigSeg,
                    "reglamHigSegAgenRies": parametrosNuevoCt.reglamHigSegAgenRies,
                    "reglamOrdSeg": parametrosNuevoCt.reglamOrdSeg,
                    "reglamOrdSegAgenRies":parametrosNuevoCt.reglamOrdSegAgenRies,
                    "deptoPrevRiesgos":parametrosNuevoCt.deptoPrevRiesgos
                },

                "zct":{
                    "estadoCentroTrabajo":1,
                    "rutEmpleadorPrincipal":parametrosNuevoCt.rutEmpleadorPrincipal,
                    "nombreEmpleadorPrincipal":parametrosNuevoCt.nombreEmpleadorPrincipal,
                    "correlativoProyectoContrato":1,
                    "nombreCentroTrabajo":replaceAll(($("#nuevo_nombre_ct").val()), '&', '&amp;'),
                    "tipoEmpresa":$("#nuevo_tipo_empresa_ct").val(),
                    "geoLatitud":$("#nuevo_latitud_ct").val(),
                    "geoLongitud":$("#nuevo_longitud_ct").val(),
                    "tipoCalleCt":$("#nuevo_tipo_calle_ct").val(),
                    "nombreCalleCt":$("#nuevo_calle_ct").val(),
                    "numeroCt":$("#nuevo_numero_ct").val(),
                    "restoDireccionCt":$("#nuevo_resto_direccion_ct").val(),
                    "localidadCt":$("#nuevo_localidad_ct").val(),
                    "comunaCt":$("#nuevo_comuna_ct").val(),
                    "descripcionActividadTrabajadoresCt":$("#nuevo_desc_act_trab").val(),
                    "nTrabajadoresPropiosCt": parseInt($("#nuevo_num_trab_hombre").val()) + parseInt($("#nuevo_num_trab_mujer").val()),
                    "nTrabajadoresHombreCt": parseInt($("#nuevo_num_trab_hombre").val()),
                    "nTrabajadoresMujerCt": parseInt($("#nuevo_num_trab_mujer").val()),
                    "comParConstituido": parametrosNuevoCt.comParConstituido,
                    "expertoPrevencionRiesgos": $("#experto_prevencion_riesgos").val(),
                    "horasSemanaDedicaCt": $("#horas_semana_dedica_ct").val(),
                    "fechaInicioCt": fechaToTimestamp($("#fecha_inicio_nuevo_ct").val()) ,
                    "tieneFechTerm": $("#tiene_fecha_termino_nuevo_ct").val(),
                    "fechaTerminoCt": fechaToTimestamp($("#fecha_termino_nuevo_ct").val())
                },

                "zpp":{
                    "codigoCausaAccidente": 1201,
                    "presenciaPeligro": parametrosNuevoCt.presenciaPeligro,
                    "fechaDeteccionPeligro": fechaToTimestamp(parametrosNuevoCt.fechaDeteccionPeligro),
                    "origen": parametrosNuevoCt.origen,
                    "cun": parametrosNuevoCt.cun
                }
            }
        });
        //console.log(resp);
        return resp;
    }
    
    // Funcion para trasformar un string de fecha a timestamp
    function fechaToTimestamp(fecha){
        // 0 -1 - 2
        //var fecha="2012-02-20";
        //var fecha="26-02-2012";

        if(fecha != null){
            fecha=fecha.split("-");
            //var nuevaFecha=fecha[2]+"/"+fecha[1]+"/"+fecha[0];
            var nuevaFecha=fecha[1]+"/"+fecha[2]+"/"+fecha[0];
            var fechaFinal = new Date(nuevaFecha).getTime();
        }else{
            fechaFinal = "";
        }

        return fechaFinal;
    }

    // Funcion para trasformar un timestamp a string de fecha
    function timestampToFecha(timestamp){
        var fechaDate = new Date(timestamp);
        var año = fechaDate.getFullYear();

        var mes = fechaDate.getMonth()+1;
        if(mes < 10){
            mes = '0'+mes;
        }

        var dia = fechaDate.getDate();
        if(dia < 10){
            dia = '0'+dia;
        }

        return año + '-' + mes + '-' + dia;
    }
    
    // Función para llenar los campos del formulario que corresponden al Ralf Prescripción de la Zona CT
    function llenarFormularioCT($window, $document, $ct){

        tiposEmpresa    = {1:"Principal",2:"Contratista",3:"Subcontratista",4:"De servicios transitorios"};
        estadosCT       = {true:"Activo",false:"Caduco"};
        tiposCalle      = {1:"Avenida",2:"Calle",3:"Pasaje"};
        si_no           = {1:"Si",2:"No"};
        si_no_bool      = {true:"Si",false:"No"};

        var estado_centro_trabajo = $document.getElementById('estado_centro_trabajo');
        var rut_empleador_principal = $document.getElementById('rut_empleador_principal');
        var nombre_empleador_principal = $document.getElementById('nombre_empleador_principal');
        var correlativo_proyecto_contrato = $document.getElementById('correlativo_proyecto_contrato');
        var tipo_empresa = $document.getElementById('tipo_empresa');
        var nombre_centro_trabajo = $document.getElementById('nombre_centro_trabajo');
        var cuv = $document.getElementById('cuv');

        var geo_latitud = $document.getElementById('geo_latitud');
        var geo_longitud = $document.getElementById('geo_longitud');

        var tipo_calle_ct = $document.getElementById('tipo_calle_ct');
        var nombre_calle_ct = $document.getElementById('nombre_calle_ct');
        var numero_ct = $document.getElementById('numero_ct');
        var comuna_ct = $document.getElementById('comuna_ct');

        var resto_direccion_ct = $document.getElementById('resto_direccion_ct');
        var localidad_ct = $document.getElementById('localidad_ct');

        var descripcion_actividad_trabajadores_ct = $document.getElementById('descripcion_actividad_trabajadores_ct');
        var n_trabajadores_propios_ct = $document.getElementById('n_trabajadores_propios_ct');
        var n_trabajadores_hombre_ct = $document.getElementById('n_trabajadores_hombre_ct');
        var n_trabajadores_mujer_ct = $document.getElementById('n_trabajadores_mujer_ct');

        var com_par_constituido = $document.getElementById('com_par_constituido');
        var experto_prevencion_riesgos = $document.getElementById('experto_prevencion_riesgos');
        var horas_semana_dedica_ct = $document.getElementById('horas_semana_dedica_ct');
        var fecha_inicio_ct = $document.getElementById('fecha_inicio_ct');
        var tiene_fech_term = $document.getElementById('tiene_fech_term');
        var fecha_termino_ct = $document.getElementById('fecha_termino_ct');

        $(estado_centro_trabajo).val('Activo');
        $(rut_empleador_principal).val($ct.rutEmpPrincipal);
        $(nombre_empleador_principal).val($ct.nombreEmpPrincipal);
        $(correlativo_proyecto_contrato).val($ct.correlativoProyecto);
        $(tipo_empresa).val(tiposEmpresa[$ct.idTipoEmpresa.codigo]);
        $(nombre_centro_trabajo).val($ct.nombreCentroTrabajo);
        $(cuv).val($ct.cuv);
        $(geo_latitud).val($ct.latitud);
        $(geo_longitud).val($ct.longitud);

        $(tipo_calle_ct).val(tiposCalle[$ct.idTipoCalle.codigo]);
        $(nombre_calle_ct).val($ct.nombreCalle);
        $(numero_ct).val($ct.numero);

        $(resto_direccion_ct).val($ct.restoDireccion);
        $(localidad_ct).val($ct.localidad);
        $(comuna_ct).val($ct.idComuna.nombre);

        $(descripcion_actividad_trabajadores_ct).val($ct.descripcionActividad);
        $(n_trabajadores_propios_ct).val(parseInt($ct.numTrabajadoresHombres) + parseInt($ct.numTrabajadoresMujeres));
        $(n_trabajadores_hombre_ct).val($ct.numTrabajadoresHombres);
        $(n_trabajadores_mujer_ct).val($ct.numTrabajadoresMujeres);

        $(com_par_constituido).val(si_no_bool[$ct.comiteParitarioConstituido]);
        $(experto_prevencion_riesgos).val(si_no_bool[$ct.expertoPrevencion]);
        $(horas_semana_dedica_ct).val($ct.hHExpertoPrevencion);

        $(fecha_inicio_ct).val(timestampToFecha($ct.fechaInicio));
        $(tiene_fech_term).val(si_no_bool[$ct.tieneFechaTerminoConocida]);

        if($ct.fechaTermino != null) {
            $(fecha_termino_ct).val(timestampToFecha($ct.fechaTermino));
        } else {
            $(fecha_termino_ct).val($ct.fechaTermino);
        }
    }
    
    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }
    
    function escapeRegExp(str) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }
</script>