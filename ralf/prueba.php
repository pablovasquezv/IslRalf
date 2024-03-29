<?php

$doc = new DOMDocument();

// Setting formatOutput to true will turn on xml formating so it looks nicely
// however if you load an already made xml you need to strip blank nodes if you want this to work
$doc->load('test.xml', LIBXML_NOBLANKS);
$doc->formatOutput = true;
// Get the root element "links"
$root = $doc->documentElement;
// Create new link element
$link = $doc->createElement("link");
// Create and add id to new link element
$id = $doc->createElement("id","298312800");
$link->appendChild($id);
// Create and add href to new link element
$href = $doc->createElement("href","www.anysite.com");
$link->appendChild($href);
// Append new link to root element
$root->appendChild($link);
print $doc->save('test.xml');
echo "the link has been added!";



$ralf='<?xml version="1.0"?>
<ralf2 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://www.w3.org/2001/04/xmlenc#" xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xsi:schemaLocation="http://www.w3.org/2001/04/xmlenc# xenc-schema.xsd    http://www.w3.org/2000/09/xmldsig# xmldsig-core-schema.xsd" xsi:noNamespaceSchemaLocation="SISESAT_TYPES_1.0.xsd" id="z_padre">
  <ZONA_A>
    <documento>
      <cun>4033185</cun>
      <folio>9595</folio>
      <fecha_emision>2017-05-15T16:49:21</fecha_emision>
      <codigo_org_admin>21</codigo_org_admin>
      <codigo_emisor>21</codigo_emisor>
      <codigo_caso>497232</codigo_caso>
      <validez>1</validez>
      <origen_informacion>1</origen_informacion>
    </documento>
  </ZONA_A>
  
<ZONA_B>
        <empleador>
            <rut_empleador>76651061-2</rut_empleador>
            <nombre_empleador>Obras Menores Sergio Saldivia Quiroz EIRL </nombre_empleador>
            <direccion_empleador>
                <tipo_calle>3</tipo_calle>
                <nombre_calle>Rio Mayo </nombre_calle>
                <numero>2392</numero>
                <resto_direccion>Coyhaique</resto_direccion>
                <localidad>111</localidad>
                <comuna>11101</comuna>
            </direccion_empleador>
            <ciiu_empleador>454000</ciiu_empleador>
                <ciiu_texto>Obras menores en construcci&#xF3;n (alba&#xF1;iles, carpinteros)</ciiu_texto>
            <n_trabajadores>6</n_trabajadores>
            <n_trabajadores_hombre>6</n_trabajadores_hombre>
            
            <tipo_empresa>2</tipo_empresa>
            <ciiu2_empleador>454000</ciiu2_empleador>
            <ciiu2_texto>Obras menores en construcci&#xF3;n (alba&#xF1;iles, carpinteros)</ciiu2_texto>
            <propiedad_empresa>1</propiedad_empresa>
            <telefono_empleador>
                <cod_pais>56</cod_pais>
                <cod_area>9</cod_area>
                <numero>992446073</numero>
            </telefono_empleador>
	    <rut_representante_legal>7588082-0</rut_representante_legal>
            <nombre_representante_legal>SERGIO MARIANO SALDIVIA QUIROZ</nombre_representante_legal>
            <tasa_ds110>2.5</tasa_ds110>
            <tasa_ds67>0</tasa_ds67>
            <ultima_eval_ds67>1</ultima_eval_ds67>
            <nro_sucursales>0</nro_sucursales>
            <promedio_anual_trabajadores>1</promedio_anual_trabajadores>
        </empleador>
    </ZONA_B>

  <ZONA_C>
    <empleado>
      <trabajador>
        <apellido_paterno>Saldivia</apellido_paterno>
        <apellido_materno>Quiroz</apellido_materno>
        <nombres>Carlos Alberto </nombres>
        <rut>11910726-1</rut>
        <fecha_nacimiento>1971-01-18</fecha_nacimiento>
        <edad>46</edad>
        <sexo>1</sexo>
        <pais_nacionalidad>152</pais_nacionalidad>
      </trabajador>
      <direccion_trabajador>
        <tipo_calle>1</tipo_calle>
        <nombre_calle>Camino a Mayer Km. 17 </nombre_calle>
        <numero>17</numero>
        <resto_direccion>Sector interior de la comuna de Villa O</resto_direccion>
        <localidad>113</localidad>
        <comuna>11302</comuna>
      </direccion_trabajador>
      <profesion_trabajador>Operador Excavadora </profesion_trabajador>
      <ciuo_trabajador>3141</ciuo_trabajador>
      <categoria_ocupacion>2</categoria_ocupacion>
      <duracion_contrato>1</duracion_contrato>
      <tipo_dependencia>1</tipo_dependencia>
      <tipo_remuneracion>1</tipo_remuneracion>
      <fecha_ingreso>2015-01-06</fecha_ingreso>
    </empleado>
  </ZONA_C>
  
  
  

<ZONA_P><accidente_fatal><fecha_accidente>2017-03-13</fecha_accidente><hora_accidente>00:00:00</hora_accidente><direccion_accidente><tipo_calle>2</tipo_calle><nombre_calle>RIO MAYER SECTOR FUNDO LAS MARGARITAS, VILLA O&#xB4;HIGGINS</nombre_calle><numero>0</numero><comuna>11302</comuna></direccion_accidente><gravedad><criterio_gravedad>1</criterio_gravedad></gravedad><fecha_defuncion>2017-03-13</fecha_defuncion><lugar_defuncion>1</lugar_defuncion><descripcion_accidente_ini>De acuerdo a los primeros antecedentes con que se cuentan, se tratar&#xED;a de accidente ocurrido

mientras se proced&#xED;a a cruzar una m&#xE1;quina excavadora a trav&#xE9;s de un puente tipo Mecano el cu&#xE1;l

por motivos que se investigan, cedi&#xF3; y a consecuencia de ello la maquina se precipito al R&#xED;o Mayer

con el Operario en su interior, Sr. CARLOS ALBERTO SALDIVIA QUIROZ.

La ca&#xED;da se produce aproximadamente desde 20 metros de altura y hasta el momento no se tiene

informaci&#xF3;n sobre el rescate del accidentado, quien presumiblemente se encontrar&#xED;a fallecido.</descripcion_accidente_ini><informante_oa><apellido_paterno>Soto</apellido_paterno><apellido_materno>Hernandez</apellido_materno><nombres>Gonzalo Manuel</nombres><rut>9716685-4</rut></informante_oa></accidente_fatal></ZONA_P>
<ZONA_Q>
        <medidas_inmediatas>
            <medidas/>
            <fecha_notificacion_medidas_inmediatas>2017-08-01</fecha_notificacion_medidas_inmediatas>        
            <investigador>
                <apellido_paterno>SOTO</apellido_paterno>
                <apellido_materno>HERNANDEZ</apellido_materno>
                <nombres>GONZALO</nombres>
                <rut>9716685-4</rut>
            </investigador>
            <telefono_investigador>
                <cod_pais/>
                <cod_area>02</cod_area>
                <numero>23937897</numero>
            </telefono_investigador>
       </medidas_inmediatas>
    </ZONA_Q><ZONA_O><seguridad>Seguridad ISL</seguridad></ZONA_O></ralf2>';

        $doc=simplexml_load_string($ralf);
       // $dom = dom_import_simplexml($doc);
       // $element = $dom->appendChild(new DOMElement('documentos_anexos'));
     

        
		//Aqui agregar metodo que valide si existe tag documentos_anexos
        print_r($doc) ;


        // $doc = new SimpleXMLElement($ralf);
        //print_r("<brs>tag".$ralf->ZONA_Q->medidas_inmediatas->documentos_anexos);
          if(!isset($doc->ZONA_Q->medidas_inmediatas->documentos_anexos)){

            print_r("No esta");
            $dom = dom_import_simplexml($doc->ZONA_Q->medidas_inmediatas->children());
             //$dom->documentElement->appendChild($parent);



             if(isset($doc->ZONA_Q->medidas_inmediatas->investigador)){
              
           // $parent = $dom->createElement('documentos_anexos');2
                //$parent=$dom->ownerDocument->createElement('documentos_anexos', '');
                $dom->insertBefore(
                $dom->ownerDocument->createElement('documentos_anexos', ''), $dom->firstChild
                );
                 //$n = $dom->createElement($parent);
                 //$n->appendChild( $dom->createTextNode( $parent ) );
                // $parent->appendChild($n);              
             }

            //$ralf=$ralf->ZONA_Q->medidas_inmediatas->addChild('documentos_anexos');

           //$ralf=$ralf->ZONA_Q->medidas_inmediatas->addChild('documentos_anexos');
           //$ralf= insertBefore('documentos_anexos',$ralf->ZONA_Q->medidas_inmediatas->fecha_notificacion_medidas_inmediatas);

        }
         echo "<br>";
         // $ralf=simplexml_load_string($ralf);
         print_r($doc);



$sxe = simplexml_load_string('<root/>');
    
    // get a dom interface on the simplexml object
    $dom = dom_import_simplexml($sxe);

    // dom adds a new element under the root
    $element = $dom->appendChild(new DOMElement('dom_element'));
    
    // dom adds an attribute on the new element
    $element->setAttribute('creator', 'dom');

    // simplexml adds an attribute on the dom element
    $sxe->dom_element['sxe_attribute'] = 'added by simplexml';

    // simplexml adds a new element under the root
    $element = $sxe->addChild('sxe_element');
    
    // simplexml adds an attribute on the new element
    $element['creator'] = 'simplexml';

    // dom finds the simplexml element (via DOMNodeList->index)
    $element = $dom->getElementsByTagName('sxe_element')->item(0);

    // dom adds an attribute on the simplexml element
    $element->setAttribute('dom_attribute', 'added by dom');
    
    echo ('<pre>');            
   // print_r($sxe);
    echo ('</pre>');







function addUser($tag, $hash) {
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->load('test.xml');

    $parent = $dom->createElement($tag);
    $dom->documentElement->appendChild($parent);
    foreach($hash as $elm => $value){
        $n = $dom->createElement($elm);
        $n->appendChild( $dom->createTextNode( $value ) );
        $parent->appendChild($n);
    }

    $dom->save('test.xml');
}

$arr = array( 'name' => 'pushpesh', 'age' => 30, 'profession' => 'SO bugger' );
addUser('user', $arr);        



?>