<script type="text/javascript">
function select_validar(id)
{        
    $.post('http://'+location.host+site+'validar/select_ralf/'+id,
    function(data) {
        $('#select_validar').html(data);        
    });
}
</script>
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
$STNumSEREMI=array(
    ""=>"Seleccione",
    1=>"Seremi de Salud de la Región de Arica y Parinacota",
    2=>"Seremi de Salud de la Región de Tarapacá",
    3=>"Seremi de Salud de la Región de Antofagasta",
    4=>"Seremi de Salud de la Región de Atacama",
    5=>"Seremi de Salud de la Región de Coquimbo",
    6=>"Seremi de Salud de la Región de Valparaíso",
    7=>"Seremi de Salud de la Región Metropolitana",
    8=>"Seremi de Salud de la Región del Libertador General Bernardo O'Higgins",
    9=>"Seremi de Salud de la Región del Maule",
    10=>"Seremi de Salud de la Región del Biobío",
    11=>"Seremi de Salud de la Región de La Araucanía",
    12=>"Seremi de Salud de la Región de Los Ríos",
    13=>"Seremi de Salud de la Región de Los Lagos",
    14=>"Seremi de Salud de la Región de Aisén del General Carlos Ibáñez del Campo",
    15=>"Seremi de Salud de la Región de Magallanes y la Antártica Chilena",
);

$cod_pais_trab=(string)$xml->ZONA_C->empleado->trabajador->pais_nacionalidad;
$cod_pais=Tipos::codigo($cod_pais_trab,'STPais_nacionalidad');
?>

<div id="container">
    <div id="header"><h1>(RALF) Informe de acciones adoptadas</h1></div>
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
                <?php $criterio_gravedad_ralf=array(1=>'Muerte del trabajador',2=>'Desaparecido producto del accidente',3=>'Maniobras de reanimación',4=>'Maniobras de rescate',5=>'Caída de altura de más de 1,8 m.',6=>'Amputación traumática',7=>'Número de trabajadores afecta el desarrollo normal de la faena',8=>'Accidente en condición hiperbárica')?>
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
        <h2>T. INFORME DE ACCIONES ADOPTADAS </h2>

        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->fecha_informe_acciones_adoptadas;?>                        
                    </td></tr>
                <tr class="label-row"><th>Fecha del informe de acciones adoptadas</th></tr>
            </tbody></table>
            <h4>Medidas no implementadas</h4>
            <table>
            <tbody><tr class="data-row"><td>    
                        <?php echo $xml->ZONA_T->acciones_adoptadas->constatacion_incumplimiento_medidas[0]->medidas_no_implementadas["fecha_verificacion"]?>                                            
                    </td></tr>
                <tr class="label-row"><th>Fecha verificacion</th></tr>
            </tbody></table>

            <h3>Medidas</h3>            
            <?php $medidas=ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_no_implementadas')->find_all();?>            
            <table id="medida1">
                <thead>
                    <tr class="label-row">                        
                        <th>Medida</th>                                                
                    </tr>
                </thead>
                <tbody>                    
                    <?php foreach ($medidas as $m):?>    
                        <tr class="data-row">                            
                            <td><?php echo $m->medida?></td>
                        </tr>                    
                    <?php endforeach;?>
                </tbody></table>
                <br />
                <br />
            <h4>Medidas no implementadas que tuvieron ampliación de plazo</h4>            
            <table>
            <tbody><tr class="data-row"><td>                        
                        <?php echo $xml->ZONA_T->acciones_adoptadas->constatacion_incumplimiento_medidas[0]->medidas_no_implementadas_plazo_ampliado["fecha_verificacion"]?>
                    </td></tr>
                <tr class="label-row"><th>Fecha verificacion</th></tr>
            </tbody></table>
            <h3>Medidas</h3>            
            <?php $medidas=ORM::factory('Medida')->where('xml_id','=',$xml_id)->where('origen','=','medidas_no_implementadas_plazo_ampliado')->find_all();?>            
            <table id="medida2">
                <thead>
                    <tr class="label-row">                        
                        <th>Medida</th>                                                
                    </tr>
                </thead>
                <tbody>                    
                    <?php foreach ($medidas as $m):?>    
                        <tr class="data-row">                            
                            <td><?php echo $m->medida?></td>
                        </tr>                    
                    <?php endforeach;?>
                </tbody></table>
                <br />
                <br />
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $si_no[(string)$xml->ZONA_T->acciones_adoptadas->aplicacion_multa_art_80_ley];?>                        
                    </td></tr>
                <tr class="label-row"><th>Aplicación Multa Articulo 80 Ley</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->monto_multa;?>                        
                    </td></tr>
                <tr class="label-row"><th>Monto de la multa</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->fecha_multa;?>
                    </td></tr>
                <tr class="label-row"><th>Fecha de la multa</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $si_no[(string)$xml->ZONA_T->acciones_adoptadas->recargo_ds67_a15];?>                        
                    </td></tr>
                <tr class="label-row"><th>Hubo recargo por D.S. 67 art. 15</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $si_no_nc[(string)$xml->ZONA_T->acciones_adoptadas->recargo_ds67_a5];?>                        
                    </td></tr>
                <tr class="label-row"><th>Hubo recargo por D.S. 67 art. 5</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->fecha_inicio_recargo_a15;?>                        
                    </td></tr>
                <tr class="label-row"><th>Fecha inicio del recargo por art. 15</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->fecha_termino_recargo_a15;?>                        
                    </td></tr>
                <tr class="label-row"><th>Fecha término del recargo por art. 15</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $si_no[(string)$xml->ZONA_T->acciones_adoptadas->comunicacion_dir_trabajo];?>                        
                    </td></tr>
                <tr class="label-row"><th>Comunicación a la Dirección del Trabajo</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->nro_comunic_dir_trabajo;?>                        
                    </td>                    
                </tr>
                <tr class="label-row"><th>Nº de comunicación con la Dirección del Trabajo</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->fecha_comunic_dir_trabajo;?>                        
                    </td>                    
                </tr>
                <tr class="label-row"><th>Fecha de comunicación con la Dirección del Trabajo</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <?php echo $si_no[(string)$xml->ZONA_T->acciones_adoptadas->comunicacion_seremi];?>                        
                    </td></tr>
                <tr class="label-row"><th>Comunicación a la SEREMI</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php if(isset($xml->ZONA_T->acciones_adoptadas->identificacion_seremi)):?>
                            <?php echo $STNumSEREMI[(int)$xml->ZONA_T->acciones_adoptadas->identificacion_seremi];?>                        
                        <?php endif?>
                    </td>                    
                </tr>
                <tr class="label-row"><th>Identificación Seremi de Salud</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->nro_comunic_seremi;?>                        
                    </td>                    
                </tr>
                <tr class="label-row"><th>Nº de comunicación con la Seremi de Salud</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->fecha_comunic_seremi;?>                        
                    </td>                    
                </tr>
                <tr class="label-row"><th>Fecha de comunicación con la Seremi de Salud</th></tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $si_no[(string)$xml->ZONA_T->acciones_adoptadas->plan_esp_trabajo_empresa];?>                      
                    </td>                    
                </tr>
                <tr class="label-row"><th>Plan especial de trabajo con la empresa</th></tr>
            </tbody></table>
            <table>
            <tbody><tr class="data-row">
                    <td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->fecha_ini_plan_trabajo_empresa;?>                        
                    </td>                    
                </tr>
                <tr class="label-row"><th>Fecha de inicio de plan de trabajo con la empresa</th></tr>
            </tbody></table>
            <table>
            <tbody><tr class="data-row"><td>
                <?php echo $xml->ZONA_T->acciones_adoptadas->resumen_plan_trabajo;?>                
            </td></tr>
                <tr class="label-row"><th>Resumen del plan de trabajo con la empresa</th></tr>
            </tbody></table>
        <h4>Documentos Anexos</h4>
        <?php $anexos=ORM::factory('Adjunto')->where('xml_id','=',$xml_id)->where('origen','=','documentos_anexos_ralf5')->find_all();?>
        <table>
            <tbody><tr class="label-row">                    
                    <th>Nombre Documento</th>
                    <th>Fecha de Documento</th>
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
        
        <h4>Información Verificador</h4>
        <table>
            <tbody><tr class="data-row">
                    <td>                        
                        <?php echo $xml->ZONA_T->acciones_adoptadas->representante_oa->nombres;?>
                    </td><td>                        
                        <?php echo $xml->ZONA_T->acciones_adoptadas->representante_oa->apellido_paterno;?>
                    </td><td>                          
                        <?php echo $xml->ZONA_T->acciones_adoptadas->representante_oa->apellido_materno;?>
                    </td><td>
                        <?php echo $xml->ZONA_T->acciones_adoptadas->representante_oa->rut;?>
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
<br>
    <?php $comentarios=ORM::factory("Comentario")->where('xml_id','=',$xml_id)->find_all();?>
    <?php if(count($comentarios)>0):?>
        <div class='error'><b>Comentarios anteriores del Admin al Documento</b></div>
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
    <br>
    <?php $comentarios=ORM::factory("Comentario")->where('xml_id','=',$xml_id)->where('tipo','=','error_suseso')->find_all();?>
    <?php if(count($comentarios)>0):?>
        <div class='error'><b>Errores de envío a suseso</b></div>
        <div class="tabla-general-wrap">
        <table class="tabla-general">
            <thead>
                <tr>
                    <th><?php echo __('ID')?></th>
                    <th><?php echo __('Error')?></th>                    
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
    <br>
    <?php echo Form::open(); ?>
    <div class='form_section_container'>
        <div class='form_section accident'>                
            <div class="row">
                <div>
                    <label>Documento Válido?</label><br>                
                    <?php echo Form::select('valido',array(""=>"Seleccione",1=>'No',2=>'Si'), $default["valido"], array("onchange"=>"select_validar(this.value);"))?>
                    <div class="error"><?php echo Arr::get($errors, 'valido'); ?></div>
                </div>                                                 
                <div id="select_validar">
                    <?php if($default["valido"]==1):?>
                        <label>Comentarios:</label><br>                
                        <?php echo Form::textarea('observacion', $default["observacion"])?>
                        <div class="error"><?php echo Arr::get($errors, 'observacion'); ?></div>                    
                    <?php endif?>
                </div>
            </div>
            
        </div>
    </div> 
<div align="right">
    <?php echo Form::submit('boton_validar', 'Aceptar')?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>    
</div>
<?php echo Form::close(); ?>