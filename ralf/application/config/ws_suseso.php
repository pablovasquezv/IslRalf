<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(   
    //CORRESPONDE A QA
    'wsdl_token_prod'=>"http://siatepqa.suseso.cl:8888/Siatep/WSToken?wsdl",

    //CORRESPONDE A PRODUCCION
    //'wsdl_token_prod'=>"http://siatep.suseso.cl:8888/SusesoSiatep/WSToken?wsdl",
    
    //CORRESPONDE A QA
    'wsdl_ingreso_prod'=>"http://sisesatqa.suseso.cl/server/wsdl",

    //CORRESPONDE A PRODUCCION
    //'wsdl_ingreso_prod'=>"http://sisesat.suseso.cl/server/wsdl",

    
    
    'usuario' => 'isl',
    'clave'=>'345612',
    
    'codigos_retorno' => array(
        '-21' => 'XML mal formado.',
        '-22' => 'Mensaje de Schema XML.',
        '-23' => 'La firma del documento no es válida.',
        '-24' => 'Algunas zonas del documento no han sido incluidas en la firma (excluyendo la zona O). ',
        '-25' => 'Certificado X509 de la firma es inválido.',
        '-31' => 'El Rut del empleador no existe o es inválido. Validación módulo – 11. ',
        '-32' => 'El Rut del trabajador no existe o es inválido. Validación módulo – 11. ',
        '-33' => 'El Rut del denunciante no existe o es inválido. Validación módulo – 11. ',
        '-34' => 'La fecha de diagnóstico debe ser mayor a la fecha de ingreso al trabajo actual. ',
        '-36' => 'La fecha del accidente debe ser mayor a la fecha de ingreso al trabajo actual.',
        '-38' => 'El Rut del médico no existe o es inválido. Validación módulo – 11.',
        '-39' => 'El Rut de la entidad no existe o es inválido. No corresponde por tipo de origen de la última entidad ingresado.',
        '-311' => 'La fecha de término de la incapacidad temporal debe ser mayor o igual a la fecha de inicio de la incapacidad temporal.',
        '-312' => 'El número de días de incapacidad temporal debe ser igual a la diferencia entre la fecha de término de la incapacidad temporal menos la fecha de inicio de la incapacidad temporal.',
        '-313' => 'El Rut del calificador no existe o es inválido. Validación módulo – 11.',
        '-314' => 'La función no es ingreso de documentos XML.',
        '-315' => 'El tipo de documento no corresponde: 1= DIAT OA;2= DIEP OA; 3= DIAT OE; 4= DIEP OE; 5= DIAT OT; 6= DIEP OT; 7= RECA; 8= RELA; 9= ALLA; 10 = ALME; 11= REIP; ',
        '-316' => 'Fecha de emisión ser menor o igual a la fecha actual. ',
        '-317' => 'Nombre Calle del pueden estar vacíos. ',
        '-318' => 'Nombres o Apellido Paterno o Materno del empleado no pueden estar vacíos. ',
        '-319' => 'Fecha Nacimiento del empleado debe ser menor a la fecha actual. ',
        '-320' => 'Nombre Calle del empleado no pueden estar vacíos. ',
        '-321' => 'La fecha de incorporación del trabajador es mayor a la fecha actual. ',
        '-327' => 'El Rut del ministro de fe no eiste o es inválido. Validación módulo – 11. ',
        '-328' => 'Fecha Nacimiento del ministro de fe debe ser menor o igual a la fecha actual. ',
        '-330' => 'Rut del encargado comisión invalido. ',
        '-331' => 'Fecha Nacimiento del encargado comisión debe ser menor o igual a la fecha actual.',
        '-332' => 'La fecha de nacimiento del calificador debe ser menor a la fecha actual. ',
        '-334' => 'Fecha de emisión del documento debe mayor o igual a la fecha mínima permitida (01/01/2005) ',
        '-335' => 'Fecha Accidente debe ser mayor o igual a la fecha mínima permitida (60 años antes de la fecha actual) ',
        '-336' => 'El código emisor del corresponde al organismo administrador que invocó el webservice. ',
        '-337' => 'CUN del documento no coincide con CUN de llamada al webservice. ',
        '-338' => 'Documento no puede tener zona de accidente y zona de enfermedad a la vez. ',
        '-339' => 'El documento debe tener la zona de accidente si en el caso hay DIEP y se calificó como accidente de trabajo, de trayecto, o laboral sin incapacidad. ',
        '-340' => 'El documento debe tener la zona de  enfermedad si en el caso hay DIAT y se calificó como enfermedad profesional o laboral sin incapacidad. ',
        '-341' => 'Fecha de nacimiento del trabajador  debe ser mayor o igual al 1/1/1900. ',
        '-342' => 'Fecha de ingreso al trabajo debe ser mayor o igual al 1/1/1900. ',
        '-343' => 'Fecha de accidente debe ser menor o igual a la fecha actual.',
        '-344' => 'Fecha de síntoma enfermedad debe ser mayor o igual al 1/1/1900. ',
        '-345' => 'Fecha de síntoma enfermedad debe ser menor o igual a la fecha actual. ',
        '-346' => 'Fecha de exposición agente enfermedad debe ser mayor o igual al 1/1/1900. ',
        '-347' => 'Fecha de exposición agente válido enfermedad debe ser menor o igual a la fecha actual.',
        '-348' => 'Fecha de diagnóstico debe ser menor o igual a la fecha actual. ',
        '-349' => 'La fecha de nacimiento del calificador debe ser mayor o igual al 1/1/1900. ',
        '-40' => 'Ingreso de documento ha sido exitoso. ',
        '-41' => 'Error de grabación de la información en Disco. La entidad Administradora deberá comunicarse con el área de Soporte de la SUSESO. ',
        '-44' => 'DIAT ya existe.',
        '-45' => 'DIEP ya existe.',
        '-48' => 'No existe denuncia. Para completar el proceso de ingreso del documento debe existir un DIAT OA o DIEP OA antes de realizar esta acción.',
        '-410' => 'Problemas al accesar la base de datos. ',
        '-411' => 'Problema al instanciar parser XML. ',
        '-412' => 'Error al Grabar en Bitácora.',
        '-413' => 'CUN no existe.',
        '-51' => 'Los argumentos recibidos no corresponden con la función indicada.',
        '-52' => 'Argumentos sin valores. En documentos RECA y posteriores, puede significar que no viene CUN en la llamada al servicio o dentro del documento XML. ',
        '-53' => 'Usuario sin perfil para realizar la función. ',
        '-55' => 'Usuario con Password vencida. EN DESUSO.',
        '-56' => 'Sistema no disponible.',
        '-57' => 'Problemas al llamar a WS Privilegio.',
        '-58' => 'Cta usuario y/o clave no validos. ',
    )
);