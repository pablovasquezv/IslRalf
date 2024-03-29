<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * LDAP User Model
 *
 * @package    Kadldap
 * @author     Beau Dacious <dacious.beau@gmail.com>
 * @copyright  (c) 2009 Beau Dacious
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Model_LDAPUser extends Model_LDAP
{
	
	protected $userinfo = array();

	public static function factory($username = NULL)
	{
		$user = new Model_LDAPUser;

		if ( NULL !== $username )
		{
			$user->get($username);
		}

		return $user;
	}

	public function __get($name)
	{
		if ( array_key_exists($name, $this->userinfo) )
		{
			$value = $this->userinfo[$name];

			if ( is_array($value) )
			{
				if ( array_key_exists('count', $value) )
				{
					unset($value['count']);
				}

				$value = ( count($value) == 1 ) ? reset($value) : $value;
			}

			return $value;
		}
	}

	public function get($username)
	{
		$userinfo = $this->ldap->user_info($username);

		if ( ! is_array($userinfo) || $userinfo['count'] == 0 )
		{
			return FALSE;
		}

		// Let's tidy up this array real quick...

		$userinfo = $userinfo[0]; // Don't need that anymore...

		foreach ( $userinfo as $key => $value )
		{
			if ( $key == 'count' || ( is_numeric($key) && array_key_exists($value, $userinfo) ) )
			{
				unset($userinfo[$key]);
			}
		}

		$this->userinfo = $userinfo;
		$this->loaded = TRUE;

		return $this; // method chaining
	}

	public function is_member_of($group)
	{
		// group model
		if ( $group instanceof Model_LDAPGroup )
		{
			return in_array($group->dn, $this->userinfo['memberof']);
		}

		// dn
		if ( in_array($group, $this->userinfo['memberof']) )
		{
			return TRUE;
		}

		// simple name
		foreach ( $this->userinfo['memberof'] as $value )
		{
			if ( preg_match("/^CN={$group}/", $value) > 0 )
			{
				return TRUE;
			}
		}

		return FALSE;
	}

}
