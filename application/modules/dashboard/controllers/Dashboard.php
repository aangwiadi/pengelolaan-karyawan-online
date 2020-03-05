<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Dashboard extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('Dashboard_m','Mymodel');
        cek_aktif_login();
    }

    public function index(){
        
        $data['main_menu'] = main_menu();
        $data['sub_menu'] = sub_menu();

        $this->load->view('templates/header-top');
        //css for this page only
        $this->load->view('dashboard_css');
        //======== end
        $this->load->view('templates/header-bottom');
        $this->load->view('templates/header-notif');
        $this->load->view('templates/main-navigation',$data);
        
        $this->load->view('dashboard_v');
        $this->load->view('templates/footer-top');
        // js for this page only
        $this->load->view('dashboard_js');
        //========= end
        $this->load->view('templates/footer-bottom');

        
    }
}