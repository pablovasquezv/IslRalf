<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Trabajador extends ORM {
    protected $_table_name = 'trabajador';
    protected $_primary_key = 'tra_id';

    protected $_has_one = array(
        'caso' => array(
            'model' => 'Caso',
            'foreign_key' => 'tra_id',
        ),
    );
    
    /**
     * Busca documentos asociados a Caso
     * @param void
     * @return array
     * @author jperez
     */
    public static function get_casos($rut) {
        echo $rut;
        die();
        $personas=ORM::factory('persona')->where('PER_PERSONA_RUT','=',$rut)->find_all();
        $datos_per=array();
        foreach($personas as $persona) {
            $datos_per['rut']=$persona->per_persona_rut;
            $datos_per['nombre']=$persona->per_persona_nombre;
            $datos_per['ap_paterno']=$persona->per_persona_ap_paterno;
            $datos_per['ap_materno']=$persona->per_persona_ap_materno;

            $trabajadores=ORM::factory('trabajador')->where('PER_PERSONA_ID','=',$persona->per_persona_id)->find_all();
            foreach($trabajadores as $trabajador) {
                $casos = ORM::factory('caso')->where('PER_TRAB_ID','=',$trabajador->PER_TRAB_ID)->find_all();
            }
        }       
        return $retorno;
    }
    
    /**
     * Retorna nombre completo de trabajador
     * @param void
     * @return array
     * @author jperez
     */
    public function nombre_completo() {
        return $this->nombres.' '.$this->apellido_paterno.' '.$this->apellido_materno;
    }
    
    /**
     *
     * @return Object La dirección completa 
     */
    public function direccion()
    {
      $tipo_calle = Kohana::$config->load('dominios.STTipoCalle');
      //$comunas    = Kohana::$config->load('dominios.STCodigo_comuna');
      $comuna = ORM::factory('comuna')->where('COM_ID', '=', $this->comuna)->find();
      return  (object) array(
          'nombre' =>  "{$tipo_calle[$this->tipo_calle]} {$this->nombre_calle}",
          'numero'  =>  $this->numero,
          'comuna'  =>  $comuna->com_nombre,
          'region'  =>  $comuna->region->regn_nmb
          );
    }
    
    /**
     *
     * @return Int  
     */
    public function edad()
    {
      return Utiles::edad($this->fecha_nacimiento);
    }
    
    /**
     *
     * @return Char 
     */
    public function sexo()
    {
      $tipo_sexo = Kohana::$config->load('dominios.STSexo');
      return $tipo_sexo[$this->sexo];
    }


    /**
     *
     * @param string $datos
     * @return <type>
     */
    public function insert($datos)
    {
      //var_dump($datos);
      $response  = NULL;
      $validation = Validation::factory($datos)
       //->rule('', 'min_length', array(':value', 1))
        //->rule('persona_email','Utiles::check_mail', array(':value'))
        ->rule('apellidoPaterno', 'not_empty')
        ->rule('apellidoMaterno', 'not_empty')
        ->rule('nombres', 'not_empty')
        ->rule('rut_dv', 'not_empty')
        ->rule('rut_dv', 'Utiles::rut',array(':value'))
        ->rule('fechaNacimiento', 'not_empty')
        ->rule('edad', 'not_empty')
        ->rule('sexo', 'not_empty')
        ->rule('tipo_calle', 'not_empty')
        ->rule('nombre_calle', 'not_empty')
        ->rule('numero', 'not_empty')
        ->rule('comuna', 'not_empty')
        ->rule('numero_telefono', 'not_empty')
        ->label('rut_dv', 'el rut del trabajador')
        ->label('nombres', 'el nombre del trabajador')
        ->label('apellidoPaterno', 'el apellido paterno')
        ->label('apellidoMaterno', 'el apellido materno')
        ->label('sexo', 'el sexo')
        ->label('fechaNacimiento', 'la fecha de nacimiento')
        ->label('tipo_calle', 'el tipo de calle')
        ->label('nombre_calle', 'el nombre de calle')
        ->label('numero', 'el número calle')
        ->label('comuna', 'la comuna trabajador')
        ->label('numero_telefono', 'el número de teléfono');

      if($validation->check())
      {
        //$this->fuente_informacion = $validation[''];
        $this->apellido_paterno = $validation['apellidoPaterno'];
        $this->apellido_materno = $validation['apellidoMaterno'];
        $this->nombres = $validation['nombres'];
        $this->rut = $validation['rut_dv'];
        $this->fecha_nacimiento = $validation['fechaNacimiento'];
        $this->edad = $validation['edad'];
        $this->sexo = $validation['sexo'];
        //$this->pais_nacionalidad = $validation[''];
        //$this->codigo_etnia = $validation[''];
        //$this->etnia_otro = $validation[''];
        $this->tipo_calle = $validation['tipo_calle'];
        $this->nombre_calle = $validation['nombre_calle'];
        $this->numero = $validation['numero'];
        //$this->localidad = $validation[''];
        $this->comuna = $validation['comuna'];
        //$this->region = $validation['region'];
        //$this->resto_direccion = $validation[''];
        //$this->profesion_trabajador = $validation[''];
        //$this->ciuo_trabajador = $validation[''];
        //$this->categoria_ocupacion = $validation[''];
        //$this->duracion_contrato = $validation[''];
        //$this->tipo_dependencia = $validation[''];
        //$this->tipo_remuneracion = $validation[''];
        $this->numero_telefono = $validation['numero_telefono'];
        //$this->cod_area = $validation[''];
        //$this->cod_pais = $validation[''];
        //$this->fecha_ingreso = $validation[''];
        //$this->clasificacion_trabajador = $validation[''];
        //$this->sistema_comun = $validation[''];

        $this->save();
        $response = $this->tra_id;
      }
      else
      {
        $response = $validation->errors('validate');
      }

      return $response;
    }
}
