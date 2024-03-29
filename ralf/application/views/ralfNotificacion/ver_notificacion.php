<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/sitio.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/themes/base/jquery.ui.all.css'?>" rel="stylesheet" media="screen" />
<link type="text/css" href="<?php echo Kohana::$config->load('sitio.url_base').'media/css/style_sisesat.css'?>" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-1.7.min.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.core.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/jquery-ui/jquery.ui.datepicker.js';?>"></script>
<script type="text/javascript" src="<?php echo Kohana::$config->load('sitio.url_base').'media/js/fechas_conf.js';?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        
    });
</script>

<div class="popup-container">
    <h2><?php echo __('Ver Notificación').": ".$id; ?></h2>
    <div class="tabla-general-wrap">
        <div class="tabla-general-wrap" id="notificaciones">
            <table id="cscs">
                <thead>
                    <tr class="label-row">
                        <th>Folio</th>
                        <th>Fecha Notificación</th>
                        <th>Autoridad</th>
                        <th>Región</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td><?php echo $notificacion->id; ?></td>
                        <td><?php echo $notificacion->fecha_notificacion_autoridad; ?></td>
                        <td><?php echo $config_ralf['147']['autoridad_receptora'][$notificacion->autoridad_receptora]; ?></td>
                        <td><?php echo $regiones[$notificacion->region_autoridad_receptora]; ?></td>
                    </tr>
                </tbody>
            </table><br>
            <table id="cscs">
                <thead>
                    <tr class="label-row">
                        <th>Paterno Autoridad</th>
                        <th>Materno Autoridad</th>
                        <th>Nombre Autoridad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td><?php echo $notificacion->apellido_paterno_autoridad; ?></td>
                        <td><?php echo $notificacion->apellido_materno_autoridad; ?></td>
                        <td><?php echo $notificacion->nombres_autoridad; ?></td>
                    </tr>
                </tbody>
            </table><br>
            <table id="cscs">
                <thead>
                    <tr class="label-row">
                        <th>Rut Autoridad</th>
                        <th>Correo Autoridad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td><?php echo $notificacion->rut_profesional_autoridad; ?></td>
                        <td><?php echo $notificacion->correo_elect_resp_autoridad; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

