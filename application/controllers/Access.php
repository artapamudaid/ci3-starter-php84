<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model(['Role_model','Menu_model','Access_model','User_model']);
        $this->load->helper('permission');
    }

    public function index(){
        $data['roles'] = $this->Role_model->all();
        $this->render('access/index', $data);
    }

    public function assign($role_id) {
        $data['role'] = $this->Role_model->get($role_id);
        $data['menus'] = $this->Menu_model->all();
        if ($this->input->method() == 'post') {
            $posted = $this->input->post();
            foreach($data['menus'] as $m){
                $menuId = $m->id;
                $can_create = isset($posted["create_$menuId"]) ? 1 : 0;
                $can_read   = isset($posted["read_$menuId"]) ? 1 : 0;
                $can_update = isset($posted["update_$menuId"]) ? 1 : 0;
                $can_delete = isset($posted["delete_$menuId"]) ? 1 : 0;
                $this->Access_model->upsert($role_id, $menuId, [
                    'can_create'=>$can_create,'can_read'=>$can_read,
                    'can_update'=>$can_update,'can_delete'=>$can_delete
                ]);
            }

            // rebuild cache for users that have this role
            $this->rebuild_cache_for_role($role_id);

            $this->session->set_flashdata('success','Permissions updated and cache rebuilt');
            redirect('access/assign/'.$role_id);
        }

        $existing = [];
        foreach($this->Access_model->get_by_role($role_id) as $p){
            $existing[$p->menu_id] = $p;
        }
        $data['existing'] = $existing;
        $this->render('access/assign', $data);
    }

    protected function rebuild_cache_for_role($role_id) {
        $users = $this->User_model->users_by_role($role_id);
        $this->load->helper('permission');
        foreach($users as $u) {
            load_user_permissions($u->id);
        }
    }
}
