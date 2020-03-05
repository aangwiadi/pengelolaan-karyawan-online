
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
      $('.modal-action').modal({
        backdrop: 'static',
        keyboard: false},'show');

        validate_form();
        cek_divisi_jabatan();
        call_datepicker();

      hide_loading();
    },
    error : function(){

    }
  })
})
function cek_divisi_jabatan(){
  $('#form-action').on('change','#divisi',function(e){
    // console.log(e.target.value);
    show_loading();
    xhr = $.ajax({
      method : "POST",
      url : "<?= base_url().$this->uri->segment(1,0).$this->uri->slash_segment(2,'both')?>list-jabatan",
      data : "divisi="+e.target.value,
      success: function(response){
        $('#jabatan').html(response);
        hide_loading();
      },
      error : function(){

      }
    })
    
  });
  $('#form-action').on('change','#jabatan',function(e){
    if (e.target.value != "" ){
      // console.log(e.target.value);
      show_loading();
      xhr = $.ajax({
        method : "POST",
        url : "<?= base_url().$this->uri->segment(1,0).$this->uri->slash_segment(2,'both')?>list-atasan",
        data : "nik="+$('#nik').val()+"&divisi="+$('#divisi').val(),
        success: function(response){
          $('#atasan1').html(response);
          $('#atasan2').html(response);
          hide_loading();
        },
        error : function(){

        }
      })
    }
  });
}
function validate_form(){
  

  $.validator.setDefaults({
    
  });
  
  $('#form-action').validate({
    rules: {
      email: {
        required: true,
        email: true,
      },
      level_user : {
        required : true
      },
      password: {
        required: true,
        minlength: 5
      },
      konfirmasi_password:{
        equalTo : '#password',
        required:true,
        minlength:5
      },
      terms: {
        required: true
      },
      nik:{required:true},
      nama_lengkap:{required:true},
      tgl_lahir:{required:true},
      tgl_masuk:{required:true},
      level_user:{required:true},
      divisi:{required:true},
      jabatan:{required:true},
      atasan1:{required:true},
      atasan2:{required:true},
    },
    messages: {
      email: {
        required: "Harus diisi",
        email: "Isi dengan Email yang benar ya"
      },
      level_user: {
        required: "Harus diisi",
      },
      password: {
        required: "Harus diisi",
        minlength: "Minimal 5 huruf"
      },
      konfirmasi_password: {
        required: "Harus diisi",
        minlength: "Minimal 5 huruf",
        equalTo: "Harus sama dengan password"
      },
      terms: "Please accept our terms",
      nik:"Harus diisi",
      nama_lengkap:"Harus diisi",
      tgl_lahir:"Harus diisi",
      tgl_masuk:"Harus diisi",
      level_user:"Harus diisi",
      divisi:"Harus diisi",
      jabatan:"Harus diisi",
      atasan1:"Harus diisi",
      atasan2:"Harus diisi",
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
          

        },
        

      })



    }
  });
}


function detail_table ( d ) {
    return 'Nama Lengkap: '+d.nama_lengkap+', Email: '+d.email+', Tanggal Lahir: '+d.tgl_lahir+'<br>'+
        'Terkhir Login: '+d.last_login+'<br>'+d.action;
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

        //Set column definition initialisation properties.
        // "columnDefs": [
        // { 
        //     "targets": [ 5 ], //last column
        //     "orderable": false, //set not orderable
        // }
        // ],
        

        "columns": [
            {
                "class":          "details-control",
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": "nik" },
            { "data": "nama_lengkap" },
            { "data": "divisi" },
            { "data": "level_user" },
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