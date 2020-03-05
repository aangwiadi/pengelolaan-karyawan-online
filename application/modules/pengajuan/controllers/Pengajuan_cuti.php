<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Pengajuan_cuti extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('Pengajuan_cuti_m','Data_model');
        cek_aktif_login();
        cek_akses_user();
        
    }

    public function index(){
        $data['main_menu'] = main_menu();
        $data['sub_menu'] = sub_menu();
        $data['cek_akses'] = cek_akses_user();
        // $data['notifikasi'] = list_notifikasi();

        $this->load->view('templates/header-top');
        //css for this page only
        
        //======== end
        $this->load->view('templates/header-bottom');
        $this->load->view('templates/header-notif');
        $this->load->view('templates/main-navigation',$data);

        $this->load->view('pengajuan_cuti_v',$data);
        
        $this->load->view('templates/footer-top');
        // js for this page only
        $this->load->view('pengajuan_cuti_js');
        //========= end
        $this->load->view('templates/footer-bottom');
  
    }

    public function tambah(){
        cek_post();
        if (cek_akses_user()['tambah'] == 0){
            redirect(base_url('unauthorized'));
        }
        $data['sisa_cuti'] = ($this->Data_model->sisa_cuti() ? $this->Data_model->sisa_cuti()['sisa_cuti'] : 0  );
        $this->load->view('pengajuan_cuti_tambah_v',$data);
    }

    public function hitung_hari_cuti(){
        echo $this->Data_model->hitung_hari_cuti();
    }

    public function simpan_tambah(){
        cek_post();
        if (cek_akses_user()['tambah'] == 0){
            redirect(base_url('unauthorized'));
        }
        echo $this->Data_model->simpan_tambah();
    }

    public function tampildata()
	{
        cek_post();
		$list = $this->Data_model->tampildata();
		$record = array();
		$no = $_POST['start'];
		foreach ($list as $data) {
			$no++;

            // tombol action - dicek juga punya akses apa engga gengs....
            $tombol_action = (cek_akses_user()['edit'] == 1 ? '<a href="#" ><span class="badge badge-primary btn-edit" data-jenis_action="edit" data-id="'.hash_id($data['nik']).'">Edit</span></a>' : '' ). 
            (cek_akses_user()['hapus'] == 1 ? ' <a href="#" ><span class="badge badge-danger btn-edit" data-jenis_action="hapus" data-id="'.hash_id($data['nik']).'">Hapus</span></a>' : '');

            // column buat data tables --
            $row = ['nik' => $data['nik'],'nomor' => $data['nomor'],'nama_lengkap' => $data['nama_lengkap'] ,'divisi' => $data['divisi'],
            'jumlah_cuti' => $data['jumlah_cuti'],'tgl_awal' => $data['tgl_awal'], 'tgl_akhir' => $data['tgl_akhir'],
            'status' => ($data['approve_atasan1'] == "" ? "Menunggu persetujuan" : ($data['approve_atasan1'] == "Y" ? "Disetujui" : "Ditolak")),
            'alasan' => $data['alasan'],'action' => $tombol_action,
            
            ];
			$record[] = $row;

        }
		$output = array(
						"draw" => $this->input->post('draw'),
						"recordsTotal" => $this->Data_model->count_all(),
						"recordsFiltered" => $this->Data_model->count_filtered(),
						"data" => $record,
				);
		//output to json format
		echo json_encode($output);
	}
}