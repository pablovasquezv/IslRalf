<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <title><?php echo $titulo_sitio; ?></title>
        <link rel="shortcut icon" href="<?php echo Kohana::$base_url; ?>media/images/favicon.ico" />
        
        <?php foreach ($stylesheet as $css)
            echo HTML::style($css, array('media' => 'screen')), PHP_EOL; ?>
        
        <?php foreach ($javascript as $js)
            echo HTML::script($js), PHP_EOL; ?>
        
        <?php foreach ($javascripts as $raw_js): ?>
            <script type="text/javascript"><?php echo $raw_js . "\n"; ?></script>
        <?php endforeach; ?>
    </head>
    <body>
        <div id="main-wrapper">
            <div class="top-bar"></div>
            <!-- Contenedor -->
            <div id="contenedor">
                <?php if (!$fancybox_view): ?>
                    <!-- Cabecera -->
                    <div id="cabecera">
                        <div class="inner">
                            <h1><?php echo $titulo_sitio; ?></h1>
                            <?php if (Auth::instance()->logged_in()): ?>
                                <?php $user = ORM::factory('User')->where("username", "=", Auth::instance()->get_user())->find(); ?>
                                <div class="user-info">
                                    <p>
                                        <label>
                                            <span class="title"><?php echo __('Usuario Conectado'); ?>:</span>
                                            <span class="data"><?php echo Auth::instance()->get_user(); ?></span>
                                        </label>
                                    </p>
                                    <p>
                                        <label>
                                            <span class="title"><?php echo __('Usuario Región'); ?>:</span>
                                            <span class="data"><?php echo ORM::factory('Region', $user->region_id)->nombre; ?></span>
                                        </label>
                                    </p>
                                    <p>
                                        <label>
                                            <span class="title"><?php echo __('Descargar'); ?>:</span>
                                            <span class="data">
                                                <a href="<?php echo Kohana::$config->load('sitio.url_base') . 'media/manual/20130627_doc_def_RALF_v25.doc'; ?>">Definiciones RALF</a>
                                        </label>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <p class="logo-sitio"><?php echo $logo_sitio; ?></p>
                            <?php if (Auth::instance()->logged_in()): ?>
                                <?php $rol = $user->roles->where('id', 'NOT IN', array(1))->find(); ?>
                                <div id="menu">
                                    <ul>
                                        <li class="first"><?php echo Html::anchor('/', 'Inicio'); ?></li>
                                        <?php if ($rol->name != 'admin'): ?>
                                            <li class="first"><?php echo Html::anchor('/caso/buscar_caso', 'Buscar'); ?></li>
                                        <?php endif; ?>
                                        <?php if ($rol->name == 'admin'): ?>
                                            <li class="first"><?php echo Html::anchor('user', 'Usuarios'); ?></li>
                                            <li class="first"><?php echo Html::anchor('reporte', 'Reportes'); ?></li>
                                            <li class="first"><?php echo Html::anchor('envios', 'Estado Envíos'); ?></li>
                                        <?php endif; ?>
                                        <li><?php echo Html::anchor('logout/', __('Salir')); ?></li>
                                    </ul>
                                </div>
                            <?php endif; // if($user_logged) ?>
                        </div>
                    </div>
                    <!-- Fin Cabecera -->
                <?php endif; ?>
                <!-- Cuerpo -->
                <div id="cuerpo">
                    <?php if (!empty($mensaje_ok)): ?>
                        <div class="mensaje_ok"><?php echo $mensaje_ok; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($mensaje_error)): ?>
                        <div class="mensaje_error"><?php echo $mensaje_error; ?></div>
                    <?php endif; ?>

                    <!-- Contenido -->
                    <div id="contenido">

                        <!-- CONTENIDO -->
                        <?php echo $contenido; ?>
                        <!-- CONTENIDO -->

                    </div>
                    <!-- Fin Contenido -->

                    <div class="clear"></div>
                </div>
                <!-- Fin Cuerpo -->

                <!-- Pie -->
                <div id="pie"></div>
                <!-- Fin Pie -->

            </div>
            <!--Fin Contenedor-->

            <div id="fondo-pie"></div>
        </div>
        <?php //echo View::factory('profiler/stats');  ?>
    </body>
</html>
