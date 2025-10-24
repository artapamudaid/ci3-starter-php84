<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model {
    protected $table = 'menus';

    public function all() {
        return $this->db->order_by('sort','ASC')->get($this->table)->result();
    }

    public function get($id) {
        return $this->db->where('id',$id)->get($this->table)->row();
    }

    public function find_by_slug($slug) {
        return $this->db->where('slug',$slug)->get($this->table)->row();
    }

    public function create($data) {
        $this->db->insert($this->table,$data);
        return $this->db->insert_id();
    }

    public function update($id,$data) {
        return $this->db->where('id',$id)->update($this->table,$data);
    }
    
    // **GET MENU TREE BERDASARKAN ROLE + PERMISSION**
    public function get_menu_tree_by_role($role) {
        // Ambil semua menu aktif
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('is_active', 1);
        $this->db->order_by('sort', 'ASC');
        $this->db->order_by('id', 'ASC');
        $all_menus = $this->db->get()->result_array();
        
        // Filter berdasarkan role permission
        $filtered_menus = $this->filter_menus_by_role($all_menus, $role);
        
        // Build tree
        return $this->build_menu_tree($filtered_menus);
    }
    
    private function filter_menus_by_role($menus, $role) {
        $filtered = array();
        
        foreach ($menus as $menu) {
            // Cek permission via menu_permissions table
            $has_access = $this->has_role_access($menu['id'], $role);
            
            // Selalu tampilkan Dashboard untuk semua role
            if ($menu['slug'] === 'dashboard' || $has_access) {
                $filtered[] = $menu;
            }
        }
        
        return $filtered;
    }
    
    private function has_role_access($menu_id, $role) {
        $this->db->where('menu_id', $menu_id);
        $this->db->where('role_id', $role);
        $this->db->where('can_read', 1);
        return $this->db->count_all_results('role_menu_access') > 0;
    }
    
    private function build_menu_tree($menus, $parent_id = 0) {
        $menu_tree = array();
        
        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $parent_id) {
                $children = $this->build_menu_tree($menus, $menu['id']);
                if ($children) {
                    $menu['children'] = $children;
                }
                $menu_tree[] = $menu;
            }
        }
        return $menu_tree;
    }
}
