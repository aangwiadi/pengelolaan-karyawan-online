<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Dashboard extends CI_Controller {

    public function index(){
        if (!$this->session->berhasil_login){
            redirect(base_url());
            return false;
        }
        $this->load->model('Dashboard_m','Mymodel');
        $data['main_menu'] = $this->Mymodel->main_menu();
        $data['sub_menu'] = $this->Mymodel->sub_menu();

        // foreach($data['main_menu'] as $m_m){
        //     echo $m_m['nama_menu'];
        // }
        // foreach($data['sub_menu'] as $m_m){
        //     echo $m_m['nama_menu'];
        // }
        $this->load->view('templates/header');
        $this->load->view('templates/main-navigation',$data);
        $this->load->view('templates/content');
        $this->load->view('templates/footer');

        
    }
}