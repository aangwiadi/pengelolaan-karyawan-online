<div class="modal fade modal-approval" id="modal-lg">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><?= $this->input->post('jenis_notif') ?></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form method="post" id="form-notifikasi">
              <div class="modal-body">
                <div class="text-center mb-3">
                  <img class="profile-user-img img-fluid img-circle" src="<?= base_url().'assets/images/profil-user/'.$pengajuan_cuti['foto'] ?>" alt="User profile picture">
                </div>
                <table class="table table-sm">
                  
                  <tbody>
                    <tr>
                      <th scope="row">No Pengajuan</th>
                      <td><?= $pengajuan_cuti['nomor'] ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Nik</th>
                      <td><?= $pengajuan_cuti['nik'] ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Nama</th>
                      <td><?= $pengajuan_cuti['nama_lengkap'] ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Tanggal Masuk</th>
                      <td><?= date('d F Y',strtotime($pengajuan_cuti['tgl_masuk'])) ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Divisi / Jabatan</th>
                      <td><?= $pengajuan_cuti['divisi'].' / '.$pengajuan_cuti['jabatan'] ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Jumlah Cuti</th>
                      <td><?= $pengajuan_cuti['jumlah_cuti'] ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Tgl Cuti</th>
                      <td><?= date('d F Y',strtotime($pengajuan_cuti['tgl_awal'])).' s/d '.date('d F Y',strtotime($pengajuan_cuti['tgl_akhir']))?></td>
                    </tr>
                    <tr>
                      <th scope="row">Tanggal Input</th>
                      <td><?= date('d F Y H:i:s',strtotime($pengajuan_cuti['tanggal'])) ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Keterangan Cuti</th>
                      <td><?= $pengajuan_cuti['alasan'] ?></td>
                    </tr>
                  </tbody>
                </table>
                <input type="hidden" name="id_notif" value="<?= $pengajuan_cuti['id_notif'] ?>" />
                <?php if ($pengajuan_cuti['nik_target'] != $pengajuan_cuti['nik_client']): ?>
                <div class="form-group">
                  <label>Persetujuan <span class="symbol required"> </span></label>
                  </br>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="approval" id="inlineRadio1" value="Y">
                    <label class="form-check-label" for="inlineRadio1">Setuju</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="approval" id="inlineRadio2" value="N"> 
                    <label class="form-check-label" for="inlineRadio2">Tolak</label>
                  </div>
                </div>
                <?php endif ?>
                <?php if ($pengajuan_cuti['nik_target'] == $pengajuan_cuti['nik_client']): 
                  echo '<button type="button" class="btn btn-'.($pengajuan_cuti['approve_atasan1'] == 'Y' ? 'success' : 'danger').' ">
                  '.($pengajuan_cuti['approve_atasan1'] == 'Y' ? 'Disetujui' : 'Ditolak').'
                </button>';
                   endif ?>
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <?php if ($pengajuan_cuti['nik_target'] != $pengajuan_cuti['nik_client']): ?>
                <button type="submit" id="simpan" class="btn btn-primary">Simpan</button>
                <?php endif ?>
                <?php if ($pengajuan_cuti['nik_target'] == $pengajuan_cuti['nik_client']): ?>
                <button type="submit" id="simpan" class="btn btn-danger">Hapus Notif</button>  
                <?php endif ?>
              </div>
            </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

<script>

  $('#form-notifikasi').validate({
    rules: {
      approval:{required:true},
    },
    messages: {
      approval:"Harus diisi",
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      error.appendTo($(element).closest('.form-group').find('.symbol'));
    },
    highlight: function (element, errorClass, validClass) {
      $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');

    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    success: function (label, element) {
        label.addClass('help-block valid');
        // mark the current input as valid and display OK icon
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
    },
    submitHandler: function () {
      $('#simpan').text('Menyimpan data...');
      $('#simpan').attr('disabled','disabled');
      // console.log(1);
      //   alert( "Form successful submitted!" );
      // $('#quickForm').submit();
      // document.getElementById("quickForm").submit();
      show_loading();
      let formdata = $('#form-notifikasi').serialize();
      xhr = $.ajax({
        method : "POST",
        url : "<?= base_url() ?>notifikasi/simpan-pengajuan-cuti",
        data : formdata,
        success: function(response){
          let result = JSON.parse(response);

          if (result.status == 'error'){
            hide_loading();
            $('#simpan').text('Simpan');
            $('#simpan').removeAttr('disabled');
          }else{
            window.location.href="<?= base_url() ?>pengajuan/pengajuan-cuti";
            $('.modal-approval').modal('hide');
            hide_loading();
          }
          Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            type: result.status,
            title: result.pesan
          })
        }
      })
    }
  });

</script>