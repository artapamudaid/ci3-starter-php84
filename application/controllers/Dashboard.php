<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    public function __construct(){
        parent::__construct();

		if ($this->session->userdata('user')->id == NULL) {
			header("Location:" . site_url('auth/login'));
		}
    }

	public function index() {
		$this->render('dashboard/index');
	}
}
