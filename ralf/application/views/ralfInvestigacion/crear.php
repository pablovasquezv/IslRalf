<?php 
$xml=$data['xml'];

$comunas=Model_St_Comuna::obtener();
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
  }else {
    $codigo_clasificacion_trabajador='n/a';
  }

$informante_oa=$xml->ZONA_P->accidente_fatal->informante_oa->nombres." ".$xml->ZONA_P->accidente_fatal->informante_oa->apellido_paterno." ".$xml->ZONA_P->accidente_fatal->informante_oa->apellido_materno;

$si_no_comun=array('SI'=>'Si','NO'=>'No');
$si_no=array(1=>'Si',2=>'No');
$si_no_nc=array(1=>'Si',2=>'No',3=>'No Corresponde');

$tìpo_calle=array(""=>"Seleccione",1 => 'Avenida',2 => 'Calle',3 => 'Pasaje');
$STDiasJornadaParcial=array(
    ''=>'Seleccione',
    1=>'1 día',
    2=>'1,5 días',
    3=>'2 días',
    4=>'2,5 días',
    5=>'3 días',
    6=>'3,5 días',
    7=>'4 días');


$codigo_modo_transporte=array(
    ''=>'Seleccione',
    '1.1'=>'1.1', '1.2'=>'1.2', '2'=>'2', '3.1'=>'3.1', '3.2'=>'3.2', '3.8'=>'3.8', '3.9'=>'3.9', '4.1'=>'4.1', '4.2'=>'4.2', '4.8'=>'4.8', '4.9'=>'4.9', '5'=>'5', '6.1'=>'6.1', '6.2'=>'6.2', '6.3'=>'6.3', '6.4'=>'6.4', '6.8'=>'6.8', '6.9'=>'6.9', '7.1'=>'7.1', '7.2'=>'7.2', '7.8'=>'7.8', '7.9'=>'7.9', '8.1'=>'8.1', '8.2'=>'8.2', '8.3'=>'8.3', '8.8'=>'8.8', '8.9'=>'8.9', '9.1'=>'9.1', '9.2'=>'9.2', '9.3'=>'9.3', '10.1'=>'10.1', '10.2'=>'10.2', '10.8'=>'10.8', '10.9'=>'10.9', '11.1'=>'11.1', '11.2'=>'11.2', '11.3'=>'11.3', '11.4'=>'11.4', '11.5'=>'11.5', '11.8'=>'11.8', '11.9'=>'11.9', '12.1'=>'12.1', '12.2'=>'12.2', '12.4'=>'12.4', '12.5'=>'12.5', '12.6'=>'12.6', '12.9'=>'12.9', '98'=>'98', '99'=>'99'
    );
$codigo_papel_lesionado=array(
    ''=>'Seleccione',1=>'1',2=> '2',3=> '3',4=> '4',5=> '5',6=> '6',8=> '8',9=> '9');
$codigo_contraparte=array(
    ''=>'Seleccione',
    '1.1'=>'1.1', '1.2'=>'1.2', '2'=>'2', '3.1'=>'3.1', '3.2'=>'3.2', '3.8'=>'3.8', '3.9'=>'3.9', '4.1'=>'4.1', '4.2'=>'4.2', '4.8'=>'4.8', '4.9'=>'4.9', '5'=>'5', '6.1'=>'6.1', '6.2'=>'6.2', '6.3'=>'6.3', '6.4'=>'6.4', '6.8'=>'6.8', '6.9'=>'6.9', '7.1'=>'7.1', '7.2'=>'7.2', '7.8'=>'7.8', '7.9'=>'7.9', '8.1'=>'8.1', '8.2'=>'8.2', '8.3'=>'8.3', '8.8'=>'8.8', '8.9'=>'8.9', '9.1'=>'9.1', '9.2'=>'9.2', '9.3'=>'9.3', '10.1'=>'10.1', '10.2'=>'10.2', '10.8'=>'10.8', '10.9'=>'10.9', '11.1'=>'11.1', '11.2'=>'11.2', '11.3'=>'11.3', '11.4'=>'11.4', '11.5'=>'11.5', '11.8'=>'11.8', '11.9'=>'11.9', '12.1'=>'12.1', '12.2'=>'12.2', '12.4'=>'12.4', '12.5'=>'12.5', '12.6'=>'12.6', '12.9'=>'12.9', '13.1'=>'13.1', '13.2'=>'13.2', '13.3'=>'13.3', '13.4'=>'13.4', '13.8'=>'13.8', '13.9'=>'13.9', '14.1'=>'14.1', '14.2'=>'14.2', '14.8'=>'14.8', '14.9'=>'14.9', '15.1'=>'15.1', '15.2'=>'15.2', '15.9'=>'15.9', '98'=>'98', '99'=>'99'
    );


//HORA DE INGRESO INI
if(isset($_POST["hora_ingreso_hr"])) {
    $default["hora_ingreso_hr"]=$_POST["hora_ingreso_hr"];
}else {
    if(isset($xml->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso)) {
        $default["hora_ingreso_hr"]= substr($xml->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso,0,2);
    }else {
        $default["hora_ingreso_hr"]="00";
    }
}

if(isset($_POST["hora_ingreso_mm"])) {
    $default["hora_ingreso_mm"]=$_POST["hora_ingreso_mm"];
}else {
    if(isset($xml->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso)) {
        $default["hora_ingreso_mm"]= substr($xml->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso,-5,-3);
    }else {
        $default["hora_ingreso_mm"]="00";
    }
}

if(isset($_POST["hora_ingreso_ss"])) {
    $default["hora_ingreso_ss"]=$_POST["hora_ingreso_ss"];
}else {
    if(isset($xml->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso)) {
        $default["hora_ingreso_ss"]= substr($xml->ZONA_INVESTIGACION->investigacion_acc->hora_ingreso,6,7);
    }else {
        $default["hora_ingreso_ss"]="00";
    }
}
//HORA DE INGRESO FIN

//HORA DE SALIDA INI
if(isset($_POST["hora_salida_hr"])) {
    $default["hora_salida_hr"]=$_POST["hora_salida_hr"];
}else {
    if(isset($xml->ZONA_INVESTIGACION->investigacion_acc->hora_salida)) {
        $default["hora_salida_hr"]= substr($xml->ZONA_INVESTIGACION->investigacion_acc->hora_salida,0,2);
    }else {
        $default["hora_salida_hr"]="00";
    }
}

if(isset($_POST["hora_salida_mm"])) {
    $default["hora_salida_mm"]=$_POST["hora_salida_mm"];
}else {
    if(isset($xml->ZONA_INVESTIGACION->investigacion_acc->hora_salida)) {
        $default["hora_salida_mm"]= substr($xml->ZONA_INVESTIGACION->investigacion_acc->hora_salida,-5,-3);
    }else {
        $default["hora_salida_mm"]="00";
    }
}

if(isset($_POST["hora_salida_ss"])) {
    $default["hora_salida_ss"]=$_POST["hora_salida_ss"];
}else {
    if(isset($xml->ZONA_INVESTIGACION->investigacion_acc->hora_salida)) {
        $default["hora_salida_ss"]= substr($xml->ZONA_INVESTIGACION->investigacion_acc->hora_salida,6,7);
    }else {
        $default["hora_salida_ss"]="00";
    }
}
//FIN HR SALIDA

$hh_array["00"]="00";
$hh_array["01"]="01";
$hh_array["02"]="02";
$hh_array["03"]="03";
$hh_array["04"]="04";
$hh_array["05"]="05";
$hh_array["06"]="06";
$hh_array["07"]="07";
$hh_array["08"]="08";
$hh_array["09"]="09";
for ($i=10;$i<=23;$i++) {
    $hh_array[$i]=$i;
}


$mm_ss_array["00"]="00";
$mm_ss_array["01"]="01";
$mm_ss_array["02"]="02";
$mm_ss_array["03"]="03";
$mm_ss_array["04"]="04";
$mm_ss_array["05"]="05";
$mm_ss_array["06"]="06";
$mm_ss_array["07"]="07";
$mm_ss_array["08"]="08";
$mm_ss_array["09"]="09";
for ($i=10;$i<=59;$i++) {
    $mm_ss_array[$i]=$i;
}

//ZONA INVST JUSTIFIACION NO LABORAL
if(isset($_POST["justificacion_no_laboral"])) {
    $default["justificacion_no_laboral"]=$_POST["justificacion_no_laboral"];
}else {
    if(isset($xml->ZONA_INVESTIGACION->investigacion_acc->justificacion_no_laboral)) {
        $default["justificacion_no_laboral"]= $xml->ZONA_INVESTIGACION->investigacion_acc->justificacion_no_laboral;
    }else {
        $default["justificacion_no_laboral"]="";
    }
}
//ORIGEN COMUN
$caso_id= $xml->ZONA_A->documento->codigo_caso;
$casoEncontrado = ORM::factory('Caso')->where('CASO_ID', '=', $caso_id)->find();

if(isset($_POST["origen_comun"])) {
    $default["origen_comun"]= $_POST["origen_comun"];
}else {
    if(isset($casoEncontrado->ORIGEN_COMUN)) {
        $default["origen_comun"]=$casoEncontrado->ORIGEN_COMUN;
    }else {
        $default["origen_comun"]="";
    }
}
//ZONA INVST CIRCUSTANCIAS ACCIDENTE
if(isset($_POST["circunstancias_accidente"])) {
    $default["circunstancias_accidente"]=$_POST["circunstancias_accidente"];
}else {
    if(isset($xml->ZONA_INVESTIGACION->investigacion_acc->circunstancias_accidente)) {
        $default["circunstancias_accidente"]= $xml->ZONA_INVESTIGACION->investigacion_acc->circunstancias_accidente;
    }else {
        $default["circunstancias_accidente"]="";
    }
}

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
    <div id="header"><h1>Informe del Accidente (investigación y medidas correctivas) (RALF Investigacion)</h1></div>
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
                    <td><?php
                            if(isset($xml->ZONA_C->empleado->trabajador->rut)){
                                echo $xml->ZONA_C->empleado->trabajador->rut;
                            }else{
                                $xml->ZONA_C->empleado->trabajador->documento_identidad; 
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
                    <td><?php echo $cod_pais;?></td>                    
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
                    <td><?php echo (isset($STLugarDefuncion[(int)$xml->ZONA_P->accidente_fatal->lugar_defuncion])) ? $STLugarDefuncion[(int)$xml->ZONA_P->accidente_fatal->lugar_defuncion] : ''; ?></td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("origen_comun", $si_no_comun, $default["origen_comun"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "origen_comun"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">                    
                    <th>Fecha defunción</th>
                    <th>Lugar Defunción</th>
                    <th>Origen Común</th>
                </tr>
            </tbody></table>
        <table>
        <tbody><tr class="data-row"><td>
            <div class="field">                
                <?php echo Form::textarea("justificacion_no_laboral", $default["justificacion_no_laboral"]); ?>
                <div class="error"><?php echo Arr::get($errors, "justificacion_no_laboral"); ?></div>
            </div>
            </td></tr>
            <tr class="label-row"><th>Justificación Origen del Accidente</th></tr>
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
                        <div class="field">                            
                            <?php echo Form::input("fecha_inicio_investigacion_acc", $default["fecha_inicio_investigacion_acc"],array('class'=>'datepicker')); ?>
                            <div class="error"><?php echo Arr::get($errors, "fecha_inicio_investigacion_acc"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("fecha_termino_investigacion_acc", $default["fecha_termino_investigacion_acc"],array('class'=>'datepicker')); ?>
                            <div class="error"><?php echo Arr::get($errors, "fecha_termino_investigacion_acc"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                                                        
                            <?php echo Form::select('hora_ingreso_hr', $hh_array,$default["hora_ingreso_hr"]); ?>
                            <?php echo Form::select('hora_ingreso_mm', $mm_ss_array,$default["hora_ingreso_mm"]); ?>
                            <?php echo Form::select('hora_ingreso_ss', $mm_ss_array,$default["hora_ingreso_ss"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "hora_ingreso"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                                                        
                            <?php echo Form::select('hora_salida_hr', $hh_array,$default["hora_salida_hr"]); ?>
                            <?php echo Form::select('hora_salida_mm', $mm_ss_array,$default["hora_salida_mm"]); ?>
                            <?php echo Form::select('hora_salida_ss', $mm_ss_array,$default["hora_salida_ss"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "hora_salida"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Fecha de Inicio Investigación del Accidente</th>
                    <th>Fecha de Termino Investigación del Accidente</th>
                    <th>Hora de Ingreso al Trabajo (hh:mm:ss)</th>
                    <th>Hora de Salida del Trabajo (hh:mm:ss)</th>
                </tr>
            </tbody></table>
        <table>
            <tbody><tr class="data-row"><td>
                        <div class="field">
                            <?php $jornada_momento_accidente=array(1=>'Ordinaria (con/sin turno)',2=>'Jornada Extraordinaria',3=>'Jornada Excepcional (con/sin turno)',4=>'Otra (indicar cuál)');?>                            
                            <?php echo Form::select("jornada_momento_accidente", $jornada_momento_accidente,$default["jornada_momento_accidente"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "jornada_momento_accidente"); ?></div>
                        </div>                        
                    </td></tr>
                <tr class="label-row"><th>Jornada al Momento del Accidente</th></tr>
            </tbody></table>

            <table>
            <tbody><tr class="data-row"><td>
                <div class="field">                
                    <?php echo Form::textarea("jornada_momento_accidente_otro", $default["jornada_momento_accidente_otro"]); ?>
                    <div class="error"><?php echo Arr::get($errors, "jornada_momento_accidente_otro"); ?></div>
                </div>
                </td></tr>
                <tr class="label-row"><th>Jornada momento accidente otro</th></tr>
            </tbody></table>
            <br>
            <br>
            <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">
                            <label>Antiguedad_Años</label><br>
                            <?php echo Form::input("antiguedad_annos", $default["antiguedad_annos"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "antiguedad_annos"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">
                            <label>Antiguedad meses</label><br>
                            <?php echo Form::input("antiguedad_meses", $default["antiguedad_meses"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "antiguedad_meses"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">
                            <label>Antiguedad días</label><br>
                            <?php echo Form::input("antiguedad_dias", $default["antiguedad_dias"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "antiguedad_dias"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">                    
                    <th colspan="3">Antiguedad</th>                    
                </tr>
            </tbody></table>
            <br>
            <br>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("trabajo_habitual_cual", $default["trabajo_habitual_cual"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "trabajo_habitual_cual"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("trabajo_habitual", $si_no,$default["trabajo_habitual"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "trabajo_habitual"); ?></div>
                        </div>                        
                    </td>                   
                    <td>
                        <div class="field">  
                            <?php $lugar_trabajo=array(1=>'Casa Matriz',2=>'Sucursal Empresa')?>                          
                            <?php echo Form::select("lugar_trabajo", $lugar_trabajo,$default["lugar_trabajo"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "lugar_trabajo"); ?></div>
                        </div>                        
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Trabajo Habitual Cuál</th>
                    <th>¿Realizaba Trabajo Habitual?</th>                    
                    <th>Lugar de Trabajo</th>
                </tr>
            </tbody></table>
            <br>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("exist_comites_lugar_acc", $si_no_nc,$default["exist_comites_lugar_acc"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "exist_comites_lugar_acc"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("nro_comites_funcio", $default["nro_comites_funcio"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "nro_comites_funcio"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("nro_comites_ds54_a1", $default["nro_comites_ds54_a1"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "nro_comites_ds54_a1"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("cumb_ob_info_ds40_a21", $si_no,$default["cumb_ob_info_ds40_a21"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "cumb_ob_info_ds40_a21"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>¿Existe comite Paritario?</th>
                    <th>Nº Comites en Funcionamiento</th>
                    <th>Nº Comites por Art 1 DS54</th>
                    <th>Cumple con informar Riesgos Laborales</th>
                </tr>
            </tbody></table>
        <br>
        <br>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("reg_ohys_al_dia", $si_no,$default["reg_ohys_al_dia"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "reg_ohys_al_dia"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("depto_pre_rie_teorico", $si_no,$default["depto_pre_rie_teorico"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "depto_pre_rie_teorico"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("depto_pre_rie_real", array(""=>"Seleccione")+$si_no,$default["depto_pre_rie_real"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "depto_pre_rie_real"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Mantiene al día Reglamento Interno</th>
                    <th>¿Debe contar con Dpto. Prevención de Riesgos?</th>
                    <th>Cuenta con Dpto. Prevención de Riesgos</th>
                </tr>
            </tbody></table>
        <br>
        <br>
        <h4>Datos del Experto en Prevención de Riesgos</h4>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("exp_pre_em_nombres", $default["exp_pre_em_nombres"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "exp_pre_em_nombres"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">                            
                            <?php echo Form::input("exp_pre_em_apellido_paterno", $default["exp_pre_em_apellido_paterno"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "exp_pre_em_apellido_paterno"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">                            
                            <?php echo Form::input("exp_pre_em_apellido_materno", $default["exp_pre_em_apellido_materno"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "exp_pre_em_apellido_materno"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">                            
                            <?php echo Form::input("exp_pre_em_rut", $default["exp_pre_em_rut"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "exp_pre_em_rut"); ?></div>
                        </div>
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
                        <div class="field">
                            <?php $tipo_cont_exp_pre_em=array(1=>'Honorarios jornada parcial',2=>'Honorarios jornada completa',3=>'Contrato indefinido jornada parcial',
                              4=>'Contrato indefinido jornada completa',5=>'Contrato plazo fijo jornada parcial',6=>'Contrato plazo fijo jornada completa',7=>'Otro')?>
                            <?php echo Form::select("tipo_cont_exp_pre_em", $tipo_cont_exp_pre_em,$default["tipo_cont_exp_pre_em"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "tipo_cont_exp_pre_em"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("tipo_cont_exp_pre_em_otro", $default["tipo_cont_exp_pre_em_otro"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "tipo_cont_exp_pre_em_otro"); ?></div>
                        </div>
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
                        <div class="field">                            
                            <?php echo Form::select("nro_dias_jor_parcial_cont_exp_pre_emp", $STDiasJornadaParcial,$default["nro_dias_jor_parcial_cont_exp_pre_emp"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "nro_dias_jor_parcial_cont_exp_pre_emp"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("nro_reg_a_s_exp_pre_em", $default["nro_reg_a_s_exp_pre_em"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "nro_reg_a_s_exp_pre_em"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <?php $cat_exp_pre_em=array(1=>'Profesional',2=>'Tecnico',3=>'Practico')?>
                            <?php echo Form::select("cat_exp_pre_em", $cat_exp_pre_em,$default["cat_exp_pre_em"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "cat_exp_pre_em"); ?></div>
                        </div>                        
                    </td>
                </tr>
                <tr class="label-row"> 
                    <th>Nº días que trabaja el experto</th>
                    <th>Nº Registro en Autoridad Sanitaria</th>
                    <th>Categoria de Experto</th>
                </tr>
            </tbody></table>
        <br>
        <br>
        <h4>Datos de la Empresa Involucrada</h4>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("programa_pre_rie", $si_no,$default["programa_pre_rie"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "programa_pre_rie"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("trabajador_reg_subcontratacion",$si_no, $default["trabajador_reg_subcontratacion"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "trabajador_reg_subcontratacion"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("registro_ac_antec_a66bis",$si_no, $default["registro_ac_antec_a66bis"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "registro_ac_antec_a66bis"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>¿Tiene Programa de Prevención de Riesgos?</th>
                    <th>Trabajador en Regimen de Sub-Contratación</th>
                    <th>Registro Antecedentes Art. 66 Bis</th>
                </tr>
            </tbody></table>
        <br>
        <br>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("comite_par_fae_emp_ppal",$si_no_nc, $default["comite_par_fae_emp_ppal"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "comite_par_fae_emp_ppal"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("depto_pre_rie_emp_ppal", $si_no_nc,$default["depto_pre_rie_emp_ppal"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "depto_pre_rie_emp_ppal"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("imp_sist_gest_sst_emp_ppal",$si_no_nc, $default["imp_sist_gest_sst_emp_ppal"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "imp_sist_gest_sst_emp_ppal"); ?></div>
                        </div>
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
                        <div class="field">                            
                            <?php echo Form::select("fiscalizacion_con_multas_mat_sst",$si_no, $default["fiscalizacion_con_multas_mat_sst"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "fiscalizacion_con_multas_mat_sst"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <?php $organismo_multas=array(1=>'DIRECCION DEL TRABAJO',2=>'SEREMI DE SALUD')?>
                            <?php echo Form::select("organismo_multas", array(""=>"Seleccione")+$organismo_multas,$default["organismo_multas"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "organismo_multas"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>Fiscalización con Multas</th>
                    <th>Organismo que Curso Multa</th>
                </tr>
            </tbody></table>
        <table>
            <tbody>
            <tr class="data-row">
                <td>
                    <div class="field">                    
                        <?php echo Form::textarea("desc_acc_invest", $default["desc_acc_invest"]); ?>
                        <div class="error"><?php echo Arr::get($errors, "desc_acc_invest"); ?></div>
                    </div>
                </td>
                <td>
                    <div class="field">                    
                        <?php echo Form::textarea("circunstancias_accidente", $default["circunstancias_accidente"]); ?>
                        <div class="error"><?php echo Arr::get($errors, "circunstancias_accidente"); ?></div>
                    </div>
                </td>
            </tr>
                <tr class="label-row">
                    <th>Descripción de como ocurrio el accidente</th>
                    <th>Circunstancia o contexto en el cual ocurrió el accidente</th>
                </tr>
            </tbody>
        </table>
        <br>
        <b>Si hubo un vehículo involucrado, debe agregar toda la Codificación del accidente</b>
        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">
                            <?php $vehiculo_involucrado=array(1=>'Sí',2=>'No')?>
                            <?php echo Form::select("vehiculo_involucrado", array(""=>"Seleccione")+$vehiculo_involucrado,$default["vehiculo_involucrado"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "vehiculo_involucrado"); ?></div>
                        </div>
                    </td>
                    <td><div class="field">                            
                            <?php echo Form::select("codigo_modo_transporte",$codigo_modo_transporte, $default["codigo_modo_transporte"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "codigo_modo_transporte"); ?></div>
                        </div></td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("codigo_papel_lesionado",$codigo_papel_lesionado, $default["codigo_papel_lesionado"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "codigo_papel_lesionado"); ?></div>
                        </div>
                    </td>                                    
                </tr>
                <tr class="label-row">
                    <th>¿Hubo un Vehiculo Involucrado?</th>
                    <th><?php echo Html::anchor('dominios/codigo_modo_transporte','Código Modo Transporte (Ver)', array('target'=>'_blank'));
?></th>
                    <th><?php echo Html::anchor('dominios/codigo_papel_lesionado','Código Papel Lesionado (Ver)', array('target'=>'_blank'));
?></th>                                                                                                                      
                </tr>
            </tbody></table>
            <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                        
                            <?php echo Form::select("codigo_contraparte",$codigo_contraparte, $default["codigo_contraparte"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "codigo_contraparte"); ?></div>
                        </div>
                    </td>  
                    <td>
                        <div class="field">
                            <?php echo Form::select("codigo_tipo_evento",Controller_Dominios::codigo_tipo_evento(), $default["codigo_tipo_evento"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "codigo_tipo_evento"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">   
                    <th><?php echo Html::anchor('dominios/codigo_contraparte','Codigo Contraparte (Ver)', array('target'=>'_blank'));
?></th>
                    <th>Código Tipo Evento</th>
                </tr>
            </tbody></table>
            <table>
            <tbody><tr class="data-row"><td>
                 <div class="field">                            
                            <?php echo Form::textarea("antecedentes_informacion_acc", $default["antecedentes_informacion_acc"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "antecedentes_informacion_acc"); ?></div>
                        </div>
                </td></tr>
                <tr class="label-row"><th>Antecedentes de Investigación</th></tr>
            </tbody></table>
            <br>
            <br>        
          <h4>Datos del Investigador del Accidente</h4>
          <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("investigador_acc_nombres", $default["investigador_acc_nombres"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "investigador_acc_nombres"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">                            
                            <?php echo Form::input("investigador_acc_apellido_paterno", $default["investigador_acc_apellido_paterno"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "investigador_acc_apellido_paterno"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">                            
                            <?php echo Form::input("investigador_acc_apellido_materno", $default["investigador_acc_apellido_materno"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "investigador_acc_apellido_materno"); ?></div>
                        </div>
                    </td><td>
                        <div class="field">                            
                            <?php echo Form::input("investigador_acc_rut", $default["investigador_acc_rut"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "investigador_acc_rut"); ?></div>
                        </div>
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
                 <div class="field">                            
                            <?php echo Form::textarea("prof_invest_acc", $default["prof_invest_acc"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "prof_invest_acc"); ?></div>
                        </div>
                </td></tr>
                <tr class="label-row"><th>Profesión investigador</th></tr>
            </tbody></table>        



        <table>
            <tbody><tr class="data-row">
                    <td>
                        <div class="field">                            
                            <?php echo Form::select("invest_es_experto",$si_no, $default["invest_es_experto"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "invest_es_experto"); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="field">                            
                            <?php $categoria_experto=array(1=>'Profesional',2=>'Tecnico',3=>'Practico')?>
                            <?php echo Form::select("categoria_experto",$categoria_experto, $default["categoria_experto"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "categoria_experto"); ?></div>
                        </div>                        
                    </td>
                    <td>
                        <div class="field">                            
                            <?php echo Form::input("nro_reg_a_s_invest_acc", $default["nro_reg_a_s_invest_acc"]); ?>
                            <div class="error"><?php echo Arr::get($errors, "nro_reg_a_s_invest_acc"); ?></div>
                        </div>
                    </td>
                </tr>
                <tr class="label-row">
                    <th>¿Investigador es Experto?</th>
                    <th>Profesión del Investigador</th>
                    <th>Nº Registro en Autoridad Sanitaria</th>
                </tr>
            </tbody></table>
        <br>
            <br>
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
                        <?php $ver=Kohana::$config->load('sitio.url_base'). $anexo->ruta;?>
                        <td>
                            <a href="<?php echo $ver; ?>">Ver</a>
                            <?php if($documento->ESTADO == 5): ?> | <?php echo HTML::anchor("ralfInvestigacion/borrar_adjunto/{$anexo->id}",'borrar',array('class'=>'fancybox-small'))?> <?php endif; ?>
                        </td>                        
                    </tr>                
                <?php endforeach;?>
            </tbody></table>
        <br />
        <br />
    </div>
</div>
<div align="right">
    <?php echo Form::submit('boton_incompleta', 'Guardar Incompleta')?>
    <?php echo Form::submit('boton_finalizar', 'Finalizar')?>
    <?php echo Form::close(); ?>
    <?php echo Form::input('volver', 'Volver', array('type' => 'button', 'onclick' => "send_page('$back_page')")); ?>    
</div>