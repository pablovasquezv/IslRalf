<?php
$xml = $data['xml'];
$comunas = Model_St_Comuna::obtenerSinFiltro();
$ciiu = (string) $xml->ZONA_B->empleador->ciiu_empleador;
$ciiu1_empleador = Tipos::codigo($ciiu,'STCIIU');
$propiedad = array(1=>'Privada',2=>'Publica');
$tipo_empresa = array(1=>'Principal',2=>'Contratista',3=>'Subcontratista',4=>'De servicios transitorios');

$nombre_trabajador = $xml->ZONA_C->empleado->trabajador->nombres." ".$xml->ZONA_C->empleado->trabajador->apellido_paterno." ".$xml->ZONA_C->empleado->trabajador->apellido_materno;
$direccion_trabajador = $xml->ZONA_C->empleado->direccion_trabajador->nombre_calle." ".$xml->ZONA_C->empleado->direccion_trabajador->numero." ".$xml->ZONA_C->empleado->direccion_trabajador->resto_direccion." ".$xml->ZONA_C->empleado->direccion_trabajador->localidad;
$direccion_empleador = $xml->ZONA_B->empleador->direccion_empleador->nombre_calle." ".$xml->ZONA_B->empleador->direccion_empleador->numero." ".$xml->ZONA_B->empleador->direccion_empleador->resto_direccion." ".$xml->ZONA_B->empleador->direccion_empleador->localidad;

$sexos = $data['sexo'];
$sexo_trabajador = $sexos[(int)$xml->ZONA_C->empleado->trabajador->sexo];

$nacionalidades = Model_St_Nacionalidad::obtener();
$contrato = $data['contrato'];

$clas_trabajador = $data['clas_trabajador'];
$cod_pais_trab = (string) $xml->ZONA_C->empleado->trabajador->pais_nacionalidad;
$cod_pais = Tipos::codigo($cod_pais_trab,'STPais_nacionalidad');
                   
$direccion_accidente = $xml->ZONA_P->accidente_fatal->direccion_accidente->nombre_calle." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->numero." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->resto_direccion." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->localidad;

$criterio_gravedad_ralf = array(1=>'Muerte del trabajador',2=>'Desaparecido producto del accidente',3=>'Maniobras de reanimación',4=>'Maniobras de rescate',5=>'Caída de altura de más de 2 m.',6=>'Amputación traumática',7=>'Número de trabajadores afecta el desarrollo normal de la faena',8 => "Accidente en condición hiperbárica");
$criterio_gravedad = $criterio_gravedad_ralf[(int)$xml->ZONA_P->accidente_fatal->gravedad->criterio_gravedad];

$STLugarDefuncion = array(1=>'Mismo lugar del Accidente',2=>'Traslado al Centro Asistencial',3=>'Centro Asistencial',4=>'Otro (indicar lugar)'); 

if (isset($xml->ZONA_C->empleado->clasificacion_trabajador) && !empty($xml->ZONA_C->empleado->clasificacion_trabajador)) {
    $cla_tra = (string) $xml->ZONA_C->empleado->clasificacion_trabajador;
    $codigo_clasificacion_trabajador = $clas_trabajador[$cla_tra];
} else {
    $codigo_clasificacion_trabajador = 'n/a';
}

$informante_oa=$xml->ZONA_P->accidente_fatal->informante_oa->nombres." ".$xml->ZONA_P->accidente_fatal->informante_oa->apellido_paterno." ".$xml->ZONA_P->accidente_fatal->informante_oa->apellido_materno;

$si_no = array(0 => '',1=>'Si',2=>'No');
$si_no_nc = array(1=>'Si',2=>'No',3=>'No Corresponde');

$tipo_calle = array(""=>"Seleccione",1 => 'Avenida',2 => 'Calle',3 => 'Pasaje');

$caso_id = $xml->ZONA_A->documento->codigo_caso;
$xml_id = $xml->ZONA_A->documento->folio;
$rut_empleador = $xml->ZONA_B->empleador->rut_empleador;

$xmlRalf1 = ORM::factory('Xml')->where('CASO_ID', '=',$caso_id)->where('TPXML_ID','=','145')->find();

$estados_ct=array(0 => '',1 => 'Activo',2 => 'Caduco');
$tipos_calle=array(0 => '', 1 => 'Avenida',2 => 'Calle',3 => 'Pasaje');
$tipos_empresa= array(0=> '',1 => 'Principal',2 => 'Contratista',3 => 'Subcontratista',4 => 'De servicios transitorios');
$comunas_ct=Model_St_Comuna::obtenerSinFiltroLlaveGlosa();
$cod_comuna_ct = "";


//NUEVOS SELECTS
$list_tipo_resolucion_informada = array(1 => 'Aplica recargo', 
                                        2 => 'Modifica recargo', 
                                        3 => 'Pone término al recargo', 
                                        4 => 'Deja sin efecto recargo');
$list_causal_de_recargo = array(1 => 'Causal letra a) artículo 15 D.S. 67', 
                                2 => 'Causal letra b) artículo 15 D.S. 67', 
                                3 => 'Causal letra c) artículo 15 D.S. 67', 
                                4 => 'Causal letra d) artículo 15 D.S. 67', 
                                5 => 'Causal letra e) artículo 15 D.S. 67', 
                                6 => 'Causal del inciso final del articulo 66 Ley N°16.744');
$list_proceso_asociado_al_recargo = array(  1 => 'Prescripción de medidas por accidente grave o fatal', 
                                            2 => 'Prescripción de medidas por enfermedad profesional', 
                                            3 => 'Prescripción de medidas por accidente del trabajo', 
                                            4 => 'Prescripción de medidas por proceso de vigilancia', 
                                            5 => 'Prescripción de medidas por asesoría IPER', 
                                            6 => 'Prescripción de medidas por autoevaluación de riesgos críticos', 
                                            7 => 'Otro');

$list_porcentaje_base_recargo = array(  1 => '20%', 
                                        2 => '25%', 
                                        3 => '32%', 
                                        4 => '40%', 
                                        5 => '50%');

?>

<div id="container">
    <div id="header"><h1>(RALF) Ralf Recargo Tasa</h1></div>
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
    <div class="zona zona-centro-trabajo">
        <h2>1. Resolución del Recargo de Tasa</h2>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">          
                            <?php echo $list_tipo_resolucion_informada[(int) $xml->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Tipo_resolucion_informada];?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Tipo_resolucion_informada;?>
                        </div>
                    </td>
                    <td>
                        <div class="field">       
                            <?php echo Utiles::full_date((string)$xml->ZONA_Recargo_Tasa->Resolucion_recargo_tasa->Fecha_de_resolucion); ?>                   
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Tipo de resolución informada</th>
                    <th>Número de resolución</th>
                    <th>Fecha de resolución</th>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="zona zona-centro-trabajo">
        <h2>2. Recargo de tasa</h2>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo $list_causal_de_recargo[(int) $xml->ZONA_Recargo_Tasa->Recargo_tasa->Causal_de_recargo];?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $list_proceso_asociado_al_recargo[(int) $xml->ZONA_Recargo_Tasa->Recargo_tasa->Proceso_asociado_al_recargo];?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Recargo_tasa->Otro_proceso_asociado_al_recargo;?>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Causal de recargo</th>
                    <th>Proceso asociado al recargo</th>
                    <th>Otro proceso asociado al recargo</th>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Recargo_tasa->Nro_Trabajadores_entidad_empleadora_TT;?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Recargo_tasa->Magnitud_de_incumplimiento_TA;?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $list_porcentaje_base_recargo[(int) $xml->ZONA_Recargo_Tasa->Recargo_tasa->Porcentaje_base_recargo];?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Utiles::full_date((string)$xml->ZONA_Recargo_Tasa->Recargo_tasa->Porcentaje_del_recargo);?>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Número total de trabajadores</th>
                    <th>Magnitud del incumplimiento</th>
                    <th>Porcentaje de base del recargo </th>
                    <th>Porcentaje del recargo</th>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_adicional_110;?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Recargo_tasa->Recargo_resultante;?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_cot_adicional;?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Recargo_tasa->Tasa_adicional_con_recargo;?>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo $xml->ZONA_Recargo_Tasa->Recargo_tasa->Vigencia;?>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Tasa adicional DS 110</th>
                    <th>Recargo resultante</th>
                    <th>Tasa de cotización adicional</th>
                    <th>Tasa adicional con recargo</th>
                    <th>Vigencia</th>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="zona zona-centro-trabajo">
        <h2>3. Declaración de Centro de Trabajo</h2>
        <table><tbody>
                <tr class="data-row">
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->CUV; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->nombre_centro_trabajo; ?></td>
                    <td><?php echo $estados_ct[(int) $xml->ZONA_ZCT->centro_de_trabajo->estado_centro_trabajo]; ?></td>
                </tr>
                <tr class="label-row">
                    <th>CUV</th>
                    <th>Nombre CT</th>
                    <th>Estado CT</th>
                </tr>
            </tbody></table>
        <table><tbody>
                <tr class="data-row">
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->rut_empleador_principal; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->nombre_empleador_principal; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->correlativo_proyecto_contrato; ?></td>
                    <td><?php echo $tipos_empresa[(int) $xml->ZONA_ZCT->centro_de_trabajo->tipo_empresa]; ?></td>
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
                     <td><?php echo $comunas[(string) $xml->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->comuna_ct] ; ?></td>
                     <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_latitud; ?></td>
                     <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->geolocalizacion->geo_longitud; ?></td>
                </tr>
                <tr class="label-row">
                    <th>Comuna</th>
                    <th>Latitud</th>
                    <th>Longitud</th>                   
                </tr>
            </tbody></table>
        <table><tbody>
                <tr class="data-row">
                    <td><?php echo $tipos_calle[(int) $xml->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->tipo_calle_ct];?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->nombre_calle_ct; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->numero_ct; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->resto_direccion_ct; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->direccion_centro_trabajo->localidad_ct; ?></td>
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
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->descripcion_actividad_trabajadores_ct; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->n_trabajadores_propios_ct; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->n_trabajadores_hombre_ct; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->n_trabajadores_mujer_ct; ?></td>
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
                    <td><?php echo $si_no[(int) $xml->ZONA_ZCT->centro_de_trabajo->com_par_constituido]; ?></td>
                    <td><?php echo $si_no[(int) $xml->ZONA_ZCT->centro_de_trabajo->experto_prevencion_riesgos]; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->horas_semana_dedica_ct; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->fecha_inicio_ct; ?></td>
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
                    <td><?php echo $si_no[(int) $xml->ZONA_ZCT->centro_de_trabajo->tiene_fech_term]; ?></td>
                    <td><?php echo $xml->ZONA_ZCT->centro_de_trabajo->fecha_termino_ct; ?></td>
                </tr>
                <tr class="label-row">
                    <th>Tiene Fecha Termino?</th>
                    <th>Fecha Termino CT</th>                  
                </tr>
            </tbody></table>
            <br><br>
    </div>
</div>
<div align="right">
    <?php if($rol_user=="admin"):?>
        <?php $back_page=URL::site("/caso/ver_caso_admin/{$caso_id}", 'http')?>
    <?php else:?>
        <?php $back_page=URL::site("/caso/ver_caso/{$caso_id}", 'http')?>
    <?php endif?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>
</div>