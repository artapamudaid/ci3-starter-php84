<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller {
    public function __construct() {
        parent::__construct();

		if ($this->session->userdata('user')->id == NULL) {
			header("Location:" . site_url('auth/login'));
		}

        $this->load->model('Category_model');
        $this->load->library('form_validation');
        $this->load->helper('permission'); // ensure helper loaded
    }

    public function index() {
        
        if (!has_permission('categories','read')) {
            
			redirect('dashboard');
;        }
        $this->render('categories/index');
    }

    public function import() {
        
        if (!has_permission('categories','read')) {
            
			redirect('dashboard');
;        }
        $this->render('categories/import');
    }

	public function download_template(){
		if (!has_permission('categories','create')) {	
			redirect('categories');
		}
		
		$this->load->helper('download');
		$filepath = FCPATH . 'assets/xlsx_templates/category_import_template.xlsx';
		force_download($filepath, NULL);
	}

	public function do_import()
	{

		if (!has_permission('categories','create')) {
			
			redirect('categories');
		}

		if (isset($_FILES["file"]["name"])) {

			$file = $this->do_upload_xls($name = 'file', $fileName = base64_encode('category_tmp_' . date('ymdHis')));

			if ($file) {

				$path = FCPATH . 'uploads/xls_tmp/';
				$pathFile = $path . $file;

				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				$spreadsheet = $reader->load($pathFile);
				$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$numrow = 1;
				foreach ($sheet as $row) {

				$category_name      		= $row['A'];

				if ($numrow > 2) {
					if (
					empty($category_name)
					) {
					continue;
					}
					$q = $this->db->query("SELECT COUNT(category_name) as nameNum FROM categories WHERE category_name = '$category_name'")->row_array();

					$nameNum = $q['nameNum'];

					if (empty($category_name) || $nameNum > 0) {
					$this->session->set_flashdata('failed', 'Duplikasi Kode Kategori');
					} else {
					$data = array(
						'category_name'       	=>  $category_name,
					);

					$this->Category_model->insert($data);


					$this->session->set_flashdata('success', 'Import Data Santri Berhasil');
					}
				}
				$numrow++;
				}

				unlink($pathFile);
			}
		}

		redirect('categories');
	}

    public function ajax_list() {
        // server-side: require read permission
        if (!$this->input->is_ajax_request() || !has_permission('categories','read')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $list = $this->Category_model->get_datatables();
        $data = [];
        $no = $_POST['start'];

        // determine per-current-user permissions to render actions server-side
        $canUpdate = has_permission('categories','update');
        $canDelete = has_permission('categories','delete');

        foreach ($list as $p) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = html_escape($p->category_name);

            // actions: only render allowed buttons
            $actions = '';
            if ($canUpdate) {
                $actions .= '<button class="btn btn-md btn-warning edit" data-id="'.$p->category_id.'"><i class="fas fa-edit"></i> Edit</button> ';
            }
            if ($canDelete) {
                $actions .= '<button class="btn btn-md btn-danger delete" data-id="'.$p->category_id.'"><i class="fas fa-trash"></i> Delete</button>';
            }
            $row[] = $actions;
            $data[] = $row;
        }

        echo json_encode([
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Category_model->count_all(),
            "recordsFiltered" => $this->Category_model->count_filtered(),
            "data" => $data,
            "csrfHash" => $this->security->get_csrf_hash()
        ]);
    }

    public function ajax_save() {

        if (!$this->input->is_ajax_request()) {
            show_error('Forbidden', 403);
            return;
        }

        $id = $this->input->post('category_id', TRUE);

        if ($id) {
            if (!has_permission('categories','update')) {
                $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                    'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
                ]));
                return;
            }
        } else {
            if (!has_permission('categories','create')) {
                $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                    'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
                ]));
                return;
            }
        }

        // validation
        $this->form_validation->set_rules('category_name','Nama Kategori','required|trim|min_length[3]|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                "status"=>"error",
                "errors"=> $this->form_validation->error_array(),
                "csrfHash"=>$this->security->get_csrf_hash()
            ]);
            return;
        }

        $data = [
            "category_name"=>$this->input->post('category_name', TRUE),
        ];

        if ($id) {
            $this->Category_model->update($id, $data);
            $msg = "Kategori berhasil diupdate";
        } else {
            $this->Category_model->insert($data);
            $msg = "Kategori berhasil ditambahkan";
        }

        echo json_encode([
            "status"=>"success",
            "message"=>$msg,
            "csrfHash"=>$this->security->get_csrf_hash()
        ]);
    }

    public function ajax_edit($id) {
        if (!$this->input->is_ajax_request() || !has_permission('categories','update')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $row = $this->Category_model->find($id);
        echo json_encode([
            "data"=>$row,
            "csrfHash"=>$this->security->get_csrf_hash()
        ]);
    }

    public function ajax_delete($id) {
        if (!$this->input->is_ajax_request() || !has_permission('categories','delete')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([
                'status'=>'error','message'=>'Forbidden','csrfHash'=>$this->security->get_csrf_hash()
            ]));
            return;
        }

        $this->Category_model->delete($id);
        echo json_encode([
            "status"=>"success",
            "message"=>"Kategori berhasil dihapus",
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
