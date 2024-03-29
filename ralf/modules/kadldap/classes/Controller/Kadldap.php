<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Module controller for Kadldap, to test configuration and connection details.  This
 * uses the Userguide controller and views, and is linked to from the userguide.
 *
 * @package    Kadldap
 * @author     Sam Wilson <sam@samwilson.id.au>
 * @copyright  (c) 2011 Sam Wilson
 * @author     Github user 'sfroeth'
 * @copyright  (c) 2011 sfroeth
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Controller_Kadldap extends Controller_Userguide
{

	public function action_index()
	{
		$view = View::factory('kadldap/index');
		$this->template->content = $view;
		$this->template->title = 'Kadldap';
		$this->template->menu = '';
		$this->template->breadcrumb = array(
			Route::get('docs/guide')->uri() => __('User Guide'),
			Route::get('docs/guide')->uri().'/kadldap' => $this->template->title,
			'Configuration Test'
		);
		$view->kadldap = Kadldap::instance();

		$view->message = FALSE;
		if (isset($_POST['login']))
		{
			$post = Validation::factory($_POST)
				->rule('username', 'not_empty')
				->rule('password', 'not_empty');
			if ($post->check())
			{
				$username = $post['username'];
				$password = arr::get($post, 'password', '');
				try
				{
					if (Auth::instance()->login($username, $password))
					{
						$view->message = 'Successful login.';
					} else
					{
						$view->message = 'Login failed.';
					}
				} catch (adLDAPException $e)
				{
					$view->message = $e->getMessage();
				}
			} else
			{
				$view->message = 'You must enter both your username and password.';
			}
		}

		if (Auth::instance()->logged_in())
		{
			$username = Auth::instance()->get_user();
			$password = Auth::instance()->password($username);
			$view->kadldap->authenticate($username, $password);
			$userinfo = $view->kadldap->user()->info($username, array('*'));
			$view->userinfo = Arr::get($userinfo, 0, array());
		} else {
			$view->userinfo = NULL;
		}
	}

	public function action_logout()
	{
		Auth::instance()->logout();
		$this->redirect('kadldap');
	}

}