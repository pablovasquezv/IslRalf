<?php defined('SYSPATH') OR die('No direct script access.');

// Kadldap testing and demonstration page.
Route::set('kadldap', 'kadldap(/<action>)')
	->defaults(array(
		'controller' => 'Kadldap',
		'action'     => 'index',
	));
