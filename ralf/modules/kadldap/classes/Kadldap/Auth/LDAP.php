<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * LDAP Driver for Kohana's Auth module.
 *
 * @package    Kadldap
 * @author     Beau Dacious <dacious.beau@gmail.com>
 * @copyright  (c) 2009 Beau Dacious
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Kadldap_Auth_LDAP extends Auth {

	/** @var Kadldap The Kadldap instance. */
	protected $kadldap;

	/** @var string The user's password is stored in the session under this key. */
	private $_password_session_suffix = '_kadldap_password';

	/** @var array[string] The groups to which the current user belongs. */
	private $_groups;

	public function __construct($config = array())
	{
		$this->kadldap = Kadldap::instance();
		parent::__construct($config);
	}

	/**
	 * Defines [Auth::login].
	 *
	 * @param <type> $username
	 * @param <type> $password
	 * @param <type> $remember
	 * @return <type>
	 */
	public function _login($username, $password, $remember)
	{
		$authenticated = $this->kadldap->authenticate($username, $password, TRUE);
		if ($authenticated)
		{
			$this->_session->set($this->_config['session_key'].$this->_password_session_suffix, $password);
			return $this->complete_login($username);
		}
		return FALSE;
	}

	/**
	 * Not used, but must be overridden.
	 * 
	 * @return void
	 */
	public function check_password($password)
	{
		
	}

	public function password($username)
	{
		return $this->_session->get($this->_config['session_key'].$this->_password_session_suffix);
	}

	/**
	 * Check if there is an active session. Optionally allows checking for a
	 * specific role (or 'group', in LDAP parlance).
	 *
	 * @param string $role Role name
	 * @return mixed
	 */
	public function logged_in($role = NULL)
	{
		$logged_in = parent::logged_in();

		// If no role requested, or not logged in, don't check for role/group
		// membership.
		if ($role == NULL OR ! $logged_in)
		{
			return $logged_in;
		} else
		{
			// If a role is being checked, first find this user's groups,
			// and then see if the requested role is in them.
			if ( ! is_array($this->_groups))
			{
				$this->_groups = $this->get_roles();
			}
			return in_array($role, $this->_groups);
		}
	}

	/**
	 * Get list of all roles that the current user holds (i.e. LDAP groups
	 * of which they are a member.
	 * 
	 * @uses adLDAPUsers::groups()
	 * @return array An array of strings
	 */
	public function get_roles()
	{
		$username = $this->get_user();
		$this->kadldap->authenticate($username, $this->password($username));
		$groups = $this->kadldap->user()->groups($username);
		return $groups ? $groups : array();
	}

}
