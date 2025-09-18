<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {
    protected $table = 'roles';

    public function all() {
        return $this->db->get($this->table)->result();
    }

    public function get($id) {
        return $this->db->where('id',$id)->get($this->table)->row();
    }

    public function create($data) {
        $this->db->insert($this->table,$data);
        return $this->db->insert_id();
    }

    public function update($id,$data) {
        return $this->db->where('id',$id)->update($this->table,$data);
    }
}
