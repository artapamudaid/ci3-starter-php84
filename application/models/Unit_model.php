<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit_model extends CI_Model {

    private $table = "units";
    private $column_order = ["unit_id","unit_name"];
    private $column_search = ["unit_name"];
    private $order = ["unit_name" => "asc"];

    private function _get_datatables_query() {
        $this->db->from($this->table);

        $i = 0;
        foreach ($this->column_search as $unit) {
            if (!empty($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($unit, $_POST['search']['value']);
                } else {
                    $this->db->or_like($unit, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']],
                $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $this->db->order_by(key($this->order), $this->order[key($this->order)]);
        }
    }

    public function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        return $this->db->get()->result();
    }

    public function count_filtered() {
        $this->_get_datatables_query();
        return $this->db->get()->num_rows();
    }

    public function count_all() {
        return $this->db->count_all($this->table);
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->where('unit_id', $id)->update($this->table, $data);
    }

    public function delete($id) {
        return $this->db->where('unit_id', $id)->delete($this->table);
    }

    public function find($id) {
        return $this->db->get_where($this->table, ["unit_id" => $id])->row();
    }
}
