<?php 
$xml=$data['xml'];
$comunas=$data['comunas'];
$ciius=$data['ciius'];
$ciiu1 = (string)$xml->ZONA_B->empleador->ciiu_empleador;
$ciiu1_empleador = $ciius[$ciiu1];
$propiedad=array(1=>'Privada',2=>'Publica');
$tipo_empresa= array(1=>'Principal',2=>'Contratista',3=>'Subcontratista',4=>'De servicios transitorios');

$nombre_trabajador= $xml->ZONA_C->empleado->trabajador->nombres." ".$xml->ZONA_C->empleado->trabajador->apellido_paterno." ".$xml->ZONA_C->empleado->trabajador->apellido_materno;
$direccion_trabajador=$xml->ZONA_C->empleado->direccion_trabajador->nombre_calle." ".$xml->ZONA_C->empleado->direccion_trabajador->numero." ".$xml->ZONA_C->empleado->direccion_trabajador->resto_direccion." ".$xml->ZONA_C->empleado->direccion_trabajador->localidad;
$direccion_empleador=$xml->ZONA_B->empleador->direccion_empleador->nombre_calle." ".$xml->ZONA_B->empleador->direccion_empleador->numero." ".$xml->ZONA_B->empleador->direccion_empleador->resto_direccion." ".$xml->ZONA_B->empleador->direccion_empleador->localidad;

$sexos=$data['sexo'];
$sexo_trabajador=$sexos[(int)$xml->ZONA_C->empleado->trabajador->sexo];


$nacionalidades=$data['nacionalidades'];
$contrato=$data['contrato'];

$clas_trabajador=$data['clas_trabajador'];

                   
$direccion_accidente=$xml->ZONA_P->accidente_fatal->direccion_accidente->nombre_calle." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->numero." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->resto_direccion." ".$xml->ZONA_P->accidente_fatal->direccion_accidente->localidad;


$criterio_gravedad_ralf=array(1=>'Muerte del trabajador',2=>'Desaparecido producto del accidente',3=>'Maniobras de reanimación',4=>'Maniobras de rescate',5=>'Caída de altura de más de 1,8 m.',6=>'Amputación traumática',7=>'Número de trabajadores afecta el desarrollo normal de la faena',8=>'Accidente en condición hiperbárica');
$criterio_gravedad=$criterio_gravedad_ralf[(int)$xml->ZONA_P->accidente_fatal->gravedad->criterio_gravedad];

$STLugarDefuncion=array(1=>'Mismo lugar del Accidente',2=>'Traslado al Centro Asistencial',3=>'Centro Asistencial',4=>'Otro (indicar lugar)'); 

  if(isset($xml->ZONA_C->empleado->clasificacion_trabajador) && !empty($xml->ZONA_C->empleado->clasificacion_trabajador)) {
    $cla_tra=(string)$xml->ZONA_C->empleado->clasificacion_trabajador;
    $codigo_clasificacion_trabajador=$clas_trabajador[$cla_tra];
  }else {
    $codigo_clasificacion_trabajador='n/a';
  }

$informante_oa=$xml->ZONA_P->accidente_fatal->informante_oa->nombres." ".$xml->ZONA_P->accidente_fatal->informante_oa->apellido_paterno." ".$xml->ZONA_P->accidente_fatal->informante_oa->apellido_materno;

$si_no=array(1=>'Si',2=>'No');
$si_no_nc=array(1=>'Si',2=>'No',3=>'No Corresponde');

$tìpo_calle=array(""=>"Seleccione",1 => 'Avenida',2 => 'Calle',3 => 'Pasaje');
$comunas=Kohana::$config->load('dominios.STCodigo_comuna');

$STDiasJornadaParcial=array(
    ''=>'Seleccione',
    1=>'1 día',
    2=>'1,5 días',
    3=>'2 días',
    4=>'2,5 días',
    5=>'3 días',
    6=>'3,5 días',
    7=>'4 días');

$cod_pais_trab=(string)$xml->ZONA_C->empleado->trabajador->pais_nacionalidad;
$cod_pais=Tipos::codigo($cod_pais_trab,'STPais_nacionalidad');

?>
<div id="container">
    <div id="header"><h1>Informe del Accidente (investigación, causas y medidas correctivas) (RALF 3)</h1></div>
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
                    <td>
                        <?php 
                            if(isset($xml->ZONA_C->empleado->trabajador->rut)){
                                echo $xml->ZONA_C->empleado->trabajador->rut;     
                            }elseif(isset($xml->ZONA_C->empleado->trabajador->documento_identidad)){
                                
                                    echo $xml->ZONA_C->empleado->trabajador->documento_identidad->identificador;    
                            }
                        ?>
                    </td>
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
                <?php $criterio_gravedad_ralf=array(1=>'Muerte del trabajador',2=>'Desaparecido producto del accidente',3=>'Maniobras de reanimación',4=>'Maniobras de rescate',5=>'Caída de altura de más de 1,8 m.',6=>'Amputación traumática',7=>'Número de trabajadores afecta el desarrollo normal de la faena',8=>'Accidente en condición hiperbárica',8=>'Accidente en condición hiperbárica')?>
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
    <div class="zona-accidente">
        <h2>R. Informe y Medidas Inmediatas </h2>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->fecha_inicio_investigacion_acc;?>
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->fecha_termino_investigacion_acc;?>                
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->hora_ingreso;?>                        
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->hora_salida;?>                        
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Fecha de Inicio Investigación del Accidente</th>
                    <th>Fecha de Termino Investigación del Accidente</th>
                    <th>Hora de Ingreso al Trabajo</th>
                    <th>Hora de Salida del Trabajo</th>
                </tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php $jornada_momento_accidente=array(1=>'Ordinaria (con/sin turno)',2=>'Jornada Extraordinaria',3=>'Jornada Excepcional (con/sin turno)',4=>'Otra (indicar cuál)');?>                            
                        <?php echo $jornada_momento_accidente[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->jornada_momento_accidente];?>                        
                
                    </td></tr>
                <tr class="label-row"><th>Jornada al Momento del Accidente</th></tr>
            </tbody></table>

            <table>
            <tbody><tr class="data-row"><td>
                <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->jornada_momento_accidente_otro)):?>
                    <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->jornada_momento_accidente_otro;?>                
                <?php endif?>
                </td></tr>
                <tr class="label-row"><th>Jornada momento accidente otro</th></tr>
            </tbody></table>
            <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->antiguedad->annos;?> (Años)                        
                    </td><td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->antiguedad->meses;?> (Meses)       
                    </td><td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->antiguedad->dias;?> (Dias)                               
                    </td>
                </tr>
                <tr class="label-row">                    
                    <th colspan="3">Antiguedad</th>                    
                </tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->trabajo_habitual_cual;?>
                    </td>
                    <td>
                        <?php echo $si_no[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->trabajo_habitual];?>
                    </td>                   
                    <td align="center">
                        <?php $lugar_trabajo=array(1=>'Casa Matriz',2=>'Sucursal Empresa')?>                          
                        <?php echo $lugar_trabajo[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->lugar_trabajo];?>                                            
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Trabajo Habitual Cuál</th>
                    <th>¿Realizaba Trabajo Habitual?</th>                    
                    <th>Lugar de Trabajo</th>
                </tr>
            </tbody></table>        
        <b>Si selecciona Sucursal Empresa Completar datos de direccion </b>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal->tipo_calle;?>                        
                        <?php endif?>
                    </td><td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal->nombre_calle;?>                        
                        <?php endif?>
                    </td><td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal->numero?>                        
                        <?php endif?>
                    </td>                    
                </tr>
                <tr class="label-row">
                    <th>Tipo calle</th>
                    <th>Nombre calle</th>                    
                    <th>Número</th>
                </tr>
            </tbody></table>
            <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal->resto_direccion?>                                                
                        <?php endif?>
                    </td><td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal->localidad?>
                        <?php endif?>   
                    </td><td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal)):?>
                            <?php echo (isset($comunas[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal->comuna])) ? $comunas[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->direccion_sucursal->comuna] : '';?>                        
                        <?php endif?>
                    </td>                    
                </tr>
                <tr class="label-row">
                    <th>Resto dir</th>
                    <th>Localidad</th>                    
                    <th>Comuna</th>
                </tr>
            </tbody></table>

        <table>
            <tbody><tr class="data-row">
                    <td>                        
                        <?php echo $si_no_nc[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exist_comites_lugar_acc]?>                        
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->nro_comites_funcio?>
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->nro_comites_ds54_a1?>
                        
                    </td>
                    <td>
                        <?php echo $si_no[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->cumb_ob_info_ds40_a21];?>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>¿Existe comite Paritario?</th>
                    <th>Nº Comites en Funcionamiento</th>
                    <th>Nº Comites por Art 1 DS54</th>
                    <th>Cumple con informar Riesgos Laborales</th>
                </tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $si_no[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->reg_ohys_al_dia];?>
                    </td>
                    <td>
                        <?php echo $si_no[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->depto_pre_rie_teorico];?>
                    </td>
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->depto_pre_rie_real)):?>
                            <?php echo $si_no[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->depto_pre_rie_real];?>                        
                        <?php endif?>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Mantiene al día Reglamento Interno</th>
                    <th>¿Debe contar con Dpto. Prevención de Riesgos?</th>
                    <th>Cuenta con Dpto. Prevención de Riesgos</th>
                </tr>
            </tbody></table>
        <h4>Datos del Experto en Prevención de Riesgos</h4>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exp_pre_em)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exp_pre_em->nombres;?>                        
                        <?php endif?>
                    </td><td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exp_pre_em)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exp_pre_em->apellido_paterno;?>                        
                        <?php endif?>
                    </td><td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exp_pre_em)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exp_pre_em->apellido_materno;?>                        
                        <?php endif?>
                    </td><td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exp_pre_em)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->exp_pre_em->rut;?>                        
                        <?php endif?>
                    </td>                    
                </tr>
                <tr class="label-row">
                    <th>Nombre</th>
                    <th>Ap. Paterno</th>                    
                    <th>Ap Materno</th>
                    <th>Rut</th>
                </tr>
            </tbody></table>

        <table>
            <tbody><tr class="data-row">                    
                    <td>
                        <?php $tipo_cont_exp_pre_em=array(1=>'Honorarios jornada parcial',2=>'Honorarios jornada completa',3=>'Contrato indefinido jornada parcial',
                              4=>'Contrato indefinido jornada completa',5=>'Contrato plazo fijo jornada parcial',6=>'Contrato plazo fijo jornada completa',7=>'Otro')?>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->tipo_cont_exp_pre_em)):?>
                            <?php echo $tipo_cont_exp_pre_em[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->tipo_cont_exp_pre_em];?>     
                        <?php endif?>                            
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->tipo_cont_exp_pre_em_otro)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->tipo_cont_exp_pre_em_otro;?>    
                        <?php endif?>
                    </td>
                </tr>
                <tr class="label-row">                    
                    <th>Tipo Contrato</th>                    
                    <th>Tipo de contrato del experto en prevención de riesgos (Otro)</th>                    
                </tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->nro_dias_jor_parcial_cont_exp_pre_emp)):?>
                            <?php echo $STDiasJornadaParcial[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->nro_dias_jor_parcial_cont_exp_pre_emp];?>
                        <?php endif?>

                    </td>
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->nro_reg_a_s_exp_pre_em)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->nro_reg_a_s_exp_pre_em;?>                                                
                        <?php endif?>
                    </td>
                    <td>                        
                        <?php $cat_exp_pre_em=array(1=>'Profesional',2=>'Tecnico',3=>'Practico')?>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->cat_exp_pre_em)):?>
                            <?php echo $cat_exp_pre_em[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->cat_exp_pre_em];?>
                        <?php endif?>
                                              
                    </td>
                </tr>
                <tr class="label-row"> 
                    <th>Nº días que trabaja el experto</th>
                    <th>Nº Registro en Autoridad Sanitaria</th>
                    <th>Categoria de Experto</th>
                </tr>
            </tbody></table>
        <h4>Datos de la Empresa Involucrada</h4>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $si_no[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->programa_pre_rie];?>                        
                    </td>
                    <td>
                        <?php echo $si_no_nc[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->trabajador_reg_subcontratacion];?>
                        
                    </td>
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->registro_ac_antec_a66bis)):?>
                            <?php echo $si_no_nc[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->registro_ac_antec_a66bis];?>                        
                        <?php endif?>                        
                    </td>
                </tr>
                <tr class="label-row">
                    <th>¿Tiene Programa de Prevención de Riesgos?</th>
                    <th>Trabajador en Regimen de Sub-Contratación</th>
                    <th>Registro Antecedentes Art. 66 Bis</th>
                </tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $si_no_nc[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->comite_par_fae_emp_ppal];?>                        
                    </td>
                    <td>
                        <?php echo $si_no_nc[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->depto_pre_rie_emp_ppal];?>
                    </td>
                    <td>
                        <?php echo $si_no_nc[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->imp_sist_gest_sst_emp_ppal];?>                        
                    </td>
                </tr>
                <tr class="label-row">
                    <th>¿Empresa cuenta con comité paritario de faena?</th>
                    <th>¿Empresa cuenta con depto. de prevención de riesgos de faena?</th>
                    <th>¿Cuenta con Sistema de Gestión SST?</th>
                </tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $si_no_nc[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->fiscalizacion_con_multas_mat_sst];?>
                    </td>
                    <td>
                        <?php $organismo_multas=array(1=>'DIRECCION DEL TRABAJO',2=>'SEREMI DE SALUD')?>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->organismo_multas)):?>
                            <?php echo $organismo_multas[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->organismo_multas];?>
                        <?php endif?>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Fiscalización con Multas</th>
                    <th>Organismo que Curso Multa</th>
                </tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->desc_acc_invest;?>
            </td></tr>
                <tr class="label-row"><th>Descripción de como ocurrio el accidente</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->codificacion_accidente->codigo_forma;?>                        
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->codificacion_accidente->codigo_agente_accidente;?>                        
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->codificacion_accidente->codigo_intencionalidad;?>                        
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->codificacion_accidente->codigo_modo_transporte;?>
                    </td>
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->codificacion_accidente->codigo_papel_lesionado;?>
                    </td>                                    
                </tr>
                <tr class="label-row">
                    <th><?php echo Html::anchor('dominios/codigo_forma','Código Forma (Ver)', array('target'=>'_blank'));
?></th>
                    <th><?php echo Html::anchor('dominios/codigo_agente_acc','Código Agente Accidente (Ver)', array('target'=>'_blank'));
?></th>             
                    <th><?php echo Html::anchor('dominios/codigo_intencionalidad','Código Intencionalidad (Ver)', array('target'=>'_blank'));
?></th>
                    <th><?php echo Html::anchor('dominios/codigo_modo_transporte','Código Modo Transporte (Ver)', array('target'=>'_blank'));
?></th>
                    <th><?php echo Html::anchor('dominios/codigo_papel_lesionado','Código Papel Lesionado (Ver)', array('target'=>'_blank'));
?></th>                
                </tr>
            </tbody></table>
            <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->codificacion_accidente->codigo_contraparte;?>                        
                    </td>  
                    <td>
                       <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->codificacion_accidente->codigo_tipo_evento)):?>
                            <?php $codigo_tipo_evento = (int) $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->codificacion_accidente->codigo_tipo_evento;?>                        
                            <?php $codigo_tipo_evento_array = Controller_Dominios::codigo_tipo_evento(); ?>
                            <?php echo $codigo_tipo_evento_array[$codigo_tipo_evento]; ?>
                        <?php endif?>
                    </td>
                </tr>
                <tr class="label-row">   
                    <th><?php echo Html::anchor('dominios/codigo_contraparte','Código Contraparte (Ver)', array('target'=>'_blank'));
?></th>                  
                    <th>Código Tipo Evento</th>
                </tr>
            </tbody></table>
            <table>
            <tbody><tr class="data-row"><td>
                <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->antecedentes_informacion_acc;?>                 
                </td></tr>
                <tr class="label-row"><th>Antecedentes de Investigación</th></tr>
            </tbody></table>        
          <h4>Datos del Investigador del Accidente</h4>
          <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->investigador_acc->nombres;?>
                        
                    </td><td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->investigador_acc->apellido_paterno;?>                        
                    </td><td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->investigador_acc->apellido_materno;?>                        
                    </td><td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->investigador_acc->rut;?>                                                
                    </td>                    
                </tr>

                <tr class="label-row">
                    <th>Nombre</th>
                    <th>Ap. Paterno</th>                    
                    <th>Ap Materno</th>
                    <th>Rut</th>
                </tr>
            </tbody></table>        

        <table>
            <tbody><tr class="data-row"><td>
                <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->prof_invest_acc;?>                 
                </td></tr>
                <tr class="label-row"><th>Profesión investigador</th></tr>
            </tbody></table>        



        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $si_no[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->invest_es_experto];?>
                    </td>
                    <td>
                        <?php $categoria_experto=array(1=>'Profesional',2=>'Tecnico',3=>'Practico')?>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->categoria_experto)):?>
                            <?php echo $categoria_experto[(string)$xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->categoria_experto];?>
                        <?php endif?>
                    </td>
                    <td>
                        <?php if(isset($xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->nro_reg_a_s_invest_acc)):?>
                            <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R1->nro_reg_a_s_invest_acc;?>                        
                        <?php endif?>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>¿Investigador es Experto?</th>
                    <th>Profesión del Investigador</th>
                    <th>Nº Registro en Autoridad Sanitaria</th>
                </tr>
            </tbody></table>
        <h4>Documentación Anexa</h4>
        <?php $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos')->find_all();?>
        <table>
            <tbody><tr class="label-row">                    
                    <th>Nombre del Documento</th>
                    <th>Fecha</th>
                    <th>Autor</th>
                    <th>Documento</th>
                </tr>     
                <?php foreach ($anexos as $anexo):?>    
                    <tr class="data-row">
                        <td><?php echo $anexo->nombre_documento?></td>
                        <td><?php echo $anexo->fecha_documento?></td>
                        <td><?php echo $anexo->autor_documento?></td>                        
                        <?php $ver=str_replace('index.php/', '', URL::site($anexo->ruta, 'http'))?>
                        <td><a href="<?php echo $ver; ?>">Ver</a></td>                        
                    </tr>
                <?php endforeach;?>
                
            </tbody></table>            
        <h4>Causas y Medidas Correctivas</h4>       
        <?php $cmcs=ORM::factory('Causa_Medida_Correctiva')->where('xml_id','=',$xml_id)->find_all();?> 
        <table>
            <tbody><tr class="label-row">                    
                    <th>ID</th>
                    <th>Causa</th>
                    <th>Medida</th>
                    <th>Plazo</th>
                </tr>                
               <?php foreach ($cmcs as $csc):?>    
                    <tr class="data-row">                        
                        <td><?php echo $csc->causa_medida_plazo_id?></td>
                        <td><?php echo $csc->causa_medida_plazo_causa?></td>
                        <td><?php echo $csc->causa_medida_plazo_medida?></td>
                        <td><?php echo $csc->causa_medida_plazo_plazo?></td>                       
                    </tr>                
                <?php endforeach;?>
            </tbody></table>
              <h4>Documentación Anexa Causas</h4>
        <?php $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos_causas')->find_all();?>
        <table>
            <tbody><tr class="label-row">                    
                    <th>Nombre del Documento</th>
                    <th>Fecha</th>
                    <th>Autor</th>
                    <th>Documento</th>
                </tr>     
                <?php foreach ($anexos as $anexo):?>    
                    <tr class="data-row">
                        <td><?php echo $anexo->nombre_documento?></td>
                        <td><?php echo $anexo->fecha_documento?></td>
                        <td><?php echo $anexo->autor_documento?></td>                        
                        <?php $ver=str_replace('index.php/', '', URL::site($anexo->ruta, 'http'))?>
                        <td><a href="<?php echo $ver; ?>">Ver</a></td>                        
                    </tr>
                <?php endforeach;?>
                
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R2->fecha_notificacion_me_correc;?>                
            </td></tr>
                <tr class="label-row"><th>Fecha de la Notificación de las medidas correctivas</th></tr>
            </tbody></table>
        <h4>Experto Investigador</h4>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R2->investigador->nombres;?>                        
                    </td><td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R2->investigador->apellido_paterno;?>                        
                    </td><td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R2->investigador->apellido_materno;?> 
                        
                    </td><td>
                        <?php echo $xml->ZONA_R->informe_y_medidas_inmediatas->ZONA_R2->investigador->rut;?>                         
                    </td>                    
                </tr>
                <tr class="label-row">
                    <th>Nombre</th>
                    <th>Ap. Paterno</th>                    
                    <th>Ap Materno</th>
                    <th>Rut</th>
                </tr>
            </tbody></table>
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
