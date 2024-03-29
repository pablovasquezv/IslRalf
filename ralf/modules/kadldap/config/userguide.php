<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Configuration variables for the AD/LDAP Module for Kohana3.
 *
 * @author     Sam Wilson
 * @copyright  (c) 2010 Sam Wilson
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
return array
(
	// Leave this alone
	'modules' => array(

		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'kadldap' => array(
			'enabled' => TRUE,
			'name' => 'Kadldap',
			'description' => 'Active Directory and LDAP authentication.',
			'copyright' => 
				HTML::mailto('dacious.beau@gmail.com', 'Beau Dacious').', '
				.HTML::mailto('sam@samwilson.id.au', 'Sam Wilson').' and '
				.HTML::anchor('http://github.com/sfroeth', 'sfroeth')
		)  
	)
);