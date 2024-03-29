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

$criterio_gravedad_ralf = array(
    1 => 'Muerte del trabajador',
    2 => 'Desaparecido producto del accidente',
    3 => 'Maniobras de reanimación',
    4 => 'Maniobras de rescate',
    5 => 'Caída de altura de más de 2 m.',
    6 => 'Amputación traumática',
    7 => 'Número de trabajadores afecta el desarrollo normal de la faena',
    8 => "Accidente en condición hiperbárica"
);
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

$cod_pais_trab=(string)$xml->ZONA_C->empleado->trabajador->pais_nacionalidad;
$cod_pais=Tipos::codigo($cod_pais_trab,'STPais_nacionalidad');
?>


<div id="container">
    <div id="header"><h1>(RALF) Informe cumplimiento medidas prescritas</h1></div>
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
                <?php $criterio_gravedad_ralf=array(1=>'Muerte del trabajador',2=>'Desaparecido producto del accidente',3=>'Maniobras de reanimación',4=>'Maniobras de rescate',5=>'Caída de altura de más de 2 m.',6=>'Amputación traumática',7=>'Número de trabajadores afecta el desarrollo normal de la faena',8 => "Accidente en condición hiperbárica")?>
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
    <div class="zona arbol_causas">     
        <h2>Causas</h2>
        <h4>Arbol de Causas</h4>
        <?php $arbol=ORM::factory('ArbolCausas')->where('xml_id','=',$xml_id)->find();?>

        <table id="arbol">
            <thead>
                <tr class="label-row">
                    <th>Lesión</th>
                    <th>Árbol</th>
                </tr>
            </thead>
            <tbody>
                <tr class="data-row">
                    <td><?php echo $arbol->lesion?></td>
                    <td>
                        <?php if($arbol->arbol_id > 0): echo HTML::anchor("arbolCausas/ver_arbol_causas/{$xml_id}",'Ver',array('class'=>'fancybox-big'));endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="zona-accidente">
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
             <br /> <br />
        <h4>Información Verificador</h4>
        <table>
            <tbody><tr class="data-row">
                    <td>                        
                        <?php echo $xml->ZONA_CAUSAS->causas_del_accidente->investigador->nombres?>
                    </td><td>                        
                        <?php echo $xml->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_paterno?>
                    </td><td>                          
                        <?php echo $xml->ZONA_CAUSAS->causas_del_accidente->investigador->apellido_materno?>
                    </td><td>
                        <?php echo $xml->ZONA_CAUSAS->causas_del_accidente->investigador->rut?>
                        
                    </td>                    
                </tr>
                <tr class="label-row">
                    <th>Nombre</th>
                    <th>Ap. Paterno</th>                    
                    <th>Ap Materno</th>
                    <th>Rut</th>
                </tr>
            </tbody></table>
            <br />
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