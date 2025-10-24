<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Items extends MY_Controller {
    public function __construct() {
        parent::__construct();

		if ($this->session->userdata('user')->id == NULL) {
			header("Location:" . site_url('auth/login'));
		}

        $this->load->model('Item_model');
        $this->load->library('form_validation');
        $this->load->helper('permission'); // ensure helper loaded
    }

    public function index() {
        
        if (!has_permission('items','read')) {
            
			redirect('dashboard');
;        }
        $this->render('items/index');
    }

    public function import() {
        
        if (!has_permission('items','read')) {
            
			redirect('dashboard');
;        }
        $this->render('items/import');
    }

	public function download_template(){
		if (!has_permission('items','create')) {	
			redirect('items');
		}
		
		$this->load->helper('download');
		$filepath = FCPATH . 'assets/xlsx_templates/item_import_template.xlsx';
		force_download($filepath, NULL);
	}

	public function do_import()
	{

		if (!has_permission('items','create')) {
			
			redirect('items');
		}

		if (isset($_FILES["file"]["name"])) {

			$file = $this->do_upload_xls($name = 'file', $fileName = base64_encode('item_tmp_' . date('ymdHis')));

			if ($file) {

				$path = FCPATH . 'uploads/xls_tmp/';
				$pathFile = $path . $file;

				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				$spreadsheet = $reader->load($pathFile);
				$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$numrow = 1;
				foreach ($sheet as $row) {

				$item_code      = $row['A']; 
				$name      		= $row['B']; 
				$description    = $row['C']; 
				$unit           = $row['D'];

				if ($numrow > 2) {
					if (
					empty($item_code) ||
					empty($name) ||
					empty($description) ||
					empty($unit)
					) {
					continue;
					}
					$q = $this->db->query("SELECT COUNT(item_code) as codeNum FROM items WHERE item_code = '$item_code'")->row_array();

					$codeNum = $q['codeNum'];

					if (empty($item_code) || $codeNum > 0) {
					$this->session->set_flashdata('failed', 'Duplikasi Kode Barang');
					} else {
					$data = array(
						'item_code'		=>  $item_code,
						'name'       	=>  $name,
						'description'   =>  $description,
						'unit'        	=>  $unit,
					);

					$this->Item_model->insert($data);


					$this->session->set_flashdata('success', 'Import Data Santri Berhasil');
					}
				}
				$numrow++;
				}

				unlink($pathFile);
			}
		}

		redirect('items');
	}

    public function ajax_list() {
        // server-side: require read permission
        if (!$this->input->is_ajax_request() || !has_permission('items','read')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $list = $this->Item_model->get_datatables();
        $data = [];
        $no = $_POST['start'];

        // determine per-current-user permissions to render actions server-side
        $canUpdate = has_permission('items','update');
        $canDelete = has_permission('items','delete');

        foreach ($list as $p) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = html_escape($p->item_code);
            $row[] = html_escape($p->name);
            $row[] = html_escape($p->description);
            $row[] = html_escape($p->unit);

            // actions: only render allowed buttons
            $actions = '';
            if ($canUpdate) {
                $actions .= '<button class="btn btn-md btn-warning edit" data-id="'.$p->item_id.'"><i class="fas fa-edit"></i> Edit</button> ';
            }
            if ($canDelete) {
                $actions .= '<button class="btn btn-md btn-danger delete" data-id="'.$p->item_id.'"><i class="fas fa-trash"></i> Delete</button>';
            }
            $row[] = $actions;
            $data[] = $row;
        }

        echo json_encode([
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Item_model->count_all(),
            "recordsFiltered" => $this->Item_model->count_filtered(),
            "data" => $data,
            "csrfHash" => $this->security->get_csrf_hash()
        ]);
    }

    public function ajax_save() {

        if (!$this->input->is_ajax_request()) {
            show_error('Forbidden', 403);
            return;
        }

        $id = $this->input->post('item_id', TRUE);

        if ($id) {
            if (!has_permission('items','update')) {
                $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                    'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
                ]));
                return;
            }
        } else {
            if (!has_permission('items','create')) {
                $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                    'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
                ]));
                return;
            }
        }

        // validation
		$this->form_validation->set_rules('item_code','Kode Barang','required|trim|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('name','Nama Barang','required|trim|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('description','Deskripsi','required|trim');
		$this->form_validation->set_rules('unit','Unit','required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                "status"=>"error",
                "errors"=> $this->form_validation->error_array(),
                "csrfHash"=>$this->security->get_csrf_hash()
            ]);
            return;
        }

        $data = [
			"item_code"=>$this->input->post('item_code', TRUE),
            "name"=>$this->input->post('name', TRUE),
            "description"=>$this->input->post('description', TRUE),
			"unit"=>$this->input->post('unit', TRUE),
        ];

        if ($id) {
            $this->Item_model->update($id, $data);
            $msg = "Barang berhasil diupdate";
        } else {
            $this->Item_model->insert($data);
            $msg = "Barang berhasil ditambahkan";
        }

        echo json_encode([
            "status"=>"success",
            "message"=>$msg,
            "csrfHash"=>$this->security->get_csrf_hash()
        ]);
    }

    public function ajax_edit($id) {
        if (!$this->input->is_ajax_request() || !has_permission('items','update')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $row = $this->Item_model->find($id);
        echo json_encode([
            "data"=>$row,
            "csrfHash"=>$this->security->get_csrf_hash()
        ]);
    }

    public function ajax_delete($id) {
        if (!$this->input->is_ajax_request() || !has_permission('items','delete')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $this->Item_model->delete($id);
        echo json_encode([
            "status"=>"success",
            "message"=>"Barang berhasil dihapus",
            "csrfHash"=>$this->security->get_csrf_hash()
        ]);
    }



	private function do_upload_xls($name = NULL, $fileName = NULL)
	{
		$this->load->library('upload');

		$config['upload_path'] = FCPATH . 'uploads/xls_tmp/';

		/* create directory if not exist */
		if (!is_dir($config['upload_path'])) {
		mkdir($config['upload_path'], 0777, TRUE);
		}

		$config['allowed_types'] = 'xlsx';
		$config['max_size'] = '10240';
		$config['file_name'] = $fileName;
		$this->upload->initialize($config);

		if (!$this->upload->do_upload($name)) {
		$this->session->set_flashdata('success', $this->upload->display_errors('', ''));
		redirect(uri_string());
		}

		$upload_data = $this->upload->data();

		return $upload_data['file_name'];
	}
}
