<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper(['url','form','permission']);
        $this->load->library(['form_validation','session']);
    }

    public function login() {

		$data['layout'] = 'auth';
        $this->render('auth/login', $data);
    }

    // AJAX login for template
    public function ajax_login() {
        $this->output->set_content_type('application/json');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username','Username','required|trim|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('password','Password','required|trim|min_length[5]|max_length[100]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'errors' => $this->form_validation->error_array(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);
        $user = $this->User_model->verify_password($username, $password);

        if ($user && $user->is_active) {
            $this->session->set_userdata('user', (object)[
                'id' => $user->id,
				'email' => $user->email,
                'username' => $user->username,
                'full_name' => $user->full_name,
				'role' => $user->role,
				'role_name' => $user->role_name
            ]);
            
            $menus = $this->Menu_model->get_menu_tree_by_role($user->role);
            $this->session->set_userdata('user_menus', $menus);
			
            load_user_permissions($user->id);
            echo json_encode([
                'status' => 'success',
                'redirect' => site_url('dashboard'),
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Username / Password salah',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
