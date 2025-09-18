<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    protected $table = 'users';

    public function create(array $data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get($id = null) {
        if ($id === null) return $this->db->get($this->table)->result();
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function find_by_username($username) {
        return $this->db->where('username', $username)->get($this->table)->row();
    }

    public function verify_password($username, $password) {
        $u = $this->find_by_username($username);
        if (!$u) return false;
        return password_verify($password, $u->password) ? $u : false;
    }

    public function assign_role($user_id, $role_id) {
        $exists = $this->db->where(['user_id'=>$user_id,'role_id'=>$role_id])->get('user_roles')->row();
        if (!$exists) $this->db->insert('user_roles', ['user_id'=>$user_id,'role_id'=>$role_id]);
    }

    public function roles($user_id) {
        return $this->db->select('r.*')
                        ->from('roles r')
                        ->join('user_roles ur','ur.role_id=r.id')
                        ->where('ur.user_id',$user_id)
                        ->get()->result();
    }

    public function users_by_role($role_id) {
        return $this->db->select('u.*')->from('users u')
            ->join('user_roles ur','ur.user_id=u.id')
            ->where('ur.role_id',$role_id)->get()->result();
    }
}
