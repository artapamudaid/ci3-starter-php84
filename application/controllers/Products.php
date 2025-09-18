<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->library('form_validation');
        $this->load->helper('permission'); // ensure helper loaded
    }

    public function index() {
        // only allow read access to view the page
        if (!has_permission('products','read')) {
            show_error('Forbidden', 403);
        }
        $this->render('products/index');
    }

    public function ajax_list() {
        // server-side: require read permission
        if (!$this->input->is_ajax_request() || !has_permission('products','read')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $list = $this->Product_model->get_datatables();
        $data = [];
        $no = $_POST['start'];

        // determine per-current-user permissions to render actions server-side
        $canUpdate = has_permission('products','update');
        $canDelete = has_permission('products','delete');

        foreach ($list as $p) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = html_escape($p->name);
            $row[] = $p->price;
            $row[] = $p->created_at;

            // actions: only render allowed buttons
            $actions = '';
            if ($canUpdate) {
                $actions .= '<button class="btn btn-sm btn-warning edit" data-id="'.$p->id.'">Edit</button> ';
            }
            if ($canDelete) {
                $actions .= '<button class="btn btn-sm btn-danger delete" data-id="'.$p->id.'">Delete</button>';
            }
            $row[] = $actions;
            $data[] = $row;
        }

        echo json_encode([
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Product_model->count_all(),
            "recordsFiltered" => $this->Product_model->count_filtered(),
            "data" => $data,
            "csrfHash" => $this->security->get_csrf_hash()
        ]);
    }

    public function ajax_save() {
        // create -> need create permission; update -> need update permission
        if (!$this->input->is_ajax_request()) {
            show_error('Forbidden', 403);
            return;
        }

        $id = $this->input->post('id', TRUE);

        if ($id) {
            if (!has_permission('products','update')) {
                $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                    'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
                ]));
                return;
            }
        } else {
            if (!has_permission('products','create')) {
                $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                    'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
                ]));
                return;
            }
        }

        // validation
        $this->form_validation->set_rules('name','Nama Produk','required|trim|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('price','Harga','required|numeric|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                "status"=>"error",
                "errors"=> $this->form_validation->error_array(),
                "csrfHash"=>$this->security->get_csrf_hash()
            ]);
            return;
        }

        $data = [
            "name"=>$this->input->post('name', TRUE),
            "price"=>$this->input->post('price', TRUE),
        ];

        if ($id) {
            $this->Product_model->update($id, $data);
            $msg = "Produk berhasil diupdate";
        } else {
            $this->Product_model->insert($data);
            $msg = "Produk berhasil ditambahkan";
        }

        echo json_encode([
            "status"=>"success",
            "message"=>$msg,
            "csrfHash"=>$this->security->get_csrf_hash()
        ]);
    }

    public function ajax_edit($id) {
        if (!$this->input->is_ajax_request() || !has_permission('products','update')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $row = $this->Product_model->find($id);
        echo json_encode([
            "data"=>$row,
            "csrfHash"=>$this->security->get_csrf_hash()
        ]);
    }

    public function ajax_delete($id) {
        if (!$this->input->is_ajax_request() || !has_permission('products','delete')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $this->Product_model->delete($id);
        echo json_encode([
            "status"=>"success",
            "message"=>"Produk berhasil dihapus",
            "csrfHash"=>$this->security->get_csrf_hash()
        ]);
    }
}
