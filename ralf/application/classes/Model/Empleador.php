<?php
	/**
	 * 
	 * SIAP
	 * BinaryBag 2011
	 * 
	 **/
?>
<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Empleador extends ORM {

  protected $_table_name = 'empleador';
  protected $_primary_key = 'emp_id';


  protected $_has_one = array(
      'caso' => array(
          'model' => 'Caso',
          'foreign_key' => 'emp_id',
      ),
  );
  
  
  public function insert($datos)
    {
      //var_dump($datos);
      $response  = NULL;
      $validation = Validation::factory($datos)
       //->rule('', 'min_length', array(':value', 1))
        //->rule('','Utiles::check_mail', array(':value'))
        ->rule('razonSocial', 'not_empty')
        ->rule('rut_dv', 'not_empty')
        ->rule('rut_dv', 'Utiles::rut',array(':value'))

        ->label('rut_dv', 'el rut del empleador')
        ->label('razonSocial', 'la razón social');

      if($validation->check())
      {
        //$this->fuente_informacion = $validation[''];
        $this->rut_empleador = $validation['rut_dv'];
        $this->nombre_empleador = $validation['razonSocial'];
        /*$this->tipo_calle = $validation['tipo_calle'];
        $this->nombre_calle = $validation['nombre_calle'];
        $this->numero = $validation['numero'];
        $this->resto_direccion = $validation['resto_direccion'];
        $this->localidad = $validation['localidad'];
        $this->comuna = $validation['comuna'];
        $this->ciiu_empleador = $validation['ciiu_empleador'];
        $this->ciiu_texto = $validation['ciiu_texto'];
        $this->n_trabajadores = $validation['n_trabajadores'];
        $this->n_trabajadores_hombre = $validation['n_trabajadores_hombre'];
        $this->n_trabajadores_mujer = $validation['n_trabajadores_mujer'];
        $this->tipo_empresa = $validation['tipo_empresa'];
        $this->ciiu2_empleador = $validation['ciiu2_empleador'];
        $this->ciiu2_texto = $validation['ciiu2_texto'];
        $this->propiedad_empresa = $validation['propiedad_empresa'];
        $this->cod_pais = $validation['cod_pais'];
        $this->cod_area = $validation['cod_area'];
        $this->numero_telefono = $validation['numero_telefono'];*/

        $this->save();
        $response = $this->emp_id;
      }
      else
      {
        $response = $validation->errors('validate');
      }

      return $response;
    }


    
}
