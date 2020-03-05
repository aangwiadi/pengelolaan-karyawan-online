<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Pengajuan_cuti_m extends CI_Model {
	public function sisa_cuti(){
		return $this->db->get_where('data_cuti',['nik' => $this->session->nik,'tahun' => date('Y')])->row_array();
	}
	public function hitung_hari_cuti(){
		if ( convert_date_to_en($this->input->post('tgl_awal')) < date('Y-m-d',strtotime(date('Y-m-d').' + '.default_konfigurasi()->hari_min_cuti.' days')) ){
			return json_encode(['status' => 'error','pesan' => 'Pengajuan minimal H-7','jumlah_cuti' => 0]);
		}else{
			$hari_libur = explode(',',default_konfigurasi()->hari_libur);
			$selisih_hari = selisih_hari(convert_date_to_en($this->input->post('tgl_awal')),convert_date_to_en($this->input->post('tgl_akhir')));
			
			$tgl_merah = $this->db
			->where(['tgl_libur >=' => convert_date_to_en($this->input->post('tgl_awal')), 'tgl_libur <=' => convert_date_to_en($this->input->post('tgl_akhir'))])
			->get('tgl_libur_nasional')->result_array();

			$jumlah_libur = 0;
			for ($i=0; $i<=$selisih_hari; $i++){
				$hari = date( 'D',strtotime(convert_date_to_en($this->input->post('tgl_awal')) .' + '.$i.' days') ) ;
				foreach($tgl_merah as $tgl_m){
					if (date( 'Y-m-d',strtotime(convert_date_to_en($this->input->post('tgl_awal')) .' + '.$i.' days') ) == $tgl_m['tgl_libur']){
						$fix_tgl_merah = true;
						for($a=0; $a<count($hari_libur); $a++){
							if ($hari == $hari_libur[$a]){
								$fix_tgl_merah = false;
							}
						}
						if ($fix_tgl_merah)
						$jumlah_libur++;
					}
				}
				for($a=0; $a<count($hari_libur); $a++){
					if ($hari == $hari_libur[$a]){
						$jumlah_libur++;
					}
				}
			}
			$sisa_cuti = ($this->sisa_cuti() ? $this->sisa_cuti()['sisa_cuti'] : 0);
			$jumlah_cuti = $selisih_hari+1-$jumlah_libur;
			if ($jumlah_cuti > $sisa_cuti){
				return json_encode(['status' => 'error','pesan' => 'Gagal menyimpan data, Pengajuan cuti melebihi sisa cuti yang dimiliki','jumlah_cuti' => $jumlah_cuti]);
			}

			// return 'selisih hari normal:'.$selisih_hari.' potong libur :'.  $jumlah_libur;
			// return date('D',strtotime($this->input->post('tgl_awal'))).'-'.count($hari_libur) ;
			return json_encode(['status' => 'success','pesan' => 'Berhasil Hore','jumlah_cuti' => $jumlah_cuti]);
		}
		// return $this->input->post('tgl_awal');
		// return date('Y-m-d',strtotime(date('Y-m-d').' + 2 days'));
	}

	public function simpan_tambah(){
		// cek data exist
		$cek = $this->db->get_where('pengajuan_cuti',['nik' => $this->session->nik, 'approve_atasan1' => ''])->row_array();
		if ($cek){
			return json_encode(['status' => 'error',
			'pesan' => 'Gagal menyimpan data, Masih ada pengajuan yang belum di proses atasan.']);
		}
		// insert ke database
		
		$cek_cuti = json_decode($this->hitung_hari_cuti(),true);
		// if ($this->hitung_hari_cuti)

		
		if ($cek_cuti['status'] == 'error'){
			return json_encode(['status' => 'error',
			'pesan' => $cek_cuti['pesan']]);
		}

		//penomoran
		$today=date("ym");		
		$rec  = $this->db->select_max('nomor','last')->like('nomor',"PC$today",'after')->get('pengajuan_cuti')->row_array();
		$lastNoTransaksi = $rec['last'];
		$lastNoUrut = substr($lastNoTransaksi, 6, 4) + 1;
		$nextNoTransaksi = 'PC'.$today.sprintf('%04s', $lastNoUrut);

		//insert pengajuan cuti
		$this->db->insert('pengajuan_cuti',['id' => hash_id($nextNoTransaksi),'nomor' => $nextNoTransaksi,'nik' => $this->session->nik, 
		'tgl_awal' => convert_date_to_en($this->input->post('tgl_awal')),'jumlah_cuti' => $cek_cuti['jumlah_cuti'] ,
		'tgl_akhir' => convert_date_to_en($this->input->post('tgl_akhir')), 'alasan' => $this->input->post('alasan'),
		'tanggal' => date('Y-m-d H:i:s')  ]);

		//update sisa cuti
		$this->db->set('sisa_cuti','sisa_cuti-'.$cek_cuti['jumlah_cuti'],false)->where('nik',$this->session->nik)->update('data_cuti');

		// notifikasi ke atasan
		
		$this->db->insert('notif',['id' => hash_id(time()),'no_referensi' => $nextNoTransaksi, 
		'nik_target' => get_data_karyawan()->atasan1,'nik_client' => $this->session->nik,
		'tanggal' => date('Y-m-d H:i:s'), 'keterangan' => 'Menunggu persetujuan atasan','jenis' => 'Pengajuan Cuti' ]);

		return json_encode(['status' => 'success','pesan' => 'Data berhasil disimpan','tes' => json_decode($this->hitung_hari_cuti(),true)]);

	}

    var $table = 'pengajuan_cuti pc';
	var $column_order = array('','nomor','dk.nama_lengkap','divisi'); //set order berdasarkan field yang di mau
	var $column_search = array('nomor','dk.nama_lengkap','divisi'); //set field yang bisa di search
	var $order = array('pc.tanggal' => 'desc'); // default order 

	private function _get_data()
	{		
        $this->db->select('pc.*,dk.nama_lengkap,dk.divisi,dk.atasan1');
		$this->db->from($this->table);
		$this->db->join('data_karyawan dk','dk.nik = pc.nik','left');
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
		$this->db->group_start(); // open group like or like
		$this->db->where('pc.nik',$this->session->nik);
		$this->db->or_where('dk.atasan1',$this->session->nik);
		$this->db->group_end(); //close group like or like
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