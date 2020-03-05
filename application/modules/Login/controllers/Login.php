<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('Login_m','Lm');
        $this->load->library('form_validation');
    }
    
    public function index(){
        if ($this->session->berhasil_login){
            redirect(base_url().'dashboard');
            return false;
        }
        $this->form_validation->set_rules('password', 'Password', ['required','max_length[10]'],
        ['required' => 'Password harus diisi','max_length' => 'Maksimal 10 huruf']);
        
        $this->form_validation->set_rules('nik', 'Nik', ['required','min_length[5]'],
        ['required' => 'Nik harus diisi','min_length' => 'Min 5 huruf']);
        
        $this->form_validation->set_error_delimiters('<div class="error-input">', '</div>');


        
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('login_v');
        }else{
            $this->_auth();
        }
    }
    public function out(){
        $this->session->sess_destroy();
        redirect(base_url());
    }

    private function _auth(){
        $cek_login = $this->Lm->auth();
        if ($cek_login == 'Berhasil Login'){
            redirect(base_url().'dashboard');
        }else{
            // redirect(base_url());
            $this->load->view('login_v');
        }
    }

}
?>