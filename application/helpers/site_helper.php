<?php

if (!function_exists('cek_aktif_login')){
    function cek_aktif_login(){
        $ci = get_instance();
        if (!$ci->session->berhasil_login){
            redirect(base_url());
        }
    }
}

function main_menu(){
    $ci = get_instance();
    $main_menu = $ci->db->select('m.*,a.akses,a.tambah,a.edit,a.hapus')
    ->from('menu m')
    ->join('akses a','a.kode_menu = m.kode_menu','left')
    ->where(['a.level_user' => $ci->session->level_user ,'m.aktif' => '1', 'level' => 'main_menu','a.akses' => '1'])
    ->order_by('m.no_urut','ASC')
    ->get()->result_array();
    
    return $main_menu;
}
function sub_menu(){
    $ci = get_instance();
    $sub_menu = $ci->db->select('m.*,a.akses,a.tambah,a.edit,a.hapus')
    ->from('menu m')
    ->join('akses a','a.kode_menu = m.kode_menu','left')
    ->where(['a.level_user' => $ci->session->level_user ,'m.aktif' => '1', 'level' => 'sub_menu','a.akses' => '1'])
    ->order_by('m.no_urut','ASC')
    ->get()->result_array();
    
    return $sub_menu;
}

if (!function_exists('cek_akses_user')){
    function cek_akses_user(){
        $ci = get_instance();

        $cek = $ci->db->select('*')->from('akses a')
        ->join('menu m','m.kode_menu = a.kode_menu','left')
        ->where(['a.level_user' => $ci->session->level_user,'m.url' => $ci->uri->segment(1,0).$ci->uri->slash_segment(2,'leading')])
        ->get()->row_array();


        if (!$cek){
            redirect(base_url('unauthorized'));
        }else{
            if ($cek['akses'] != 1){
                redirect(base_url('unauthorized'));
            }else{
                return $cek;
            }
        }

    }
}

if (!function_exists('cek_post')){
    function cek_post(){
        if (!count($_POST)) 
        redirect(base_url('unauthorized'));
    }
}
if (!function_exists('cek_ajax')){
    function cek_ajax(){
        $ci = get_instance();
        if(!$ci->input->is_ajax_request()){
            redirect(base_url('unauthorized'));
        }
    }
}

if (!function_exists('hash_password')){
    function hash_password($pw = 'hash_pwd'){
		$st = '';
		for ($i=0; $i < strlen($pw); $i++ ){
			$st.= hash('sha256',substr($pw, $i,1));
		}
		return hash('sha256',$st);
    }
}
if (!function_exists('hash_id')){
    function hash_id($id = 'hash_id'){
		$st = '';
		for ($i=0; $i < strlen($id); $i++ ){
			$st.= hash('sha256',substr($id, $i,1));
		}
		return hash('md5',$st);
    }
}
if (!function_exists('convert_date_to_en')){
    function convert_date_to_en($tgl = '09-09-1988'){
        if (strlen($tgl) > 10){
            $date = explode(' ',$tgl);
            return (explode('-',$date[0])[2].'-'.explode('-',$date[0])[1].'-'.explode('-',$date[0])[0]).' '. $date[1];
        }else{
            return explode('-',$tgl)[2].'-'.explode('-',$tgl)[1].'-'.explode('-',$tgl)[0];
        }
    }
}
if (!function_exists('convert_date_to_id')){
    function convert_date_to_id($tgl = '1988-09-09'){
        if (strlen($tgl) > 10){
            $date = explode(' ',$tgl);
            return (explode('-',$date[0])[2].'-'.explode('-',$date[0])[1].'-'.explode('-',$date[0])[0]).' '. $date[1];
        }else{
            return explode('-',$tgl)[2].'-'.explode('-',$tgl)[1].'-'.explode('-',$tgl)[0];
        }
    }
}
if (!function_exists('selisih_hari')){
    function selisih_hari($date_1 , $date_2 , $differenceFormat = '%a' ){
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat);
    }
}

if (!function_exists('default_konfigurasi')){
    function default_konfigurasi(){
        $ci = get_instance();
        return $ci->db->get('konfigurasi')->row();
    }
}

if (!function_exists('get_data_karyawan')){
    function get_data_karyawan(){
        $ci = get_instance();
        return $ci->db->get_where('data_karyawan',['nik' => $ci->session->nik])->row();
    }
}

if (!function_exists('list_notifikasi')){
    function list_notifikasi(){
        $ci = get_instance();
        return $ci->db->select('n.*,dk.nama_lengkap,dk.foto')->from('notif n')
        ->join('data_karyawan dk','dk.nik = n.nik_client','left')
        ->where(['n.nik_target' => $ci->session->nik, 'read' => 'N'])->get()->result_array();
    }
}

if (!function_exists('hitung_waktu')){
    function hitung_waktu($waktu = '2020-01-01 00:00:00'){
        return 'a';
    }
}