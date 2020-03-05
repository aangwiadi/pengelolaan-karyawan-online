
<script type="text/javascript">

//set default swal sweet alert..
const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
});




$('.btn-action').on('click',function(){
  show_loading();

  xhr = $.ajax({
    method : "POST",
    url : "<?= base_url().$this->uri->segment(1,0).$this->uri->slash_segment(2,'both')?>tambah",
    data:"jenis=tambah",
    success: function(response){
      $('.tampil-modal').html(response);
      $('.tampil-modal .modal-action').modal({
        backdrop: 'static',
        keyboard: false},'show');

        validate_form();
        call_datepicker();
        tanggal_cuti();
      hide_loading();
    },
    error : function(){

    }
  })
})

function tanggal_cuti(){
  $('.datepicker').datepicker({autoclose: true, todayHighlight: true}).on('changeDate', function(selected) {
      
    show_loading();
    xhr = $.ajax({
      method : "POST",
      url : "<?= base_url().$this->uri->segment(1,0).$this->uri->slash_segment(2,'both')?>hitung-hari-cuti",
      data : "tgl_awal="+$('#tgl_awal').val()+"&tgl_akhir="+$('#tgl_akhir').val(),
      success : function(response){
        console.log(response);
        result = JSON.parse(response);
        if (result.status == 'success'){
          $('.jumlah-cuti').html(result.jumlah_cuti);
        }else{
          Toast.fire({
            type: result.status,
            title: result.pesan
          })
        }
        hide_loading();
      },
      error: function(error){

      }
    })

  })
}
function validate_form(){
  $('#form-action').validate({
    rules: {
      
      tgl_awal:{required:true},
      tgl_akhir:{required:true},
      alasan:{required:true,minlength:5},
    },
    messages: {
      
      tgl_awal:"Harus diisi",
      tgl_akhir:"Harus diisi",
      alasan:{required:"Harus diisi",minlength:'Minimal 5 Huruf' },
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
    //   element.closest('.form-group').append(error);
    //   error.insertAfter(element.closest('.form-group').find('label'));
        // element.closest('.form-group').find('label').append(error);

    //   error.appendTo(element.closest('.form-group').find('label'));
    // error.insertAfter(element);
        // if (element.attr("type") == "radio" || element.attr("type") == "checkbox") { // for chosen elements, need to insert the error after the chosen container
        //     error.insertAfter($(element).closest('.form-group').children('div').children().last());
        // } else if (element.attr("name") == "dd" || element.attr("name") == "mm" || element.attr("name") == "yyyy") {
        //     error.insertAfter($(element).closest('.form-group').children('div'));
        // } else {
        //     error.appendTo($(element).closest('.form-group').find('.symbol'));
        //     // error.insertAfter(element);
        //     // for other inputs, just perform default behavior
        // }

        error.appendTo($(element).closest('.form-group').find('.symbol'));
    },
    highlight: function (element, errorClass, validClass) {
    //   $(element).addClass('is-invalid');
      $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');

    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).closest('.form-group').removeClass('has-error');

    //   $(element).removeClass('is-invalid');
    //   $(element).addClass('is-valid');
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
      let formdata = $('#form-action').serialize();
      xhr = $.ajax({
        method : "POST",
        url : "<?= base_url().$this->uri->segment(1,0).$this->uri->slash_segment(2,'both') ?>simpan-tambah",
        data : formdata,
        success: function(response){
          let result = JSON.parse(response);

          if (result.status == 'error'){
            hide_loading();
            $('#simpan').text('Simpan');
            $('#simpan').removeAttr('disabled');
          }else{
            reload_table();
            $('.modal-action').modal('hide');
            hide_loading();
          }
          Toast.fire({
            type: result.status,
            title: result.pesan
          })
        }
      })
    }
  });
}



function detail_table ( d ) {
    return 'Tgl Cuti : '+ d.tgl_awal +' s/d '+d.tgl_akhir+'</br>Alasan : '+d.alasan+'<br>'+d.action;
}
$(document).ready(function () {
    table_data = $('#example1').DataTable({

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?= base_url().$this->uri->segment(1,0).$this->uri->slash_segment(2,'leading')."/tampildata"; ?>",
            "type": "POST"
        },

        // Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ -2,5 ], //last column
            // "targets": 'nosort', //last column
            "orderable": false, //set not orderable
        }
        ],
        

        "columns": [
            {
                "class":          "details-control",
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": "nomor" },
            { "data": "nama_lengkap" },
            { "data": "divisi" },
            { "data": "jumlah_cuti" },
            { "data": "status" },
            // { "data": "action" },
        ],

    });
    var detailRows = [];
    $('#example1 tbody').on( 'click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table_data.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
 
            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );
            row.child( detail_table( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );

    // action edit dan delete
    $('#example1 tbody').on('click','.btn-edit',function(e){
      // alert(e.data);
      console.log('id',e.target.dataset.id);
      console.log('jenis action',e.target.dataset.jenis_action);
    })

 
    // On each draw, loop over the `detailRows` array and show any child rows
    // table_data.on( 'draw', function () {
    //     $.each( detailRows, function ( i, id ) {
    //         $('#'+id+' td.details-control').trigger( 'click' );
    //     } );
    // } );

  
});
function reload_table()
{
  table_data.ajax.reload(null,false); //reload datatable ajax 
}


</script>