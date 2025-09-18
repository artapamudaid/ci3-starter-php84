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
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('username','username','required|trim|min_length[3]|max_length[50]');
            $this->form_validation->set_rules('password','password','required|trim|min_length[5]|max_length[100]');

            if ($this->form_validation->run() === TRUE) {
                $username = $this->input->post('username', TRUE);
                $password = $this->input->post('password', TRUE);

                $user = $this->User_model->verify_password($username, $password);
                if ($user && $user->is_active) {
                    $this->session->set_userdata('user', (object)[
                        'id' => $user->id,
                        'username' => $user->username,
                        'full_name' => $user->full_name
                    ]);
                    // build permission cache
                    load_user_permissions($user->id);

                    redirect('products');
                } else {
                    $this->session->set_flashdata('error','Username / Password salah atau akun nonaktif');
                    redirect('auth/login');
                }
            }
        }
        $this->render('auth/login');
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
                'username' => $user->username,
                'full_name' => $user->full_name
            ]);
            load_user_permissions($user->id);
            echo json_encode([
                'status' => 'success',
                'redirect' => site_url('products'),
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
