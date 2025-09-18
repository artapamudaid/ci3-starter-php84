<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access_model extends CI_Model {
    protected $table = 'role_menu_access';

    public function get_by_role($role_id) {
        return $this->db->where('role_id',$role_id)->get($this->table)->result();
    }

    public function get($role_id, $menu_id) {
        return $this->db->where(['role_id'=>$role_id,'menu_id'=>$menu_id])->get($this->table)->row();
    }

    public function upsert($role_id, $menu_id, $data) {
        $exists = $this->get($role_id,$menu_id);
        if ($exists) {
            return $this->db->where('id',$exists->id)->update($this->table,$data);
        } else {
            $data['role_id']=$role_id;
            $data['menu_id']=$menu_id;
            return $this->db->insert($this->table,$data);
        }
    }

    public function role_has_permission($role_id, $menu_slug, $action) {
        $this->db->select("rma.*")
            ->from('role_menu_access rma')
            ->join('menus m','m.id=rma.menu_id')
            ->where('rma.role_id',$role_id)
            ->where('m.slug',$menu_slug);
        $row = $this->db->get()->row();
        if (!$row) return false;
        $field = 'can_'.$action;
        return !empty($row->$field);
    }
}
