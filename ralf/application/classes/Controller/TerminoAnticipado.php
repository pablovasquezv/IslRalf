<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Termino_Anticipado extends Controller_Website {

    public function __construct(Kohana_Request $request)
    {
        parent::__construct($request);
    }

    public function action_index()
    {
        $vista = View::factory('termino_index');
        $vista->set ('task_list', $this->get_tasklist());
        $this->request->response = $vista;
    }

    public function action_add ()
    {
        $vista = View::factory('task_add');
        $this->request->response = $vista;

        if ( ! isset($_POST) OR ! array_key_exists('title', $_POST))
        return;

        if ( $this->create_task () )
        $vista->set ('message', 'Task added');
        else
        $vista->set ('message', 'Error al añadir');
    }

    private function create_task ()
    {
        $newtask = ORM::factory('task');
        $newtask->title = $_POST['title'];
        $newtask->description = $_POST['description'];
        $newtask->created = strftime('%Y-%m-%d %H.%M.%S');
        $newtask->duedate = $this->getAsDate($_POST['duedate']);
        $newtask->save();

        return $newtask->saved();
    }

    private function getAsDate ($value)
    {
        $result = null;
        if (isset ($value)) {
        $result = $value." 00.00.00";
        }
        print_r($result);
        return $result;
    }

    private function get_tasklist ()
    {
        $tasks = ORM::factory('task')->find_all();

        $result = array();

        foreach ($tasks as $task)
        $result[] = $task;

        return $result;
    }
}