<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Dashboard_m extends CI_Model {

    public function main_menu(){
        return $this->db->select('m.*,a.akses,a.tambah,a.edit,a.hapus')->from('menu m')->join('akses a','a.kode_menu = m.kode_menu','left')
        ->where(['m.level' => 'main_menu','a.akses' => '1' ])->get()->result_array();
    }
    public function sub_menu(){
        return $this->db->select('m.*,a.akses,a.tambah,a.edit,a.hapus')->from('menu m')->join('akses a','a.kode_menu = m.kode_menu','left')
        ->where(['m.level' => 'sub_menu','a.akses' => '1' ])->get()->result_array();
    }

}