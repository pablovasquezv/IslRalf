<?php defined('SYSPATH') OR die('No direct script access.');

class Form extends Kohana_Form {
	public static function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
	{		
		
		if(!is_numeric(strpos($name, 'hora'))) {			
		    //Si aún no se ha especificado agrega un mensaje "Seleccione" como primer elemento en options. Este se  muestra con el atributo "prompt"
		    $prompt     = array(__('select'),__('SELECT'),__('Select'),__('selecciona'),__('SELECCIONA'),__('Selecciona'),__('seleccione'),__('SELECCIONE'),__('Seleccione'));
		    $not_prompt = TRUE;
		    foreach($options as $o){if(in_array($o, $prompt)){$not_prompt=FALSE;break;}}
		    if($not_prompt){$options=is_array($options) ? array(''=>"Seleccione") + $options : $options;}
		}
		
	    return parent::select($name,$options,$selected,$attributes);
  	}


}
