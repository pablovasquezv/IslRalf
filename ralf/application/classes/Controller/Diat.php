<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Diat extends Controller_Website {
    public function action_index() {
      echo "Ingreso de denuncias QA";

        /*
        $response['xml']='<?xml version="1.0"?>
<DIAT>  
  <ZONA_A>  
    <documento>
      <folio>600006</folio>
      <fecha_emision>2014-06-02T18:31:11</fecha_emision>
      <codigo_org_admin>21</codigo_org_admin>
      <codigo_emisor>21</codigo_emisor>
      <codigo_caso>600006</codigo_caso>
      <validez>1</validez>
      <origen_informacion>1</origen_informacion>
    </documento>
  </ZONA_A>
  <ZONA_B>
    <empleador>
      <rut_empleador>19983137-2</rut_empleador>
      <nombre_empleador> TEST TEST TEST</nombre_empleador>
      <direccion_empleador>
        <tipo_calle>2</tipo_calle>
        <nombre_calle>SIMON BOLIVAR</nombre_calle>
        <numero>2812</numero>
        <resto_direccion>F</resto_direccion>
        <localidad>131</localidad>
        <comuna>13120</comuna>
      </direccion_empleador>
      <ciiu_empleador>602300</ciiu_empleador>
      <ciiu_texto>Transporte de carga por carretera</ciiu_texto>
      <n_trabajadores>3</n_trabajadores>
      <n_trabajadores_hombre>3</n_trabajadores_hombre>
      <n_trabajadores_mujer>0</n_trabajadores_mujer>
      <tipo_empresa>1</tipo_empresa>
      <propiedad_empresa>1</propiedad_empresa>
      <telefono_empleador>
        <cod_pais>56</cod_pais>
        <cod_area>9</cod_area>
        <numero>22255881</numero>
      </telefono_empleador>
    </empleador>
  </ZONA_B>
  <ZONA_C>
    <empleado>
      <trabajador>
        <apellido_paterno>ORTEGA</apellido_paterno>
        <apellido_materno>VALENCIA</apellido_materno>
        <nombres>MAURICIO</nombres>
        <rut>19983137-2</rut>
        <fecha_nacimiento>1980-11-01</fecha_nacimiento>
        <edad>33</edad>
        <sexo>1</sexo>
        <pais_nacionalidad>152</pais_nacionalidad>
      </trabajador>
      <direccion_trabajador>
        <tipo_calle>1</tipo_calle>
        <nombre_calle>AMERICO VESPUCIO</nombre_calle>
        <numero>1765</numero>
        <resto_direccion> </resto_direccion>
        <localidad>131</localidad>
        <comuna>13102</comuna>
      </direccion_trabajador>
      <profesion_trabajador>CONDUCTOR</profesion_trabajador>
      <ciuo_trabajador>3141</ciuo_trabajador>
      <categoria_ocupacion>2</categoria_ocupacion>
      <duracion_contrato>1</duracion_contrato>
      <tipo_dependencia>1</tipo_dependencia>
      <tipo_remuneracion>1</tipo_remuneracion>
      <fecha_ingreso>2011-03-01</fecha_ingreso>
    </empleado>
  </ZONA_C>
  <ZONA_D>
    <accidente>
      <fecha_accidente>2014-06-02T15:30:00</fecha_accidente>
      <hora_ingreso>06:00:00</hora_ingreso>
      <direccion_accidente>
        <tipo_calle>2</tipo_calle>
        <nombre_calle>MIRAFLORES</nombre_calle>
        <numero>0</numero>
        <resto_direccion>.</resto_direccion>
        <localidad>131</localidad>
        <comuna>13102</comuna>
      </direccion_accidente>
      <lugar_accidente>EMPRESA SOREPA LUGAR DESCARGA CAMIONES</lugar_accidente>
      <que>ESPERANDO CARGA</que>
      <como>AL ESPERAR CARGA CAMINANDO POR VEREDA UN TROZO DE MADERA FUE PISADO POR UN CAMION SALIO DISPARADO Y LO GOLPEA EN ZONA COSTAL DERECHO</como>
      <trabajo_habitual_cual>CONDUCTOR</trabajo_habitual_cual>
      <trabajo_habitual>1</trabajo_habitual>
      <gravedad>1</gravedad>
      <tipo_accidente>1</tipo_accidente>
      <hora_salida>18:00:00</hora_salida>
      <detalle_prueba/>
    </accidente>
  </ZONA_D>
  <ZONA_F>
    <denunciante>
      <nombre_denunciante>EDGARDO</nombre_denunciante>
      <rut_denunciante>13200350-5</rut_denunciante>
      <clasificacion>7</clasificacion>
    </denunciante>
  </ZONA_F>
</DIAT>';
                    $response['cun']='95384';
                    $result = Ws_Spm::ingresa_caso($response);          
                    var_dump($result);              
                    
*/
  die();
  }

}
