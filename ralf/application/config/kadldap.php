<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Configuration variables for the AD/LDAP Module for Kohana3.
 *
 * @author     Beau Dacious <dacious.beau@gmail.com>
 * @author     Sam Wilson <sam@samwilson.id.au>
 * @copyright  (c) 2009 Beau Dacious
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
return array(
	'kadldap' => array(
		'domain_controllers' => array('172.16.7.24'), // array('dc.example.com','dc1.example.com')
		'account_suffix'     => '@isl.gob.local', // '@example.com'
		'base_dn'            => 'cn=usuarioralf,ou=Usuarios,dc=isl,dc=gob,dc=local', // 'dc=example,dc=com',
		'ad_username'        => NULL,
		'ad_password'        => NULL
	)
);
