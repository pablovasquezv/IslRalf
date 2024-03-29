<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Auth extends Controller_Template {

    protected $_auth_required = TRUE;
    protected $_user = FALSE;

    /**
     * Se ejecuta ANTES de cada action...
     * Carga la configuración y verifica la autenticación.
     * @author jperez
     */
    public function before() {
        parent::before();
        if ($this->auto_render) {

            // Initialize empty values
            /* $this->template->contenido = '';
              $this->template->titulo   = '';
              $this->template->lista_css = array();
              $this->template->lista_js = array(); */
            $this->template->mensaje_error = null;

            $this->template->stylesheet = array();
            $this->template->javascript = array();
            $this->template->javascripts = array();

            //$this->template->messages     = NULL;
            //$this->template->js_messages  = NULL;
            // Esto es para marcar claramente cuando estamos viendo la version en desarrollo de un sitio

            $this->template->titulo_sitio = Kohana::$config->load('sitio.name');
            $this->template->logo_sitio = HTML::image(Kohana::$config->load('sitio.url_base') . '/media/images/logo.jpg', array('alt' => $this->template->titulo_sitio, 'width' => '159', 'height' => '143'));
            $this->template->titulo_contenido = NULL;
            $this->template->buscador = NULL;

            $this->template->bloque_superior = NULL;
            $this->template->bloque_central = NULL;
            $this->template->bloque_inferior = NULL;

            $this->template->breadcrumb = NULL;
            $this->template->columna_lateral_enabled = TRUE;
            $this->template->fancybox_view = FALSE;
        }
    }

    /**
     * Se ejecuta DESPUES de cada action...
     * Se ocupa de la presentación de la respuesta.
     * @author jperez
     */
    public function after() {
        if ($this->auto_render) {
            $lista_css = array(
                'media/css/themes/base/jquery.ui.all.css',
                'media/css/sitio.css',
            );

            $lista_js = array(
                'media/js/jquery-1.6.2.js',
                'media/js/jquery.mousewheel-3.0.2.pack.js',
                'media/js/jquery.fancybox-1.3.4.pack.js',
                //'media/js/jquery.fancybox-1.3.1.js',
                'media/js/jquery-ui/jquery.ui.core.js',
                'media/js/jquery-ui/jquery.ui.widget.js',
                'media/js/jquery-ui/jquery.ui.datepicker.js',
                'media/js/jquery-ui/jquery.ui.tabs.js',
                'media/js/jquery-ui/jquery.ui.position.js',
                'media/js/jquery.quicksearch.js',
                'media/js/jquery.multi-select.js',
                'media/js/application.js',
                'media/js/functions.js',
                'media/js/jquery-impromptu.3.1.min.js',
                'media/js/fechas_conf.js',
                'media/js/tabs_conf.js',
                'media/js/jquery.dataTables.js',
                'media/js/ajax.js',
                    //'media/js/bootstrap.js',
            );

            $this->template->stylesheet = array(
                "media/css/themes/base/jquery.ui.all.css",
                "media/css/jquery.fancybox-1.3.1.css",
                "media/css/denuncias.css",
                "media/css/multi-select.css",
                "media/css/jquery.fancybox.layout.css",
                "media/css/impromptu.css",
                "media/css/layout.css",
                "media/css/sitio.css",
                "media/css/style_sisesat.css",
            );

            //Utiles::search_media('media/css/', 'css');
            $this->template->javascript = $lista_js;

            //fancybox view
            if ($this->template->fancybox_view) {
                $key = array_search('media/css/layout.css', $this->template->stylesheet);
                if ($key)
                    unset($this->template->stylesheet[$key]);
                //agregamos un pequeño js
                array_push($this->template->javascript, '/media/js/fancybox.resize.js');
            }

            $mensaje_ok = Session::instance()->get_once('flash_mensaje_ok');
            if (!empty($mensaje_ok)) {
                $this->template->mensaje_ok = $mensaje_ok;
            }
            $mensaje_error = Session::instance()->get_once('flash_mensaje_error');
            if (!empty($mensaje_error)) {
                $this->template->mensaje_error = $mensaje_error;
            }
        }
        parent::after();
    }

    /**
     *
     * @param <type> $errors
     */
    public function action_login($errors = array()) {
        if (Auth::instance()->get_user())
            $this->redirect('/');

        //Para evitar consultas innecesarias, manejamos los campos vacíos antes.
        $post = $this->request->post();
        if ($post) {
            if (!$post['username'])
                $errors['username'] = __('username is empty');
            if (!$post['password'])
                $errors['password'] = __('password is empty');

            if (count($errors) > 0) {
                $this->show_message(__('Usuario o Contraseña no puede estar vacío'), 12, 250);
            } else {
                //ROLES
                $user_id = ORM::factory('User')->where("username", "=", $this->request->post('username'))->find();
                $roles = ORM::factory('RolUser')->where('user_id','=', $user_id->id)->count_all();
                
                if($roles == 0) {
                    $this->show_message(__('No tienes Roles para RALF'), 12, 250);
                } else {
                
                    $user = Auth::instance()->login($this->request->post('username'), $this->request->post('password'));
                    if ($user) {
                        $this->redirect($this->session_vars('url_on_logout'));
                    } else {
                        $this->show_message(__('Usuario o Contraseña incorrecta, intente otra vez'), 12, 250);
                    }
                }
            }
        }
        $this->template->contenido = View::factory('auth/auth.login')->set('errors', $errors);
    }

    protected function add_jquery($add) {
        $jquery = "jQuery(document).ready(function(){" . $add . "});";
        array_push($this->template->javascripts, $jquery);
    }

    protected function show_message($message, $font_size = 11, $width = 400, $redirect = NULL, $talign = 'center', $balign = 'center', $button = 'Aceptar') {
        if (is_array($messages = $message))
            $message = implode('<br />', $messages);
        $this->add_jquery(Utiles::prompt($message, $button, $redirect, $width, $font_size, $talign, $balign));
    }

    /**
     *
     */
    public function action_logout() {
        Auth::instance()->logout(TRUE);
        $this->redirect('login');
    }

    /**
     *
     * @param type $var
     * @param type $value
     * @param type $delete
     * @return type
     */
    public function session_vars($var, $value = NULL, $delete = FALSE) {
        $session = Session::instance();

        if ($var AND $value)
            $session->set($var, $value);

        if ($delete)
            $session->delete($var);

        return $session->get($var);
    }

    /**
     *
     */
    protected function check_auth() {
        $this->uri_on_session();

        if ($this->_auth_required AND ! Auth::instance()->logged_in()) {
            Session::instance()->set('url', $_SERVER['REQUEST_URI']);
            $this->redirect('login');
        } elseif (($this->_auth_required AND Auth::instance()->logged_in()) OR Auth::instance()->logged_in()) {
            $this->_user = Auth::instance()->get_user();
            View::set_global('_user', $this->_user);
        }

        if (Auth::instance()->logged_in()) {
            $this->_user = Auth::instance()->get_user();
            View::set_global('_user', $this->_user);
        }
    }

    /**
     * Memoriza la última pagina después que la sesión termina automáticamente
     */
    private function uri_on_session() {
        //solo guardará el primer request antes de ir al login
        if (!$this->session_vars('url_on_logout'))
            $this->session_vars('url_on_logout', URL::site($this->request->uri(), TRUE));
    }

}
