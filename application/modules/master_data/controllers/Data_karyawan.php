<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Data_karyawan extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('Data_karyawan_m','Data_model');
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

        $this->load->view('data_karyawan_v',$data);
        
        $this->load->view('templates/footer-top');
        // js for this page only
        $this->load->view('data_karyawan_js');
        //========= end
        $this->load->view('templates/footer-bottom');
  
    }
    public function tambah(){
        cek_post();
        if (cek_akses_user()['tambah'] == 0){
            redirect(base_url('unauthorized'));
        }
        $this->load->view('data_karyawan_tambah_v');
    }

    public function simpan_tambah(){
        cek_post();
        if (cek_akses_user()['tambah'] == 0){
            redirect(base_url('unauthorized'));
        }
        echo $this->Data_model->simpan_tambah();
    }
    public function list_jabatan(){
        cek_post();
        if ($this->input->post('divisi') != "") {
            $list_jabatan = ['STAFF' => 'Staff','HEAD' => 'Head','SPV' => 'Supervisor',
                'ASS_MANAGER' => 'Assisten Manager','MANAGER' => 'Manager','DIREKTUR' => 'Direktur','PRESDIR' => 'Presiden Direktur'];
            echo "<option value=''>Pilih Jabatan</option>";
            foreach($list_jabatan as $key => $value){
                echo "<option value='$key'>$value</option>";
            }
        }else{
            echo "<option value=''>Pilih Divisi Dulu</option>";
        }
              
    }
    public function list_atasan(){
        cek_post();
        $data = $this->Data_model->list_atasan();
        if ($data){
            echo "<option value=''>Pilih Atasan</option>";
            foreach($data as $atasan){
                echo "<option value='$atasan[nik]'>$atasan[nama_lengkap]</option>";
            }
        }else{
            echo "<option value=''>Pilih Atasan</option>";
            echo "<option value='".$this->input->post('nik')."'>Diri Sendiri</option>";
        }
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
            $row = ['nik' => $data['nik'] ,'level_user' => $data['level_user'],'last_login'=>$data['last_login'],
            'nama_lengkap' => $data['nama_lengkap'],'divisi' => $data['divisi'],'email' => $data['email'],
            'tgl_lahir' => $data['tgl_lahir'],
            'action' => $tombol_action,
            
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