<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
        'default' => array
	(
		'type'       => 'MySQL',
		'connection' => array(
			/**
			 * There are two ways to define a connection for PostgreSQL:
			 *
			 * 1. Full connection string passed directly to pg_connect()
			 *
			 * string   info
			 *
			 * 2. Connection parameters:
			 *
			 * string   hostname    NULL to use default domain socket
			 * integer  port        NULL to use the default port
			 * string   username
			 * string   password
			 * string   database
			 * boolean  persistent
			 * mixed    ssl         TRUE to require, FALSE to disable, or 'prefer' to negotiate
			 *
			 * @link http://www.postgresql.org/docs/current/static/libpq-connect.html
			 */
			'hostname'   => '172.16.6.74', /*Requerido*/
			'username'   => 'user_ralf', /*Requerido*/
			'password'   => 'ralf_qa', /*Requerido*/
			'persistent' => FALSE,
			'database'   => 'ralf_produccion', /*Requerido*/
		),
		'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
		'schema'       => '', /*Requerido*/
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
);
