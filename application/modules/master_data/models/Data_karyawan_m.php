<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Data_karyawan_m extends CI_Model {
	public function list_atasan(){
		return $this->db->get_where('data_karyawan',['divisi' => $this->input->post('divisi')])->result_array();
	}
	public function simpan_tambah(){
		// cek user exist
		$cek = $this->db->get_where('users',['nik' => $this->input->post('nik')])->row_array();
		if ($cek){
			return json_encode(['status' => 'error','pesan' => 'Gagal menyimpan data,Nik sudah ada..']);
		}
		// insert ke table user
		$this->db->insert('users',
		['nik' => $this->input->post('nik'),
		'password' => hash_password($this->input->post('password')),
		'level_user' => $this->input->post('level_user'),
		'last_login' => date('Y-m-d H:i:s')
		]);
		//insert ke table data karyawan
		$this->db->insert('data_karyawan',['nik' => $this->input->post('nik'),'nama_lengkap' => $this->input->post('nama_lengkap'),
		'email' => $this->input->post('email'),'divisi' => $this->input->post('divisi'),
		'tgl_lahir' => convert_date_to_en($this->input->post('tgl_lahir')),
		'tgl_masuk' => convert_date_to_en($this->input->post('tgl_masuk')),
		'divisi' => $this->input->post('divisi'),'jabatan' => $this->input->post('jabatan'),
		'atasan1' => $this->input->post('atasan1'),'atasan2' => $this->input->post('atasan2')  ]);

		return json_encode(['status' => 'success','pesan' => 'Data berhasil disimpan']);

	}

    var $table = 'users u';
	var $column_order = array('','u.nik','nama_lengkap','divisi','level_user'); //set order berdasarkan field yang di mau
	var $column_search = array('u.nik','level_user','nama_lengkap','divisi'); //set field yang bisa di search
	var $order = array('u.nik' => 'desc'); // default order 

	private function _get_data()
	{		
        $this->db->select('u.level_user,u.last_login,dk.*');
		$this->db->from($this->table);
		$this->db->join('data_karyawan dk','dk.nik = u.nik','left');
		$i = 0;	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // cek kalo ada search data
			{				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open group like or like
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close group like or like
			}
			$i++;
		}		
		if(isset($_POST['order'])) // cek kalo click order
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function tampildata()
	{
		$this->_get_data();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result_array();
	}

	function count_filtered()
	{
		$this->_get_data();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
}