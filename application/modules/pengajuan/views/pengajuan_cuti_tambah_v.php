<div class="modal fade modal-action" id="modal-lg">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?= ucwords($this->uri->segment(3,0))?> Data</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="" role="form" id="form-action">
      <div class="modal-body">
        <!-- form start -->
            <div class="form-group">
              <label class="control-label">
                Pilih Periode <span class="symbol required"> </span>
              </label>
              <div class="input-group input-daterange datepicker" data-date-format="dd-mm-yyyy">
                <input class="form-control" required type="text" id="tgl_awal" name="tgl_awal" value="" readonly="">
                  <span class="input-group-addon bg-primary"> s/d </span>
                <input class="form-control" required type="text" id="tgl_akhir" name="tgl_akhir" value="" readonly="">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label class="control-label">
                  Sisa cuti</span>
                </label>
                <div class="alert alert-primary" role="alert">
                  <?= $sisa_cuti ?>
                </div>
              </div>
              <div class="col-md-6">
                <label class="control-label">
                  Jumlah Pengajuan Cuti</span>
                </label>
                <div class="alert alert-success jumlah-cuti" role="alert">
                  ....
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Keterangan Cuti <span class="symbol required"> </span></label>
              <textarea class="form-control" name="alasan" rows="3" placeholder="Enter ..."></textarea>
            </div>
            
            <span class="symbol required"> Harus diisi 
            <!-- <div class="form-group mb-0">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="terms" class="custom-control-input" id="exampleCheck1">
                <label class="custom-control-label" for="exampleCheck1">I agree to the <a href="#">terms of service</a>. <span class="symbol required"> </label>
              </div>
            </div> -->
          <!-- /.card-body -->
          
        
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
        <button type="submit" id="simpan" class="btn btn-primary">Simpan</button>
      </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>