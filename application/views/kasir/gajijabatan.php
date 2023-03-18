  
    <section class="content">
        
        <?= $this->session->flashdata('msg') ?>
          <div class="row"> 
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                       <div class="header"> 
                             <center><b>DATA GAJI JABATAN</b></center>
                        </div>
                        <div class="body"> 

                            <div class="table-responsive">
                             <table class="table table-bordered table-striped table-hover dataTable js-basic-example">
                              <thead>
                                    <tr>   
                                        <th>ID Jabatan</th> 
                                        <th>Nama Jabatan</th>    
                                        <th>Gaji</th>      
                                        <th>Aksi</th>   
                                    </tr>
                                </thead>  
                            
                                <?php $i = 1; foreach ($list_data as $row): ?> 
  
                                  <tr> 
                                    <th> <?=$row->id_jabatan?> </th>
                                    <td> <?=$row->nama_jabatan?> </td>  
                                    <td> Rp <?= number_format($row->gaji,0,'','.') ?> </td>  
                                    <td> 
                                       <?php if ($row->id_jabatan > 0) { ?>
                                             <center>
                                            <a data-toggle="modal" data-target="#edit-<?=$row->id_jabatan?>" >
                                                <button class="btn  bg-indigo">Edit Gaji</button>
                                            </a> 
                                        </center>
                                    <?php } ?>
                                    </td>
                                  </tr>


                                  <?php endforeach; ?>
                              </table> 
                           </div>

                        </div>
                    </div>
                </div>
            </div> 
       
    </section>

 
 
<?php $i = 1; foreach ($list_data as $row): ?> 
    <div class="modal fade" id="edit-<?=$row->id_jabatan?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h4 class="modal-title" id="defaultModalLabel"><center>Form Edit Data </center></h4>
                </div>
                <div class="modal-body">
                  <form action="<?= base_url('bendahara/update_gajijabatan')?>" method="Post"  >  
                 
                         <table class="table table-bordered"> 
                                <tr>   
                                    <th>Nama Jabatan</th> 
                                    <th> 
                                       <input type="hidden" required value="<?=$row->id_jabatan?>" name="id" class="form-control" readonly>
                                       <?=$row->nama_jabatan?>
                                    </th>  
                                </tr> 
                                <tr>   
                                    <th>Gaji</th> 
                                    <th>
                                       <input type="text" required value="<?=$row->gaji?>" name="gaji" class="form-control">
                                    </th>  
                                </tr>  
                        </table>
                 
                <input  type="submit" class="btn bg-indigo btn-block"   name="update" value="Edit">  <br><br>
          
                    <?php echo form_close() ?> 
                </div> 
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
 
<?php endforeach; ?>