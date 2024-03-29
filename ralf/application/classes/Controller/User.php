<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Website {

    public function action_index() {
        if($this->get_rol() == 'admin') {
            $this->template->mensaje_error = NULL;

            $users = ORM::factory('User')
                    ->select('region.nombre')
                    ->join('region', 'LEFT')->on('region.id', '=', 'user.region_id' )
                    ->order_by('name', 'ASC')
                    ->find_all()
                    ;
            //echo Database::instance()->last_query;die();
            $this->template->contenido = View::factory('user/admin_index')
                    ->set('users', $users)
                    ;
        } else {
            $this->redirect("error");
        }
    }

    public function action_edit() {
        $this->auto_render = false;
        $user_id = $this->request->param('id');
        $mensaje_error = "";
        $errors = array();
        
        if (isset($_POST) && Valid::not_empty($_POST)) {
            $user = ORM::factory('User')->where('id','=',$user_id)->find();
            if(isset ($_POST['boton_editar_usuario'])) {
                $post = Validation::factory($this->request->post())
                        //->rule('email', 'email')->rule('email', 'not_empty')->label('email', 'Email')
                        ->rule('region_id', 'not_empty')->label('region_id', 'Región')
                        ;

                if($post->check() && count($errors) == 0) {
                    $result = TRUE;
                    if($result) {
                        if($user->loaded()){
                            $db = Database::instance();
                            $db->query(Database::UPDATE, 'UPDATE users '
                                    . 'SET region_id = '.$post['region_id'].' '
                                    . 'WHERE id = '.$user_id.';');
                            $user->region_id = $post['region_id'];
                            
                            //ROLES
                            DB::delete('roles_users')->where('user_id', '=', $user->id)->execute();
                            if(isset($post['check'])) {
                                foreach ($post['check'] as $key => $rol) {
                                    $r = ORM::factory('RolUser');
                                    $r->role_id = $key;
                                    $r->user_id = $user_id;
                                    $r->save();
                                }
                            }
                        }
                        $mensaje_error = "Usuario editado exitosamente";
                    } else {
                        $mensaje_error = "Error al editar usuario";
                    }
                } else {

                    $errors = $post->errors('validate') + $errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }
        
        $user = ORM::factory('User')->where('id','=',$user_id)->find();
        $roles = ORM::factory("Rol")->find_all();
        foreach ($roles as $r) {
            $roles_id[$r->id] = $r->id;
        }
        $regiones = Utiles::regiones();
        $this->response->body (
            View::factory('user/edit')
                ->set('errors', $errors)
                ->set('roles', $roles)
                ->set('roles_id', $roles_id)
                ->set('user', $user)
                ->set('user_id', $user_id)
                ->set('config_ralf', $this->config_ralf)
                ->set('regiones', $regiones)
                ->set('mensaje_error', $mensaje_error)
        );
    }

    public function action_add() {
        $this->auto_render = false;
        $mensaje_error = "";
        $errors = array();
        
        $default['name'] = null;
        $default['email'] = null;
        $default['roles'] = null;
        $default['region_id'] = null;
        
        if (isset($_POST) && Valid::not_empty($_POST)) {
            if(isset($_POST['boton_add_usuario'])) {
                
                $post = Validation::factory($this->request->post())
                        ->rule('name', 'not_empty')->label('name', 'Nombre')
                        ->rule('email', 'email')
                        ->rule('email', 'not_empty')->label('email', 'Email')
                        ->rule('roles', 'not_empty')->label('roles', 'Rol')
                        ->rule('region_id', 'not_empty')->label('region_id', 'Región')
                        ;
                
                $cant_user = ORM::factory('User')->where('email','=', $post['email'])->count_all();
                if($post->check() && count($errors) == 0) {
                    if($cant_user > 0){
                        $mensaje_error = "Usuario ya existe";
                    } else {
                        $username = explode('@', $post['email']);
                        $u = ORM::factory('User');
                        $u->email = trim($post['email']);
                        $u->name = trim($post['name']);
                        $u->username = $username[0];
                        $u->password = '';
                        $u->region_id = $post['region_id'];
                        $u->save();
                        
                        //ROLES
                        foreach ($post['roles'] as $key => $rol) {
                            $r = ORM::factory('RolUser');
                            $r->role_id = $key;
                            $r->user_id = $u->id;
                            $r->save();
                        }
                        $mensaje_error = "Usuario agregado exitosamente";
                    }
                } else {
                    $default['name'] = $post['name'];
                    $default['email'] = $post['email'];
                    $default['roles'] = isset($post['roles']) ? $post['roles'] : null;
                    $default['region_id'] = $post['region_id'];
                        
                    $errors = $post->errors('validate') + $errors;
                    $mensaje_error = __(Kohana::message('messages', 'failed'), array(':cantidad:' => count($errors)));
                }
            }
        }
        
        $roles = ORM::factory("Rol")->find_all();
        foreach ($roles as $r) {
            $roles_id[$r->id] = $r->id;
        }
        $regiones = Utiles::regiones();
        $this->response->body (
            View::factory('user/add')
                ->set('errors', $errors)
                ->set('roles', $roles)
                ->set('default', $default)
                ->set('config_ralf', $this->config_ralf)
                ->set('regiones', $regiones)
                ->set('mensaje_error', $mensaje_error)
        );
    }

}
