<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Dominios extends Controller_Website {

    public function action_codigo_forma() {

        $codigo_forma=Controller_Dominios::codigo_forma();                
        $this->template->mensaje_error = NULL;
        $this->template->contenido = View::factory('dominios/codigo_forma')->set('codigo_forma',$codigo_forma);
    }

    public function action_codigo_agente_acc() {

        $codigos=Controller_Dominios::STCodigo_agente_accidente();                
        $this->template->mensaje_error = NULL;
        $this->template->contenido = View::factory('dominios/codigo_agente_acc')->set('codigos',$codigos);
    }

    public function action_codigo_intencionalidad() {

        $codigos=Controller_Dominios::STCodigo_intencionalidad();                
        $this->template->mensaje_error = NULL;
        $this->template->contenido = View::factory('dominios/codigo_intencionalidad')->set('codigos',$codigos);
    }

    public function action_codigo_modo_transporte() {
        $codigos=Controller_Dominios::STCodigo_modo_transporte();           
        $this->template->mensaje_error = NULL;
        $this->template->contenido = View::factory('dominios/codigo_modo_transporte')->set('codigos',$codigos);
    }
    public function action_codigo_papel_lesionado() {
        $codigos=Controller_Dominios::STCodigo_papel_lesionado();           
        $this->template->mensaje_error = NULL;
        $this->template->contenido = View::factory('dominios/codigo_papel_lesionado')->set('codigos',$codigos);
    }

    public function action_codigo_contraparte() {
        $codigos=Controller_Dominios::STCodigo_contraparte();           
        $this->template->mensaje_error = NULL;
        $this->template->contenido = View::factory('dominios/codigo_contraparte')->set('codigos',$codigos);
    }    


    public static function codigo_forma() {    
        $codigo_forma=array(
            11=>'Caídas de personas con desnivelación [caídas desde alturas (árboles, edificios, andamios, escaleras, máquinas de trabajo, vehículos) y en profundidades (pozos, fosos, excavaciones, aberturas en el suelo)].',
            12=>'Caídas de personas que ocurren al mismo nivel.',
            21=>'Derrumbe (caídas de masas de tierra, de rocas, de piedras, de nieve).',
            22=>'Desplome (de edificios, de muros, de andamios, de escaleras, de pilas de mercancías).',
            23=>'Caídas de objetos en curso de manutención manual.',
            24=>'Otras caídas de objetos.',
            31=>'Pisadas sobre objetos.',
            32=>'Choques contra objetos inmóviles (a excepción de choques debidos a una caída anterior).',
            33=>'Choque contra objetos móviles',
            34=>'Golpes por objetos móviles (comprendidos los fragmentos volantes y las partículas), a excepción de los golpes por objetos que caen.',
            41=>'Atrapada por un objeto.',
            42=>'Atrapada entre un objeto inmóvil y un objeto móvil.',
            43=>'Atrapada entre dos objetos móviles (a excepción de los objetos volantes o que caen)',
            51=>'Esfuerzos físicos excesivos al levantar objetos.',
            52=>'Esfuerzos físicos excesivos al empujar objetos o tirar de ellos.',
            53=>'Esfuerzos físicos excesivos al manejar o lanzar objetos.',
            54=>'Falsos movimientos',
            61=>'Exposición al calor (de la atmósfera o del ambiente de trabajo).',
            62=>'Exposición al frío (de la atmósfera o del ambiente de trabajo).',
            63=>'Contacto con sustancias u objetos ardientes.',
            64=>'Contacto con sustancias u objetos muy fríos.',
            7=>'Exposición a, o contacto con, la corriente eléctrica',
            81=>'Contacto por inhalación, por ingestión o por absorción con sustancias nocivas.',
            82=>'Exposición a radiaciones ionizantes.',
            83=>'Exposición a otras radiaciones.',
            91=>'Otras formas de accidente, no clasificadas bajo otros epígrafes.',
            92=>'Accidentes no clasificados por falta de datos suficientes.');

        return $codigo_forma;
    }

    public static function codigo_tipo_evento() {
        $tipo_evento = array(
            1 => 'Evento relacionado con la lesión de tránsito de transporte terrestre',
            2 => 'Evento relacionado con la lesión  de transporte terrestre no considerada de tránsito',
            3 => 'Evento relacionado con la lesión de transporte terrestre - sin especificar entre tránsito o no considerado de tránsito',
            4 => 'Vehículo de transporte como sitio del evento que ocasionó la lesión',
            5 => 'Choque o colisión de transporte acuático',
            6 => 'Choque o colisión de transporte aéreo o espacial',
            8 => 'Otro tipo de evento especificado, relacionado con la lesión de transporte',
            9 => 'Tipo de evento, no especificado, relacionado con la lesión de transporte',
        );
        return $tipo_evento;
    }

    public static function STLugarDefuncion() {
        $retorno = array(
            1 => "Mismo lugar del Accidente", 
            2 => "Traslado al Centro Asistencial",
            3 => "Centro Asistencial",
            4 => "Otro (indicar lugar)",
        );
        return $retorno;
    }

    public static function STCriterio_gravedad_RALF() {
        $retorno = array(
            1 => "Muerte del trabajador",
            2 => "Desaparecido producto del accidente",
            3 => "Maniobras de reanimación",
            4 => "Maniobras de rescate",
            5 => "Caída de altura de más de 1,8 m.",
            6 => "Amputación traumática",
            7 => "Número de trabajadores afecta el desarrollo normal de la faena",
            8 => "Accidente en condición hiperbárica"
        );
        return $retorno;
    }

    public static function STTipoCalle() {
        $retorno = array(
            1 => "Avenida",
            2 => "Calle",
            3 => "Pasaje",
            );
        return $retorno;
    }

    public static function si_no() {
        $retorno = array(
            1 => "Sí",
            2 => "No",
            3 => "No corresponde"
        );
        return $retorno;
    }

    public static function STTipoJornada() {
            return array(
                1 => "Jornada Ordinaria (Con/Sin Turno)",
                2 => "Jornada Extraordinaria",
                3 => "Jornada Excepcional (Con/Sin Turno)",
                4 => "Otra (Indicar cual)",
            );
        }

        public static function STLugarTrabajo() {
            return array(
                1 => "Casa Matriz",
                2 => "Sucursal Empresa",
            );
        }

        public static function STDiasJornadaParcial() {
            return array(
                1 => "1 día",
                2 => "1,5 días",
                3 => "2 días",
                4 => "2,5 días",
                5 => "3 días",
                6 => "3,5 días",
                7 => "4 días"
            );
        }

        public static function STCategoriaExperto() {
            return array(
                1 => "Profesional",
                2 => "Técnico",
                3 => "Practico",
            );
        }

        public static function STOrg_multas() {
            return array(
                1 => "Dirección del Trabajo",
                2 => "Seremi de Salud",
            );
        }

        public static function STTipoContratoExperto() {
            return array(
                1 => "Honorarios Jornada Parcial",
                2 => "Honorarios Jornada Completa",
                3 => "Contrato Indefinido Jornada Parcial",
                4 => "Contrato Indefinido Jornada Completa",
                5 => "Contrato Plazo Fijo Jornada Parcial",
                6 => "Contrato Plazo Fijo Jornada Completa",
                7 => "Otro",
            );
        }

        public static function STCodigo_agente_accidente() {
            return array(
                111 => 'Máquinas de vapor.', 
                112 => 'Máquinas de combustión interna.', 
                119 => 'Otros', 
                121 => 'Arboles de transmisión.', 
                122 => 'Correas, cables, poleas, cadenas, engranajes.', 
                129 => 'Otros.', 
                131 => 'Prensas mecánicas.', 
                132 => 'Tornos.', 
                133 => 'Fresadoras.', 
                134 => 'Rectificadoras y muelas.', 
                135 => 'Cizallas.', 
                136 => 'Forjadoras.', 
                137 => 'Laminadoras.', 
                139 => 'Otras.', 
                141 => 'Sierras circulares.', 
                142 => 'Otras sierras.', 
                143 => 'Máquinas de moldurar.', 
                144 => 'Cepilladoras.', 
                149 => 'Otras.', 
                151 => 'Segadoras, incluso segadoras-trilladoras.', 
                152 => 'Trilladoras.', 
                159 => 'Otras.', 
                161 => 'Máquinas de rozar.', 
                169 => 'Otras.', 
                191 => 'Máquinas para desmontes, excavaciones, etc., a excepción de los medios de transporte.', 
                192 => 'Máquinas de hilar, de tejer y otras máquinas para la industria textil.', 
                193 => 'Máquinas para la manufactura de productos alimenticios y bebidas.', 
                194 => 'Máquinas para la fabricación del papel.', 
                195 => 'Máquinas de imprenta.', 
                199 => 'Otras.', 
                211 => 'Grúas.', 
                212 => 'Ascensores, montacargas.', 
                213 => 'Cabrestantes.', 
                214 => 'Poleas.', 
                219 => 'Otros.', 
                221 => 'Ferrocarriles interurbanos.', 
                222 => 'Equipos de transporte por vía férrea utilizados en las minas, las galerías, las canteras, los establecimientos industriales, los muelles, etc.', 
                229 => 'Otros.', 
                231 => 'Tractores.', 
                232 => 'Camiones.', 
                233 => 'Carretillas motorizadas.', 
                234 => 'Vehículos motorizados no clasificados bajo otros epígrafes.', 
                235 => 'Vehículos de tracción animal.', 
                236 => 'Vehículos accionados por la fuerza del hombre.', 
                239 => 'Otros.', 
                24 => 'Medios de transporte por aire.', 
                251 => 'Medios de transporte por agua con motor.', 
                252 => 'Medios de transporte por agua sin motor.', 
                261 => 'Transportadores aéreos por cable.', 
                262 => 'Transportadores mecánicos a excepción de los transportadores aéreos por cable.', 
                269 => 'Otros.', 
                311 => 'Calderas.', 
                312 => 'Recipientes de presión sin fogón.', 
                313 => 'Cañerías y accesorios de presión.', 
                314 => 'Cilindros de gas.', 
                315 => 'Cajones de aire comprimido, equipo de buzo.', 
                319 => 'Otros.', 
                321 => 'Altos hornos.', 
                322 => 'Hornos de refinería.', 
                323 => 'Otros hornos.', 
                324 => 'Estufas.', 
                325 => 'Fogones.', 
                33 => 'Plantas refrigeradoras.', 
                341 => 'Máquinas giratorias.', 
                342 => 'Conductores y cables eléctricos.', 
                343 => 'Transformadores.', 
                344 => 'Aparatos de mando y de control.', 
                349 => 'Otros.', 
                35 => 'Herramientas eléctricas manuales.', 
                361 => 'Herramientas manuales accionadas mecánicamente a excepción de las herramientas eléctricas manuales.', 
                362 => 'Herramientas manuales no accionadas mecánicamente.', 
                369 => 'Otros.', 
                37 => 'Escaleras, rampas móviles.', 
                38 => 'Andamios.', 
                39 => 'Otros aparatos no clasificados bajo otros epígrafes.', 
                41 => 'Explosivos.', 
                421 => 'Polvos.', 
                422 => 'Gases, vapores, humos.', 
                423 => 'Líquidos no clasificados bajo otros epígrafes.', 
                424 => 'Productos químicos no clasificados bajo otros epígrafes.', 
                429 => 'Otros.', 
                43 => 'Fragmentos volantes.', 
                441 => 'Radiaciones ionizantes.', 
                449 => 'Radiaciones de otro tipo.', 
                49 => 'Otros materiales y sustancias no clasificados bajo otros epígrafes.', 
                511 => 'Condiciones climáticas.', 
                512 => 'Superficies de tránsito y de trabajo.', 
                513 => 'Agua.', 
                519 => 'Otros.', 
                521 => 'Pisos.', 
                522 => 'Espacios exiguos.', 
                523 => 'Escaleras.', 
                524 => 'Otras superficies de tránsito y de trabajo.', 
                525 => 'Aberturas en el suelo y en las paredes.', 
                526 => 'Factores que crean el ambiente (alumbrado, ventilación, temperatura, ruidos, etc.).', 
                529 => 'Otros.', 
                531 => 'Tejados y revestimientos de galerías, de túneles, etc.', 
                532 => 'Pisos de galerías, de túneles, etc.', 
                533 => 'Frentes de minas, túneles, etc.', 
                534 => 'Pozos de minas.', 
                535 => 'Fuego.', 
                536 => 'Agua.', 
                539 => 'Otros.', 
                611 => 'Animales vivos.', 
                612 => 'Productos de animales.', 
                69 => 'Otros agentes no clasificados bajo otros epígrafes.', 
                7 => 'Agentes no clasificados por falta de datos suficientes', 
            );
        }

        public static function STCodigo_intencionalidad() {
            return array(
                1 => "No intencional",
                2 => "Daño intencional auto infligido",
                3 => "Agresión",
                4 => "Otro tipo de Violencia",
                5 => "Intencionalidad no determinada",
                6 => "Complicaciones de atención médica o quirúrgica",
                8 => "Otro tipo de intencionalidad específica",
                9 => "Intencionalidad no específica"
            );
        }

        public static function STCodigo_papel_lesionado() {
            return array(
                1 => "Persona a pie, transeúnte",
                2 => "Conductor u operario",
                3 => "Pasajero",
                4 => "Persona que aborda o se baje de un vehículo",
                5 => "Persona en la parte exterior de un vehículo",
                6 => "Ocupante de vehículo no especificado de otra forma",
                8 => "Otro papel de la persona lesionada especificado",
                9 => "Rol de la persona lesionada no especificado"
            );
        }

        public static function STCodigo_contraparte() {
            return array(
                "1.1"=>'Persona a pie',
                "1.2"=>'Persona que use un dispositivo de transporte peatonal',
                "2"=>'Vehículo de pedal',
                "3.1"=>'Vehículo de tracción animal',
                "3.2"=>'Animal montado',
                "3.8"=>'Otro dispositivo de transporte no motorizado especificado',
                "3.9"=>'Dispositivo de transporte no motorizado no especificado',
                "4.1"=>'Bicicleta motorizada',
                "4.2"=>'Motocicleta',
                "4.8"=>'Otro vehículo motorizado de dos ruedas especificado',
                "4.9"=>'Vehículo motorizado de dos ruedas no especificado',
                "5"=>'Vehículo motorizado de tres ruedas',
                "6.1"=>'Carro motorizado, vehículo station wagon, furgoneta pequeña para pasajeros, vehículo tipo jeep, vehículo utilitario deportivo, 4x4',
                "6.2"=>'minibús, furgoneta de pasajeros',
                "6.3"=>'Camioneta de platón, furgoneta para transportar bienes o de trabajo, ambulancia, carro casa',
                "6.4"=>'Vehículo de transporte liviano de cuatro o más ruedas utilizado en actividades deportivas y de tiempo libre',
                "6.8"=>'Otro vehículo de transporte liviano de cuatro o más ruedas especificado',
                "6.9"=>'Vehículo de transporte liviano de cuatro o más ruedas no especificado',
                "7.1"=>'Bus',
                "7.2"=>'Camión',
                "7.8"=>'Otro vehículo de transporte pesado especificado',
                "7.9"=>'Vehículo de transporte pesado no especificado',
                "8.1"=>'Tren ferrocarril',
                "8.2"=>'Tranvía',
                "8.3"=>'Funicular, monocarril',
                "8.8"=>'Otro vehículo férreo  especificado',
                "8.9"=>'Vehículo férreo  no especificado',
                "9.1"=>'Vehículo especial usado en la industria',
                "9.2"=>'Vehículo especial usado en la agricultura',
                "9.3"=>'Vehículo especial usado en la construcción',
                "10.1"=>'Motonieve',
                "10.2"=>'Aerodeslizador que transite en el suelo o en pantanos',
                "10.8"=>'Otro vehículo todo terreno especificado',
                "10.9"=>'Vehículo todo terreno no especificado',
                "11.1"=>'Barco mercante',
                "11.2"=>'Barco de servicio público (de pasajeros)',
                "11.3"=>'Bote de pesca, barco de arrastre',
                "11.4"=>'Otro vehículo acuático motorizado especificado',
                "11.5"=>'Velero, yate sin motor',
                "11.8"=>'Otro vehículo acuático sin motor',
                "11.9"=>'Vehículo acuático, no especificado como motorizado o sin motor',
                "12.1"=>'Aeronave con motor',
                "12.2"=>'Aeronave sin motor',
                "12.4"=>'Nave espacial',
                "12.5"=>'Paracaídas utilizado al saltar de una aeronave con averías',
                "12.6"=>'Paracaídas utilizado al saltar de una aeronave en buenas condiciones',
                "12.9"=>'Aeronave no especificada',
                "13.1"=>'Vehículo estacionado a un lado de la carretera o en un estacionamiento de vehículos',
                "13.2"=>'Objeto pequeño desprendido',
                "13.3"=>'Objeto fijo pequeño o ligero',
                "13.4"=>'Objeto fijo grande o pesado',
                "13.8"=>'Otro objeto estacionario o fijo especificado',
                "13.9"=>'Objeto estacionario o fijo no especificado',
                "14.1"=>'Animal descuidado',
                "14.2"=>'Animal arreado',
                "14.8"=>'Otro animal especificado',
                "14.9"=>'Animal no especificado',
                "15.1"=>'Movimiento repentino de un vehículo, sin colisión, que resulte en lesión',
                "15.2"=>'Volcada de un vehículo sin colisión',
                "15.9"=>'Sin contraparte: no especificado',
                "98"=>'Otra contraparte especificada',
                "99"=>'Contraparte no especificada'
            );
        }

        public static function STCodigo_Tipo_evento() {
            return array(
                1 => "Evento relacionado con la lesión de tránsito de transporte terrestre",
                2 => "Evento relacionado con la lesión de transporte terrestre no considerada de tránsito",
                3 => "Evento relacionado con la lesión de transporte terrestre - sin especificar entre tránsito o no considerado de tránsito",
                4 => "Vehículo de transporte como sitio del evento que ocasionó la lesión",
                5 => "Choque o colisión de transporte acuático",
                6 => "Choque o colisión de transporte aéreo o espacial",
                8 => "Otro tipo de evento especificado, relacionado con la lesión de transporte",
                9 => "Tipo de evento, no especificado, relacionado con la lesión de transporte"
            );
        }

        public static function STCodigo_modo_transporte() {
            return array(
                '1.1' => 'Persona a pie', 
                '1.2' => 'Persona que use un dispositivo de transporte peatonal', 
                '2' => 'Vehículo de pedal', 
                '3.1' => 'Vehículo', 
                '3.2' => 'Animal montado', 
                '3.8' => 'Otro dispositivo de transporte no motorizado especificado', 
                '3.9' => 'Dispositivo de transporte no motorizado no especificado', 
                '4.1' => 'Bicicleta motorizada', 
                '4.2' => 'Motocicleta', 
                '4.8' => 'Otro vehículo motorizado de dos ruedas especificado', 
                '4.9' => 'Vehículo motorizado de dos ruedas no especificado', 
                '5' => 'Vehículo motorizado de tres ruedas', 
                '6.1' => 'Carro motorizado, vehículo \station wagon\, furgoneta pequeña para pasajeros, vehículo tipo \jeep\, vehículo utilitario deportivo, 4x4', 
                '6.2' => 'minibús, furgoneta de pasajeros', 
                '6.3' => 'Camioneta de platón, furgoneta de bienes o de trabajo, ambulancia, carro casa', 
                '6.4' => 'Vehículo de transporte liviano de cuatro o más ruedas utilizado en actividades deportivas y de tiempo libre', 
                '6.8' => 'Otro vehículo de transporte liviano de cuatro o más ruedas especificado', 
                '6.9' => 'Vehículo de transporte liviano de cuatro o más ruedas no especificado', 
                '7.1' => 'Bus', 
                '7.2' => 'Camión', 
                '7.8' => 'Otro vehículo de transporte pesado especificado', 
                '7.9' => 'Vehículo de transporte pesado no especificado', 
                '8.1' => 'Tren ferrocarril', 
                '8.2' => 'Tranvía', 
                '8.3' => 'Funicular, monocarril', 
                '8.8' => 'Otro vehículo férreo  especificado', 
                '8.9' => 'Vehículo férreo  no especificado', 
                '9.1' => 'Vehículo especial utilizado principalmente en la industria', 
                '9.2' => 'Vehículo especial utilizado principalmente en la agricultura', 
                '9.3' => 'Vehículo especial utilizado principalmente en la construcción', 
                '10.1' => 'Motonieve', 
                '10.2' => 'Aerodeslizador que transite en el suelo o en pantanos', 
                '10.8' => 'Otro vehículo todo terreno especificado', 
                '10.9' => 'Vehículo todo terreno no especificado', 
                '11.1' => 'Barco mercante', 
                '11.2' => 'Barco de servicio público (de pasajeros)', 
                '11.3' => 'Bote de pesca, barco de arrastre', 
                '11.4' => 'Otro vehículo acuático motorizado especificado', 
                '11.5' => 'Velero, yate sin motor', 
                '11.8' => 'Otro vehículo acuático sin motor especificado', 
                '11.9' => 'Vehículo acuático, no especificado como motorizado o sin motor', 
                '12.1' => 'Aeronave', 
                '12.2' => 'Aeronave sin motor', 
                '12.4' => 'Nave espacial', 
                '12.5' => 'Paracaídas utilizado al saltar de una aeronave con averías', 
                '12.6' => 'Paracaídas utilizado al saltar de una aeronave en buenas condiciones', 
                '12.9' => 'Aeronave no especificada', 
                '98' => 'Otro modo de transporte especificado', 
                '99' => 'Modo de transporte no especificado', 
            );
        }

        public static function STCodigo_forma() {
            return array(
                11 => 'Caídas de personas con desnivelación [caídas desde alturas (árboles, edificios, andamios, escaleras, máquinas de trabajo, vehículos) y en profundidades (pozos, fosos, excavaciones, aberturas en el suelo)].', 
                12 => 'Caídas de personas que ocurren al mismo nivel.', 
                21 => 'Derrumbe (caídas de masas de tierra, de rocas, de piedras, de nieve).', 
                22 => 'Desplome (de edificios, de muros, de andamios, de escaleras, de pilas de mercancías).', 
                23 => 'Caídas de objetos en curso de manutención manual.', 
                24 => 'Otras caídas de objetos.', 
                31 => 'Pisadas sobre objetos.', 
                32 => 'Choques contra objetos inmóviles (a excepción de choques debidos a una caída anterior).', 
                33 => 'Choque contra objetos móviles.', 
                34 => 'Golpes por objetos móviles (comprendidos los fragmentos volantes y las partículas), a excepción de los golpes por objetos que caen.', 
                41 => 'Atrapada por un objeto.', 
                42 => 'Atrapada entre un objeto inmóvil y un objeto móvil.', 
                43 => 'Atrapada entre dos objetos móviles (a excepción de los objetos volantes o que caen).', 
                51 => 'Esfuerzos físicos excesivos al levantar objetos.', 
                52 => 'Esfuerzos físicos excesivos al empujar objetos o tirar de ellos.', 
                53 => 'Esfuerzos físicos excesivos al manejar o lanzar objetos.', 
                54 => 'Falsos movimientos.', 
                61 => 'Exposición al calor (de la atmósfera o del ambiente de trabajo).', 
                62 => 'Exposición al frío (de la atmósfera o del ambiente de trabajo).', 
                63 => 'Contacto con sustancias u objetos ardientes.', 
                64 => 'Contacto con sustancias u objetos muy fríos.', 
                7 => 'Exposición a, o contacto con, la corriente eléctrica', 
                81 => 'Contacto por inhalación, por ingestión o por absorción con sustancias nocivas.', 
                82 => 'Exposición a radiaciones ionizantes.', 
                83 => 'Exposición a otras radiaciones.', 
                91 => 'Otras formas de accidente, no clasificadas bajo otros epígrafes.', 
                92 => 'Accidentes no clasificados por falta de datos suficientes.', 
            );
        }

	public static function STNumSEREMI() {
		return array(	
			1 => 'Seremi de Salud de la Región de Arica y Parinacota',
			2 => 'Seremi de Salud de la Región de Tarapacá',
			3 => 'Seremi de Salud de la Región de Antofagasta',
			4 => 'Seremi de Salud de la Región de Atacama',
			5 => 'Seremi de Salud de la Región de Coquimbo',
			6 => 'Seremi de Salud de la Región de Valparaíso',
			7 => 'Seremi de Salud de la Región Metropolitana',
			8 => "Seremi de Salud de la Región del Libertador General Bernardo O'Higgins",
			9 => 'Seremi de Salud de la Región del Maule',
			10 => 'Seremi de Salud de la Región del Biobío',
			11 => 'Seremi de Salud de la Región de La Araucanía',
			12 => 'Seremi de Salud de la Región de Los Ríos',
			13 => 'Seremi de Salud de la Región de Los Lagos',
			14 => 'Seremi de Salud de la Región de Aisén del General Carlos Ibáñez del Campo',
			15 => 'Seremi de Salud de la Región de Magallanes y la Antártica Chilena',
		);	
	}

}

// End Caso
