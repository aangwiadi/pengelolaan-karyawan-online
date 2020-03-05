<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_m extends CI_Model{

    public function auth(){
        $cek = $this->db->get_where('users',['nik' => $this->input->post('nik',true)])->row_array();
        if ($cek){
            if($cek['blokir'] == '1'){
                $this->session->set_flashdata('pesan','<div class="alert alert-danger" role="alert">
                        User anda Terblokir!!, hubungi administrator..
                    </div>');
                    return "Gagal Login";
                    redirect(base_url());
            }else{
                $cek_password = $this->db->select('u.level_user,dk.*')->from('users u')
                ->join('data_karyawan dk','dk.nik = u.nik','left')
                ->where(['u.nik' => $cek['nik'], 'u.password' => hash_password($this->input->post('password',true)) ])->get()->row_array();
                if ($cek_password){
                    // berhasil login == masuk bos ku neng kui
                    $this->session->berhasil_login = true;
                    $this->session->set_userdata(['level_user' => $cek_password['level_user'], 
                    'nama_lengkap' =>$cek_password['nama_lengkap'],'nik'=>$cek_password['nik'],
                    'tgl_lahir' => $cek_password['tgl_lahir'] 
                    ]);

                    $this->db->where('nik',$cek['nik'])->update('users',['salah_password' => 0,'last_login' => date('Y-m-d H:i:s')]);
                    return "Berhasil Login";
                    redirect(base_url().'dashboard');
                }else{
                    // salah password
                    
                    $this->db->set('salah_password','salah_password + 1',false)
                    ->where('nik',$cek['nik'])->update('users');
                    $sisa_kesempatan = 2 - $cek['salah_password'];

                    if ($cek['salah_password'] == 2){
                        $this->db->where('nik',$cek['nik'])->update('users',['blokir' => '1']);
                        $this->session->set_flashdata('pesan','<div class="alert alert-danger" role="alert">
                            User anda Terblokir!!, hubungi administrator..
                        </div>');
                    }else{
                        $this->session->set_flashdata('pesan','<div class="alert alert-danger" role="alert">
                        Password salah, sisa kesempatan '.$sisa_kesempatan.'. Silahkan coba lagi..
                    </div>');
                    }
                    return "Gagal Login";
                    redirect(base_url());
                }
            }
        }else{
            $this->session->set_flashdata('pesan','<div class="alert alert-danger" role="alert">
                Username atau password salah bro..
            </div>');
            return "Gagal Login";
            redirect(base_url());
        }
    }

    public function index_login(){
        return "tes2";
    }

}