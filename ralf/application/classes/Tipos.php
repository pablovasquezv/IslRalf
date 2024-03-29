<?php

defined('SYSPATH') or die('No direct script access.');

class Tipos {
  public static function codigo($valor,$modelo)
  {
    if($modelo=='STCIIU') {
        $st_origen='St_Ciiu';
    }elseif($modelo=='STCodigo_comuna') {
      $st_origen='St_Comuna';
    }elseif($modelo=='STCIUO') {
        $st_origen='St_Ciuo';
    }elseif($modelo=='STPais_nacionalidad') {
        $st_origen='St_Nacionalidad';
    }

    
    if(isset($valor) && !empty($valor)) {
      $m=ORM::factory($st_origen)->where('codigo','=',$valor)->find();
      if($m->loaded()) {
        $salida=$m->nombre;
      }else {
        $salida="n/a";
      }
    }else {
      $salida="n/a";
    }
    return $salida;

  }

  public static function dominios() {
    $lista=array(
      'nacionalidades'=>Model_St_Nacionalidad::obtener(),
      'comunas'=>Model_St_Comuna::obtener(),
      'ciuos'=>Model_St_Ciuo::obtener(),
      'ciius'=>Model_St_Ciiu::obtener(),
      );
    return $lista;
  }
  


}
