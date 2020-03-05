<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Notifikasi extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('Notifikasi_m','Data_model');
        cek_aktif_login();
        
    }

    public function approval(){
        cek_ajax();
        if ($this->input->post('jenis_notif') == 'Pengajuan Cuti'){
            $data['pengajuan_cuti'] = $this->Data_model->pengajuan_cuti();
            $this->load->view('notifikasi_cuti_v',$data);
        }
        
    }

    public function simpan_pengajuan_cuti(){
        cek_ajax();
        echo $this->Data_model->simpan_pengajuan_cuti();
    }

    
}