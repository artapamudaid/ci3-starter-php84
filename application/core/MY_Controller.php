<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    protected function render($view, $data = []) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layouts/main', $data);
    }
}
