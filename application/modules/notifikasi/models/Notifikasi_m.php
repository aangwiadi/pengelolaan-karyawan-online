<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Notifikasi_m extends CI_Model {
	public function pengajuan_cuti(){
		return $this->db->select('n.id as id_notif,n.nik_target,n.nik_client,pc.*,dk.*')->from('notif n')
		->join('pengajuan_cuti pc','pc.nomor = n.no_referensi','left')
		->join('data_karyawan dk','dk.nik = pc.nik','left')
		->where(['n.id' => $this->input->post('id')])->get()->row_array();
	}
	public function simpan_pengajuan_cuti(){
		// get data notif
		$notif = $this->db->get_where('notif',['id' => $this->input->post('id_notif')])->row_array();

		$ok = $this->db->affected_rows();

		
		if ($ok){
			// kondisi hapus notif
			if ($notif['nik_target'] == $notif['nik_client']){
				$this->db->where('id',$this->input->post('id_notif'))->update('notif',['read' => 'Y']);
				return json_encode(['status' => 'success', 'pesan' => 'Data berhasil disimpan']);	
			}

			//update status pengajuan sesuai approval
			$this->db->where('nomor',$notif['no_referensi'])->update('pengajuan_cuti',['approve_atasan1' => $this->input->post('approval'),
			'nik_atasan1' => $this->session->nik, 'tgl_approve_atasan1' => date('Y-m-d H:i:s') ]);

			//update status read current notif
			$this->db->where('id',$this->input->post('id_notif'))->update('notif',['read' => 'Y']);

			//notif ke pemohon
			$this->db->insert('notif',['id' => hash_id(time()), 'no_referensi' => $notif['no_referensi'],'nik_target' => $notif['nik_client'],
			'nik_client' => $notif['nik_client'],'tanggal' => date('Y-m-d H:i:s'), 'keterangan' => 'Pengajuan cuti : '.$notif['no_referensi'].' sudah diproses',
			'jenis' => 'Pengajuan Cuti'
			]);
			
			return json_encode(['status' => 'success', 'pesan' => 'Data berhasil disimpan']);
		}else{
			return json_encode(['status' => 'error', 'pesan' => 'Gagal, data tidak sesuai']);
		}

	}
	
}