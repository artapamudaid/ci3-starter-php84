<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('current_user')) {
    function current_user() {
        $CI =& get_instance();
        return $CI->session->userdata('user') ?? null;
    }
}

if (!function_exists('load_user_permissions')) {
    function load_user_permissions($user_id) {
        $CI =& get_instance();
        $CI->load->database();
        $row = $CI->db->select('permissions')->from('user_permission_cache')->where('user_id',$user_id)->get()->row();
        if ($row) {
            $perms = json_decode($row->permissions, true);
            $CI->session->set_userdata('user_permissions', $perms);
            return $perms;
        }
        $CI->load->model(['User_model','Access_model','Menu_model']);
        $roles = $CI->User_model->roles($user_id);
        $perms = [];
        foreach($roles as $r) {
            $accesses = $CI->Access_model->get_by_role($r->id);
            foreach($accesses as $a) {
                $menu = $CI->Menu_model->get($a->menu_id);
                if (!$menu) continue;
                $slug = $menu->slug;
                if (!isset($perms[$slug])) $perms[$slug] = ['create'=>0,'read'=>0,'update'=>0,'delete'=>0];
                $perms[$slug]['create'] = $perms[$slug]['create'] || (int)$a->can_create;
                $perms[$slug]['read']   = $perms[$slug]['read']   || (int)$a->can_read;
                $perms[$slug]['update'] = $perms[$slug]['update'] || (int)$a->can_update;
                $perms[$slug]['delete'] = $perms[$slug]['delete'] || (int)$a->can_delete;
            }
        }
        $CI->session->set_userdata('user_permissions', $perms);
        $CI->db->replace('user_permission_cache', [
            'user_id' => $user_id,
            'permissions' => json_encode($perms)
        ]);
        return $perms;
    }
}

if (!function_exists('has_permission')) {
    function has_permission($menu_slug, $action = 'read') {
        $CI =& get_instance();
        $user = current_user();
        if (!$user) return false;

        // super-admin bypass
        if (!isset($CI->load_roles_checked)) {
            $CI->load->model('User_model');
            $CI->load_roles_checked = $CI->User_model->roles($user->id);
        }
        foreach ($CI->load_roles_checked as $r) {
            if (strtolower($r->name) === 'super-admin' || strtolower($r->name) === 'superadmin') {
                return true;
            }
        }

        $perms = $CI->session->userdata('user_permissions') ?? null;
        if (!$perms) {
            $perms = load_user_permissions($user->id);
        }

        if (!isset($perms[$menu_slug])) return false;
        $field = $action;
        return !empty($perms[$menu_slug][$field]);
    }
}
