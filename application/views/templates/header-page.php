<!-- Content Header (Page header) -->
<?php
    $ci = get_instance();
?>
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?= ucwords(str_replace('-',' ',$ci->uri->segment(2,0))) ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><?= ucwords(str_replace('-',' ',$ci->uri->segment(1,0))) ?></li>
              <li class="breadcrumb-item active"><?= ucwords(str_replace('-',' ',$ci->uri->segment(2,0))) ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>