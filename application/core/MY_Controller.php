<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    protected function render($view, $data = []) {
		$layout = isset($data['layout']) ? $data['layout'] : 'main';
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layouts/' . $layout, $data);
    }
}
