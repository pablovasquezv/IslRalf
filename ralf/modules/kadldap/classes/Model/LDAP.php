<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * LDAP Model
 *
 * This is to be extended by actual models.
 * @package    Kadldap
 * @author     Beau Dacious <dacious.beau@gmail.com>
 * @copyright  (c) 2009 Beau Dacious
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Model_LDAP {

	protected $ldap;

	protected $loaded = FALSE;

	public function __construct()
	{
		$this->ldap = Kadldap::instance();
	}

	public function is_loaded()
	{
		return $this->loaded;
	}

}
