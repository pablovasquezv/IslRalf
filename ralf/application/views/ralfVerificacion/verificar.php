<?php 
$xml=$data['xml'];
$comunas=Model_St_Comuna::obtenerSinFiltro();
$ciiu=(string)$xml->ZONA_B->empleador->ciiu_empleador;
$ciiu1_empleador=Tipos::codigo($ciiu,'STCIIU');
$propiedad=array(1=>'Privada',2=>'Publica');
$tipo_empresa= array(1=>'Principal',2=>'Contratista',3=>'Subcontratista',4=>'De servicios transitorios');

$nombre_trabajador= $xml->ZONA_C->empleado->trabajador->nombres." ".$xml->ZONA_C->empleado->trabajador->apellido_paterno." ".$xml->ZONA_C->empleado->trabajador->apellido_materno;
$direccion_trabajador=$xml->ZONA_C->empleado->direccion_trabajador->nombre_calle." ".$xml->ZONA_C->empleado->direccion_trabajador->numero." ".$xml->ZONA_C->empleado->direccion_trabajador->resto_direccion." ".$xml->ZONA_C->empleado->direccion_trabajador->localidad;
$direccion_empleador=$xml->ZONA_B->empleador->direccion_empleador->nombre_calle." ".$xml->ZONA_B->empleador->direccion_empleador->numero." ".$xml->ZONA_B->empleador->direccion_empleador->resto_direccion." ".$xml->ZONA_B->empleador->direccion_empleador->localidad;

$sexos=$data['sexo'];
$sexo_trabajador=$sexos[(int)$xml->ZONA_C->empleado->trabajador->sexo];


$nacionalidades=Model_St_Nacionalidad::obtener();
$contrato=$data['contrato'];

$clas_trabajador=$data['clas_trabajador'];
$cod_pais_trab=(string)$xml->ZONA_C->empleado->trabajador->pais_nacionalidad;
$cod_pais=Tipos::codigo($cod_pais_trab,'STPais_nacionalidad');
                   
$direccion_accidente=$xml->ZONA_P->accidente_fatal->direccion_accidente->nombre_calle." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->numero." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->resto_direccion." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->localidad;


$criterio_gravedad_ralf=array(1=>'Muerte del trabajador',2=>'Desaparecido producto del accidente',3=>'Maniobras de reanimación',4=>'Maniobras de rescate',5=>'Caída de altura de más de 2 m.',6=>'Amputación traumática',7=>'Número de trabajadores afecta el desarrollo normal de la faena',8=>'Accidente en condición hiperbárica');
$criterio_gravedad=$criterio_gravedad_ralf[(int)$xml->ZONA_P->accidente_fatal->gravedad->criterio_gravedad];

$STLugarDefuncion=array(1=>'Mismo lugar del Accidente',2=>'Traslado al Centro Asistencial',3=>'Centro Asistencial',4=>'Otro (indicar lugar)'); 

if(isset($xml->ZONA_C->empleado->clasificacion_trabajador) && !empty($xml->ZONA_C->empleado->clasificacion_trabajador)) {
    $cla_tra=(string)$xml->ZONA_C->empleado->clasificacion_trabajador;
    $codigo_clasificacion_trabajador=$clas_trabajador[$cla_tra];
} else {
    $codigo_clasificacion_trabajador='n/a';
}

$informante_oa=$xml->ZONA_P->accidente_fatal->informante_oa->nombres." ".$xml->ZONA_P->accidente_fatal->informante_oa->apellido_paterno." ".$xml->ZONA_P->accidente_fatal->informante_oa->apellido_materno;

$si_no=array(0 => '',1=>'Si',2=>'No');
$si_no_nc=array(1=>'Si',2=>'No',3=>'No Corresponde');

$tìpo_calle=array(""=>"Seleccione",1 => 'Avenida',2 => 'Calle',3 => 'Pasaje');

$caso_id = $xml->ZONA_A->documento->codigo_caso;
$xml_id = $xml->ZONA_A->documento->folio;
$rut_empleador = $xml->ZONA_B->empleador->rut_empleador;

$estados_ct=array(0 => '',1 => 'Activo',2 => 'Caduco');
$tipos_calle=array(0 => '', 1 => 'Avenida',2 => 'Calle',3 => 'Pasaje');
$tipos_empresa= array(0=> '',1 => 'Principal',2 => 'Contratista',3 => 'Subcontratista',4 => 'De servicios transitorios');
$comunas_ct=Model_St_Comuna::obtenerSinFiltroLlaveGlosa();
//$si_no=array('Si'=>1,'No'=>2);
$cod_comuna_ct = "";

?>
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
<div id="container">
    <div id="header"><h1>(RALF) Ralf Verificación</h1></div>
    <h2>A. Identificación del Documento</h2>
    <table class="info-table">
        <tbody><tr class="label-row">
                <th>CUN</th>
                <th>Folio</th>
                <th>Código del Caso</th>
                <th>Fecha de Emisión</th>                
            </tr>
            <tr class="data-row">
                <td><?php echo $xml->ZONA_A->documento->cun ? : 'N/A'; ?></td>
                <td><?php echo $xml->ZONA_A->documento->folio ? : 'N/A'; ?></td>
                <td><?php echo $xml->ZONA_A->documento->codigo_caso ? : 'N/A'; ?></td>
                <td><?php echo Utiles::full_date((string)$xml->ZONA_A->documento->fecha_emision, TRUE); ?></td>
                
            </tr>
        </tbody></table>
    <div class="zona zona-empleador">
        <h2>B. Identificación del Empleador</h2>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $xml->ZONA_B->empleador->nombre_empleador;?></td>
                <td><?php echo $xml->ZONA_B->empleador->rut_empleador;?></td>
                <td><?php echo $direccion_empleador;?></td>
                <td><?php echo $comunas[(string)$xml->ZONA_B->empleador->direccion_empleador->comuna];?></td>
                <td><?php echo $xml->ZONA_B->empleador->telefono_empleador->numero ? : 'n/a';?></td>
            </tr>
            <tr class="label-row">
                <th>Nombre o Razón Social</th>
                <th>RUT</th>
                <th>Dirección (Calle, Nº, Depto, Población, Villa, Ciudad)</th>
                <th>Comuna</th>
                <th>Número de Teléfono</th>
            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $ciiu1_empleador; ?></td>
                <td>
                    <span class="hombres"><span><?php echo $xml->ZONA_B->empleador->n_trabajadores_hombre;?></span>Hombres</span>
                    -
                    <span class="mujeres"><span><?php echo $xml->ZONA_B->empleador->n_trabajadores_mujer;?></span>Mujeres</span>
                </td>
                <td><?php echo $propiedad[(int)$xml->ZONA_B->empleador->propiedad_empresa];?></td>
                <td><?php echo $tipo_empresa[(int)$xml->ZONA_B->empleador->tipo_empresa];?></td>
                <td><?php echo $xml->ZONA_B->empleador->promedio_anual_trabajadores;?></td>                    
            </tr>
            <tr class="label-row">
                <th>Actividad Económica</th>
                <th>Nº de trabajadores</th>
                <th>Propiedad de la Empresa</th>
                <th>Tipo de Empresa</th>
                <th>Promedio Anual de Trabajadores</th>
            </tr>
        </tbody></table>
    </div>
    <div class="zona zona-centro-trabajo">
        <h2>CT. Identificación del Centro de Trabajo</h2>
        <h4>Centro de Trabajo</h4>
            <?php echo HTML::anchor("centroTrabajo/obtener_centro_trabajo/{$rut_empleador}/{$xml_id}/",'Asignar Centro de Trabajo',array('class'=>'fancybox-big'))?>
        <table><tbody>
                <tr class="data-row">
                    <td><?php echo $default["cuv"]; ?></td>
                    <td><?php echo $default["nombre_centro_trabajo"]; ?></td>
                    <td><?php echo $estados_ct[(int) $default["estado_centro_trabajo"]]; ?></td>
                </tr>
                <tr class="label-row">
                    <th>CUV</th>
                    <th>Nombre CT</th>
                    <th>Estado CT</th>
                                        
                </tr>
            </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $default["rut_empleador_principal"]; ?></td>
                <td><?php echo $default["nombre_empleador_principal"]; ?></td>
                <td><?php echo $default["correlativo_proyecto_contrato"]; ?></td>
                <td><?php echo $tipos_empresa[(int) $default["tipo_empresa"]]; ?></td>
            </tr>
            <tr class="label-row">
                <th>RUT Empleador Principal</th>
                <th>Nombre Empleador Principal</th>
                <th>Correlativo Proy./Cont.</th>
                <th>Tipo Empresa</th>                   
            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $default["comuna_ct"] == 0 ? '' : $comunas[(string) $default["comuna_ct"]]; ?></td>
                <td><?php echo $default["geo_latitud"]; ?></td>
                <td><?php echo $default["geo_longitud"]; ?></td>
            </tr>
            <tr class="label-row">
                <th>Comuna</th>
                <th>Latitud</th>
                <th>Longitud</th>                   
            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $tipos_calle[(int) $default["tipo_calle_ct"]]; ?></td>
                <td><?php echo $default["nombre_calle_ct"]; ?></td>
                <td><?php echo $default["numero_ct"]; ?></td>
                <td><?php echo $default["resto_direccion_ct"]; ?></td>
                <td><?php echo $default["localidad_ct"]; ?></td>
            </tr>
            <tr class="label-row">
                <th>Tipo Calle</th>
                <th>Nombre Calle CT</th>
                <th>Numero CT</th>
                <th>Resto Direccion CT</th>
                <th>Localidad CT</th>
            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $default["descripcion_actividad_trabajadores_ct"]; ?></td>
                <td><?php echo $default["n_trabajadores_propios_ct"]; ?></td>
                <td><?php echo $default["n_trabajadores_hombre_ct"]; ?></td>
                <td><?php echo $default["n_trabajadores_mujer_ct"]; ?></td>
            </tr>
            <tr class="label-row">
                <th>Desc. Act. Trabajadores CT</th>
                <th>Trabajadores CT</th>
                <th>Trab. Hombres CT</th>
                <th>Trab. Mujeres CT</th>
            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $si_no[(int) $default["com_par_constituido"]]; ?></td>
                <td><?php echo $si_no[(int) $default["experto_prevencion_riesgos"]]; ?></td>
                <td><?php echo $default["horas_semana_dedica_ct"]; ?></td>
                <td><?php echo $default["fecha_inicio_ct"]; ?></td>
            </tr>
            <tr class="label-row">
                <th>Comite Paritario?</th>
                <th>Prevencionista es Experto</th>
                <th>Horas a la Semana</th>
                <th>Fecha Inicio CT</th>
            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $si_no[(int) $default["tiene_fech_term"]]; ?></td>
                <td><?php echo $default["fecha_termino_ct"]; ?></td>
            </tr>
            <tr class="label-row">
                <th>Tiene Fecha Termino?</th>
                <th>Fecha Termino CT</th>                  
            </tr>
        </tbody></table>
        <br><br>
    </div>
    <div class="zona zona-trabajador">
        <h2>C. Identificación del Trabajador/a</h2>
        <table><tbody>
                <tr class="data-row">
                    <td><?php echo $nombre_trabajador?></td>
                    <td><?php echo $xml->ZONA_C->empleado->trabajador->rut; ?></td>
                    <td><?php echo $direccion_trabajador?></td>
                    <td><?php echo $comunas[(string)$xml->ZONA_C->empleado->direccion_trabajador->comuna];?></td>
                    
                </tr>
                <tr class="label-row">
                    <th>Nombre</th>
                    <th>RUT</th>
                    <th>Dirección (Calle, Nº, Depto, Población, Villa, Ciudad)</th>
                    <th>Comuna</th>
                </tr>
            </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $sexo_trabajador?></td>
                <td><?php echo $xml->ZONA_C->empleado->trabajador->edad ?></td>
                <td><?php echo $xml->ZONA_C->empleado->trabajador->fecha_nacimiento; ?></td>                    
                <td><?php echo $cod_pais?></td>                    
                <td><?php echo $xml->ZONA_C->empleado->profesion_trabajador; ?></td>
            </tr>
            <tr class="label-row">
                <th>Sexo</th>
                <th>Edad</th>
                <th>Fecha de Nacimiento</th>                    
                <th>País</th>
                <th>Profesión u Oficio</th>
            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo Utiles::full_date((string)$xml->ZONA_C->empleado->fecha_ingreso); ?></td>
                <td><?php echo $contrato[(string)$xml->ZONA_C->empleado->duracion_contrato]; ?></td>                    
                <td><?php echo $codigo_clasificacion_trabajador ?></td>
            </tr>
            <tr class="label-row">
                <th>Fecha de ingreso</th>
                <th>Tipo de contrato</th>                    
                <th>Clasificación trabajador</th>
            </tr>
        </tbody></table>
    </div>
    <div class="zona zona-accidente">
        <h2>P. Datos del Accidente </h2>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $xml->ZONA_P->accidente_fatal->fecha_accidente?></td>
                <td><?php echo $xml->ZONA_P->accidente_fatal->hora_accidente?></td>                                        
                <td><?php echo $direccion_accidente?></td>
                <td><?php echo $comunas[(string)$xml->ZONA_P->accidente_fatal->direccion_accidente->comuna];?></td>                    
            </tr>
            <tr class="label-row">
                <th>Fecha del accidente</th>
                <th>Hora del accidente</th>
                <th>Dirección (Calle, Nº, Depto, Población, Villa, Ciudad)</th>
                <th>Comuna</th>

            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row"><td><?php echo $xml->ZONA_P->accidente_fatal->descripcion_accidente_ini?></td></tr>
            <tr class="label-row"><th>¿Que pasó o cómo ocurrió el accidente?</th></tr>
        </tbody></table>
        <table><tbody>                
            <?php $criterio_gravedad_ralf=array(1=>'Muerte del trabajador',2=>'Desaparecido producto del accidente',3=>'Maniobras de reanimación',4=>'Maniobras de rescate',5=>'Caída de altura de más de 2 m.',6=>'Amputación traumática',7=>'Número de trabajadores afecta el desarrollo normal de la faena',8=>'Accidente en condición hiperbárica')?>
            <?php foreach($xml->ZONA_P->accidente_fatal->gravedad->criterio_gravedad as $g):?>
                <tr class="data-row"><td>
                    <?php 
                        echo Form::checkbox("criterio_{$g}",$g,true,array('disabled'=>'disabled'));
                        echo $criterio_gravedad_ralf[(int)$g];
                    ?>
                </td></tr>
            <?php endforeach?>                                    
            <tr class="label-row">                    
                <th>Criterio Gravedad RALF</th>
            </tr>
        </tbody></table>
        <table><tbody>
            <tr class="data-row">                    
                <td><?php echo $xml->ZONA_P->accidente_fatal->fecha_defuncion?></td>
                <td>
                    <?php if(isset($xml->ZONA_P->accidente_fatal->lugar_defuncion)):?>
                        <?php echo $STLugarDefuncion[(int)$xml->ZONA_P->accidente_fatal->lugar_defuncion];?>
                    <?php endif?>                    
                </td>
            </tr>
            <tr class="label-row">                    
                <th>Fecha defunción</th>
                <th>Lugar Defunción</th>
            </tr>
        </tbody></table>
        <h4>Información Informante Organismo Administrador</h4>
        <table><tbody>
            <tr class="data-row">
                <td><?php echo $informante_oa; ?></td>
                <td><?php echo $xml->ZONA_P->accidente_fatal->informante_oa->rut; ?></td>
                <td class="email"><?php echo $xml->ZONA_P->accidente_fatal->correo_electronico_informante_oa; ?></td>
                <td><?php echo $xml->ZONA_P->accidente_fatal->telefono_informante_oa->cod_area." ".$xml->ZONA_P->accidente_fatal->telefono_informante_oa->numero; ?></td>
            </tr>
            <tr class="label-row">
                <th>Nombre</th>
                <th>RUT</th>
                <th>Correo Electrónico</th>
                <th>Número de Teléfono</th>
            </tr>
        </tbody></table>
    </div>
    <div class="zona zona-accidente">
        <h2>Verificar Medidas</h2>
        <div class="error"><?php echo Arr::get($errors, "medidas_correctivas"); ?></div>
        <?php $medidas = ORM::factory('MedidaCorrectivaRalf145')->where('xml_id','=',$xml_id_origen)->find_all();?>
        <?php $tipos_medida = array(1=>'Medida de control Ingeneril',2=>'Medida de control Administrativo',3=>'Medida de control Protección Personal')?>
        <table id="cscs">
            <thead>
                <tr class="label-row">
                    <th>Folio</th>
                    <th>Cod. Causa</th>
                    <th>Causa</th>
                    <th>Medida Inmediata</th>
                    <th>Fecha Plazo Cumplimiento</th>
                    <th>Verificar</th>
                </tr>
            </thead>
            <?php foreach ($medidas as $med):?>
                <tbody>
                    <tr class="data-row">
                        <td><?php echo $med->id; ?></td>
                        <td><?php echo $med->codigo_causa; ?></td>
                        <td><?php echo $med->glosa_causa; ?></td>
                        <td><?php echo ($med->medida_inmediata==1) ? 'SI' : 'NO'; ?></td>
                        <td><?php echo $med->plazo_cumplimiento; ?></td>
                        <td>
                            <?php if($documento->ESTADO == 5): ?>
                                <?php echo HTML::anchor("ralfVerificacion/medida_verificar/{$med->id}", ($med->fecha_verificacion != "") ? 'Editar' : 'Verificar',array('class'=>'fancybox-big')); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            <?php endforeach;?>
        </table>
        <br/><br/>
        
        <h4>Documentación Anexa</h4>        
        <?php echo HTML::anchor("adjuntos/documento_anexo/{$xml_id}",'Agregar Anexos',array('class'=>'fancybox-narrow'))?>    
        <?php $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();?>
        <div class="error"><?php echo Arr::get($errors, "documentos_anexos"); ?></div> 
        <table id="adjuntos">
            <thead>
                <tr class="label-row">
                    <th>Nombre Documento</th>
                    <th>Fecha de Documento</th>
                    <th>Autor</th>
                    <th>Documento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($anexos as $anexo):?>
                    <tr class="data-row">
                        <td><?php echo $anexo->nombre_documento?></td>
                        <td><?php echo $anexo->fecha_documento?></td>
                        <td><?php echo $anexo->autor_documento?></td>
                        <td>
                            <a href="<?php echo Kohana::$config->load('sitio.url_base'). $anexo->ruta; ?>" target="_blank">Ver</a>
                            <?php if($documento->ESTADO == 5): ?> | <?php echo HTML::anchor("ralf4/borrar_adjunto/{$anexo->id}",'borrar',array('class'=>'fancybox-small'))?> <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <br/><br/>
        
        <h4>Información Investigador</h4>
        <table>
            <tbody>
                <tr class="data-row">
                    <td>
                        <div class="field">
                            <?php echo Form::input("investigador_nombres", $default["investigador_nombres"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "investigador_nombres"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">
                            <?php echo Form::input("investigador_apellido_paterno", $default["investigador_apellido_paterno"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "investigador_apellido_paterno"); ?></div>
                        </div>
                    </td><td>
                          <div class="field">
                            <?php echo Form::input("investigador_apellido_materno", $default["investigador_apellido_materno"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "investigador_apellido_materno"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">
                            <?php echo Form::input("investigador_rut", $default["investigador_rut"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "investigador_rut"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Nombre</th>
                    <th>Ap. Paterno</th>
                    <th>Ap Materno</th>
                    <th>Rut</th>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div align="right">
    <?php $llamar_servicio=URL::site("caso/ingreso_visita/".$caso->CASO_ID."/".$caso->ultimo_documento(), 'http'); ?>        
    <?php echo Form::input('boton_llamar_servicio', 'Crear Visita Prevención en Terreno', array('type' => 'button', 'onclick' => "send_page('$llamar_servicio')")); ?>
    <?php echo Form::submit('boton_incompleta', 'Guardar Incompleta')?>
    <?php echo Form::submit('boton_finalizar', 'Finalizar')?>
    <?php echo Form::close(); ?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>
</div>