<?php
$ciiu=(string)$xml->ZONA_B->empleador->ciiu_empleador;
$ciiu2 = (string)$xml->ZONA_B->empleador->ciiu2_empleador;

$ciiu1_empleador=Tipos::codigo($ciiu,'STCIIU');
$ciiu2_empleador=Tipos::codigo($ciiu2,'STCIIU');

$cod_comuna_emp=(string)$xml->ZONA_B->empleador->direccion_empleador->comuna;
$comuna_empleador=Tipos::codigo($cod_comuna_emp,'STCodigo_comuna');

$cod_tipo_calle_empleado=(int)$xml->ZONA_B->empleador->direccion_empleador->tipo_calle;

if($cod_tipo_calle_empleado==1){
  $tipo_calle_empleador="Avenida";

}elseif($cod_tipo_calle_empleado==2){
  $tipo_calle_empleador="Calle";

}elseif($cod_tipo_calle_empleado==3){
  $tipo_calle_empleador="Pasaje";
}else {
    $tipo_calle_empleador="n/a";
}

?>
<h3><?php echo __('Identificación del Empleador')?></h3>
 <div class='form_section_container'>
  <div class='form_section employer'>
    <div class='row'>
      <div class='field nombre'>
        <label for="complaint_diat_attributes_employer_attributes_nombre"><?php echo __('Nombre'); ?></label><br />
        <div class='protected_field nombre'><?php echo $xml->ZONA_B->empleador->nombre_empleador;?></div>
      </div>
      <div class='field rut'>
        <label for="complaint_diat_attributes_employer_attributes_rut"><?php echo __('Rut'); ?></label><br />
        <div class='protected_field rut'><?php echo $xml->ZONA_B->empleador->rut_empleador;?></div>
      </div>
    </div>
    <div class='row'>
     <!-- <div class='address'>-->
        <div class='field codigo_tipo_calle'>
          <label for="complaint_diat_attributes_employer_attributes_address_attributes_codigo_tipo_calle"><?php echo __('Tipo calle'); ?></label><br />
          <div class='protected_field codigo_tipo_calle'><?php echo $tipo_calle_empleador; ?></div>
        </div>
        <div class='field nombre_calle'>
          <label for="complaint_diat_attributes_employer_attributes_address_attributes_nombre"><?php echo __('Nombre'); ?></label><br />
          <div class='protected_field nombre_calle'><?php echo $xml->ZONA_B->empleador->direccion_empleador->nombre_calle;?></div>
        </div>
        <div class='field numero_calle'>
          <label for="complaint_diat_attributes_employer_attributes_address_attributes_numero"><?php echo __('Número'); ?></label><br />
          <div class='protected_field numero_calle'><?php echo $xml->ZONA_B->empleador->direccion_empleador->numero;?></div>
        </div>
        <div class='field resto_direccion'>
          <label for="complaint_diat_attributes_employer_attributes_address_attributes_resto_direccion"><?php echo __('Villa / población / sector'); ?></label><br />
          <div class='protected_field resto_direccion'><?php echo $xml->ZONA_B->empleador->direccion_empleador->resto_direccion ? : 'n/a'; ?></div>
        </div>
        <div class='field localidad'>
          <label for="complaint_diat_attributes_employer_attributes_address_attributes_localidad"><?php echo __('Localidad'); ?></label><br />
          <div class='protected_field localidad'><?php echo trim($xml->ZONA_B->empleador->direccion_empleador->localidad) ? : 'n/a'; ?></div>
        </div>
        <div class='field codigo_comuna'>
          <label for="complaint_diat_attributes_employer_attributes_address_attributes_codigo_comuna"><?php echo __('Comuna'); ?></label><br />
          <div class='protected_field codigo_comuna'><?php echo $comuna_empleador;?></div>
        </div>
      <!--</div>
      <div class='telephone'>-->
        <div class='field codigo_area'>
          <label for="complaint_diat_attributes_employer_attributes_telephone_attributes_codigo_area"><?php echo __('Cód. área'); ?></label><br />
          <div class='protected_field codigo_area'>
            <?php echo $xml->ZONA_B->empleador->telefono_empleador->cod_pais ? "({$xml->ZONA_B->empleador->telefono_empleador->cod_pais})" : ''; ?>
            <?php echo $xml->ZONA_B->empleador->telefono_empleador->cod_area ? : 'n/a'; ?>
          </div>
        </div>
        <div class='field numero_telefono'>
          <label for="complaint_diat_attributes_employer_attributes_telephone_attributes_numero"><?php echo __('Nº teléfono'); ?></label><br />
          <div class='protected_field numero_telefono'><?php echo $xml->ZONA_B->empleador->telefono_empleador->numero ? : 'n/a';?></div>
        </div>
      <!--</div>-->
    </div>
    <div class='row'>
      <div class='field codigo_tipo_empresa'>
        <label for="complaint_diat_attributes_employer_attributes_codigo_tipo_empresa"><?php echo __('Tipo empresa'); ?></label><br />
        <div class='protected_field codigo_tipo_empresa'><?php echo $tipo_empresa[(string)$xml->ZONA_B->empleador->tipo_empresa]; ?></div>
      </div>
      <div class='field codigo_propiedad_empresa'>
        <label for="complaint_diat_attributes_employer_attributes_codigo_propiedad_empresa"><?php echo __('Propiedad empresa'); ?></label><br />
        <div class='protected_field codigo_propiedad_empresa'><?php echo $prop_empresa[(string)$xml->ZONA_B->empleador->propiedad_empresa]; ?></div>
      </div>
      <div class='field n_trabajadores_hombre'>
        <label for="complaint_diat_attributes_employer_attributes_n_trabajadores_hombre"><?php echo __('Nº trabajadores hombre'); ?></label><br />
        <div class='protected_field n_trabajadores_hombre'><?php echo $xml->ZONA_B->empleador->n_trabajadores_hombre;?></div>
      </div>
      <div class='field n_trabajadores_mujer'>
        <label for="complaint_diat_attributes_employer_attributes_n_trabajadores_mujer"><?php echo __('Nº trabajadores mujer'); ?></label><br />
        <div class='protected_field n_trabajadores_mujer'><?php echo $xml->ZONA_B->empleador->n_trabajadores_mujer;?></div>
      </div>
    </div>
    <div class='row'>
      <div class='field ciiu'>
        <label for="complaint_diat_attributes_employer_attributes_ciiu"><?php echo __('Actividad principal'); ?></label><br />
        <div class='protected_field ciiu'><?php echo$ciiu1_empleador; ?></div>
      </div>
      <div class='field ciiu_desc'>
        <label for="complaint_diat_attributes_employer_attributes_ciiu_desc"><?php echo __('Descripción actividad principal'); ?></label><br />
        <div class='protected_field ciiu_desc'><?php echo $xml->ZONA_B->empleador->ciiu_texto;?></div>
      </div>
    </div>
    <div class='row'>
      <?php if($ciiu2_empleador!='n/a'):?>
      <div class='field ciiu_secundario'>
        <label for="complaint_diat_attributes_employer_attributes_ciiu_secundario"><?php echo __('Actividad secundaria'); ?></label><br />
        <div class='protected_field ciiu_secundario'><?php echo $ciiu2_empleador; ?></div>
      </div>
    <?php endif?>
    </div>
    <div class='row'>
    <?php if(isset($xml->ZONA_B->empleador->ciiu2_texto) && !empty($xml->ZONA_B->empleador->ciiu2_texto)):?>
      <div class='field ciiu_secundario_desc'>
        <label for="complaint_diat_attributes_employer_attributes_ciiu_secundario_desc"><?php echo __('Descripción actividad secundaria'); ?></label><br />
        <div class='protected_field ciiu_secundario_desc'><?php echo $xml->ZONA_B->empleador->ciiu2_texto;?></div>
      </div>
    <?php endif?>
    </div>
  </div>
</div>
<?php if(isset($xml->ZONA_B->empleador->rut_representante_legal)):?>
  <?php echo View::factory('documento/zona/zona_b_complemento')->set('xml',$xml)->render(); ?>       
<?php endif?>
